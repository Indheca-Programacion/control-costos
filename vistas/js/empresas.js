$(function(){

	let tableList = document.getElementById('tablaEmpresas');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	// if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/EmpresaAjax.php', '#tablaEmpresas');
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/EmpresaAjax.php', '#tablaEmpresas');

	// Confirmar la eliminación de la Empresa
	// $("table tbody").on("click", "button.eliminar", function (e) {
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Empresa (Razón Social: '+folio+') ?',
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
	// btnEnviar.addEventListener("click", enviar);
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

  	/*=============================================
	Abrir el input al presionar el logo (figure)
	=============================================*/
	$("#imgLogo").click(function(){
		document.getElementById('logo').click();
	})

	/*=============================================
	Actualizar el previsual del logo
	=============================================*/
	$("#logo").change(function(){

	    var imagen = this.files[0];
	    
		/*=============================================
		VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
		=============================================*/
		if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {

			$("#logo").val("");

	        Swal.fire({
	          title: 'Error en el tipo de archivo',
	          text: '¡La imagen debe estar en formato JPG o PNG!',
	          icon: 'error',
	          confirmButtonText: '¡Cerrar!'
	        })

		} else if (imagen["size"] > 2000000) {

	        $("#logo").val("");

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
		        $("#imgLogo.previsualizar").attr("src", rutaImagen);
	        })

		}

	})

	/*=============================================
	Abrir el input al presionar la imágen (figure)
	=============================================*/
	$("#imgImagen").click(function(){
		document.getElementById('imagen').click();
	})

	/*=============================================
	Actualizar el previsual de la imágen
	=============================================*/
	$("#imagen").change(function(){

	    var imagen = this.files[0];
	    
		/*=============================================
		VALIDAMOS EL FORMATO DE LA IMAGEN SEA JPG O PNG
		=============================================*/
		if (imagen["type"] != "image/jpeg" && imagen["type"] != "image/png") {

			$("#imagen").val("");

	        Swal.fire({
	          title: 'Error en el tipo de archivo',
	          text: '¡La imagen debe estar en formato JPG o PNG!',
	          icon: 'error',
	          confirmButtonText: '¡Cerrar!'
	        })

		} else if (imagen["size"] > 2000000) {

	        $("#imagen").val("");

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
		        $("#imgImagen.previsualizar").attr("src", rutaImagen);
	        })

		}

	})

});