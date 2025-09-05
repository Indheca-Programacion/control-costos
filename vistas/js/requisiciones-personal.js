$(function(){
    let tableList = document.getElementById('tablaRequisiciones');
	let parametrosTableList = { responsive: true };

	// Realiza la petición para actualizar el listado de requisiciones
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
			$(idTabla).DataTable({

				autoWidth: false,
				responsive: ( parametros.responsive === undefined ) ? true : parametros.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,

		        createdRow: function (row, data, index) {
		        	if ( data.colorTexto != '' ) $('td', row).eq(3).css("color", data.colorTexto);
		        	if ( data.colorFondo != '' ) $('td', row).eq(3).css("background-color", data.colorFondo);
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
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/RequisicionPersonalAjax.php', '#tablaRequisiciones', parametrosTableList);

	$('.select2').select2({
		tags: false
	});

	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	// Envio del formulario para Crear o Editar registros
	function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);

		formulario.submit();
	}

	$('#btnAuth').click(function() {
		let datos = new FormData();
		let btnAgregar = this;
		let id = $('#id_requisicion').val()
		datos.append("accion","autorizar");
		datos.append("id",id)
		$.ajax({
			url: rutaAjax+'app/Ajax/RequisicionPersonalAjax.php',
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
			if (respuesta.errorMessage) {
				crearToast('bg-danger', 'Autorizar Requisicion', 'ERROR', respuesta.errorMessage);
			}else{
				crearToast('bg-success', 'Autorizar Requisicion', 'OK', respuesta.respuestaMessage);
		$(btnAgregar).prop('hidden', true);

			}
		})
		.fail(function(error) {
			console.log(error);
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
		$(btnAgregar).prop('disabled', true);
    });

	
})