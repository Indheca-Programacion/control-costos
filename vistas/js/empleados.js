$(function(){

  let tableList = document.getElementById('tablaEmpleados');

  // LLamar a la funcion fAjaxDataTable() para llenar el Listado  
  if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/EmpleadoAjax.php', '#tablaEmpleados');

  // Confirmar la eliminación del Empleado
  $(tableList).on("click", "button.eliminar", function (e) {

    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents('form');

    Swal.fire({
      title: '¿Estás Seguro de querer eliminar este Empleado (Nombre: '+folio+') ?',
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

  // Envio del formulario para Crear o Editar registros
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

  $("#foto").hide();

  /*=============================================
  Abrir el input al presionar la imágen (figure)
  =============================================*/
  $("#imgFoto").click(function(){
    document.getElementById('foto').click();
  })

  /*=============================================
  Actualizar el previsual de la imágen
  =============================================*/
  $("#foto").change(function(){

    var imagen = this.files[0];
    
    /*=============================================
    VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
    =============================================*/
    if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {

      $("#foto").val("");

      Swal.fire({
        title: 'Error en el tipo de archivo',
        text: '¡La imagen debe estar en formato JPG o PNG!',
        icon: 'error',
        confirmButtonText: '¡Cerrar!'
      })

    } else if (imagen["size"] > 2000000) {

      $("#foto").val("");

      Swal.fire({
        title: 'Error en el tamaño del archivo',
        text: '¡La imagen no debe pesar más de 2MB!',
        icon: 'error',
        confirmButtonText: '¡Cerrar!'
      })

    } else {

      var datosImagen = new FileReader;
      datosImagen.readAsDataURL(imagen);

      $(datosImagen).on("load", function(event){

        var rutaImagen = event.target.result;

        $(".previsualizar").attr("src", rutaImagen);

      })

    }

  })

});