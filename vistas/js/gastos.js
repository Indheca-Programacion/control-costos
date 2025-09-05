$(function(){

    let tableList = document.getElementById('tablaGastos');
	let tableGastos = document.getElementById('tablaDetallesGastos');
	let elementSectionGasto= document.querySelector('#gasto-section');
	let dataTableGastos = null;
	if (tableGastos != null){
		obtenerDatos()
	}

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ){
		$.ajax({
			url: `${rutaAjax}app/Ajax/GastosAjax.php`,
			method: 'GET',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
		}).done(function(respuesta) {
			datos = respuesta.datos
			dataTableGastos = $('#tablaGastos').DataTable({
				autoWidth: false,
				info: true,
				paging: true,
				searching: true,
				responsive:  true ,
				data: datos.registros,
				columns:datos.columnas,
				columnDefs: [
					{targets: '_all', 
					orderable: false },
					{
						targets: 2, // Suponiendo que la columna "Estado" es la tercera (índice 2)
                		render: function ( data, type, row ) {
							if (data === 'ABIERTO') {
								badgeClass = 'badge badge-warning';
							} else if (data === 'EN PROCESO') {
								badgeClass = 'badge badge-primary';
							} else if (data === 'PROCESADO') {
								badgeClass = 'badge badge-info';
							} else if (data === 'PAGADO') {
								badgeClass = 'badge badge-success';
							} else {
								badgeClass = 'badge badge-secondary';
							}
							return '<span class="' + badgeClass + '">' + data + '</span>';
						}
					}
				],
				buttons: [{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' }
					// { extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
	
				language: LENGUAJE_DT,
				aaSorting: [],
			});
			dataTableGastos.buttons().container().appendTo('#tablaGastos_wrapper .row:eq(0)');

		}).fail(function(error){
			console.log(error)
		});
	} 

	// Confirmar la eliminación del Gastos
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Gastos (Descripción: '+folio+') ?',
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

	$(tableGastos).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Detalle (Descripción: '+folio+') ?',
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

	function obtenerDatos()
	{
		let gastoId = $('#gastoId').val()
		let datos = []
		$.ajax({
			url: `${rutaAjax}app/Ajax/GastosAjax.php?gasto=${gastoId}`,
			method: 'GET',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
		}).done(function(respuesta) {
			datos = respuesta.datos
			dataTableGastos = $('#tablaDetallesGastos').DataTable({
				autoWidth: false,
				info: false,
				paging: false,
				searching: false,
				responsive:  true ,
				data: datos,
				columns:[ 
					{data:'consecutivo', mData:'consecutivo'}, 
					{data:'fecha', mData:'fecha'}, 
					{data:'gasto', mData:'gasto'},
					{data:'costo', mData:'costo'},
					{data:'obra', mData:'obra'},
					{data:'economico', mData:'economico'},
					{data:'descripcion', mData:'descripcion'},
					{data:'observaciones', mData:'observaciones'},
					{data:'acciones', mData:'acciones'},
				],
				columnDefs: [
					{targets: '_all', 
					orderable: false }
				],
				language: LENGUAJE_DT,
			});
		}).fail(function(error){
			console.log(error)
		});
	}

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

	// $('#tipoGasto').on('change',function (e) {
	// 	let tipo = this
	// 	$("#bancoSection").show();
	// 	if(tipo.value == 2) $("#bancoSection").hide();
	// })

	$('#btnAddPartida').on('click',function (e){
		let btnGuardar = this;
		let gastoId = $('#gastoId').val()

		let data = dataTableGastos.rows().data().toArray();

		let token = document.getElementById('_token');
		
		let elementFecha = document.getElementById('datetimepicker3')
		let elementTipoGasto = document.getElementById('tipo')
		let elementObra = document.getElementById('obra-destino')
		let elementDescripcion = document.getElementById('descripcion')
		let elementCosto = document.getElementById('costo')
		let elementEconomico = document.getElementById('economico')
		let elementObservaciones = document.getElementById('observaciones')
		let elementCantidad = document.getElementById('cantidad')
		let elementSolicito = document.getElementById('solicito')

		let obra = $('#obra-destino option:selected').text()
		let gasto = $('#tipo option:selected').text()
		let descripcion = $('#descripcion option:selected').text()

		elementFecha.classList.remove("is-invalid");
		elementPadre = elementFecha.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementSolicito.classList.remove("is-invalid");
		elementPadre = elementSolicito.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementTipoGasto.classList.remove("is-invalid");
		elementPadre = elementTipoGasto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementObra.classList.remove("is-invalid");
		elementPadre = elementObra.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementDescripcion.classList.remove("is-invalid");
		elementPadre = elementDescripcion.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);
		
		elementCosto.classList.remove("is-invalid");
		elementPadre = elementCosto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementEconomico.classList.remove("is-invalid");
		elementPadre = elementEconomico.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);
		
		elementObservaciones.classList.remove("is-invalid");
		elementPadre = elementObservaciones.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);
		
		elementCantidad.classList.remove("is-invalid");
		elementPadre = elementCantidad.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;

		if ( elementFecha.value == '' ) {
			elementFecha.classList.add("is-invalid");
			elementPadre = elementFecha.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una fecha");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( elementTipoGasto.value == '' ) {
			elementTipoGasto.classList.add("is-invalid");
			elementPadre = elementTipoGasto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar un tipo de gasto.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( elementObra.value == '' ) {
			elementObra.classList.add("is-invalid");
			elementPadre = elementObra.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una obra");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( elementDescripcion.value == '' ) {
			elementDescripcion.classList.add("is-invalid");
			elementPadre = elementDescripcion.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una descripcion.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( elementEconomico.value == '' ) {
			elementEconomico.classList.add("is-invalid");
			elementPadre = elementEconomico.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una descripcion.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( parseFloat(elementCosto.value) < 0 || elementCosto.value == '' ) {
			elementCosto.classList.add("is-invalid");
			elementPadre = elementCosto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe ser mayor a 0.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}else if (elementCosto.value.length > 15) {
			elementCosto.classList.add("is-invalid");
			elementPadre = elementCosto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe ser menor a 10 caracteres.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
		}
		if ( parseFloat(elementCantidad.value) == 0 || elementCantidad.value == '' ) {
			elementCantidad.classList.add("is-invalid");
			elementPadre = elementCantidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe ser mayor a 0.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}else if (elementCantidad.value.length > 15) {
			elementCantidad.classList.add("is-invalid");
			elementPadre = elementCantidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe ser menor a 10 caracteres.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
		}
		if ( elementSolicito.value == ''){
			elementSolicito.classList.add("is-invalid");
			elementPadre = elementSolicito.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			// newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("El empleado que solicitó es obligatorio.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( errores ) return;

		let datos = {
			consecutivo: data.length + 1,
			fecha: elementFecha.value,
			gasto: gasto,
			tipoGasto: elementTipoGasto.value,
			costo: elementCosto.value,
			obra: obra,
			economico: elementEconomico.value,
			descripcion: descripcion,
			observaciones: elementObservaciones.value,
		}

		data.push(datos)
		let datosPost = new FormData(document.getElementById("formAddPartida"));
		datosPost.append("_token",token.value)
		datosPost.append("gastoId",gastoId)
		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: datosPost,
			cache: false,
			contentType: false,
			 processData: false,
			 dataType: 'json',
			beforeSend: () => {
				$(btnGuardar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {

			if ( respuesta.error ) {
				let elementErrorValidacion = elementSectionGasto.querySelector('.error-validacion');
				elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
				$(elementErrorValidacion).removeClass("d-none");

				return;
			}
			// console.log(respuesta)
			crearToast('bg-success', 'Crear Indirecto', 'OK', respuesta.respuestaMessage);
			location.reload();
		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
	
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el indirecto, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnGuardar).prop('disabled', false);
		});


		$('#tipo').val($('#tipo option:first').val()).trigger('change.select2');	
		$('#obra-destino').val($('#obra-destino option:first').val()).trigger('change.select2');	
		$('#codigo').val($('#codigo option:first').val()).trigger('change.select2');	
		$('#descripcion').val($('#descripcion option:first').val()).trigger('change.select2');	
		
		$('#proveedor').val('');
		$('#cantidad').val(0);
		elementFecha.value = ''	
		elementEconomico.value = ''
		elementCosto.value = '0'
		elementObservaciones.value = ''

	})
	//select 2 y datetimepickers
	$('.select2').select2({
		language: 'es',
		tags: false,
		width: '100%'
		// theme: 'bootstrap4'
	});

	$('#datetimepicker5').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});
	$('#datetimepicker1').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});
	$('#datetimepicker2').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});
	$('#datetimepicker3').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});

	$('#editarFechaGasto').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});

	$("#btnSubirArchivos").click(function(){
		document.getElementById('archivo').click();
	})
	let gastoDetalleId= null;

	/*========================================================
 	Validar tipo y tamaño de los archivos
 	========================================================*/
 	$("#archivo").change(function() {

 		// $("div.subir-comprobantes span.lista-archivos").html('');
 		let archivos = this.files;
 		if ( archivos.length == 0) return;

 		let error = false;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];
		    
			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "application/pdf" && archivo["type"] != "text/xml" ) {

				error = true;

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF o XML!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			}

 		}

 		if ( error ) {
 			$("#archivo").val("");

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

	}) // $("#archivos").change(function(){

	let modalVerArchivos = document.querySelector('#modalVerArchivos');
	/*==============================================================
	Visualizar los archivos
	==============================================================*/
	let tipo_archivo = null;
	$(document).on('click',"#btn-subirArchivo",function(){
		Swal.fire({
			title: 'Selecciona el tipo de archivo',
			input: 'radio',
			inputOptions: {
				1: 'Factura',
				2: 'Soporte'
			},
			inputValidator: (value) => {
				if (!value) {
					return 'Debes seleccionar un tipo de archivo';
				}
			},
			showCancelButton: true,
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (result.isConfirmed) {
				tipo_archivo = result.value; // 1 para Factura, 2 para Soporte
				gastoDetalleId = this.getAttribute('folio');
				document.getElementById('archivoSubir').click();
			}
		});
	})

	// Envio del formulario para subir los archivos
	$("#archivoSubir").change(function() {

		let archivos = this.files;
		
		let token = document.getElementById('_token');
		if ( archivos.length == 0) return;

		let error = false;

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];
			
			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "application/pdf" && archivo["type"] != "text/xml" ) {

				error = true;

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
					title: 'Error en el tipo de archivo',
					text: '¡El archivo "'+archivo["name"]+'" debe ser PDF o XML!',
					icon: 'error',
					confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				Swal.fire({
					title: 'Error en el tamaño del archivo',
					text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
					icon: 'error',
					confirmButtonText: '¡Cerrar!'
				})

			}

		}

		if ( error ) {
			$("#archivo").val("");

			return;
		}

		let formData = new FormData();

		// Iteramos sobre todos los archivos seleccionados
		for (let i = 0; i < this.files.length; i++) {
			formData.append('archivos[]', this.files[i]); // El nombre del archivo en el servidor será archivos[]
		}

		formData.append("accion","subir-archivo")
		formData.append("tipo",tipo_archivo)
		formData.append("gastoDetalleId",gastoDetalleId)
		formData.append("_token",token.value)
		// Enviamos la solicitud AJAX
		fetch(rutaAjax+"app/Ajax/GastosAjax.php", {
			method: 'POST',
			body: formData
		})
		.then(response => response.text())
		.then(data => {
			crearToast('bg-success', 'Insertar Documentos', 'OK', data.respuestaMessage);
			location.reload();
		})
		.catch(error => {
		console.error('Error:', error);
		});

   	}) // $("#archivos").change(function(){

	$(document).on('click','.btn-mostrar-modal',function() {
		let folio = this.getAttribute('folio').toUpperCase();
		$("#modalVerArchivosLabel span").html(folio);
		$("#modalVerArchivos div#accordionArchivos").html('');

		let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');
		$(elementErrorValidacion).addClass("d-none");

		let token = $('input[name="_token"]').val();
		let gastoDetalleId = $(this).attr("folio");

		let datos = new FormData();
		datos.append("accion", "verArchivos");
		datos.append("_token", token);
		datos.append("gastoDetalleId", gastoDetalleId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/GastosAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				if ( respuesta.error ) {
					// let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				let facturas = respuesta.archivos.filter(archivo => archivo.tipo == "1");
				let soportes = respuesta.archivos.filter(archivo => archivo.tipo == "2");

				let crearAcordeon = (archivos, tipo) => {
					let acordeonId = tipo === 1 ? "accordionFacturas" : "accordionSoportes";
					let tituloAcordeon = tipo === 1 ? "Facturas" : "Soportes";

					let acordeonHtml = `
						<div class="card">
							<div class="card-header" id="heading-${acordeonId}">
								<h2 class="mb-0">
									<button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse-${acordeonId}" aria-expanded="false" aria-controls="collapse-${acordeonId}">
										${tituloAcordeon}
									</button>
								</h2>
							</div>
							<div id="collapse-${acordeonId}" class="collapse" aria-labelledby="heading-${acordeonId}" data-parent="#modalVerArchivos div#accordionArchivos">
								<div class="card-body p-0" id="${acordeonId}">
								</div>
							</div>
						</div>`;
					
					$("#modalVerArchivos div#accordionArchivos").append(acordeonHtml);

					archivos.forEach((archivo, index) => {
						let elementIconoEliminar = `<i class="fas fa-trash-alt fa-xs text-danger eliminarArchivo" style="cursor: pointer; position: absolute; top: 12px; right: 8px;" gastoDetalleId="${archivo.gastoDetalleId}" archivoId="${archivo.id}" folio="${archivo.titulo}"></i>`;

						let elementArchivo = `
							<div class="card">
								<div class="card-header px-0 py-2" id="heading-${tipo}-${index+1}">
									<h2 class="mb-0">
										<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-${tipo}-${index+1}" aria-expanded="false" aria-controls="collapse-${tipo}-${index+1}">
											${archivo.titulo}
										</button>${elementIconoEliminar}
									</h2>
								</div>

								<div id="collapse-${tipo}-${index+1}" class="collapse" aria-labelledby="heading-${tipo}-${index+1}" data-parent="#${acordeonId}">
									<div class="card-body p-0">
										<embed src="${archivo.ruta}#toolbar=1&navpanes=0" type="application/pdf" width="100%" height="600px" />
									</div>
								</div>
							</div>`;

						$(`#modalVerArchivos div#${acordeonId}`).append(elementArchivo);
					});
				};

				crearAcordeon(facturas, 1);
				crearAcordeon(soportes, 2);

		    }

		})

	})

	// Confirmar la eliminación de los Archivos
	$("#modalVerArchivos div.accordion").on("click", "i.eliminarArchivo", function (e) {

		let btnEliminar = this;
	    let folio = $(this).attr("folio");

	    Swal.fire({
			title: '¿Estás seguro de querer eliminar este Archivo (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarlo!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if ( result.isConfirmed ) eliminarArchivo(btnEliminar);
	    })

	});

	// Envio del formulario para Eliminar el archivo
	function eliminarArchivo(btnEliminar = null){

		if ( btnEliminar == null ) return;

		let token = $('input[name="_token"]').val();
		let archivoId = $(btnEliminar).attr("archivoId");
		let gastoDetalleId = $(btnEliminar).attr("gastoDetalleId");

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarArchivo");
		datos.append("archivoId", archivoId);
		datos.append("gastoDetalleId", gastoDetalleId);
		$.ajax({
		    url: rutaAjax+"app/Ajax/GastosAjax.php",
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

		    		$(btnEliminar).parent().parent().parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    		$(btnEliminar).parent().parent().parent().remove();

		    		let btnVerArchivos = document.getElementById("verArchivos");
		    		let elementSpan = btnVerArchivos.querySelector('span');
		    		let cantidadImagenes = elementSpan.innerHTML;
		    		btnVerArchivos.querySelector('span').innerHTML = --cantidadImagenes;

		    		if ( cantidadImagenes == 0 ) {
		    			btnVerArchivos.removeChild(elementSpan);
		    			$(btnVerArchivos).prop('disabled', true);
		    		}

		    	} else {

		    		$(btnEliminar).parent().parent().parent().before('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	}

	/*==============================================================
	Boton para crear Requisiciones
	==============================================================*/
	$('#modalCrearRequisicion').on('shown.bs.modal', function (event) {

		let elementSelectPeriodo = this.querySelector('select#modalCrearRequiInsumoIndirecto_periodos');
		let options = elementSelectPeriodo.querySelectorAll('option');
		if ( options.length > 1 ) {
			let btnGuardar = this.querySelector('button.btnGuardar');
			$(btnGuardar).prop('disabled', false);
		}
		let periodos = $('#periodos').val();
		for ( let i = 0; i < periodos; i++ ) {
			let newOption = `<option value="${i+1}">
								Semana ${i+1}
							</option>`;

			$(elementSelectPeriodo).append(newOption);
		}
		let obraId = document.getElementById("obra");
	
		fetch( `${rutaAjax}app/Ajax/RequisicionAjax.php?obraId=${obraId.value}`, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			if ( data.error ) {
				let elementList = document.createElement('li'); // prepare a new li DOM element
				let newContent = document.createTextNode(data.errorMessage);
				elementList.appendChild(newContent); //añade texto al div creado.
				elementErrorValidacion.querySelector('ul').appendChild(elementList);
	
				$(elementErrorValidacion).removeClass("d-none");
	
				return;
			}
			$("#modalCrearRequiInsumoIndirecto_folio").val(data)
		});
	
	});

	$('#btnCrearRequisicion').on('click',function() {
		let token = $('input[name="_token"]').val();
		let gastoId = $('#gastoId').val();
		let obraId = $('#obra').val();
		let periodo = $('#modalCrearRequiInsumoIndirecto_periodos').val();
		let folio = $('#modalCrearRequiInsumoIndirecto_folio').val();

		let elementPeriodo = document.getElementById("modalCrearRequiInsumoIndirecto_periodos");

		elementPeriodo.classList.remove("is-invalid");
		elementPadre = elementPeriodo.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		if ( periodo == '' ) {
			elementPeriodo.classList.add("is-invalid");
			elementPadre = elementPeriodo.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			
			newContent = document.createTextNode("La semana es obligatoria.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			return
		}

		let datos = new FormData();
		datos.append("accion", "crearRequisicion");
		datos.append("_token", token);
		datos.append("gastoId", gastoId);
		datos.append("obraId", obraId);
		datos.append("folio", folio);
		datos.append("periodo", periodo);
		$.ajax({
		    url: rutaAjax+'app/Ajax/GastosAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				if ( respuesta.error ) {
					let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				location.reload()
		    }

		})
	});

	$("#btnDownload").click(function(event) {
		event.preventDefault();

		let gastoId = $('#gastoId').val();
		window.open(`${rutaAjax}app/Ajax/GastosAjax.php?gastoId=${gastoId}`, '_blank');
	})

	/*==============================================================
	Editar el gasto
	==============================================================*/
	$(document).on('click', '.btn-editar', function () {

		
		let modal = $('#modalEditarGasto'); // Select the modal
		
		// Extract data from the row
		let gastoId = $(this).attr('folio'); // Assuming the button has a data-id attribute with the gasto ID

		$.ajax({
			url: `${rutaAjax}app/Ajax/GastosAjax.php?gastoDetalleId=${gastoId}`,
			method: 'GET',
			dataType: 'json',
			success: function (respuesta) {
				if (respuesta.error) {
					Swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}

				// Extract data from the response
				let { id, fecha, tipoGasto, obra, obraDetalle, costo, economico, observaciones, solicito, cantidad, proveedor, factura } = respuesta.datos;

				// Fill the modal inputs with the extracted data
				modal.find('#gastoDetalleId').val(id);
				modal.find('#editarCantidad').val(cantidad);
				modal.find('#editarProveedor').val(proveedor);
				modal.find('#editarSolicito').val(solicito);
				modal.find('#editarFechaGasto').val(fecha);
				modal.find('#editarFactura').val(factura);
				modal.find('#editarTipoGasto').val(tipoGasto).trigger('change.select2');
				modal.find('#editarObraGastoo').val(obra).trigger('change.select2');
				modal.find('#editarDescripcionGasto').val(obraDetalle).trigger('change.select2');
				modal.find('#editarCosto').val(costo);
				modal.find('#editarNumeroEconomico').val(economico);
				modal.find('#editarObservacionesGasto').val(observaciones);

				// Show the modal
				modal.modal('show');
			},
			error: function (error) {
				console.error('Error:', error);
				Swal.fire({
					title: 'Error',
					text: 'Ocurrió un error al intentar obtener los datos del gasto.',
					icon: 'error',
					confirmButtonText: 'Cerrar'
				});
			}
		});

	});

	/*==============================================================
	Actualizar el gasto
	==============================================================*/
	$(document).on('click', '#btnActualizarGasto', function () {
		let modal = $('#modalEditarGasto'); // Select the modal

		let formData = new FormData(document.getElementById('formEditarGasto')); // Get the form data from the form by ID
		
		formData.append('accion', 'actualizarGasto');

		$.ajax({
			url: `${rutaAjax}app/Ajax/GastosAjax.php`,
			method: 'POST',
			data: formData,
			contentType: false,
			processData: false,
			dataType: 'json',
			success: function (respuesta) {
				if (respuesta.error) {
					Swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}
				// Show a success message and refresh the page
				Swal.fire({
					title: 'Éxito',
					text: 'El gasto se actualizó correctamente.',
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}).then(() => {
					location.reload(); // Refresh the page to see the updated data
				});
			},
			error: function (error) {
				console.error('Error:', error);
				Swal.fire({
					title: 'Error',
					text: 'Ocurrió un error al intentar actualizar el gasto.',
					icon: 'error',
					confirmButtonText: 'Cerrar'
				});
			}
		});
	})

	$("#btnDescargarFacturas").on('click', function () {
		let gastoId = $('#gastoId').val();
		window.open(`${rutaAjax}app/Ajax/GastosAjax.php?accion=descargarFacturas&gastoId=${gastoId}`, '_blank');
	});

	$('#btnAutorizarGasto').on('click', function () {
		let gastoId = $('#gastoId').val();
		let token = $('input[name="_token"]').val();

		let datos = new FormData();
		datos.append("accion", "autorizarGasto");
		datos.append("_token", token);
		datos.append("gastoId", gastoId);

		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success:function(respuesta) {

				if (respuesta.error) {
					Swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}

				Swal.fire({
					title: 'Éxito',
					text: 'El gasto fue autorizado correctamente.',
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}).then(() => {
					location.reload();
				});
			}
		})
	});

	$('#btnEnProceso').on('click', function () {
		let gastoId = $('#gastoId').val();
		let token = $('input[name="_token"]').val();

		let datos = new FormData();
		datos.append("accion", "enProcesoGasto");
		datos.append("_token", token);
		datos.append("gastoId", gastoId);

		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success:function(respuesta) {

				if (respuesta.error) {
					Swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}

				Swal.fire({
					title: 'Éxito',
					text: 'El gasto fue marcado como en proceso correctamente.',
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}).then(() => {
					location.reload();
				});
			}
		})
	});

	$('#btnProcesado').on('click', function () {
		let gastoId = $('#gastoId').val();
		let token = $('input[name="_token"]').val();

		let datos = new FormData();
		datos.append("accion", "procesarGasto");
		datos.append("_token", token);
		datos.append("gastoId", gastoId);

		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success:function(respuesta) {

				if (respuesta.error) {
					Swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}

				Swal.fire({
					title: 'Éxito',
					text: 'El gasto fue marcado como procesado correctamente.',
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}).then(() => {
					location.reload();
				});
			}
		})
	});

	$("#btnPagado").on('click', function () {
		let gastoId = $('#gastoId').val();
		let token = $('input[name="_token"]').val();

		let datos = new FormData();
		datos.append("accion", "marcarPagado");
		datos.append("_token", token);
		datos.append("gastoId", gastoId);

		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success:function(respuesta) {

				if (respuesta.error) {
					Swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}

				Swal.fire({
					title: 'Éxito',
					text: 'El gasto fue marcado como pagado correctamente.',
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}).then(() => {
					location.reload();
				});
			}
		})
	});

	$("#modalBuscarRequisicion").on("shown.bs.modal", function () {
		if (!$.fn.DataTable.isDataTable("#tablaRequisiciones")) {
		$.ajax({
			url: `${rutaAjax}app/Ajax/RequisicionAjax.php`,
			method: "GET",
			cache: false,
			contentType: false,
			processData: false,
			dataType: "json",
			success: function (data) {
			// Remove the last 4 columns from the columns definition and data
			const columns = data.datos.columnas.slice(0, -6);

			// Remove the last 4 fields from each row in registros

			$("#tablaRequisiciones").DataTable({
				autoWidth: false,
				responsive: true,
				info: false,
				data: data.datos.registros,
				columns: columns,
				columnDefs: [
					// { targets: [0,1,2,3,4], orderable: false }
				],
				createdRow: (row, data, index) => {
					row.classList.add("seleccionable");
				},
			});
			},
		});
		}
	});

	$("#tablaRequisiciones").on("click", "tbody tr.seleccionable", function () {
		let data = $('#tablaRequisiciones').DataTable().row(this).data();
		console.log(data.id);

		let requisicionId = data.id;
		let gastoId = $('#gastoId').val();
		let token = $('input[name="_token"]').val();

		Swal.fire({
			title: '¿Enlazar Requisición?',
			text: `¿Deseas enlazar la requisición con folio: ${data.folio}? Esto cerrará el gasto actual y no se podrá agregar más partidas.`,
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Sí, enlazar',
			cancelButtonText: 'Cancelar'
		}).then((result) => {
			if (result.isConfirmed) {
				let formData = new FormData();
				formData.append('accion', 'enlazarRequisicion');
				formData.append('requisicionId', requisicionId);
				formData.append('gastoId', gastoId);
				formData.append('_token', token);

				$.ajax({
					url: `${rutaAjax}app/Ajax/GastosAjax.php`,
					method: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					dataType: 'json',
					success: function(respuesta) {
						if (respuesta.error) {
							Swal.fire({
								title: 'Error',
								text: respuesta.errorMessage,
								icon: 'error',
								confirmButtonText: 'Cerrar'
							});
							return;
						}
						Swal.fire({
							title: 'Éxito',
							text: 'La requisición fue enlazada correctamente.',
							icon: 'success',
							confirmButtonText: 'Cerrar'
						}).then(() => {
							location.reload();
						});
					},
					error: function(error) {
						Swal.fire({
							title: 'Error',
							text: 'Ocurrió un error al enlazar la requisición.',
							icon: 'error',
							confirmButtonText: 'Cerrar'
						});
					}
				});
			}
		});
	});

})