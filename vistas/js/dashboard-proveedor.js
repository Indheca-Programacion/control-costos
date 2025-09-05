$(document).ready(function () {
  $("#bienvenidaModal").modal("show");

  $("#formBienvenida").on("submit", function (e) {
    e.preventDefault();
    alert("Datos guardados correctamente");
    $("#bienvenidaModal").modal("hide");
  });
});

$(".select2").select2({
  tags: true,
  width: "100%",
});

$("#tablaComprobantes").DataTable(
  {
    language: LENGUAJE_DT,
    responsive: true,
    autoWidth: false,
    order: [[0, "asc"]]
  }
);

/*==============================================================
BOTON PARA AUTORIZAR SOLICITUD
==============================================================*/
$(".btnEnviar").click(function () {
  const datos = [
    { key: "idProveedor", value: $("#idProveedor").val() },
    { key: "nombre", value: $("#nombre").val() },
    {
      key: "apellidoPaterno",
      value: $("#apellidoPaterno").val(),
    },
    {
      key: "apellidoMaterno",
      value: $("#apellidoMaterno").val(),
    },
    {
      key: "zona",
      value: $("#zona").val(),
    },
    {
      key: "domicilio",
      value: $("#domicilio").val(),
    },
  ];
  let camposVacios = datos.filter((d) => !String(d.value || "").trim());

  if (camposVacios.length > 0) {
    let campos = camposVacios.map((d) => d.key).join(", ");
    Swal.fire({
      icon: "warning",
      title: "Campos requeridos",
      text: "Por favor, completa los siguientes campos: " + campos,
      confirmButtonText: "Aceptar",
    });
    return;
  }

  // Ocultar botones y mostrar mensaje de carga
  $("#botonesModal").addClass("d-none");
  $("#mensajeCargando").removeClass("d-none");

  const _token = $("#_token").val();

  let dataSend = new FormData();
  dataSend.append("accion", "actualizarDatosProveedor");
  dataSend.append("_token", _token);
  dataSend.append("datos", JSON.stringify(datos));

  $.ajax({
    url: rutaAjax + "app/Ajax/ProveedorAjax.php",
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
          $("#bienvenidaModal").modal("hide");
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
