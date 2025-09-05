$(function(){

    let tableList = document.getElementById('tablaCotizaciones');

    let tablaProveedores = document.getElementById("tablaProveedores");
    let dataTableSeleccionarProveedor = $('#tablaProveedores').DataTable();
    // Crear un botón para agregar proveedor
    let btnAgregarProveedor = document.createElement('button');
    btnAgregarProveedor.textContent = 'Agregar Proveedor';
    btnAgregarProveedor.className = 'btn btn-primary btn-sm float-right';
    btnAgregarProveedor.type = 'button';
    btnAgregarProveedor.style.marginBottom = '10px';

    // Insertar el botón antes de la tabla
    // let tablaProveedoresRequeridosContainer = document.getElementById('tablaProveedoresRequeridos').parentNode;
    // tablaProveedoresRequeridosContainer.insertBefore(btnAgregarProveedor, tablaProveedoresRequeridosContainer.firstChild);

    // Inicializar la tabla
    // let dataTableProveedoresRequeridos = $('#tablaProveedoresRequeridos').DataTable({
    //     searching: false, // Deshabilitar la búsqueda
    //     paging: false,    // Deshabilitar la paginación
    //     info: false,      // Ocultar la información de registros
    //     language: LENGUAJE_DT,
    //     aaSorting: [],
    //     columns: [
    //         { title: '#', data: 'consecutivo' },
    //         { title: 'Proveedor', data: 'proveedor' },
    //         { title: 'Fecha', data: 'fecha' },
    //         { title: 'Costo', data: 'costo' },
    //         { title: 'Acciones', data: 'acciones' }
    //     ]
    // });

    // Agregar evento al botón para abrir el modal de agregar proveedor
    btnAgregarProveedor.addEventListener('click', function () {
        $('#modalBuscarProveedor').modal('show');
    });
    // LLamar a la funcion fAjaxDataTable() para llenar el Listado  
    if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/CotizacionesAjax.php', '#tablaCotizaciones');

    // Envio del formulario para Crear o Editar registros
    function enviar(){
        btnEnviar.disabled = true;
        mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

        padre = btnEnviar.parentNode;
        padre.removeChild(btnEnviar);

      formulario.submit(); // Enviar los datos
    }

    let formulario = document.getElementById("formSend");
    let mensaje = document.getElementById("msgSend");
    let btnEnviar = document.getElementById("btnSend");
    if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

    // Abrir el modal y cargar la lista de proveedores
    let modalBuscarProveedor = document.getElementById("modalBuscarProveedor");
    if (modalBuscarProveedor != null) {
        $(modalBuscarProveedor).on("show.bs.modal", function () {
            $.ajax({
                url: rutaAjax + 'app/Ajax/ProveedorAjax.php',
                method: 'GET',
                dataType: 'json',
                success: function (response) {
                        tablaProveedores.innerHTML = ""; // Limpiar la tabla
                        
                        dataTableSeleccionarProveedor = $(tablaProveedores).DataTable({
                            destroy: true,
                            data: response.datos.registros,
                            columns: [
                                { data: 'consecutivo', title: '#' },
                                { data: 'proveedor', title: 'Proveedor' },
                                { data: 'telefono', title: 'Telefono' }
                            ],
                            createdRow: (row, data, index) => {
                                row.classList.add('seleccionable');
                            },
                            language: LENGUAJE_DT,
                        });
                },
                error: function () {
                    console.error("Error al cargar los proveedores.");
                }
            });
        });

    }

    dataTableSeleccionarProveedor.on('click', 'tbody tr.seleccionable', function () {
        let data = dataTableSeleccionarProveedor.row(this).data();

		let cantidadDatos = dataTableProveedoresRequeridos.data().toArray().length;
		
		let row = {
			"id":'<input type="checkbox" disabled checked >  <button type="button" class="btn btn-danger btn-sm eliminarPartida"><i class="fas fa-trash-alt"></i></button>',
			"consecutivo": cantidadDatos+1,
			"proveedor": data.proveedor,
            "fecha": "",
            "costo": "$ 0.00",
            "acciones": '<button type="button" class="btn btn-danger btn-sm eliminarPartida"><i class="fas fa-trash-alt"></i></button>'
		}

        dataTableProveedoresRequeridos.row.add(row).draw();

        $('#modalBuscarProveedor').modal('hide');
	});

    $('#tablaProveedoresRequeridos').on('click', 'button.eliminarPartida', function () {
        
		let rowIndex = dataTableProveedoresRequeridos.row($(this).closest('tr')).index();
        
		let data = dataTableProveedoresRequeridos.data().toArray();
		data.splice(rowIndex, 1);
		data.forEach((element, index) => {
			element.consecutivo = index + 1;
		});

		dataTableProveedoresRequeridos.clear().rows.add(data).draw();
	});


    /*======================================================
	Abrir el input al presionar el botón Cargar Cotizaciones
	======================================================*/
	$("#btnSubirCotizaciones").click(function(){
		document.getElementById('cotizacionArchivos').click();
	})

    /*================================================
    Validar tipo y tamaño de los archivos Cotizaciones
    ================================================*/
    $("#cotizacionArchivos").change(function(){

        // $("div.subir-cotizaciones span.lista-archivos").html('');
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

                // $("#cotizacionArchivos").val("");
                // $("div.subir-cotizaciones span.lista-archivos").html('');

                Swal.fire({
                    title: 'Error en el tipo de archivo',
                    text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
                    icon: 'error',
                    confirmButtonText: '¡Cerrar!'
                })

                return false;

            } else if ( archivo["size"] > 4000000 ) {

                error = true;

                // $("#cotizacionArchivos").val("");
                // $("div.subir-cotizaciones span.lista-archivos").html('');

                Swal.fire({
                    title: 'Error en el tamaño del archivo',
                    text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
                    icon: 'error',
                    confirmButtonText: '¡Cerrar!'
                })

                return false;

            }

        }

        if ( error ) {
            $("#cotizacionArchivos").val("");

            return;
        }

        for (let i = 0; i < archivos.length; i++) {

            let archivo = archivos[i];

            $("div.subir-cotizaciones span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

        }

    }) // $("#cotizacionArchivos").change(function(){
    
    $('.verArchivo').on('click', function () {
        var archivoRuta = $(this).attr('archivoRuta');
        $('#pdfViewer').attr('src', archivoRuta);
        // Mostrar el modal
        $('#pdfModal').modal('show');
    });

    // // Confirmar la eliminación de los Archivos
	// $("div.subir-cotizaciones").on("click", "i.eliminarArchivo", function (e) {

	// 	let btnEliminar = this;
	//     // let archivoId = $(this).attr("archivoId");
	//     let folio = $(this).attr("folio");

	//     Swal.fire({
	// 		title: '¿Estás Seguro de querer eliminar este Archivo (Folio: '+folio+') ?',
	// 		text: "No podrá recuperar esta información!",
	// 		icon: 'warning',
	// 		showCancelButton: true,
	// 		confirmButtonColor: '#d33',
	// 		cancelButtonColor: '#3085d6',
	// 		confirmButtonText: 'Sí, quiero eliminarlo!',
	// 		cancelButtonText:  'No!'
	//     }).then((result) => {
	// 		if (result.isConfirmed) {
	// 			eliminarArchivo(btnEliminar);
	// 		}
	//     })

	// });

    // // Envio del formulario para Cancelar el registro
	// function eliminarArchivo(btnEliminar = null){

	// 	if ( btnEliminar == null ) return;		

	// 	let archivoId = $(btnEliminar).attr("archivoId");

	// 	// $(btnEliminar).prop('disabled', true);

	// 	let token = $('input[name="_token"]').val();
	// 	let requisicionId = $('input#requisicionId').val();

	// 	let datos = new FormData();
	// 	datos.append("_token", token);
	// 	datos.append("accion", "eliminarArchivo");
	// 	datos.append("archivoId", archivoId);
	// 	datos.append("requisicionId", requisicionId);

	// 	$.ajax({
	// 	    url: rutaAjax+"app/Ajax/RequisicionAjax.php",
	// 	    method: "POST",
	// 	    data: datos,
	// 	    cache: false,
	// 	    contentType: false,
	// 	    processData: false,
	// 	    dataType: "json",
	// 	    success:function(respuesta){

	// 	    	// console.log(respuesta)
	// 	    	// Si la respuesta es positiva pudo eliminar el archivo
	// 	    	if (respuesta.respuesta) {

	// 	    		$(btnEliminar).parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

	// 	    		$(btnEliminar).parent().remove();

	// 	    	} else {

	// 	    		$(btnEliminar).parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

	// 	    		// $(btnEliminar).prop('disabled', false);

	// 		    }

	//     		setTimeout(function(){ 
	//     			$(".alert").remove();
	//     		}, 5000);

	// 	    }

	// 	})

	// }
});