$(function(){

	let tableList = document.getElementById('tablaInformacionTecnica');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/InformacionTecnicaAjax.php', '#tablaInformacionTecnica');

	// Confirmar la eliminación de la Información Técnica
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Información Técnica (Título: '+folio+') ?',
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

		formulario.submit();
	}
	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	/*=============================================
	Abrir el input al presionar la imágen (figure)
	=============================================*/
	$("#btnSubirArchivo").click(function(){
		document.getElementById('archivo').click();
	})

	/*=============================================
 	Validar tipo y tamaño del archivo
 	=============================================*/
 	$("#archivo").change(function(){

	    let archivo = this.files[0];
	    
		/*=============================================
		VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA WORD, EXCEL, PDF o IMAGEN
		=============================================*/
		
		if ( archivo["type"] != "application/msword" && archivo["type"] != "application/vnd.openxmlformats-officedocument.wordprocessingml.document" && archivo["type"] != "application/vnd.ms-excel" && archivo["type"] != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" && archivo["type"] != "application/pdf" && archivo["type"] != "image/jpeg" && archivo["type"] != "image/png" ) {

			$("#archivo").val("");

			Swal.fire({
			  title: 'Error en el tipo de archivo',
			  text: '¡El archivo debe ser Word, Excel, PDF o Imágen!',
			  icon: 'error',
			  confirmButtonText: '¡Cerrar!'
			})

		} else if ( archivo["size"] > 4000000 ) {

			$("#archivo").val("");

			Swal.fire({
			  title: 'Error en el tamaño del archivo',
			  text: '¡El archivo no debe pesar más de 4MB!',
			  icon: 'error',
			  confirmButtonText: '¡Cerrar!'
			})

		} else {

			$("#archivoActual").val(archivo["name"]);
			$("#formato").val(archivo["type"]);

		}

	}) // $("#archivo").change(function(){

 	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
	});

});