$(function(){

  let tableList = document.getElementById('tablaSucursales');

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado
  if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/SucursalAjax.php', '#tablaSucursales');

  // Confirmar la eliminación de la Sucursal
  // $("table tbody").on("click", "button.eliminar", function (e) {
  $(tableList).on("click", "button.eliminar", function (e) {

    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents('form');

    Swal.fire({
      title: '¿Estás Seguro de querer eliminar esta Sucursal (Descripción: '+folio+') ?',
      text: "No podrá recuperar esta información!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, quiero eliminarla!',
      cancelButtonText:  'No!'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    })

  });

  // Envio del formulario para Crear o Editar registros
  function enviar(){
    btnEnviar.disabled = true;
    mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit();
  }
  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

  // Activar el elemento Select2
  $('.select2').select2({
    tags: false
  });
  let elementEmpresaId = $('#empresaId.select2.is-invalid');
  if ( elementEmpresaId.length == 1 ) { 
    $('.select2-selection.select2-selection--single').css('border-color', '#dc3545');
    $('.select2-selection.select2-selection--single').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
    $('.select2-selection.select2-selection--single').css('background-repeat', 'no-repeat');
    $('.select2-selection.select2-selection--single').css('background-position', 'right calc(0.375em + 1.0875rem) center');
    $('.select2-selection.select2-selection--single').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
  }

});