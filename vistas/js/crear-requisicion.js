$(function(){
    let dataTableSeleccionarInsumos = $("#tablaSeleccionarInsumos").DataTable();
    let dataTableSeleccionarIndirectos = $("#tablaSeleccionarIndirectos").DataTable();

    let parametrosTableList = { responsive: false };

    let elementModalBuscarInsumo = document.querySelector("#modalBuscarInsumo");
    let elementModalBuscarIndirecto = document.querySelector("#modalBuscarIndirecto");
    let elementModalAgregarPartida = document.querySelector("#modalAgregarPartida");

    let tableRequiDetalles = document.getElementById("tablaRequiDetalles");

    let tablaProveedores = null;

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

    $(".select2").select2({
        language: "es",
        tags: false,
        width: "100%",
        // theme: 'bootstrap4'
    });

    $("#fechaRequerida").datetimepicker({
        format: "DD/MMMM/YYYY",
        widgetPositioning: {
            horizontal: "auto",
            vertical: "bottom",
        },
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
    $("#btnSubirCotizaciones").click(function () {
        document.getElementById("cotizacionArchivos").click();
    });

    /*================================================
    Validar tipo y tamaño de los archivos Cotizaciones
    ================================================*/
    $("#cotizacionArchivos").change(function () {
        // $("div.subir-cotizaciones span.lista-archivos").html('');
        let archivos = this.files;
        if (archivos.length == 0) return;

        let error = false;

        for (let i = 0; i < archivos.length; i++) {
        let archivo = archivos[i];

        /*==========================================
                VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
                ==========================================*/

        if (archivo["type"] != "application/pdf") {
            error = true;

            // $("#cotizacionArchivos").val("");
            // $("div.subir-cotizaciones span.lista-archivos").html('');

            Swal.fire({
            title: "Error en el tipo de archivo",
            text: '¡El archivo "' + archivo["name"] + '" debe ser PDF!',
            icon: "error",
            confirmButtonText: "¡Cerrar!",
            });

            return false;
        } else if (archivo["size"] > 4000000) {
            error = true;

            // $("#cotizacionArchivos").val("");
            // $("div.subir-cotizaciones span.lista-archivos").html('');

            Swal.fire({
            title: "Error en el tamaño del archivo",
            text:
                '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
            icon: "error",
            confirmButtonText: "¡Cerrar!",
            });

            return false;
        }
        // else {

        // $("div.subir-cotizaciones span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

        // }
        }

        if (error) {
        $("#cotizacionArchivos").val("");

        return;
        }

        for (let i = 0; i < archivos.length; i++) {
        let archivo = archivos[i];

        $("div.subir-cotizaciones span.lista-archivos").append(
            '<p class="font-italic text-info mb-0 text-right">' +
            archivo["name"] +
            "</p>"
        );
        }

        let cloneElementArchivos = this.cloneNode(true);
        cloneElementArchivos.removeAttribute("id");
        cloneElementArchivos.name = "cotizacionArchivos[]";
        $("div.subir-cotizaciones").append(cloneElementArchivos);
    }); // $("#cotizacionArchivos").change(function(){
    

    // Buscar insumo
    $("button#btnBuscarInsumo").on("click", function (e) {
        
        let tableList = document.getElementById("tablaSeleccionarInsumos");
        $(tableList).DataTable().destroy();
        tableList.querySelector("tbody").innerHTML = "";

        let obraId = document.getElementById("obraId");

        $.ajax({
            url: rutaAjax + 'app/Ajax/InsumoAjax.php',
            method: 'POST',
            dataType: 'json',
            data: {
            accion: 'buscarInsumos',
            obraId: obraId.value
            },
            success: function(data) {
            if (data.error) {
                swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.errorMessage
                });
                return;
            }

            dataTableSeleccionarInsumos = $(tableList).DataTable({
                autoWidth: false,
                responsive: parametrosTableList.responsive === undefined ? true : parametrosTableList.responsive,
                data: data.datos.registros,
                columns: data.datos.columnas,
                columnDefs: [
                // { targets: [0], visible: false, searchable: false },
                // { targets: [1], className: 'col-fixed-left' },
                // { targets: arrayColumnsTextRight, className: 'text-right' },
                // { targets: arrayColumnsTextCenter, className: 'text-center' },
                // { targets: arrayColumnsOrderable, orderable: false }
                ],
                createdRow: (row, data, index) => {
                row.classList.add("seleccionable");
                },
                language: LENGUAJE_DT,
                aaSorting: [],
            });

            $(elementModalBuscarInsumo).modal("show");
            },
            error: function(xhr, status, error) {
            console.log("Error:", error);
            }
        });
    });

    dataTableSeleccionarInsumos.on("click", "tbody tr.seleccionable", function () {
        let data = dataTableSeleccionarInsumos.row(this).data();
        
        const insumoId = document.getElementById("modalAgregarPartida_insumoId");
        const descripcion = document.getElementById("modalAgregarPartida_descripcion");
        const unidad = document.getElementById("modalAgregarPartida_unidad");
        const unidadId = document.getElementById("modalAgregarPartida_unidadId");
        const obraDetalleId = document.getElementById("modalAgregarPartida_obraDetalleId");
        
        insumoId.value = data.id;
        descripcion.value = data.descripcion;
        unidad.value = data.unidad;
        unidadId.value = data.unidadId;
        obraDetalleId.value = data.id;
        
        $("#modalAgregarPartida_unidad").trigger("change");
        $(elementModalBuscarInsumo).modal("hide");
        $(elementModalAgregarPartida).modal("show");
    });

    $("button#btnBuscarIndirecto").on("click", function (e) {
        let tableList = document.getElementById("tablaSeleccionarIndirectos");
        $(tableList).DataTable().destroy();
        tableList.querySelector("tbody").innerHTML = "";

        let obraId = document.getElementById("obraId");

        $.ajax({
            url: rutaAjax + 'app/Ajax/IndirectoAjax.php',
            method: 'POST',
            dataType: 'json',
            data: {
            accion: 'buscarIndirectos',
            obraId: obraId.value
            },
            success: function(data) {
            if (data.error) {
                swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.errorMessage
                });
                return;
            }
            console.log(data.datos.columnas);

            dataTableSeleccionarIndirectos = $(tableList).DataTable({
                autoWidth: false,
                responsive: parametrosTableList.responsive === undefined ? true : parametrosTableList.responsive,
                data: data.datos.registros,
                columns: [
                    { data: "consecutivo" },
                    { data: "descripcion" },
                    { data: "unidad" }
                ],
                columnDefs: [
                // { targets: [0], visible: false, searchable: false },
                // { targets: [1], className: 'col-fixed-left' },
                // { targets: arrayColumnsTextRight, className: 'text-right' },
                // { targets: arrayColumnsTextCenter, className: 'text-center' },
                // { targets: arrayColumnsOrderable, orderable: false }
                ],
                createdRow: (row, data, index) => {
                row.classList.add("seleccionable");
                },
                language: LENGUAJE_DT,
                aaSorting: [],
            });

            $(elementModalBuscarIndirecto).modal("show");
            },
            error: function(xhr, status, error) {
            console.log("Error:", error);
            }
        });
    });

    dataTableSeleccionarIndirectos.on("click", "tbody tr.seleccionable", function () {
        let data = dataTableSeleccionarIndirectos.row(this).data();
        const descripcion = document.getElementById("modalAgregarPartida_descripcion");
        const unidad = document.getElementById("modalAgregarPartida_unidad");
        const unidadId = document.getElementById("modalAgregarPartida_unidadId");
        const obraDetalleId = document.getElementById("modalAgregarPartida_obraDetalleId");

        descripcion.value = data.descripcion;
        unidad.value = data.unidad;
        unidadId.value = data.unidadId;
        obraDetalleId.value = data.id;
        
        $("#modalAgregarPartida_unidad").trigger("change");

        $(elementModalBuscarIndirecto).modal("hide");
        $(elementModalAgregarPartida).modal("show");
    }
    );

    // Abrir el input al presionar el botón Subir Fotos
    $("#btnSubirFotos").click(function () {
        // document.getElementById('fotos').click();
        document.getElementById("modalAgregarPartida_fotos").click();
    });

    // Validar tipo y tamaño de los archivos de fotos
    $("#modalAgregarPartida_fotos").change(function () {
    $("div.subir-fotos span.previsualizar").html("");
    let archivos = this.files;

    for (let i = 0; i < archivos.length; i++) {
        let archivo = archivos[i];

        /*================================================
            VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA JPG O PNG
            ================================================*/

        if (archivo["type"] != "image/jpeg" && archivo["type"] != "image/png") {
        // $("#fotos").val("");
        $("#modalAgregarPartida_fotos").val("");
        // $("div.subir-fotos span.lista-fotos").html('');
        $("div.subir-fotos span.previsualizar").html("");

        Swal.fire({
            title: "Error en el tipo de archivo",
            text: '¡El archivo "' + archivo["name"] + '" debe ser JPG o PNG!',
            icon: "error",
            confirmButtonText: "¡Cerrar!",
        });
        } else if (archivo["size"] > 1000000) {
        // $("#fotos").val("");
        $("#modalAgregarPartida_fotos").val("");
        // $("div.subir-fotos span.lista-fotos").html('');
        $("div.subir-fotos span.previsualizar").html("");

        Swal.fire({
            title: "Error en el tamaño del archivo",
            text: '¡El archivo "' + archivo["name"] + '" no debe pesar más de 1MB!',
            icon: "error",
            confirmButtonText: "¡Cerrar!",
        });
        } else {
        // $("div.subir-fotos span.lista-fotos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

        let datosImagen = new FileReader();
        datosImagen.readAsDataURL(archivo);

        $(datosImagen).on("load", function (event) {
            let rutaImagen = event.target.result;

            let elementPicture = `<picture>
                                            <img src="${rutaImagen}" class="img-fluid img-thumbnail" style="width: 100%">
                                        </picture>
                                        <p class="font-italic text-info mb-0">${archivo["name"]}</p>`;
            $("div.subir-fotos span.previsualizar").append(elementPicture);
        });
        }
    }
    }); // $("#fotos").change(function() {

    //Funcion para agregar una nueva partida
    $(elementModalAgregarPartida).on(
    "click",
    "button.btnAgregarPartida",
        function (event) {
            
            let elementDescripcion = document.getElementById(
            "modalAgregarPartida_descripcion"
            );
            let elementId = document.getElementById(
            "modalAgregarPartida_obraDetalleId"
            );
            let elementCostoUnitario = document.getElementById(
            "modalAgregarPartida_costo_unitario"
            );
            
            let descripcion = elementDescripcion.value;

            let elementCantidad = document.getElementById(
            "modalAgregarPartida_cantidad"
            );
            let elementUnidad = document.getElementById("modalAgregarPartida_unidad");
            let elementUnidadId = document.getElementById(
            "modalAgregarPartida_unidadId"
            );
            let elementFoto = document.getElementById("modalAgregarPartida_fotos");
            let elementConcepto = document.getElementById(
            "modalAgregarPartida_concepto"
            );
            let elementFotos = document.getElementById("modalAgregarPartida_fotos");
            let elementCosto = document.getElementById("modalAgregarPartida_costo");

            const files = elementFoto.files;

            let cantidad = elementCantidad.value;
            let costo = elementCosto.value;
            let unidad = elementUnidad.value.trim();
            let concepto = elementConcepto.value.trim();
            let costoUnitario = elementCostoUnitario.value;

            let elementPadre = null;
            let newDiv = null;
            let newContent = null;

            elementCosto.classList.remove("is-invalid");
            elementPadre = elementCosto.parentElement;
            newDiv = elementPadre.querySelector("div.invalid-feedback");
            if (newDiv != null) elementPadre.removeChild(newDiv);

            elementCantidad.classList.remove("is-invalid");
            elementPadre = elementCantidad.parentElement;
            newDiv = elementPadre.querySelector("div.invalid-feedback");
            if (newDiv != null) elementPadre.removeChild(newDiv);

            elementUnidad.classList.remove("is-invalid");
            elementPadre = elementUnidad.parentElement;
            newDiv = elementPadre.querySelector("div.invalid-feedback");
            if (newDiv != null) elementPadre.removeChild(newDiv);

            elementConcepto.classList.remove("is-invalid");
            elementPadre = elementConcepto.parentElement;
            newDiv = elementPadre.querySelector("div.invalid-feedback");
            if (newDiv != null) elementPadre.removeChild(newDiv);

            elementCostoUnitario.classList.remove("is-invalid");
            elementPadre = elementCostoUnitario.parentElement;
            newDiv = elementPadre.querySelector("div.invalid-feedback");
            if (newDiv != null) elementPadre.removeChild(newDiv);

            let errores = false;
            if (costo == "") {
                elementCosto.classList.add("is-invalid");
                elementPadre = elementCosto.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                // newContent = document.createTextNode("La cantidad es obligatoria.");
                newContent = document.createTextNode(
                    "El valor del campo Costo no puede ser menor a 0.01."
                );
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            } else if (costo.length > 28) {
                elementCosto.classList.add("is-invalid");
                elementPadre = elementCosto.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                newContent = document.createTextNode(
                    "El campo costo debe ser máximo de 8 dígitos."
                );
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            }

            if (parseFloat(cantidad) < 0.01 || cantidad == "") {
                elementCantidad.classList.add("is-invalid");
                elementPadre = elementCantidad.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                // newContent = document.createTextNode("La cantidad es obligatoria.");
                newContent = document.createTextNode(
                    "El valor del campo Cantidad no puede ser menor a 0."
                );
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            } else if (cantidad.length > 10) {
                elementCantidad.classList.add("is-invalid");
                elementPadre = elementCantidad.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                newContent = document.createTextNode(
                    "El campo Cantidad debe ser máximo de 8 dígitos."
                );
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            }

            if (costoUnitario == "") {
                elementCostoUnitario.classList.add("is-invalid");
                elementPadre = elementCostoUnitario.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                // newContent = document.createTextNode("La cantidad es obligatoria.");
                newContent = document.createTextNode(
                    "El valor del campo Cantidad no puede ser menor a 0."
                );
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            } else if (costoUnitario.length > 14) {
                elementCostoUnitario.classList.add("is-invalid");
                elementPadre = elementCostoUnitario.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                newContent = document.createTextNode(
                    "El campo Cantidad debe ser máximo de 8 dígitos."
                );
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            }

            if (unidad == "") {
                elementUnidad.classList.add("is-invalid");
                elementPadre = elementUnidad.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                newContent = document.createTextNode("La unidad es obligatoria.");
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            }

            if (concepto == "") {
                elementConcepto.classList.add("is-invalid");
                elementPadre = elementConcepto.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                newContent = document.createTextNode("El concepto es obligatorio.");
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
                } else if (concepto.length > 500) {
                elementConcepto.classList.add("is-invalid");
                elementPadre = elementConcepto.parentElement;
                newDiv = document.createElement("div");
                newDiv.classList.add("invalid-feedback");
                newContent = document.createTextNode(
                    "El concepto debe ser máximo de 500 caracteres."
                );
                newDiv.appendChild(newContent); //añade texto al div creado.
                elementPadre.appendChild(newDiv);

                errores = true;
            }

            if (errores) return;

            // let tableRequisicionDetalles = document.querySelector('#tablaRequisicionDetalles tbody');
            let tableRequisicionDetalles = document.querySelector("#tablaRequiDetalles tbody");
            let registros = tableRequisicionDetalles.querySelectorAll("tr");

            let registrosNuevos =
            tableRequisicionDetalles.querySelectorAll("tr[nuevo]");
            let partida = registrosNuevos.length + 1;

            let elementRow = `<tr nuevo partida="${partida}">
                                <td partida class="text-right">
                                    <span>${registros.length + 1}</span>
                                    <input type="hidden" name="detalles[partida][]" value="${partida}">
                                    <button type='button' class='btn btn-xs btn-danger ml-1 eliminar'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </td>
                                <td>${descripcion}</td>
                                <td>${cantidad}<input type="hidden" name="detalles[cantidad][]" value="${cantidad}"></td>
                                <td>${costo}</td>
                                <td>${unidad}</td>
                                <td>${concepto}</td>
                                <td style="display: none;">${elementId}</td>
                                <td style="display: none;">${files}</td>
                            </tr>`;
            $(tableRequisicionDetalles).append(elementRow);

            let rowNuevoRegistro = tableRequisicionDetalles.querySelector(
            `tr[partida="${partida}"]`
            );
            let columnaConcepto = rowNuevoRegistro.querySelector("td:last-child");

            let cloneElementUnidadId = elementUnidadId.cloneNode(true);
            cloneElementUnidadId.removeAttribute("id");
            cloneElementUnidadId.type = "hidden";
            cloneElementUnidadId.name = "detalles[unidadId][]";
            $(columnaConcepto).append(cloneElementUnidadId);

            let cloneElementUnidad = elementUnidad.cloneNode(true);
            cloneElementUnidad.removeAttribute("id");
            cloneElementUnidad.type = "hidden";
            cloneElementUnidad.name = "detalles[unidad][]";
            $(columnaConcepto).append(cloneElementUnidad);

            let cloneElementConcepto = elementConcepto.cloneNode(true);
            cloneElementConcepto.removeAttribute("id");
            cloneElementConcepto.name = "detalles[concepto][]";
            cloneElementConcepto.classList.add("d-none");
            $(columnaConcepto).append(cloneElementConcepto);

            let cloneElementCosto = elementCosto.cloneNode(true);
            cloneElementCosto.removeAttribute("id");
            cloneElementCosto.name = "detalles[costo][]";
            cloneElementCosto.classList.add("d-none");
            $(columnaConcepto).append(cloneElementCosto);

            let cloneElementFotos = elementFotos.cloneNode(true);
            cloneElementFotos.removeAttribute("id");
            cloneElementFotos.name = "detalle_imagenes[" + partida + "][]";
            $(columnaConcepto).append(cloneElementFotos);
            $("#modalAgregarPartida_fotos").val("");
            $("div.subir-fotos span.previsualizar").html("");

            let cloneElementODId = elementId.cloneNode(true);
            cloneElementODId.removeAttribute("id");
            cloneElementODId.name = "detalles[obraDetalleId][]";
            cloneElementODId.classList.add("d-none");
            $(columnaConcepto).append(cloneElementODId);

            let cloneElementCostoUnitario = elementCostoUnitario.cloneNode(true);
            cloneElementCostoUnitario.removeAttribute("id");
            cloneElementCostoUnitario.name = "detalles[costo_unitario][]";
            cloneElementCostoUnitario.classList.add("d-none");
            $(columnaConcepto).append(cloneElementCostoUnitario);

            elementCostoUnitario.value = "0";
            elementCantidad.value = "1";
            elementUnidad.value = "";
            elementConcepto.value = "";
            elementCosto.value = "0";

            $(elementModalAgregarPartida).modal("hide");

            $("#btnSiguiente").prop("disabled", false);
        }
    );

    // Eliminar una partida
    $(tableRequiDetalles).on("click", "button.eliminar", function (event) {
        this.parentElement.parentElement.remove();

        // Renumerar las partidas
        let tableRequisicionDetalles =
            tableRequiDetalles.querySelector("tbody");
        let registros = tableRequisicionDetalles.querySelectorAll("tr");
        registros.forEach((registro, index) => {
            registro.setAttribute("partida", index + 1);
            registro.querySelector("td[partida] span").innerHTML = index + 1;
        });

        if (registros.length == 0) $("#btnSiguiente").prop("disabled", true);
    });

    $('#btnSiguiente').on('click', function () {
        
        if ($('#obraId').val() == "") {
            Swal.fire({
                title: 'Error',
                text: 'Debe seleccionar una obra antes de continuar.',
                icon: 'error',
                confirmButtonText: 'Cerrar'
            });
            return;
        }
        let step1 = document.getElementById("formulario-step-1");
        let step2 = document.getElementById("formulario-step-2");
        let step3 = document.getElementById("formulario-step-3");

        let step1Trigger = document.getElementById("stepper1trigger1");
        let step2Trigger = document.getElementById("stepper1trigger2");
        let step3Trigger = document.getElementById("stepper1trigger3");

        let btnAnterior = document.getElementById("btnAnterior");
        let btnSiguiente = document.getElementById("btnSiguiente");
        let btnCrearRequisicion = document.getElementById("btnCrearRequisicion");

        if (step2.classList.contains("d-none")) {
            step1.classList.add("d-none");
            step2.classList.remove("d-none");
            step3.classList.add("d-none");

            btnSiguiente.setAttribute("disabled", "true");
            btnAnterior.classList.remove("d-none");

            step2Trigger.classList.add("active");
        } else if (step3.classList.contains("d-none")) {
            step1.classList.add("d-none");
            step2.classList.add("d-none");
            step3.classList.remove("d-none");

            btnCrearRequisicion.classList.remove("d-none");
            btnSiguiente.classList.add("d-none");

            step3Trigger.classList.add("active");
        }

        
    });

    $('#btnAnterior').on('click', function () {
        
        let step1 = document.getElementById("formulario-step-1");
        let step2 = document.getElementById("formulario-step-2");
        let step3 = document.getElementById("formulario-step-3");

        let step1Trigger = document.getElementById("stepper1trigger1");
        let step2Trigger = document.getElementById("stepper1trigger2");
        let step3Trigger = document.getElementById("stepper1trigger3");

        let btnSiguiente = document.getElementById("btnSiguiente");
        let btnAnterior = document.getElementById("btnAnterior");
        let btnCrearRequisicion = document.getElementById("btnCrearRequisicion");

        if (step2.classList.contains("d-none") ) {
            step2.classList.remove("d-none");
            step3.classList.add("d-none");

            btnCrearRequisicion.classList.add("d-none");
            btnSiguiente.classList.remove("d-none");

            step1Trigger.classList.add("active");
            step2Trigger.classList.remove("active");
        } else {
            step1.classList.remove("d-none");
            step2.classList.add("d-none");
            btnSiguiente.removeAttribute("disabled");

            step2Trigger.classList.remove("active");
            step3Trigger.classList.remove("active");
        }


        // Ocultar el botón anterior si estamos en el step 1
        if (!step1.classList.contains("d-none")) {
            btnAnterior.classList.add("d-none");
        }
    });

    //==================================================
    // Porveedores
    //==================================================

    $("#modalSeleccionarProveedor").on("shown.bs.modal", function () {
        if (!$.fn.DataTable.isDataTable("#tablaProveedores")) {
        let columnas = [
            { data: "consecutivo" },
            { data: "proveedor" },
            { data: "direccion" },
            { data: "correo" },
            { data: "estrellas" },
            { data: "telefono" },
        ];
        $.ajax({
            url: `${rutaAjax}app/Ajax/ProveedorAjax.php?accion=listar`,
            method: "GET",
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (data) {
            tablaProveedores = $("#tablaProveedores").DataTable({
                autoWidth: false,
                responsive:
                parametrosTableList.responsive === undefined
                    ? true
                    : parametrosTableList.responsive,
                info: false,

                data: data.datos.registros,
                columns: data.datos.columnas,
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

    $("#tablaProveedores").on("click", "tbody tr.seleccionable", function () {
        let data = tablaProveedores.row(this).data();

        $("#proveedor").val(data["proveedor"]);
        $("#telefono").val(data["telefono"]);

        $("#proveedorId").val(data["id"]);

        $("#modalSeleccionarProveedor").modal("hide");
    });

    //==================================================
    // Crear Cotizaciones
    //==================================================
    $("#btnCrearRequisicion").on("click", function (e) {
        e.preventDefault();
        let form = document.getElementById("formSend");
        let formData = new FormData(form);

        formData.append("accion", "crear");
        formData.append("_token", document.querySelector("input[name='_token']").value);
        formData.append("folio", document.getElementById("folio").value);
        formData.append("divisa", document.getElementById("divisa").value);
        formData.append("tipoRequisicion", document.getElementById("tipoRequisicion").value);
        formData.append("fechaRequerida", document.getElementById("fechaRequerida").value);
        formData.append("direccion", document.getElementById("direccion").value);
        formData.append("especificaciones", document.getElementById("especificaciones").value);
        formData.append("categoriaId", document.getElementById("categoriaId").value);
        formData.append("justificacion", document.getElementById("justificacion").value);
        formData.append("proveedorId", document.getElementById("proveedorId").value);

        formData.append("presupuesto", document.getElementById("presupuesto").value);

        $.ajax({
            url: `${rutaAjax}app/Ajax/RequisicionAjax.php`,
            method: "POST",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (data) {
                if (data.error) {
                    let errorMessages = Object.values(data.errors).join('<br>');
                    Swal.fire({
                        title: "Error",
                        html: errorMessages,
                        icon: "error",
                        confirmButtonText: "Cerrar",
                    });
                } else {
                    Swal.fire({
                        title: "Éxito",
                        text: "Requisición creada correctamente.",
                        icon: "success",
                        confirmButtonText: "Cerrar",
                    }).then(() => {
                        let sectionRequisiciones = document.getElementById("creacionRequisicion");
                        sectionRequisiciones.classList.add("d-none");
                        let sectionTerminacion = document.getElementById("terminacionRequisicion");
                        sectionTerminacion.classList.remove("d-none");
                        
                        let requisicionId = data.respuesta.id;
                        let prefijo = data.respuesta.obras.prefijo

                        $('#requisicionLink').text(`${prefijo}-${data.respuesta.folio}`);

                        $('#requisicionLink').attr('href', `${rutaAjax}requisiciones/${requisicionId}/editar`);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                Swal.fire({
                    title: "Error",
                    text: "Ocurrió un error al crear la requisición.",
                    icon: "error",
                    confirmButtonText: "Cerrar",
                });
            }
        });
    });

    //==================================================
    // Cambio de Directo /Indirecto
    //==================================================
    $("#tipoGasto").on("change", function () {
        let tipo = $(this).val();
        if (tipo == "1") {
            $("#btnBuscarIndirecto").addClass("d-none");
            $("#btnBuscarInsumo").removeClass("d-none");
        } else {
            $("#btnBuscarInsumo").addClass("d-none");
            $("#btnBuscarIndirecto").removeClass("d-none");
        }
    });

    //==================================================
    // Obtener ultimo folio
    //==================================================
    $("#obraId").on("change", function () {
        let obraId = document.getElementById("obraId");
        fetch(`${rutaAjax}app/Ajax/RequisicionAjax.php?obraId=${obraId.value}`, {
        method: "GET", // *GET, POST, PUT, DELETE, etc.
        cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
        headers: {
        "Content-Type": "application/json",
        },
        })
        .then((response) => response.json())
        .catch((error) => console.log("Error:", error))
        .then((data) => {
            if (data.error) {
                console.error("Error al obtener el folio:", data.errorMessage);

                return;
            }
        $("#folio").val(data);
        });

        $.ajax({
        url: `${rutaAjax}app/Ajax/ObraAjax.php?accion=getPresupuestos&obraId=${obraId.value}`,
        method: "GET",
        dataType: "json",
        success: function (data) {
            if(data.error) {
            Swal.fire({
                title: "Error",
                text: data.errorMessage || "Ocurrió un error",
                icon: "error",
                confirmButtonText: "OK",
            });
            } else {
            // Llenar el select de presupuestos
            const $presupuesto = $("#presupuesto");
            $presupuesto.empty();
            $presupuesto.append(new Option("General", 0));
            $.each(data.presupuestos, function (index, presupuesto) {
                $presupuesto.append(new Option(presupuesto.descripcion, presupuesto.id));
            });
            }
        },
        error: function (error) {
            console.log("Error al obtener el presupuesto:", error);
        },
        });
    })

    $("#modalAgregarPartida_costo_unitario, #modalAgregarPartida_cantidad").on("input", function () {
    let cantidad = $("#modalAgregarPartida_cantidad").val();
    let costo_unitario = $("#modalAgregarPartida_costo_unitario").val();

    costo_unitario = costo_unitario == "" ? 0 : costo_unitario;
    cantidad = cantidad == "" ? 0 : cantidad;

    let costo = 0;

    if (cantidad !== 0) {
        costo = costo_unitario * cantidad;
    }

    $("#modalAgregarPartida_costo").val(costo.toFixed(6));
    });
});