$(function(){

  let tableList = document.getElementById('tablaPermisosCategoriaProveedores');

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado  
  if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/PermisoCategoriaProveedorAjax.php', '#tablaPermisosCategoriaProveedores');

  // Confirmar la eliminación del Usuario
  // $("table tbody").on("click", "button.eliminar", function (e) {
  $(tableList).on("click", "button.eliminar", function (e) {

    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents('form');

    Swal.fire({
      title: '¿Estás Seguro de querer eliminar este Permiso (Nombre: '+folio+') ?',
      text: "No podrá recuperar esta información!",
      icon: 'warning',
      showCancelButton: true, 
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, quiero eliminarlo!',
      cancelButtonText:  'No!'
    }).then((result) => {
      if (result.isConfirmed) {
        form.submit();
      }
    })

  });

  function enviar(){
    btnEnviar.disabled = true;
    mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

    padre = btnEnviar.parentNode;
    padre.removeChild(btnEnviar);

    formulario.submit(); // Enviar los datos
  }
  let formulario = document.getElementById("formSend");
  let mensaje = document.getElementById("msgSend");
  let btnEnviar = document.getElementById("btnSend");
  if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

});