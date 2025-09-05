$(function(){

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
		tags: false
	});
	let elementInicialServicioEstatusId = $('#inicialServicioEstatusId.select2.is-invalid');
	if ( elementInicialServicioEstatusId.length == 1 ) { 
		$('.select2-selection.select2-selection--single').css('border-color', '#dc3545');
		$('.select2-selection.select2-selection--single').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('.select2-selection.select2-selection--single').css('background-repeat', 'no-repeat');
		$('.select2-selection.select2-selection--single').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('.select2-selection.select2-selection--single').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

	/*=====================================================================================
	Activar/Desactivar columna Automático en tarjeta Perfiles con Permiso Modificar Estatus
	=====================================================================================*/
	$("#accordionPerfiles table tbody tr td input[value='modificar']").click(function(event) {
		let inputAutomatico = this.parentNode.parentNode.querySelector("td input[value='automatico']");

		if ( this.checked ) {
			inputAutomatico.disabled = false;
		} else {
			inputAutomatico.checked = false;
			inputAutomatico.disabled = true;
		}
	});

});
