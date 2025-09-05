$(function () {
  let tableList = document.getElementById("tablaPuestos");

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if (tableList != null)
    fAjaxDataTable(rutaAjax + "app/Ajax/PuestoAjax.php", "#tablaPuestos");

  // Confirmar la eliminación del Color
  $(tableList).on("click", "button.eliminar", function (e) {
    e.preventDefault();
    var puesto = $(this).attr("puesto");
    var form = $(this).parents("form");

    Swal.fire({
      title:
        "¿Estás Seguro de querer eliminar este Puesto (Descripción: " +
        puesto +
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

    formulario.submit();
  }

  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if (btnEnviar != null) btnEnviar.addEventListener("click", enviar);

  // Activar el elemento Select2
  $(".select2").select2({
    tags: false,
    width: "100%",
  });
});
