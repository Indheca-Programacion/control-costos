$(function(){
	let tablaAsistencias = document.getElementById('tablaAsistencias')
	let tablaAsistenciasDetalles = document.getElementById('tablaAsistenciasDetalles')

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tablaAsistencias != null ) fAjaxDataTable(rutaAjax+'app/Ajax/AsistenciasAjax.php', '#tablaAsistencias');

	// Envio del formulario para Crear o Editar registros
	function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);
		
		// let formData = new FormData(formulario); 	
		// console.log([...formData.entries()])

		formulario.submit();
	}

	let formulario = document.getElementById("formCreaAsistenciaSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	//Actualizar valores
	$("#btnSubirLista").click(function(){
		document.getElementById('archivos').click();
	})

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});

	// Al seleccionar una obra, se llenan los select de puesto y semana
	$('#filtroObraId').change(function() {
		let obraId = $(this).val();
		if(obraId != 0)
		$.ajax({
			url: `${rutaAjax}app/Ajax/AsistenciasAjax.php?obraId=${obraId}`,
			method: 'GET',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
		}).done(function(respuesta) {
			let puesto = $('#puesto');
			puesto.empty();
			puesto.append($('<option>').text("SELECCIONE UN PUESTO").val(0));
			$.each(respuesta, function(index, value) {
				puesto.append($('<option>').text(value.descripcion).val(value.obraDetalleId));
			});
			let trabajaodres = $('#trabajadorId');
			trabajaodres.empty();
			trabajaodres.append($('<option>').text("SELECCIONE UN EMPLEADO").val(0));
		}).fail(function(error){
			console.log(error)
		});
	});

	// Al seleccionar el puesto se busca las personas asociadas a ese puesto
	$('#puesto').change(function() {
		obtenerEmpleados()
	})

	$('#fecha').change(function(){
		obtenerEmpleados()
	})

	// Se activa cuando se manda guardar
	$('.btnAgregarJornada').on('click',function (e){
		let obraId = document.getElementById("filtroObraId");
		let puesto = document.getElementById("puesto");
		let puestoText = puesto.options[puesto.selectedIndex].text;
		let trabajador = document.getElementById("trabajadorId");
		let trabajadorText = trabajador.options[trabajador.selectedIndex].text;
		// let archivos = document.getElementById("archivos");

		let fecha = $('#fecha').val();
		let horaEntradas = $('#horaEntrada').val();
		let horaSalidas = $('#horaSalida').val();
		let horaExtras = $('#horasExtras').val();
		let falta = $('#falta');
		let incapacidad = $('#incapacidad');
		let vacaciones = $('#vacaciones');
        let observacion = $('#observacion').val();

		//Se quitan las alertas de error en caso de existir
		obraId.classList.remove("is-invalid");
		elementPadre = obraId.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		puesto.classList.remove("is-invalid");
		elementPadre = puesto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		trabajador.classList.remove("is-invalid");
		elementPadre = trabajador.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;
		//Se evalua si hay un error
		if ( obraId.value == 0 ) {
			obraId.classList.add("is-invalid");
			elementPadre = obraId.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("La obra es obligatoria.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}else if ( puesto.value == 0 ) {
			puesto.classList.add("is-invalid");
			elementPadre = puesto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El puesto es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( trabajador.value == 0 ) {
			trabajador.classList.add("is-invalid");
			elementPadre = trabajador.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El trabajador es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( errores ) return;
		//Se desactivan el selector de obra y semana
		obraId.disabled = true
		
		let incidencia = 'N/A'
		if (falta.is(':checked') || incapacidad.is(':checked') || vacaciones.is(':checked')) {
			horaEntradas = '00:00'
			horaSalidas = '00:00'
			horaExtras = '0'
			if (falta.is(':checked')) {
				incidencia = 'Falta';
			} else if(incapacidad.is(':checked')) {
				incidencia = 'Incapacidad';
			} else {
				incidencia = 'Vacaciones'
			}
		}

		let tablaAsistencias = document.querySelector('#tablaAsistenciasDetalles tbody');
		let registros = tablaAsistencias.querySelectorAll('tr');

		let registrosNuevos = tablaAsistencias.querySelectorAll('tr[nuevo]');
		let partida = registrosNuevos.length + 1;
		let elementRow = `<tr nuevo partida="${partida}">
							<td partida class="text-right">
								<span>${registros.length + 1}</span>
								<input type="hidden" name="jornada[partida][]" value="${partida}">
								<button type='button' class='btn btn-xs btn-danger ml-1 eliminar'>
									<i class='far fa-times-circle'></i>
								</button>
							</td>
							<td class="text-center">${puestoText}</td>
							<td class="text-center">${trabajadorText}</td>
							<td class="text-center">${fecha}</td>
							<td class="text-center">${horaEntradas}</td>
							<td class="text-center">${horaSalidas}</td>
							<td class="text-center">${horaExtras}</td>
							<td class="text-center">${incidencia}</td>
						</tr>`;
		$(tablaAsistenciasDetalles).append(elementRow);

		let rowNuevoRegistro = tablaAsistenciasDetalles.querySelector(`tr[partida="${partida}"]`);
		let columnaConcepto = rowNuevoRegistro.querySelector('td:last-child');

		switch (incidencia) {
			case 'Falta':
				incidencia = 1
				break;
			case 'Incapacidad':
				incidencia = 2
			break;
			case 'Vacaciones':
				incidencia = 3
			break;
			case 'N/A':
				incidencia = 0
			break;
			default:
				break;
		}

		const inputObraId = document.createElement('input');
		inputObraId.type = 'hidden';
		inputObraId.name = 'jornada[obraId][]';
		inputObraId.value = obraId.value;
		$(columnaConcepto).append(inputObraId);

		const inputTrabajador = document.createElement('input');
		inputTrabajador.type = 'hidden';
		inputTrabajador.name = 'jornada[trabajador][]';
		inputTrabajador.value = trabajador.value;
		$(columnaConcepto).append(inputTrabajador);

		const inputFechas = document.createElement('input');
		inputFechas.type = 'hidden';
		inputFechas.name = 'jornada[fecha][]';
		inputFechas.value = fecha;
		$(columnaConcepto).append(inputFechas);

		const inputHrEntrada= document.createElement('input');
		inputHrEntrada.type = 'hidden';
		inputHrEntrada.name = 'jornada[hrEntrada][]';
		inputHrEntrada.value = horaEntradas;
		$(columnaConcepto).append(inputHrEntrada);

		const inputHrSalida= document.createElement('input');
		inputHrSalida.type = 'hidden';
		inputHrSalida.name = 'jornada[hrSalida][]';
		inputHrSalida.value = horaSalidas;
		$(columnaConcepto).append(inputHrSalida);
		
		const inputHrExtra= document.createElement('input');
		inputHrExtra.type = 'hidden';
		inputHrExtra.name = 'jornada[hrExtra][]';
		inputHrExtra.value = horaExtras;
		$(columnaConcepto).append(inputHrExtra);

		const inputIncidencias= document.createElement('input');
		inputIncidencias.type = 'hidden';
		inputIncidencias.name = 'jornada[incidencia][]';
		inputIncidencias.value = incidencia;
		$(columnaConcepto).append(inputIncidencias);

        const inputObservaciones = document.createElement('input');
		inputObservaciones.type = 'hidden';
		inputObservaciones.name = 'jornada[observacion][]';
		inputObservaciones.value = observacion;
		$(columnaConcepto).append(inputObservaciones);

		// const inputArchivos = archivos.cloneNode(true);
		// inputArchivos.removeAttribute('id');
		// inputArchivos.name = 'jornada_archivos['+partida+'][]';
		// $(columnaConcepto).append(inputArchivos);
		// $("#archivos").val("");
		// $("div.subir-archivos span.previsualizar").html('');

		$(puesto).prop("selectedIndex", 0).trigger("change");
		$(trabajador).prop("selectedIndex", 0).trigger("change");
		
		$('#prima').val(0)
		$('#comida').val(0)
		$('#horasExtras').val(0)
		$('#observacion').val('')
		$("#falta").prop("checked", false);
		$("#incapacidad").prop("checked", false);
		$("#vacaciones").prop("checked", false);


	});

	// Eliminar la partida agregada
	$(tablaAsistenciasDetalles).on("click", "button.eliminar", function (event) {
		this.parentElement.parentElement.remove();

		// Renumerar las partidas
		let tableRequisicionDetalles = tablaAsistenciasDetalles.querySelector('tbody');
		let registros = tableRequisicionDetalles.querySelectorAll('tr');
		registros.forEach( (registro, index) => {
			registro.setAttribute('partida', index + 1);
			registro.querySelector('td[partida] span').innerHTML = index + 1;
		});

		if ( registros.length == 0 ){
			let obraId = document.getElementById("filtroObraId");
			obraId.disabled = false
			let semana = document.getElementById("semana");
			semana.disabled = false
		}
	});

	$("#archivos").change(function() {

		$("div.subir-archivos span.previsualizar").html('');
		let archivos = this.files;

		if ( archivos.length == 0) return;
		let error = false;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];
		    
			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "application/pdf" ) {

				error = true;

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 4000000 ) {

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
			// else {

				// $("div.subir-comprobantes span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

			// }

 		}

 		if ( error ) {
 			$("#archivos").val("");

 			return;
 		}

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];

			$("div.subir-archivos span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

		}
	
	})

	function obtenerEmpleados(){
		let id = $('#puesto').val();
		let fecha = $('#fecha').val();
		if(id != 0)
		$.ajax({
			url: `${rutaAjax}app/Ajax/AsistenciasAjax.php?reqPersonalId=${id}&fecha=${fecha}`,
			method: 'GET',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
		}).done(function(respuesta) {
			let trabajaodres = $('#trabajadorId');
			trabajaodres.empty();
			trabajaodres.append($('<option>').text("SELECCIONE UN EMPLEADO").val(0));
			if (respuesta.registros !== '') {
				$.each(respuesta.registros, function(index, value) {
					trabajaodres.append($('<option>').text(value.nombreCompleto).val(value.id));
				});	
			}
		}).fail(function(error){
			console.log(error)
		});
	}
});
