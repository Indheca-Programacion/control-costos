$(function(){

	let tableList = document.getElementById('tablaInventarios');
	let tablaSalidaDetalles = document.getElementById('tablaSalidaDetalles');
	
	let datatTableSalidasDetalles = null;
	let parametrosTableList = { responsive: true };

	if( tablaSalidaDetalles != null ) {
		let inventarioId = $('#inventarioSalida').val();

		fetch( rutaAjax+'app/Ajax/InventarioSalidaAjax.php?inventarioId='+inventarioId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			dataTableSalidas = $('#tablaSalidaDetalles').DataTable({
				info: false,
				paging: false,
				pageLength: 10,
				searching: false,
				autoWidth: false,
				responsive: (parametrosTableList.responsive === undefined) ? true : parametrosTableList.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,
				language: LENGUAJE_DT,
				columnDefs: [
					{ orderable: false, targets: '_all' }
				],
				aaSorting: [],
			})
		}); // .then( data => {
	}

	$('#tablaSalidas tbody').on('click', 'td.details-control', function () {
		var tr = $(this).closest('tr');
		var row = dataTableSalidas.row(tr);
		var icon = $(this).find('i');

		if (row.child.isShown()) {
			// This row is already open - close it
			row.child.hide();
			icon.removeClass('fa-caret-down').addClass('fa-caret-right');
		} else {
			// Open this row
			row.child(format(row.data())).show();
			icon.removeClass('fa-caret-right').addClass('fa-caret-down');
		}
	});

	function format(d){
		// `d` is the original data object for the row
		let rows = d.partidas.map(detalle => {
			return '<tr>' +
				'<td>' + detalle.cantidad + '</td>' +
				'<td>' + detalle.descripcion + '</td>' +
				'<td>' + detalle.numeroParte + '</td>' +
				'<td>' + detalle['unidad.descripcion'] + '</td>' +
			'</tr>';
		}).join('');

		return '<table class="table table-sm table-bordered"> <thead> <tr> <th> Cantidad</th>  <th>Descripcion</th> <th>Numero de Parte</th> <th>Unidad</th>  </tr> </thead> ' + rows + '</table>';
    }

	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Inventario (Orden de compra: '+folio+') ?',
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

		var data = dataTableDetalles.data().toArray();

		var dataURL = signaturePad.toDataURL();

		let dataSend = new FormData(formulario)
		
		dataSend.append("detalles",data);
		dataSend.append("firma",dataURL);
		
		formulario.submit(dataSend);
	}

	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	let tipo = null

	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	// *************************************************
	// SELECT2 Y DATE TIME PICKERS
	// *************************************************

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	}); // $('.select2').select2({

	$('.select2Add').select2({
		tags: true
		// ,theme: 'bootstrap4'
	}); // $('.select2').select2({

    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    }); // $('.input-group.date').datetimepicker({

	$('#fechaEntregaDTP').datetimepicker();

	// *************************************************
	// Canvas para firmar
	// *************************************************

	const canvas = document.querySelector("#canvas");

	const signaturePad = new SignaturePad(canvas);

	$('#btnLimpiar').on('click',function(){
		signaturePad.clear();
	})
});
