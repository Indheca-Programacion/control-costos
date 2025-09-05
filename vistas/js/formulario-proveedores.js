$(document).ready(function () {
  // *************************************************
  // * VARIABLES GLOBALES
  // *************************************************

  // VARIABLES GLOBALES PARA FORMULARIOS (CREAR - EDITAR)
  const $formulario = $("#formSend");
  const $mensaje = $("#msgSend");
  const $btnEnviar = $("#btnSend");

  // ARREGLO PARA AGREAR INPUTS
  const camposProveedor = [
    "razonSocial",
    "rfc",
    "correoElectronico",
    "nombreApellido",
    "telefono",
    "origenProveedor",
    "entregaMaterial",
    "diasCredito",
  ];

  const camposArchivo = [
    "constanciaFiscal",
    "opinionCumplimiento",
    "comprobanteDomicilio",
    "datosBancarios",
  ];

  // *************************************************
  // * FUNCIONES
  // *************************************************

  // ACCION CHANGE DE TODOS LOS INPUTS DE TIPO FILE
  camposArchivo.forEach((idCampo) => {
    const input = document.getElementById(idCampo);

    if (input) {
      input.addEventListener("change", function () {
        if (input.files && input.files.length > 0) {
          const archivo = input.files[0];
          subirArchivo(idCampo, archivo);
        } else {
          console.log(`No se seleccionó archivo para ${idCampo}`);
        }
      });
    }
  });

  // FUNCION GUARDAR ARCHIVOS EN SERVIDOR TEMPORALES POR AJAX
  function subirArchivo(campo, archivo) {
    const formData = new FormData();
    formData.append("archivo", archivo);
    formData.append("campo", campo);

    $.ajax({
      url: rutaAjax + "app/Ajax/FormularioProveedorAjax.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      dataType: "json",
      success: function (respuesta) {
        if (respuesta.ok) {
        } else {
          console.error("Error al subir:", respuesta.mensaje);
        }
      },
      error: function (xhr, status, error) {
        console.error("Error AJAX:", error);
      },
    });
  }

  // FUNCION PARA OBTENER EL FORMULARIO
  function manejarEnvioFormulario(
    e,
    datosProveedor,
    $formulario,
    $btnEnviar,
    $mensaje
  ) {
    e.preventDefault(); // evita el envío del formulario

    // Iterar sobre los campos definidos
    datosProveedor.forEach((campo) => {
      datosProveedor[campo] = $(`#${campo}`).val();
    });

    // Manejar campos especiales (radios)
    datosProveedor.tipoProveedor = $(".tipo-proveedor:checked").val() || "0";
    datosProveedor.claveProveedor = $(".clave-proveedor:checked").val() || "0";

    // Guardar en localStorage
    Object.entries(datosProveedor).forEach(([key, value]) => {
      localStorage.setItem(key, value);
    });

    // Mostrar mensaje visual
    $btnEnviar.prop("disabled", true);
    $mensaje.html(
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>"
    );

    $btnEnviar.remove();

    // Enviar el formulario
    $formulario.submit();
  }

  // FUNCION RESTAURAR FORMULARIO
  function restaurarDatosFormulario(camposProveedor) {
    camposProveedor.forEach((campo) => {
      $(`#${campo}`).val(localStorage.getItem(campo) || "");
    });

    // Restaurar tipoProveedor (radio)
    const tipoGuardado = localStorage.getItem("tipoProveedor");
    if (tipoGuardado) {
      $(`.tipo-proveedor[value="${tipoGuardado}"]`).prop("checked", true);
    }

    // Restaurar claveProveedor (radio)
    const claveGuardada = localStorage.getItem("claveProveedor");
    if (claveGuardada) {
      $(`.clave-proveedor[value="${claveGuardada}"]`).prop("checked", true);
    }
  }

  // FUNCION LIMPIAR LOCALSTORAGE
  function limpiarLocalStorageFormulario(camposProveedor) {
    camposProveedor.forEach((campo) => {
      localStorage.removeItem(campo);
    });

    localStorage.removeItem("tipoProveedor");
    localStorage.removeItem("claveProveedor");
  }

  // SELECTOR DE UN SOLO TIPO DE PROVEEDOR
  $(".tipo-proveedor").on("change", function () {
    if ($(this).is(":checked")) {
      $(".tipo-proveedor").not(this).prop("checked", false);
    }
  });

  // SOLO PERMITE UNA CLABE
  $(".clave-proveedor").on("change", function () {
    if ($(this).is(":checked")) {
      $(".clave-proveedor").not(this).prop("checked", false);
    }
  });

  // CONFIGURACION PARA OBTENER EL NOMBRE DEL ARCHIVO EN EL UNPUT FILE
  $(".custom-file-input").on("change", function () {
    const fileName = this.files[0]?.name || "Seleccionar archivo";
    const label = $(this).next(".custom-file-label");
    label.addClass("selected").text(fileName);
  });

  // Activar el elemento Select2
  $(".select2").select2({
    tags: false,
    width: "100%",
    // theme: 'bootstrap4'
  });
  // Date picker
  $(".input-group.date").datetimepicker({
    format: "DD/MMMM/YYYY",
  });

  // *************************************************
  // * VALIDACIONES
  // *************************************************

  //VERFICA SI HAY DATOS FALTANTES EN EL FORMULARIO
  const erroresVisibles = $(".invalid-feedback:visible").length > 0;

  // FUNCION PARA VALIDAR DATOS Y ENVIAR
  $btnEnviar.on("click", function (e) {
    manejarEnvioFormulario(
      e,
      camposProveedor,
      $formulario,
      $btnEnviar,
      $mensaje
    );
  });

  // VALIDA SI HAY ERRORES SI HAY ALMACENA LOS DATOS INGRESADOS
  // EN LOCALSTORAGE
  if (erroresVisibles) {
    restaurarDatosFormulario(camposProveedor);
  } else {
    limpiarLocalStorageFormulario(camposProveedor);
  }

  //* VALIDACION DE ARCHIVOS
  $(
    "#comprobanteDomicilio, #constanciaFiscal, #opinionCumplimiento, #datosBancarios"
  ).on("change", function () {
    let file = this.files[0];
    let allowedTypes = ["application/pdf"];
    let maxSize = 5 * 1024 * 1024; // 5MB

    if (!file) {
      Swal.fire({
        icon: "warning",
        title: "Archivo requerido",
        text: "Debes seleccionar un archivo.",
      });
      return;
    }

    if (!allowedTypes.includes(file.type)) {
      Swal.fire({
        icon: "error",
        title: "Tipo no permitido",
        text: "Solo se permiten archivos PDF.",
      });
      $(this).val("");
      $(this).next(".custom-file-label").html("Seleccionar archivo");
      return;
    }

    if (file.size > maxSize) {
      Swal.fire({
        icon: "error",
        title: "Archivo demasiado grande",
        text: "El archivo no debe superar los 5MB.",
      });
      $(this).val("");
      $(this).next(".custom-file-label").html("Seleccionar archivo");
      return;
    }

    // Si todo está bien, muestra el nombre del archivo
    $(this).next(".custom-file-label").html(file.name);
  });
});
