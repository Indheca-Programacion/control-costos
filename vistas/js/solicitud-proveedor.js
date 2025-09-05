let parametrosTableList = { responsive: true };

$(function () {
  let tablaProveedoresAutorizar = document.getElementById(
    "tablaProveedoresAutorizar"
  );

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tablaProveedoresAutorizar != null)
    fAjaxDataTable(
      rutaAjax + "app/Ajax/SolicitudProveedorAjax.php",
      "#tablaProveedoresAutorizar"
    );

  // Envio del formulario para Crear o Editar registros
  function enviar() {
    btnEnviar.disabled = true;
    mensaje.innerHTML =
      "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit();
  }

  $(tablaProveedoresAutorizar).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents("form");

    Swal.fire({
      title: "¿Estás Seguro de eliminar esta solicitud (Id: " + folio + ") ?",
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

  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  // Activar el elemento Select2
  $(".select2").select2({
    tags: false,
    width: "100%",
    // ,theme: 'bootstrap4'
  });

  /*==============================================================
	BOTON PARA VER ARCHIVOS
	==============================================================*/
  $(".verArchivo").on("click", function () {
    var archivoRuta = $(this).attr("archivoRuta");
    $("#pdfViewer").attr("src", archivoRuta);
    // Mostrar el modal
    $("#pdfModal").modal("show");
  });

  // Al cerrar el modal, limpia el src del iframe
  $("#pdfModal").on("hidden.bs.modal", function () {
    $("#pdfViewer").attr("src", "");
  });
});

/*==============================================================
	BOTON PARA ABRIR MODAL AUTORIZAR
	==============================================================*/
$("#btnAutorizarProveedorModal").on("click", function () {
  $("#autorizarProveedorModal").modal("show");
});

/*==============================================================
	BOTON PARA ABRIR MODAL RECHAZAR
	==============================================================*/
$("#btnRechazarProveedorModal").on("click", function () {
  $("#rechazarProveedorModal").modal("show");
});

/*==============================================================
BOTON PARA AUTORIZAR SOLICITUD
==============================================================*/
$(".btnAutorizarSolicitudProveedor").click(function () {
  // Ocultar botones y mostrar mensaje de carga
  $("#botonesModal").addClass("d-none");
  $("#mensajeCargando").removeClass("d-none");

  const _token = $("#_token").val();

  // DATOS DE LA SOLICITUD
  const detallesSolicitud = [
    { key: "idSolicitudProveedor", value: $("#idSolicitudProveedor").val() },
    {
      key: "observacionSolicitudProveedor",
      value: $("#observacionSolicitudProveedor").val(),
    },
    {
      key: "rfc",
      value: $("#rfc").val(),
    },
    {
      key: "razonSocial",
      value: $("#razonSocial").val(),
    },
    {
      key: "correoElectronico",
      value: $("#correoElectronico").val(),
    },
    {
      key: "nombreApellido",
      value: $("#nombreApellido").val(),
    },
    {
      key: "telefono",
      value: $("#telefono").val(),
    },
    {
      key: "origenProveedor",
      value: $("#origenProveedor").val(),
    },
    {
      key: "tipoProveedor",
      value: $("#tipoProveedor").val(),
    },
    {
      key: "claveProveedor",
      value: $("#claveProveedor").val(),
    },
    {
      key: "entregaMaterial",
      value: $("#entregaMaterial").val(),
    },
    {
      key: "diasCredito",
      value: $("#diasCredito").val(),
    },
    {
      key: "estatusSolicitudProveedor",
      value: $("#estatusSolicitudProveedor").val(),
    },
  ];

  // DATOS PARA ENVIAR
  let dataSend = new FormData();
  dataSend.append("accion", "autorizarSolicitud");
  dataSend.append("_token", _token);
  dataSend.append("detallesSolicitud", JSON.stringify(detallesSolicitud));

  $.ajax({
    url: rutaAjax + "app/Ajax/SolicitudProveedorAjax.php",
    method: "POST",
    data: dataSend,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
  })
    .done(function (respuesta) {

      // FUNCION ERROR
      if (respuesta.error === true) {
        Swal.fire({
          icon: "error",
          title: "Hubo un Problema",
          text: respuesta.mensaje || "Error en el sistema",
          confirmButtonText: "Aceptar",
        });
      } else {
        
        // FUNCION SUCCESS
        Swal.fire({
          icon: "success",
          title: "¡Éxito!",
          text: respuesta.mensaje || "La operación se realizó correctamente.",
          confirmButtonText: "Aceptar",
        }).then(() => {
          $("#autorizarProveedorModal").modal("hide");
          location.reload();
        });
      }
    })
    .fail(function (error) {
      console.error(error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Ocurrió un error al procesar la solicitud.",
        confirmButtonText: "Aceptar",
      });
    })
    .always(function () {
      // Mostrar botones y ocultar mensaje de carga (opcional)
      $("#botonesModal").removeClass("d-none");
      $("#mensajeCargando").addClass("d-none");
    });
});

