$(function(){
	let tablaPlantillas = document.getElementById('tablaPlantillas')
	let tablaPlantillasDetalles = document.getElementById('tablaPlantillasDetalles')

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tablaPlantillas != null ) fAjaxDataTable(rutaAjax+'app/Ajax/PlantillaAjax.php', '#tablaPlantillas');

	if (tablaPlantillasDetalles !=null) {
		let plantillaId = $('#plantilla').val();
		fetch(`${rutaAjax}app/Ajax/PlantillaAjax.php?plantillaId=${plantillaId}`, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			$(tablaPlantillasDetalles).DataTable({

				autoWidth: false,
				responsive:  true,
				info: false,
				paging: true,
				searching: true,
				data: data.datos.registros,
				columns: data.datos.columnas,
	
				language: LENGUAJE_DT,
				aaSorting: [],
	
			}); // $(tableListResumen).DataTable({
		});
	}

	// Confirmar la eliminación del Usuario
	$(tablaPlantillas).on("click", "button.eliminar", function (e) {

		e.preventDefault();
		var folio = $(this).attr("folio");
		var form = $(this).parents('form');
	
		Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Plantilla (Nombre: '+folio+') ?',
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

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});

	// Se activa cuando se manda guardar
	$('#btnAgregar').on('click',function (e){
		let btnGuardar = this;

		let elementErrorValidacion = document.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");
		
		let fk_plantillaId = $('#fk_plantillaId').val();
		let token = document.getElementById('_token');
		
		let tipo = document.getElementById("tipo");
		
		let insumo = 'directoId'
		if (tipo.value == 1) insumo = "indirectoId"
		let directoindirecto = document.getElementById(insumo);
		let elementCantidad = document.getElementById("cantidad");
		let presupuesto = document.getElementById("presupuesto");

		let cantidad = elementCantidad.value !== '' ? numeroFormato(elementCantidad.value)  : 0;

		//Se quitan las alertas de error en caso de existir
		tipo.classList.remove("is-invalid");
		elementPadre = tipo.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		directoindirecto.classList.remove("is-invalid");
		elementPadre = directoindirecto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementCantidad.classList.remove("is-invalid");
		elementPadre = elementCantidad.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		presupuesto.classList.remove("is-invalid");
		elementPadre = presupuesto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;
		//Se evalua si hay un error
		if ( tipo.value == 0 ) {
			tipo.classList.add("is-invalid");
			elementPadre = tipo.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El tipo es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( directoindirecto.value == '' ) {
			directoindirecto.classList.add("is-invalid");
			elementPadre = directoindirecto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El directo/indirecto es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( presupuesto.value == '' ) {
			presupuesto.classList.add("is-invalid");
			elementPadre = presupuesto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El presupuesto es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}
		if ( cantidad < 0 ) {
			elementCantidad.classList.add("is-invalid");

			elementPadre = elementCantidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("La cantidad debe ser mayor a 0.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( cantidad == '' ) {
			elementCantidad.classList.add("is-invalid");
			elementPadre = elementCantidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("La cantidad es obligatoria.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( errores ) return;
		let datosPost = new FormData();
		datosPost.append("_token",token.value)
		datosPost.append("presupuesto",numeroFormato(presupuesto.value))
		datosPost.append("fk_plantilla",fk_plantillaId)
		datosPost.append("cantidad",cantidad)
		datosPost.append(insumo, directoindirecto.value)
		datosPost.append("accion",'addDetalle')
		$.ajax({
			url: rutaAjax+'app/Ajax/PlantillaAjax.php',
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

			if ( respuesta.errors ) {

				// console.log(Object.keys(respuesta.errors))
				let errors = Object.values(respuesta.errors);

				// respuesta.errors.forEach( (item, index) => {
				errors.forEach( (item) => {
					let elementList = document.createElement('li'); // prepare a new li DOM element
					let newContent = document.createTextNode(item);
					elementList.appendChild(newContent); //añade texto al div creado.
					elementErrorValidacion.querySelector('ul').appendChild(elementList);
				});

			}
			crearToast('bg-success', 'Crear Indirecto', 'OK', respuesta.respuestaMessage);
			location.reload();
		})
		.fail(function(error) {
			// console.log("*** Error ***");
			console.log(error);
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

		$('#cantidad').val(0)
		$('#presupuesto').val(0)
		$(directoindirecto).val('').change()


	});

	// Eliminar la partida agregada
	$(tablaPlantillasDetalles).on("click", "button.eliminar", function (e) {
		e.preventDefault();
		var folio = $(this).attr("folio");
		var form = $(this).parents('form');
	
		Swal.fire({
			title: '¿Estás Seguro de querer eliminar este detalle (Nombre: '+folio+') ?',
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

	$('#tipo').on("change",function(){
		if (this.value==1) {
			$('.directo').addClass("d-none");
			$('.indirecto').removeClass("d-none");
		} 
		if (this.value==2) {
			$('.directo').removeClass("d-none");
			$('.indirecto').addClass("d-none");
		} 
		if (this.value == '') {
			$('.directo').addClass("d-none");
			$('.indirecto').addClass("d-none");
		}
	});

	$('#importarMaterialesModal button.btnimpMateriales').on("click",function(){
		let btnAgregar = this;
		let datos = new FormData()

		let id = $('#obra').val();
		let token = $('input[name="_token"]').val();
		let plantilla = $('#plantilla').val();

		datos.append("obra",id)
		datos.append("plantilla",plantilla)
		datos.append("_token", token);
		datos.append("accion","importDetalle");
		$.ajax({
			url: rutaAjax+'app/Ajax/PlantillaAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {
			// console.log(respuesta)
			if ( respuesta.error ) {

				crearToast('bg-error', 'Importar Plantilla', 'Error', respuesta.errorMessage);
				
				return;

			}

			crearToast('bg-success', 'Importar Plantilla', 'OK', respuesta.respuestaMessage);
			location.reload()

		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
			// console.log(error);
			crearToast('bg-error', 'Importar Plantilla', 'OK', error.errorMessage);

		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
	});

});
