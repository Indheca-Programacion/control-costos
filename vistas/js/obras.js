$(function(){

	let tableList = document.getElementById('tablaObras');
	let parametrosTableList = { responsive: true };

	// Realiza la petición para actualizar el listado de obras
	function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

		fetch( rutaAjax, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			// console.log(data.datos)

			$(idTabla).DataTable({

				autoWidth: false,
				responsive: ( parametros.responsive === undefined ) ? true : parametros.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,

		        createdRow: function (row, data, index) {
		        	if ( data.colorTexto != '' ) $('td', row).eq(4).css("color", data.colorTexto);
		        	if ( data.colorFondo != '' ) $('td', row).eq(4).css("background-color", data.colorFondo);
		        },

				buttons: [{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }],

				language: LENGUAJE_DT,
				aaSorting: [],

			}).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); // $(idTabla).DataTable({
		}); // .then( data => {

	} // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	// if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/ObraAjax.php', '#tablaObras', parametrosTableList);
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/ObraAjax.php', '#tablaObras', parametrosTableList);

	// Confirmar la eliminación de la Obra
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Obra (Descripción: '+folio+') ?',
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
	$('.select2Add').select2({
		tags: true
		// ,theme: 'bootstrap4'
	});
	//Date picker
    // $('#fechaSolicitudDTP').datetimepicker({
    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    });

    let minDate = moment().subtract(12, 'months')
	let maxDate = moment().add(1, 'hours');

	$('#fechaInicioDTP').datetimepicker('minDate', minDate);

	minDate = $('#fechaInicioDTP').datetimepicker('viewDate');

	$('#fechaFinalizacionDTP').datetimepicker('minDate', minDate);
	$('#fechaFinalizacionDTP').datetimepicker('maxDate', maxDate);

	let elementEmpresaId = $('#empresaId.select2.is-invalid');
	let elementEstatusId = $('#estatusId.select2.is-invalid');
	if ( elementEmpresaId.length == 1 ) {
		$('span[aria-labelledby="select2-empresaId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementEstatusId.length == 1) {
		$('span[aria-labelledby="select2-estatusId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-estatusId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

});