/*==============================================================
BOTON PARA RECHAZAR SOLICITUD
==============================================================*/
$(".btnRechazarSolicitudProveedor").click(function () {
  const observacion = $("#observacionRechazo").val();

  if (!observacion && !observacion.trim()) {
    Swal.fire({
      icon: "warning",
      title: "Observación requerida",
      text: "Por favor, indica el motivo del rechazo.",
      confirmButtonText: "Aceptar",
    });
    return;
  }

  // Ocultar botones y mostrar mensaje de carga
  $("#botonesModalRechazar").addClass("d-none");
  $("#mensajeCargandoRechazar").removeClass("d-none");

  const _token = $("#_token").val();

  // DATOS DE LOS INPUTS
  const detallesSolicitud = [
    { key: "idSolicitudProveedor", value: $("#idSolicitudProveedor").val() },
    {
      key: "observacionSolicitudProveedor",
      value: $("#observacionRechazo").val(),
    },
    {
      key: "rfc",
      value: $("#rfc").val(),
    },
    {
      key: "razonSocial",
      value: $("#razonSocial").val(),
    },
    {
      key: "correoElectronico",
      value: $("#correoElectronico").val(),
    },
    {
      key: "nombreApellido",
      value: $("#nombreApellido").val(),
    },
    {
      key: "telefono",
      value: $("#telefono").val(),
    },
    {
      key: "origenProveedor",
      value: $("#origenProveedor").val(),
    },
    {
      key: "tipoProveedor",
      value: $("#tipoProveedor").val(),
    },
    {
      key: "claveProveedor",
      value: $("#claveProveedor").val(),
    },
    {
      key: "entregaMaterial",
      value: $("#entregaMaterial").val(),
    },
    {
      key: "diasCredito",
      value: $("#diasCredito").val(),
    },
    {
      key: "estatusSolicitudProveedor",
      value: $("#estatusSolicitudProveedor").val(),
    },
  ];

  let dataSend = new FormData();
  dataSend.append("accion", "rechazarSolicitud");
  dataSend.append("_token", _token);
  dataSend.append("detallesSolicitud", JSON.stringify(detallesSolicitud));

  $.ajax({
    url: rutaAjax + "app/Ajax/SolicitudProveedorAjax.php",
    method: "POST",
    data: dataSend,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
  })
    .done(function (respuesta) {
      if (respuesta.error === true) {
        Swal.fire({
          icon: "error",
          title: "Hubo un Problema",
          text: respuesta.mensaje || "Error en el sistema",
          confirmButtonText: "Aceptar",
        }).then(()=>{
          location.reload();
        })
      } else {
        Swal.fire({
          icon: "success",
          title: "¡Éxito!",
          text: respuesta.mensaje || "La operación se realizó correctamente.",
          confirmButtonText: "Aceptar",
        }).then(() => {
          $("#rechazarProveedorModal").modal("hide");
          location.reload();
        });
      }
    })
    .fail(function (error) {
      console.error(error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Ocurrió un error al procesar la solicitud.",
        confirmButtonText: "Aceptar",
      });
    })
    .always(function () {
      // Mostrar botones y ocultar mensaje de carga (opcional)
      $("#botonesModalRechazar").removeClass("d-none");
      $("#mensajeCargandoRechazar").addClass("d-none");
    });
});

