$(function(){

	let tableList = document.getElementById('tablaMensajes');
	let parametrosTableList = { responsive: true };

	// Ejecuta en cuanto la página esté lista
  	$(document).ready(function() {
		// LLamar a la funcion fAjaxDataTable() para llenar el Listado
		if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/ConfiguracionCorreoElectronicoAjax.php', '#tablaMensajes', parametrosTableList);
	});

	let campoPerfilesCrear = document.getElementById('perfilesCrear');
	let campoEstatusModificarUsuarioCreacion = document.getElementById('estatusModificarUsuarioCreacion');
	let camposEstatusModificarPerfiles = document.querySelectorAll('select.estatusModificarPerfiles');
	let camposUploadDocumentos = document.querySelectorAll('input.uploadDocumentos');
	let campoUsuarioUploadDocumento = document.getElementById('usuarioUploadDocumento');
	let campoPerfilesUploadDocumento = document.getElementById('perfilesUploadDocumento');

	$("button#btnActualizarAvisos").prop('disabled', false);

	$("button#btnActualizarAvisos").on('click', function (e) {

		// if ( campoFecha.value == "" ) {
		// 	return;
		// }

		// $(this).prop('disabled', true);

		let mensaje = document.getElementById("msgActualizarAvisos");
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		let token = $('input[name="_token"]').val();
		let perfilesCrear = $(campoPerfilesCrear).val();
		let estatusModificarUsuarioCreacion = $(campoEstatusModificarUsuarioCreacion).val();

		arrayEstatusModificarPerfiles = [];
		camposEstatusModificarPerfiles.forEach( (element, index) => {
			let estatusId = element.getAttribute('data-estatus-id');
			let elementEstatusValue = $(element).val();

			if ( elementEstatusValue.length > 0 ) {
				let objEstatus = {
					id: estatusId,
					perfiles: elementEstatusValue
				};

				arrayEstatusModificarPerfiles.push(objEstatus);
			}
		});

		arrayUploadDocumentos = [];
		camposUploadDocumentos.forEach( (element, index) => {
			if ( element.checked ) arrayUploadDocumentos.push(element.value);
		});

		let usuarioUploadDocumento = ( campoUsuarioUploadDocumento.checked ) ? 1 : 0;
		let perfilesUploadDocumento = $(campoPerfilesUploadDocumento).val();

		let datos = new FormData();
		// let datos = new FormData(document.getElementById("formHorometro"))
		datos.append("accion", "actualizarAvisos");
		datos.append("_token", token);
		datos.append("perfilesCrear", JSON.stringify(perfilesCrear));
		datos.append("estatusModificarUsuarioCreacion", JSON.stringify(estatusModificarUsuarioCreacion));
		datos.append("estatusModificarPerfiles", JSON.stringify(arrayEstatusModificarPerfiles));
		datos.append("uploadDocumentos", JSON.stringify(arrayUploadDocumentos));
		datos.append("usuarioUploadDocumento", usuarioUploadDocumento);
		datos.append("perfilesUploadDocumento", JSON.stringify(perfilesUploadDocumento));

		$.ajax({
		    url: rutaAjax+"app/Ajax/ConfiguracionCorreoElectronicoAjax.php",
		    method: "POST",
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

		    	// console.log(respuesta)
		    	mensaje.innerHTML = "";	    	

		    	// Si la respuesta es positiva pudo agregar el registro
		    	if (respuesta.respuesta) {

		    		$(mensaje).after('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    	} else {

		    		$(mensaje).after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	});

});
