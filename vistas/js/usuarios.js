$(function () {
  let parametrosTableList = { responsive: true };
  let tableList = document.getElementById("tablaUsuarios");
  let elementmodalAsignarPuesto = document.querySelector("#modalAsignarPuesto");

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tableList != null)
    fAjaxDataTable(rutaAjax + "app/Ajax/UsuarioAjax.php", "#tablaUsuarios");

  // Confirmar la eliminación del Usuario
  // $("table tbody").on("click", "button.eliminar", function (e) {
  $(tableList).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar este Usuario (Usuario: " +
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
        form.submit();
      }
    });
  });

  // Envio del formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit(); // Enviar los datos
  }
  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  // Activar el elemento Select2
  $(".select2").select2({
    tags: false,
  });
  let elementEmpresaId = $("#empresaId.select2.is-invalid");
  if (elementEmpresaId.length == 1) {
    $(".select2-selection.select2-selection--single").css(
      "border-color",
      "#dc3545"
    );
    $(".select2-selection.select2-selection--single").css(
      "background-image",
      "url(" + rutaAjax + "vistas/img/is-invalid.svg)"
    );
    $(".select2-selection.select2-selection--single").css(
      "background-repeat",
      "no-repeat"
    );
    $(".select2-selection.select2-selection--single").css(
      "background-position",
      "right calc(0.375em + 1.0875rem) center"
    );
    $(".select2-selection.select2-selection--single").css(
      "background-size",
      "calc(0.75em + 0.375rem) calc(0.75em + 0.375rem"
    );
  }

  $("#foto").hide();

  /*=============================================
  Abrir el input al presionar la imágen (figure)
  =============================================*/
  $("#imgFoto").click(function () {
    document.getElementById("foto").click();
  });

  /*=============================================
  Actualizar el previsual de la imágen
  =============================================*/
  $("#foto").change(function () {
    var imagen = this.files[0];

    /*=============================================
      VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
      =============================================*/

    if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
      $("#foto").val("");

      Swal.fire({
        title: "Error en el tipo de archivo",
        text: "¡La imagen debe estar en formato JPG o PNG!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else if (imagen["size"] > 2000000) {
      $("#foto").val("");

      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: "¡La imagen no debe pesar más de 2MB!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else {
      var datosImagen = new FileReader();
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function (event) {
        var rutaImagen = event.target.result;

        $(".previsualizar").attr("src", rutaImagen);
      });
    }
  });

  $("#firma").hide();

  /*=============================================
  Abrir el input al presionar la firma (picture)
  =============================================*/
  $("#imgFirma").click(function () {
    document.getElementById("firma").click();
  });

  /*=============================================
  Actualizar el previsual de la firma
  =============================================*/
  $("#firma").change(function () {
    let imagen = this.files[0];

    /*============================================
    VALIDAMOS EL FORMATO DE LA FIRMA SEA JPG O PNG
    ============================================*/

    if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {
      $("#firma").val("");

      Swal.fire({
        title: "Error en el tipo de archivo",
        text: "¡La imagen debe estar en formato JPG o PNG!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else if (imagen["size"] > 2000000) {
      $("#firma").val("");

      Swal.fire({
        title: "Error en el tamaño del archivo",
        text: "¡La imagen no debe pesar más de 2MB!",
        icon: "error",
        confirmButtonText: "¡Cerrar!",
      });
    } else {
      let datosImagen = new FileReader();
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function (event) {
        let rutaImagen = event.target.result;

        $(".previsualizarFirma").attr("src", rutaImagen);
      });
    }
  });

  let tableListPuesto = document.getElementById("tablaPuestos");

  function fActualizarListado(rutaAjax, idTabla, parametros = {}) {
    fetch(rutaAjax, {
      method: "GET",
      cache: "no-cache",
      headers: {
        "Content-Type": "application/json",
      },
    })
      .then((response) => response.json())
      .catch((error) => console.log("Error:", error))
      .then((data) => {
        $(idTabla)
          .DataTable({
            autoWidth: false,
            responsive:
              parametros.responsive === undefined
                ? true
                : parametros.responsive,
            data: data.datos.registros,
            columns: data.datos.columnas,
            paging: false, // Deshabilita la paginación
            searching: false, // Deshabilita la barra de búsqueda
            info: false,
            createdRow: function (row, data, index) {
              if (data.colorTexto != "")
                $("td", row).eq(3).css("color", data.colorTexto);
              if (data.colorFondo != "")
                $("td", row).eq(3).css("background-color", data.colorFondo);
            },
            language: LENGUAJE_DT,
            aaSorting: [],
          })
          .buttons()
          .container()
          .appendTo(idTabla + "_wrapper .row:eq(0)");
      });
  }

  let idUsuario = $("#idUsuario").val();

  if (tableListPuesto != null)
    fActualizarListado(
      rutaAjax +
        `app/Ajax/PuestoAjax.php?accion=puestoUsuario&idUsuario=${idUsuario}`,
      "#tablaPuestos",
      parametrosTableList
    );

  // BOTTON ELIMINAR PUESTO ASIGNADO
  $(tableListPuesto).on("click", "button.eliminar", function (e) {
    e.preventDefault();

    var idPuestoAsignado = $(this).attr("idPuestoAsignado");
    var nombreUbicacion = $(this).attr("nombreUbicacion").toUpperCase();
    var nombrePuesto = $(this).attr("nombrePuesto").toUpperCase();

    Swal.fire({
      title: `¿Confirma que desea eliminar el Puesto Asignado: ${nombrePuesto} - ${nombreUbicacion}?`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Sí, quiero eliminarlo!",
      cancelButtonText: "No!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: rutaAjax + "app/Ajax/PuestoAjax.php",
          type: "POST",
          data: {
            accion: "eliminarPuestoAsignado",
            idPuestoAsignado: idPuestoAsignado,
          },
          success: function (response) {
            let respuesta = typeof response === 'string' ? JSON.parse(response) : response;
            crearToast("bg-success", `${respuesta.respuestaMessage} `, "OK");
            location.reload();
          },
          error: function (xhr, status, error) {
            crearToast("bg-danger", error, "OK");
          },
        });
      }
    });
  });

  // BOTTON ASIGNAR PUESTO
  $("#modalAsignarPuesto button.btnAsignarPuesto").on("click", function (e) {
    let idObraElement = $("#idObra");
    let idPuestoElement = $("#idPuesto");
    let errorBox = $("#modalAsignarPuesto .error-validacion");
    let errorList = errorBox.find("ul");

    // Limpiar validaciones anteriores
    idObraElement.removeClass("is-invalid");
    idPuestoElement.removeClass("is-invalid");
    errorList.empty();
    errorBox.addClass("d-none");

    let errores = [];

    if (idObraElement.val().trim() === "") {
      idObraElement.addClass("is-invalid");
      errores.push("Debe seleccionar una ubicación.");
    }

    if (idPuestoElement.val().trim() === "") {
      idPuestoElement.addClass("is-invalid");
      errores.push("Debe seleccionar un puesto.");
    }

    if (errores.length > 0) {
      errores.forEach((msg) => {
        errorList.append(`<li>${msg}</li>`);
      });
      errorBox.removeClass("d-none");
      return;
    }

    let token = $('input[name="_token"]').val();

    let dataSend = new FormData();

    dataSend.append("_token", token);
    dataSend.append("idUsuario", idUsuario);
    dataSend.append("idPuesto", idPuestoElement.val());
    dataSend.append("idObra", idObraElement.val());

    dataSend.append("accion", "asignarPuesto");

    $.ajax({
      url: rutaAjax + "app/Ajax/PuestoAjax.php",
      method: "POST",
      data: dataSend,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
    })
      .done(function (respuesta) {
        let errorBox = document.querySelector(
          "#modalAsignarPuesto .error-validacion"
        );
        let errorList = errorBox.querySelector("ul");
        // Limpiar errores anteriores
        errorList.innerHTML = "";

        if (respuesta.respuestaMessage == "Puesto asignado ya existe") {
          // Crear y mostrar nuevo mensaje de error
          let errorItem = document.createElement("li");
          errorItem.textContent = "El Puesto ya esta asignado a este perfil";
          errorList.appendChild(errorItem);
          errorBox.classList.remove("d-none");
          return;
        }

        // Muestra la respuesta del servidor (mensaje o error)
        crearToast(
          "bg-success",
          "Puesto asignado",
          "OK",
          respuesta.respuestaMessage
        );
        $("#modalAsignarPuesto").modal("hide");

        location.reload();
      })
      .fail(function (error) {
        let errorBox = document.querySelector(
          "#modalAsignarPuesto .error-validacion"
        );
        let errorList = errorBox.querySelector("ul");

        // Limpiar errores anteriores
        errorList.innerHTML = "";

        // Crear y mostrar nuevo mensaje de error
        let errorItem = document.createElement("li");
        errorItem.textContent =
          "Ocurrió un error en el sistema. Intenta nuevamente o contacta al administrador.";
        errorList.appendChild(errorItem);

        // console.error("Error AJAX:", error); // Log para desarrolladores
        errorBox.classList.remove("d-none");
      });
  });
});
