$(function(){

	let tableList = document.getElementById('tablaInventarios');
	let tableInventarioDetalles = document.getElementById('tablaInventarioDetalles');
	let dataTableDetalles = null;
	let dataTableSalidas = null;
	let dataTableSalidasDetalles = null;
	let dataTableSalidasDetallesResguardo = null
	let parametrosTableList = { responsive: true };

	let elementModalBuscarInsumo = document.querySelector('#modalBuscarInsumo');
	let elementModalBuscarIndirecto = document.querySelector('#modalBuscarIndirecto');

	let dataTableSeleccionarInsumos = $('#tablaSeleccionarInsumos').DataTable();
	let dataTableSeleccionarIndirectos = $('#tablaSeleccionarIndirectos').DataTable();


	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) {
		fetch( rutaAjax+'app/Ajax/InventarioAjax.php', {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			
			// TABLA ENTREDAS
			$('#tablaInventarios').DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,
				buttons: [
					{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
				layout: {
					topStart: 'buttons'
				},
				language: LENGUAJE_DT,
				aaSorting: [],

			})

			// TABLA SALIDAS
			$('#tablaSalidasDeEntrada').DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registrosSalidas,
				columns: data.datos.columnasSalidas,
				buttons: [
					{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
				layout: {
					topStart: 'buttons'
				},
				language: LENGUAJE_DT,
				aaSorting: [],

			})

			// TABLA INVENTARIO PARTIDAS
			$('#tablaInventarioGeneral').DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registroAlmacen,
				columns: data.datos.columnasInventario,
				buttons: [
					{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
				

				layout: {
					topStart: 'buttons'
				},
				language: LENGUAJE_DT,
				aaSorting: [],

			})


			
		}); // .then( data => {
		// fAjaxDataTable(rutaAjax+'app/Ajax/InventarioAjax.php', '#tablaInventarios');
	}

	if ( tableInventarioDetalles != null &&  $('#inventarioId').val() == 0 ) {
		let requisicionId = $('#requisicionId').val();
		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?requisicionId='+requisicionId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			if (data.datos.registros.length == 0) {
				dataTableDetalles = $('#tablaInventarioDetalles').DataTable({
					info: false,
					paging: false,
					pageLength: 100,
					searching: false,
					autoWidth: false,
					responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
					data: data.datos.registros,
					columns: data.datos.columnas,
					language: LENGUAJE_DT,
					aaSorting: [],
				})
			}else{
				dataTableDetalles = $('#tablaInventarioDetalles').DataTable({
					info: false,
					paging: false,
					pageLength: 100,
					searching: false,
					autoWidth: false,
					responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
					data: data.datos.registros,
					columns: data.datos.columnas,
					columnDefs: [
						{
							targets: 2, // Columna donde quieres agregar el input
							render: function (td, cellData, rowData, row, col) {

								let cantidad = rowData.cantidad
								
								return '<input class="form-control form-control-sm cantidad" min="1" max="'+rowData.cantidad_disponible+'" type="number" value="' + cantidad + '">';
							}
						},
						{
							targets: 4, // Columna donde quieres agregar el input
							render: function (td, cellData, rowData, row, col) {
								return '<input class="form-control form-control-sm text-uppercase numeroParte" type="text" value="'+ rowData.numeroParte +'">';
							}
						},
						{
							render: DataTable.render.select(),
							targets: 0
						},
						{ targets: [0,1,2,3,4,5], orderable: false }
					],
					select: {
						style: 'multi',
						selector: 'td:first-child',
						selectable: function (rowData) {
							return rowData.cantidad_disponible !== 0;
						}
					},
					language: LENGUAJE_DT,
					aaSorting: [],
				})
			}
		}); // .then( data => {

	} else if( tableInventarioDetalles != null ) {
		let inventarioId = $('#inventarioId').val();

		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?inventarioId='+inventarioId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			dataTableDetalles = $('#tablaInventarioDetalles').DataTable({
				info: false,
				paging: false,
				pageLength: 100,
				searching: false,
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,
				columnDefs: [
					{ targets: [0,1,2,3,4,5], orderable: false },
				],
				language: LENGUAJE_DT,
				aaSorting: []
			})

			data.salidas.columnas.unshift({ 
				"className": 'details-control',
				"orderable": false,
				"data": null,
				"defaultContent": '',
				"render": function () {
					return '<i class="fas fa-caret-right"></i>';
				},
			  width:"15px"
			});

			dataTableSalidas = $('#tablaSalidas').DataTable({
				info: false,
				paging: true,
				pageLength: 10,
				searching: false,
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.salidas.registros,
				columns: data.salidas.columnas,
				language: LENGUAJE_DT,
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

	$('#collapseFiltros').on('show.bs.collapse', function (event) {
		let btnVerFiltros = document.getElementById('btnVerFiltros');
		btnVerFiltros.querySelector('i').classList.remove("fa-eye");
		btnVerFiltros.querySelector('i').classList.add("fa-eye-slash");
	})
	
	// $('#collapseFiltros').on('hidden.bs.collapse', function (event) {
	$('#collapseFiltros').on('hide.bs.collapse', function (event) {
		let btnVerFiltros = document.getElementById('btnVerFiltros');
		btnVerFiltros.querySelector('i').classList.remove("fa-eye-slash");
		btnVerFiltros.querySelector('i').classList.add("fa-eye");
	})

	$('#btnFiltrar').on('click', function (e) {
		$(tableList).DataTable().destroy();
		tableList.querySelector('tbody').innerHTML = '';

		let almacenId = $('#filtroAlmacen').val();
		let descripcion = $('#filtroDescripcion').val();

		fActualizarListado(`${rutaAjax}app/Ajax/InventarioAjax.php?almacenId=${almacenId}&descripcion=${descripcion}`, '#tablaInventarios', parametrosTableList);
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

	$('.select2Modal').select2({
		tags: true,
		dropdownParent: $('#modalCrearSalida')
		// ,theme: 'bootstrap4'
	}); // $('.select2').select2({

    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    }); // $('.input-group.date').datetimepicker({

	$('#fechaEntregaDTP').datetimepicker();

	// *************************************************
	// Creacion de Entrada
	// *************************************************

	// Modifica la cantidad
	$(tableInventarioDetalles).on('change', '.cantidad', function() {

		var rowIndex = $(this).closest('tr').index();
		let newValue = $(this).val()
		
		if (isNaN(newValue) || newValue == "") newValue = 1

		dataTableDetalles.cell(rowIndex, 2).data(newValue).draw();
	});

	// Modifica el numero de parte
	$(tableInventarioDetalles).on('change', '.numeroParte', function() {

		var rowIndex = $(this).closest('tr').index();
		let newValue = $(this).val()
		
		if (newValue == '') newValue = 'NA';

		dataTableDetalles.cell(rowIndex, 4).data(newValue).draw();

	});

	// *************************************************
	// Salidas
	// *************************************************

	let salidaId=0;
	$('#modalCrearSalida').on('shown.bs.modal', function () {

		let inventarioId = $('#inventarioId').val();
		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?inventarioId='+inventarioId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			data.datos.columnas.pop(); 	
			dataTableSalidasDetalles = $('#tablaSalidasDetalles').DataTable({
				info: false,
				paging: false,
				pageLength: 100,
				searching: false,
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.data,
				columns: data.datos.columnas,
				columnDefs: [
					{
						render: DataTable.render.select(),
						targets: 0
					},
					{
						targets: 1, // Columna donde quieres agregar el input
						render: function (td, cellData, rowData, row, col) {
							let cantidad = rowData.cantidad < rowData.cantidadDisponible ? rowData.cantidad : rowData.cantidadDisponible;
							return '<input class="form-control form-control-sm cantidad" min="1" max="'+rowData.cantidadDisponible+'" type="number" value="' + cantidad + '">';
						}
					},
					{ targets: [0,1,2,3,4,5], orderable: false },
					// { targets: [0], className: 'text-right' }
				],
				language: LENGUAJE_DT,
				aaSorting: [],
				select: {
					style: 'multi',
					selector: 'td:first-child'
				},
				
			})

		}); // .then( data => {

		$('.select2Add').select2({
			tags:true,
			dropdownParent: $('#modalCrearSalida')
		});
		$('.select2Add').trigger('focus');
	});

	$('#modalCrearSalida').on('hidden.bs.modal', function () {
		$('#tablaSalidasDetalles').DataTable().destroy();
	});

	$('#modalCrearSalida .btnGuardarSalida').on('click',function(){
		let token = $('#token').val();
		let inventario = $('#inventarioId').val();

		let data = dataTableSalidasDetalles.rows({ selected: true }).data().toArray();

		if (data.length === 0) {
			crearToast("bg-danger","error",'',"Se debe seleccionar al menos un registro")
			return;
		}

		let dataSend = new FormData()
		dataSend.append("accion","crearSalida");
		dataSend.append("_token",token);
		dataSend.append("inventario",inventario);
		dataSend.append("detalles",JSON.stringify(data));
		
		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
		})
		.done(function(respuesta) {

			if ( respuesta.error ) {
				crearToast('bg-danger', 'Crear Salida', 'Error', respuesta.errorMessage);
				return;
			}
			// Suponiendo que generaResguardo es un array de objetos con "partida" y "descripcion"
			let listado = respuesta.generaResguardo.map(item => `<li><strong>${item.partida}:</strong> ${item.descripcion}</li>`).join('');

			Swal.fire({
				title: "Listado de inventario que genera resguardo",
				html: `<ul style="text-align: left;">${listado}</ul>`,
				icon: "info",
				confirmButtonText: "Aceptar"
			}).then((result) => {
				if (result.isConfirmed) {
					crearToast('bg-success', 'Crear Salida', 'OK', respuesta.respuestaMessage);
					window.location.reload();

				}
			})
		})
	});

	// *************************************************
	// RESGUARDOS
	// *************************************************

	$('#tablaSalidas').on("click", "button.resguardo", function (e) {
		e.preventDefault();
		var salidaId = $(this).attr('salidaId'); // Encuentra el formulario más cercano

		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?inventarioSalidaId='+salidaId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			dataTableSalidasDetallesResguardo = $('#tablaSalidasResguardo').DataTable({
				info: false,
				paging: false,
				pageLength: 100,
				searching: false,
				select: true, // Habilita la selección de filas
				autoWidth: false,
				responsive: (parametrosTableList.responsive === undefined) ? true : parametrosTableList.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,
				columnDefs: [
					{
						targets: 0, // Columna donde agregamos el checkbox
						orderable: false,
						className: 'dt-body-center',
						render: function (data, type, row) {

						if (row.cantidadDisponible === 0) {
							return '<i class="fa fa-check"></i>';
						} else {
							return '<input type="checkbox" class="row-checkbox">';
						}
						}
					},
					{
						targets: 3, // Columna donde quieres agregar el input
						render: function (td, cellData, rowData, row, col) {
							let cantidadDisponible = rowData.cantidadDisponible;

							return '<input class="form-control form-control-sm cantidad" ' + 
							   (rowData.cantidadDisponible === 0 ? 'disabled' : '') + ' min="1" max="'+rowData.cantidadDisponible+'" type="number" value="' + cantidadDisponible + '">';
						}
					},
				],
				select: {
					style: 'multi',
					selector: 'td:first-child',
					selectable: function (rowData) {
						return rowData.cantidadDisponible !== 0;
					}
				},
				language: LENGUAJE_DT,
				aaSorting: [],
			});
		});

		$("#salidaId").val(salidaId); // Asignarlo al input
		// Abre el modal
		$('#modalCrearResguardo').modal('show');
	});

	$('#modalCrearResguardo').on('hidden.bs.modal', function () {
		$("#salidaId").val(""); // Asignarlo al input
		$('#tablaSalidasResguardo').DataTable().destroy();
	});

	// CREAR RESGUARDO
	$('#modalCrearResguardo .btnGuardarResguardo').on('click',function(){

		let recibe = $('#recibeResguardo').val();
		let token = $('#token').val();
		let observaciones = $('#observacionesResguardo').val();
		let fecha = $('#fecha').val();

		var salidaId = $('#salidaId').val();// Encuentra el formulario más cercano


		if(signaturePadResguardo.isEmpty()){
			crearToast("bg-danger","error",'',"Se debe ingresar la firma")
			return
		}
		let data = [];
	
		$('#tablaSalidasResguardo tbody tr').each(function() {
			let row = $(this);

			if (row.find('.row-checkbox').prop('checked')) { 

				let rowData = dataTableSalidasDetallesResguardo.row(row.index()).data();

				let cantidad = row.find('.cantidad').val() || rowData.cantidad;
				rowData.cantidad = cantidad;
			
				data.push(rowData);

			}
		});

		if (data.length === 0) {
			crearToast("bg-danger","error",'',"Se debe seleccionar al menos un registro")
			return;
		}

		if (recibe == '' || recibe == 0) {
			crearToast("bg-danger","error",'',"Se debe ingresar el nombre de la persona que recibe")
			return;
		}

		let dataSend = new FormData()
		dataSend.append("accion","crearSalidaResguardo");
		dataSend.append("_token",token);
		dataSend.append("observaciones",observaciones);
		dataSend.append("fechaAsignacion",fecha);
		dataSend.append("firma",signaturePadResguardo.toDataURL());
		dataSend.append("usuarioRecibio",recibe);
		dataSend.append("inventario",salidaId);
		dataSend.append("detalles",JSON.stringify(data));

		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {
				crearToast('bg-danger', 'Crear Resguardo', 'Error', respuesta.errorMessage);
				return;
			}

			crearToast('bg-success', 'Crear Resguardo', 'OK', respuesta.respuestaMessage);
			window.location.href= respuesta.ruta;
		})
		

	});

	// *************************************************
	// TERMINA RESGUARDOS
	// *************************************************

	// Modifica la cantidad
	$('#tablaSalidasDetalles').on('change', '.cantidad', function() {

		var rowIndex = $(this).closest('tr').index();
		let newValue = $(this).val()
		
		if (isNaN(newValue) || newValue == "") newValue = 1

		dataTableSalidasDetalles.cell(rowIndex, 1).data(newValue).draw();
	});

	$('#tablaSalidas').on("click", "button.btn-firmar-salida",function(){
		salidaId = $(this).attr("folio");
	});

	// Firmar Salida
	$('#btnFirmarSalida').on("click",function(){
		let recibe = $('#usuarioIdRecibe').val();
		let token = $('#token').val();

		if (recibe == '') {
			crearToast("bg-danger","error",'',"Se debe seleccionar un usuario")
			return;
		}

		if(signaturePad2.isEmpty()){
			crearToast("bg-danger","error",'',"Se debe ingresar la firma")
			return
		}

		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioSalidaAjax.php',
			method: 'POST',
			data: {
				accion: 'firmarSalida',
				_token: token,
				salida: salidaId,
				usuarioIdRecibe: recibe,
				firma: signaturePad2.toDataURL()
			},
			dataType: 'json'
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {
				crearToast('bg-danger', 'Firmar Salida', 'Error', respuesta.errorMessage);
				return;
			}

			crearToast('bg-success', 'Firmar Salida', 'OK', respuesta.respuestaMessage);

			location.reload();
			window.location.href= respuesta.ruta;


		})
	});

	// *************************************************
	// Buscar Material
	// *************************************************

	$('button#btnBuscarInsumo').on('click', function (e) {

		let tableList = document.getElementById('tablaSeleccionarInsumos');
		$(tableList).DataTable().destroy();
		tableList.querySelector('tbody').innerHTML = '';
	
		// if ( insumoTipoId == '' ) return;
	
		fetch( `${rutaAjax}app/Ajax/InsumoAjax.php`, {
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
	
				$(elementErrorValidacion).removeClass("d-none");
	
				return;
			}
	
			dataTableSeleccionarInsumos = $(tableList).DataTable({
	
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				// info: false,
				// paging: false,
				// searching: false,
				data: data.datos.registros,
				columns: data.datos.columnas,
	
				columnDefs: [
					{ targets: [5], visible: false, searchable: false },
					// { targets: [1], className: 'col-fixed-left' },
					// { targets: arrayColumnsTextRight, className: 'text-right' },
					// { targets: arrayColumnsTextCenter, className: 'text-center' },
					// { targets: arrayColumnsOrderable, orderable: false }
				],
	
				createdRow: (row, data, index) => {
					row.classList.add('seleccionable');
				},
	
				language: LENGUAJE_DT,
				aaSorting: [],
	
			}); // $(tableListResumen).DataTable({
	
			$(elementModalBuscarInsumo).modal('show');
	
		}); // .then( data => {
	
	});
	
	dataTableSeleccionarInsumos.on('click', 'tbody tr.seleccionable', function () {
		let data = dataTableSeleccionarInsumos.row(this).data();
		
		const descripcion = document.getElementById('descripcion')
		const unidad = document.getElementById('unidad')
		const directo = document.getElementById('directo')
		const indirecto = document.getElementById('indirecto')
		
		descripcion.value = data.descripcion
		unidad.value = data.unidad
		directo.value = data.id
		indirecto.value = null
	
		// $('#modalAgregarIndirecto_indirectoTipoId').trigger('change')
		// $('#modalAgregarIndirecto_unidadId').trigger('change')
		$(elementModalBuscarInsumo).modal('hide');
	});

	$('button#btnBuscarIndirecto').on('click', function (e) {
		let tableList = document.getElementById('tablaSeleccionarIndirectos');
		$(tableList).DataTable().destroy();
		tableList.querySelector('tbody').innerHTML = '';
		// if ( insumoTipoId == '' ) return;
	
		fetch( `${rutaAjax}app/Ajax/IndirectoAjax.php`, {
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

	
				return;
			}
	
			dataTableSeleccionarIndirectos = $(tableList).DataTable({
	
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				// info: false,
				// paging: false,
				// searching: false,
				data: data.datos.registros,
				columns: data.datos.columnas,
	
				columnDefs: [
					{ targets: [5], visible: false, searchable: false },
					// { targets: [1], className: 'col-fixed-left' },
					// { targets: arrayColumnsTextRight, className: 'text-right' },
					// { targets: arrayColumnsTextCenter, className: 'text-center' },
					// { targets: arrayColumnsOrderable, orderable: false }
				],
	
				createdRow: (row, data, index) => {
					row.classList.add('seleccionable');
				},
	
				language: LENGUAJE_DT,
				aaSorting: [],
	
			}); // $(tableListResumen).DataTable({
	
			$(elementModalBuscarIndirecto).modal('show');
	
		}); // .then( data => {
	
	});
	
	dataTableSeleccionarIndirectos.on('click', 'tbody tr.seleccionable', function () {
		let data = dataTableSeleccionarIndirectos.row(this).data();
		
		const descripcion = document.getElementById('descripcion')
		const unidad = document.getElementById('unidad')
		const directo = document.getElementById('directo')
		const indirecto = document.getElementById('indirecto')
		
		descripcion.value = data.descripcion
		unidad.value = data.unidad
		directo.value = null
		indirecto.value = data.id
	
		$(elementModalBuscarIndirecto).modal('hide');
	});

	$('#btnAgregarPartida').on('click',function(){
		let descripcion = $('#descripcion').val();
		let cantidad = $('#cantidad').val();
		let unidad = $('#unidad').val();
		let directo = $('#directo').val();
		let indirecto = $('#indirecto').val();
		let numeroParte = $('#numeroParte').val();

		if(descripcion == '' || cantidad == '' || unidad == ''){
			crearToast("bg-danger","error",'',"Se deben llenar todos los campos")
			return
		}

		let cantidadDatos = dataTableDetalles.data().toArray().length;

		let data = {
			"id":'<input type="checkbox" disabled checked >  <button type="button" class="btn btn-danger btn-sm eliminarPartida"><i class="fas fa-trash-alt"></i></button>',
			"consecutivo": cantidadDatos+1,
			"cantidad_disponible": cantidad,
			"descripcion": descripcion,
			"cantidad": cantidad,
			"unidad": unidad,
			"directo": directo,
			"indirecto": indirecto,
			"paritda": null,
			"numeroParte": numeroParte
		}
		
		dataTableDetalles.row.add(data).draw();
		$('#descripcion').val('');
		$('#cantidad').val('');
		$('#unidad').val('');
		$('#directo').val('');
		$('#indirecto').val('');
		$('#numeroParte').val('NA');
	});

	$('#tablaInventarioDetalles').on('click', 'button.eliminarPartida', function () {
		let rowIndex = dataTableDetalles.row($(this).closest('tr')).index();

		let data = dataTableDetalles.data().toArray();
		data.splice(rowIndex, 1);
		data.forEach((element, index) => {
			element.consecutivo = index + 1;
		});

		dataTableDetalles.clear().rows.add(data).draw();
	});

	// *************************************************
	// Crear Entrada
	// *************************************************

	$('#btnGuardar').on('click',function(){
		let elementErrorValidacion = document.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");
		
		if(signaturePadCrearEntrada.isEmpty()){
			crearToast("bg-danger","error",'',"Se debe ingresar la firma")
			return
		}

		let data = dataTableDetalles.data().toArray();

		const tabla = document.getElementById('tablaInventarioDetalles');
		
		const filas = tabla.getElementsByTagName('tr');
		const arrayChequeados = [];
		for (let i = 1; i < filas.length; i++) {
			const celdas = filas[i].getElementsByTagName('td');
		  	const checkbox = celdas[0].querySelector('input[type="checkbox"]'); // Asume que el checkbox está en la primera celda
			if ( checkbox !== null && checkbox.checked) {
				// Ajusta los índices según la posición de los datos en las celdas
				arrayChequeados.push(i-1);
			}
		}

		// filtra los datos según los índices de las filas chequeadas
		const dataChecked = data.filter((_, index) => arrayChequeados.includes(index));

		if (dataChecked.length === 0) {
			crearToast("bg-danger","error",'',"Se debe seleccionar al menos un registro")
			return;
		}

		// obtiene la imagen de la firma
		let dataURL = signaturePadCrearEntrada.toDataURL();
		let elementFirma = document.getElementById("firma");

		elementFirma.value = dataURL;

		let dataSend = new FormData(formulario)

		let requisicionId = $('#requisicionId').val();
		dataSend.append("requisicionId",requisicionId);

		dataSend.append("accion","guardar");
		dataSend.append("detalles",JSON.stringify(dataChecked));

		console.log(dataChecked)

		
		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
		})
		.done(function(respuesta) {

			if ( respuesta.error ) {

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
	
				} else {
	
					let elementList = document.createElement('li'); // prepare a new li DOM element
					let newContent = document.createTextNode(respuesta.errorMessage);
					elementList.appendChild(newContent); //añade texto al div creado.
					elementErrorValidacion.querySelector('ul').appendChild(elementList);
	
				}
	
				$(elementErrorValidacion).removeClass("d-none");
	
				$(btnGuardar).prop('disabled', false);
	
				return;
	
			}

			crearToast('bg-success', 'Crear Inventario', 'OK', respuesta.respuestaMessage);

			window.location.href= respuesta.ruta;
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
	});

	// *************************************************
	// Imagenes
	// *************************************************
	let inventario_detalle=null;
	$(document).on('click',".btn-subirArchivo",function(){
		inventario_detalle = this.getAttribute('id')
		document.getElementById('archivoSubir').click();
	})

	$("#archivoSubir").change(function() {
		let archivos = this.files;
		let token = document.getElementById('token');
		if ( archivos.length == 0) return;

		let error = false;

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];
			
			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "image/jpeg" && archivo["type"] != "image/png" && archivo["type"] != "image/jpg" ) {

				error = true;

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
					title: 'Error en el tipo de archivo',
					text: '¡El archivo "'+archivo["name"]+'" debe ser jpeg, png o jpg!',
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
		formData.append("inventario_detalle",inventario_detalle)
		formData.append("_token",token.value)
		// Enviamos la solicitud AJAX
		fetch(rutaAjax+"app/Ajax/InventarioAjax.php", {
			method: 'POST',
			body: formData
		})
		.then(response => response.text())
		.then(data => {
			crearToast('bg-success', 'Insertar Documentos', 'OK', data.respuestaMessage);
		})
		.catch(error => {
		console.error('Error:', error);
		});

   	}) // $("#archivos").change(function(){

	let modalVerImagenes = document.getElementById('modalVerImagenes');

	$(document).on('click', '.verImagenes',function(e){
		let partida = this.getAttribute('partida');
		$("#modalVerImagenesLabel span").html(partida);
		$("#modalVerImagenes div.imagenes").html('');

		let token = $('input[name="_token"]').val();
		let detalleId = $(this).attr("partida");

		let datos = new FormData();
		datos.append("accion", "verImagenes");
		datos.append("_token", token);
		datos.append("partida", detalleId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/InventarioAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

				if ( respuesta.error ) {
					let elementErrorValidacion = modalVerImagenes.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				respuesta.imagenes.forEach( (imagen, index) => {
					let elementImagen = `
						<div class="col mb-4">
							<div class="card">
								<img src="${imagen.ruta.slice(5)}" class="card-img-top" alt="${imagen.titulo}">
							</div>
						</div>`;

					$("#modalVerImagenes div.imagenes").append(elementImagen);
				});

		    }

		})

	})


	// *************************************************
	// Canvas para firmar
	// *************************************************

	const canvasFirma = document.querySelector("#canvasFirma");
	const canvasFirmaResguardo = document.querySelector("#canvasFirmaResguardo");
	const canvasFirmaCrearEntrada = document.querySelector("#canvasFirmaCrearEntrada");
	
	let signaturePad2, signaturePadResguardo, signaturePadCrearEntrada;
	
	if (canvasFirma) {
		signaturePad2 = new SignaturePad(canvasFirma);
	}
	if (canvasFirmaResguardo) {
		signaturePadResguardo = new SignaturePad(canvasFirmaResguardo);
	}
	
	if (canvasFirmaCrearEntrada) {
		signaturePadCrearEntrada = new SignaturePad(canvasFirmaCrearEntrada);
	}
	
	// Solo agregar eventos si existen los elementos y sus SignaturePads
	$('#btnLimpiarFirmaCrearEntrada').on('click', function () {
		if (signaturePadCrearEntrada) signaturePadCrearEntrada.clear();
	});
	
	$('#btnLimpiarFirmaSalida').on('click', function () {
		if (signaturePad2) signaturePad2.clear();
	});
	
	$('#btnLimpiarResguardo').on('click', function () {
		if (signaturePadResguardo) signaturePadResguardo.clear();
	});
	
});
