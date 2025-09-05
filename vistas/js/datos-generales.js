$(function () {
  // *************************************************
  // * VARIABLES GLOBALES
  // *************************************************
  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  // *************************************************
  // * HELPERS
  // *************************************************

  // Envio del formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit(); // Enviar los datos
  }
  // Agregar funciones select2 a los inputs
  $(".select2").select2({
    tags: true,
    width: "100%",
  });

  //VALIDAR QUE EL ARCHIVO SEA PDF
  function validarArchivoPDF(archivo) {
    // Verificar que sea PDF
    if (archivo.type !== "application/pdf") {
      Swal.fire({
        title: "Error en el tipo de archivo",
        text: `¡El archivo "${archivo.name}" debe ser PDF!`,
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
      return false;
    }

    // Verificar tamaño máximo (4MB)
    if (archivo.size > 4000000) {
      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: `¡El archivo "${archivo.name}" no debe pesar más de 4MB!`,
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
      return false;
    }

    return true; // ✅ Pasa validaciones
  }

  // Función para mostrar SweetAlert de éxito
  function mostrarExito(mensaje, callback = null) {
    Swal.fire({
      icon: "success",
      title: "¡Éxito!",
      text: mensaje || "La operación se realizó correctamente.",
      confirmButtonText: "Aceptar",
    }).then(() => {
      if (typeof callback === "function") callback();
    });
  }

  // Función para mostrar SweetAlert de error
  function mostrarError(mensaje) {
    Swal.fire({
      icon: "error",
      title: "Hubo un problema",
      text: mensaje || "Ocurrió un error en el sistema",
      confirmButtonText: "Aceptar",
    });
  }

  // *************************************************
  // * FUNCIONES RELACIONADAS A LOS ARCHIVOS
  // *************************************************

  /*==============================================================
    Abrir el input al presionar el botón Cargar archivo
    ==============================================================*/
  let tipo = 0;

  $(".btnSubirArchivo").click(function () {
    document.getElementById("archivo").click();
    tipo = $(this).attr("id"); // Get the id of the clicked button and assign it to tipo
  });

  /*========================================================
    Validar tipo y tamaño de los archivos
    ========================================================*/
  $("#archivo").change(function () {
    let archivo = this.files[0];
    if (!archivo) return;

    if (!validarArchivoPDF(archivo)) {
      $(this).val("");
      return;
    }

    let formData = new FormData();
    formData.append("tipo", tipo);
    formData.append("archivo", archivo);
    formData.append("accion", "subirArchivos");

    $.ajax({
      url: rutaAjax + "app/Ajax/ProveedorArchivoAjax.php",
      method: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        if (respuesta.error === true) {
          mostrarError(respuesta.mensaje, () => location.reload());
        } else {
          mostrarExito(respuesta.mensaje, () => location.reload());
        }
      })
      .fail(function (error) {
        mostrarError("Ocurrió un error al procesar la solicitud.");
      })
      .always(function () {});
  });

  /*========================================================
    Ver el archivo PDF en un iframe al hacer clic en el botón "Ver Archivo"
    ========================================================*/
  $(".verArchivo").on("click", function () {
    const archivoRuta = $(this).attr("archivoRuta");
    $("#archivoIframe").attr("src", archivoRuta);
  });

  /*========================================================
    Eliminar el archivo al hacer clic en el botón "Eliminar Archivo"
    ========================================================*/
  $("i.eliminarArchivo").on("click", function (e) {
    let folio = $(this).attr("folio");
    let archivoId = $(this).attr("archivoId");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar este Archivo (Folio: " +
        folio +
        ") ?",
      text: "No podrá recuperar esta información!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, quiero eliminarlo!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        eliminarArchivo(archivoId);
      }
    });
  });

  /*========================================================
    Eliminar el archivo al hacer clic en el botón "Eliminar Archivo"
    ========================================================*/
  function eliminarArchivo(archivoId = null) {
    if (archivoId == null) return;

    let dataSend = new FormData();
    dataSend.append("accion", "eliminarArchivo");
    dataSend.append("archivoId", archivoId);

    $.ajax({
      url: rutaAjax + "app/Ajax/ProveedorArchivoAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        if (respuesta.error === true) {
          mostrarError(respuesta.mensaje);
        } else {
          mostrarExito(respuesta.mensaje, () => location.reload());
        }
      })
      .fail(function (error) {
        mostrarError("Ocurrió un error al procesar la solicitud.");
      })
      .always(function () {});
  }
});
