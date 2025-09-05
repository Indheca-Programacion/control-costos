$(function(){
	let tablaAsistencias = document.getElementById('tablaAsistencias')

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tablaAsistencias != null ) fAjaxDataTable(rutaAjax+'app/Ajax/NominasAjax.php', '#tablaAsistencias');
	$(tablaAsistencias).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Nomina (semana: '+folio+') ?',
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
		
		let data = $('#tablaNominasDetalles').DataTable().rows().data().toArray();

		for (let i = 0; i < data.length; i++) {
			let primas = document.createElement("input");
			primas.type = "hidden";
			primas.name = "datos[primas][]";
			primas.value = data[i].primas;
			formulario.appendChild(primas);

			let comida = document.createElement("input");
			comida.type = "hidden";
			comida.name = "datos[comida][]";
			comida.value = data[i].comida;
			formulario.appendChild(comida);

			let prestamos = document.createElement("input");
			prestamos.type = "hidden";
			prestamos.name = "datos[prestamos][]";
			prestamos.value = data[i].prestamos;
			formulario.appendChild(prestamos);

			let descuentos = document.createElement("input");
			descuentos.type = "hidden";
			descuentos.name = "datos[descuentos][]";
			descuentos.value = data[i].descuentos;
			formulario.appendChild(descuentos);

			let pension = document.createElement("input");
			pension.type = "hidden";
			pension.name = "datos[pension][]";
			pension.value = data[i].pension;
			formulario.appendChild(pension);

			let neto = document.createElement("input");
			neto.type = "hidden";
			neto.name = "datos[neto][]";
			neto.value = data[i].neto;
			formulario.appendChild(neto);

			let empleado = document.createElement("input");
			empleado.type = "hidden";
			empleado.name = "datos[empleado][]";
			empleado.value = data[i].empleado;
			formulario.appendChild(empleado);

			let obradetalle = document.createElement("input");
			obradetalle.type = "hidden";
			obradetalle.name = "datos[obradetalle][]";
			obradetalle.value = data[i].obraDetalle;
			formulario.appendChild(obradetalle);

			let salario = document.createElement("input");
			salario.type = "hidden";
			salario.name = "datos[salario][]";
			salario.value = data[i].salario;
			formulario.appendChild(salario);

			let hrsExtras = document.createElement("input");
			hrsExtras.type = "hidden";
			hrsExtras.name = "datos[hrsExtras][]";
			hrsExtras.value = data[i].hrsExtras;
			formulario.appendChild(hrsExtras);
		}
		
		formulario.submit();
	}

	let formulario = document.getElementById("formCrearNominaSend");
	let tablaDetalles = document.getElementById("tablaNominasDetalles");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});
	let fechaInicio
	// Al seleccionar una obra, se llenan los select de puesto y semana
	$('#filtroObraId').change(function() {
		let obraId = $(this).val();
		$('#btnSend').addClass('d-none');
		$('#tablaNominasDetalles').DataTable().clear().draw();
		if(obraId != 0){
			$.ajax({
				url: `${rutaAjax}app/Ajax/NominasAjax.php?obraId=${obraId}`,
				method: 'GET',
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
			}).done(function(respuesta) {
				let semanas = respuesta.respuesta
				fechaInicio = respuesta.fecha
				let semana = $('#semana');
				semana.empty();
				semana.append($('<option>').text("SELECCIONE UNA SEMANA").val(0));
				semanas.forEach(index => {
					let opcion = $('<option></option>').attr('value', index).text('Semana '+index);
					semana.append(opcion);
				});
			}).fail(function(error){
				console.log(error)
			});
		}
	});
	// Al seleccionar una semana se llenan las fechas de acuerdo a la semana correspondiendo por el dia de obra
	$('#semana').change(function(){
		let numeroSemana = $(this).val();
		let fechas = obtenerDias(fechaInicio,parseInt(numeroSemana));
		let obra = $('#filtroObraId').val();
		$('#btnSend').addClass('d-none');
		if (numeroSemana > 0){
			$.ajax({
				url: `${rutaAjax}app/Ajax/NominasAjax.php?fechaInicial=${fechas.inicio}&fechaFinal=${fechas.fin}&obra=${obra}`,
				method: 'GET',
				cache: false,
				contentType: false,
				processData: false,
				dataType: 'json',
			}).done(function(respuesta) {
				if( respuesta.respuesta.length>0){
					calcularNeto(respuesta.respuesta)
					$('#btnSend').removeClass('d-none');
				}
			}).fail(function(error){
				console.log(error)
			});
		}else{
			$('#tablaNominasDetalles').DataTable().clear().draw();
		}
	});

	function calcularNeto(datos) {
		let consecutivo = 1;
		datos.forEach(element => {
			let domingo = (element.dias-element.domingo)/6
			let puesto = element.puesto
			let totalDias = element.dias+domingo
			let sd = element.salario/7
			let sueldo = totalDias * sd
			let puHrsEx = sd/8*1.5
			let hrsEx = puHrsEx * element.hrsExtras
			let totalSueldo = sueldo+hrsEx+element.comida+element.primas;
			element.neto = (totalSueldo - (element.prestamos+element.descuentos+element.pension)).toFixed(2);
			element.consecutivo =+ consecutivo

			consecutivo++
		});

		let columnas = [
			{data:"consecutivo"},
			{data:"nombre"},
			{data:"puesto"},
			{data:"salario"},
			{data:"hrsExtras"},
			{data:"primas"},
			{data:"comida"},
			{data:"prestamos"},
			{data:"descuentos"},
			{data:"pension"},
			{data:"neto"}
		]

		let arrayEditable = [5,6,7,8,9]
		let arrayRight = [0,3,4,5,6,7,8,9,10]

		let tablaExistente = $('#tablaNominasDetalles').DataTable();

		tablaExistente.destroy();

		let tabla = $(tablaDetalles).DataTable({
			autoWidth: false,
			info: false,
			paging: false,
			searching: false,
			data: datos,
			columns: columnas,
			columnDefs: [
				{ targets: arrayEditable, className: 'text-right editable' },
				{ targets: arrayRight, className: 'text-right' },
			],
			language: LENGUAJE_DT,
			aaSorting: [],
		})

		tablaDetalles.parentElement.classList.add("table-responsive");

	}

	function obtenerDias(fechaInicial, numeroSemana) {
		let fecha = new Date(fechaInicial+ 'T00:00:00');
		let diaSemana = fecha.getUTCDay();
		if (diaSemana < 4 ) numeroSemana += 1
		fecha.setDate(fecha.getUTCDate() + (numeroSemana - 1) * 7);
	
		let ajuste = (diaSemana < 4) ? 3 - diaSemana : 10 - diaSemana;
		fecha.setDate(fecha.getUTCDate() + ajuste);
	
		let inicioSemana = new Date(fecha.getTime() - 6 * 24 * 60 * 60 * 1000);
		let finSemana = new Date(fecha.getTime());
	
		return {
			inicio: formatearFecha(inicioSemana),
			fin: formatearFecha(finSemana)
		};
	}

    $(tablaDetalles).on('dblclick', 'tbody td.editable', function () {
		let currentValue = $(this).text();
		$(this).html('<input type="number" class="campoConDecimal" value="' + currentValue + '">');
		$(this).find('input').focus();
	});
	
	$(tablaDetalles).on('blur', 'input', function() {
		let input = $(this);
		let value = input.val()!= '' ? input.val() : 0
		let newValue = parseFloat(value);
		let celda = input.closest('td');
		let filaIdx = celda.parent().index();
		let columnaIdx = celda.index();

		// Obtener la instancia de DataTables
		let tabla = $('#tablaNominasDetalles').DataTable();

		// Modificar el valor de la celda mediante DataTables
		tabla.cell({ row: filaIdx, column: columnaIdx }).data(newValue).draw();

		// Obtener los datos actualizados de la tabla
		let datos = tabla.data().toArray();

		// Hacer algo con los datos obtenidos de la tabla
		calcularNeto(datos);
	});
});