/*==============================================================
MODAL PARA RECHAZAR LOS ARCHIVOS
==============================================================*/
$(".estadoArchivo").on("click", function () {
  const estadoArchivo = $(this).attr("estadoArchivo");

  if (estadoArchivo == "AUTORIZADO") {
    $("#tituloEstado").text("autorizar").addClass("text-success");
    $(".subtituloEstado").text("Escriba una observacion..");

    $("#observacionEstadoArchivo").attr(
      "placeholder",
      "Escribe una obsevación (opcional)"
    );
  } else {
    $("#tituloEstado").text("rechazar").addClass("text-danger");
    $(".subtituloEstado").text("Escriba el motivo del rechazo.");
    $("#observacionEstadoArchivo").attr(
      "placeholder",
      "Escribe el motivo del rechazo (OBLIGATORIO)"
    );
  }

  $("#archivoId").attr("value", $(this).attr("archivoId"));
  $("#estadoArchivo").attr("value", estadoArchivo);

  // Mostrar el modal
  $("#estadoArchivoModal").modal("show");
});

// Al cerrar el modal, limpia el src del iframe
$("#estadoArchivoModal").on("hidden.bs.modal", function () {
  $("#archivoId").attr("value", "");
  $("#estadoArchivo").attr("value", "");
  $("#observacionEstadoArchivo").val("").attr("placeholder", "");

  $("#tituloEstado").text("").removeAttr("class");
});

/*==============================================================
BOTON PARA AUTORIZAR SOLICITUD
==============================================================*/
$(".btnEstadoArchivo").click(function () {
  const estadoArchivo = $("#estadoArchivo").val();

  if (estadoArchivo === "RECHAZADO") {
    const observacion = $("#observacionEstadoArchivo").val();

    if (!observacion && !observacion.trim()) {
      Swal.fire({
        icon: "warning",
        title: "Observación requerida",
        text: "Por favor, indica el motivo del rechazo.",
        confirmButtonText: "Aceptar",
      });
      return;
    }
  }

  // Ocultar botones y mostrar mensaje de carga
  $("#botonesModalEstadoArchivo").addClass("d-none");
  $("#mensajeCargandoBotonesArchivos").removeClass("d-none");

  let dataSend = new FormData();
  dataSend.append("accion", "estadoArchivo");
  dataSend.append("_token", $("#_token").val());
  dataSend.append("estadoArchivo", $("#estadoArchivo").val());
  dataSend.append("archivoId", $("#archivoId").val());
  dataSend.append("idSolicitudProveedor", $("#idSolicitudProveedor").val());
  dataSend.append(
    "observacionEstadoArchivo",
    $("#observacionEstadoArchivo").val()
  );

  $.ajax({
    url: rutaAjax + "app/Ajax/SolicitudProveedorAjax.php",
    method: "POST",
    data: dataSend,
    cache: false,
    contentType: false,
    processData: false,
    dataType: "json",
  })
    .done(function (respuesta) {
      if (respuesta.error === true) {
        Swal.fire({
          icon: "error",
          title: "Hubo un Problema",
          text: respuesta.mensaje || "Error en el sistema",
          confirmButtonText: "Aceptar",
        });
      } else {
        Swal.fire({
          icon: "success",
          title: "¡Éxito!",
          text: respuesta.mensaje || "La operación se realizó correctamente.",
          confirmButtonText: "Aceptar",
        }).then(() => {
          $("#autorizarProveedorModal").modal("hide");
          location.reload();
        });
      }
    })
    .fail(function (error) {
      console.error(error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Ocurrió un error al procesar la solicitud.",
        confirmButtonText: "Aceptar",
      });
    })
    .always(function () {
      // Mostrar botones y ocultar mensaje de carga (opcional)
      $("#botonesModalEstadoArchivo").removeClass("d-none");
      $("#mensajeCargandoBotonesArchivos").addClass("d-none");
    });
});
