$(function(){
	const TIEMPO_DESCARGA = 350;

	let tableList = document.getElementById('tablaTareas');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/TareaAjax.php', '#tablaTareas');

	// Confirmar la eliminación del Ubicacion
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Ubicación (Descripción: '+folio+') ?',
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
	$('.select2').select2({
		tags: false
	});
	$('#fecha_inicio').datetimepicker({
		timepicker: false,
		format: 'DD/MMMM/YYYY'
	});
	$('#fecha_estimada').datetimepicker({
		timepicker: false,
		format: 'DD/MMMM/YYYY'
	});
	// Envio del formulario para Crear o Editar registros
	function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);

		formulario.submit();
	}

	function enviarOBS(){
		btnEnviarObs.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviarObs.parentNode;
		padre.removeChild(btnEnviarObs);

		formularioOBS.submit();
	}

	// Envio del formulario para Cancelar el registro
	function eliminarArchivo(btnEliminar = null){

		if ( btnEliminar == null ) return;		

		let archivoId = $(btnEliminar).attr("archivoId");
		// $(btnEliminar).prop('disabled', true);

		let token = $('input[name="_token"]').val();
		let tareaId = $('#fk_tarea').val();


		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarArchivo");
		datos.append("archivoId", archivoId);
		datos.append("tareaId", tareaId);

		$.ajax({
		    url: rutaAjax+"app/Ajax/TareaAjax.php",
		    method: "POST",
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

		    	// console.log(respuesta)
		    	// Si la respuesta es positiva pudo eliminar el archivo
		    	if (respuesta.respuesta) {

		    		$(btnEliminar).parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    		$(btnEliminar).parent().remove();

		    	} else {

		    		$(btnEliminar).parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    		// $(btnEliminar).prop('disabled', false);

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	}

	$("#btnSubirArchivos").click(function(){
		document.getElementById('archivos').click();
	})

	$('#archivos').change(function () {
		let archivos = this.files;
		if ( archivos.length == 0) return;

		let error = false;

		for (let i = 0; i < archivos.length; i++) {

		   let archivo = archivos[i];
		   
		   /*==========================================
		   VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
		   ==========================================*/
		   
		   if ( archivo["size"] > 4000000 ) {

			   error = true;

			   // $("#comprobanteArchivos").val("");
			   // $("div.subir-comprobantes span.lista-archivos").html('');

			   Swal.fire({
				 title: 'Error en el tamaño del archivo',
				 text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				 icon: 'error',
				 confirmButtonText: '¡Cerrar!'
			   })

		   }

		}

		if ( error ) {
			$("#archivos").val("");

			return;
		}

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];

			$("div.subir-archivos span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

		}

	   let cloneElementArchivos = this.cloneNode(true);
	   cloneElementArchivos.removeAttribute('id');
	   cloneElementArchivos.name = 'archivos[]';
	   $("div.subir-archivos").append(cloneElementArchivos);
	})

	$("div.subir-archivos").on("click", "i.eliminarArchivo", function (e) {

		let btnEliminar = this;
	    // let archivoId = $(this).attr("archivoId");
	    let folio = $(this).attr("folio");

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Archivo (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarlo!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if (result.isConfirmed) {
				eliminarArchivo(btnEliminar);
			}
	    })

	});

	$("#btnDescargarArchivos").click(function(event) {

		event.preventDefault();

		let btnDescargarArchivos = this;
		let tareaId = $('#fk_tarea').val();
		
		$.ajax({
			url: `${rutaAjax}tareas/${tareaId}/download`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarArchivos.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarArchivos.disabled = false;
		});

	})

	let formulario = document.getElementById("formSend");
	let formularioOBS = document.getElementById("formSendObservacicones");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	let btnEnviarObs = document.getElementById("btnSendObs");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);
	if ( btnEnviarObs != null ) btnEnviarObs.addEventListener("click", enviarOBS);

	/*=================================================
	Ver requisicion
	=================================================*/
	$("#verRequisicion").click(function(){
		let requisicionId = $("#requisicionId").val();
		$.ajax({
			url: `${rutaAjax}app/Ajax/TareaAjax.php`,
			method: 'POST',
			data: {
				'accion': 'verRequisicion',
				'tareaId': $('#tareaId').val(),
			},
			dataType: 'json',
			success: function(response) {
				if (response.error) {
					Swal.fire({
						title: 'Error',
						text: response.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
				} else {
					// Handle the response data as needed
					Swal.fire({
						title: 'Requisición',
						text: 'Datos cargados correctamente.',
						icon: 'success',
						confirmButtonText: 'Cerrar'
					});
					window.location.href = `${rutaAjax}requisiciones/${requisicionId}/editar`;
				}
			},
			error: function(error) {
				Swal.fire({
					title: 'Error',
					text: 'Ocurrió un error al cargar la requisición.',
					icon: 'error',
					confirmButtonText: 'Cerrar'
				});
			}
		});
	})




});