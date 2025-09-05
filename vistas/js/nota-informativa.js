$(function(){

    let tableList = document.getElementById('tablaNotasInformativas');

    // LLamar a la funcion fAjaxDataTable() para llenar el Listado  
    if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/NotaInformativaAjax.php', '#tablaNotasInformativas');

    // Confirmar la eliminación del Usuario
    // $("table tbody").on("click", "button.eliminar", function (e) {
    $(tableList).on("click", "button.eliminar", function (e) {

        e.preventDefault();
        var folio = $(this).attr("folio");
        var form = $(this).parents('form');

        Swal.fire({
        title: '¿Estás Seguro de querer eliminar esta Nota informativa (requisicion: '+folio+') ?',
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

    $('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});

    let elementInsumoTipoId = $('#insumoTipoId.select2.is-invalid');
	let elementUnidadId = $('#unidadId.select2.is-invalid');
	if ( elementInsumoTipoId.length == 1 ) {
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementUnidadId.length == 1) {
		$('span[aria-labelledby="select2-unidadId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}


})