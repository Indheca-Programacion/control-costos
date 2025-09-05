$(function(){

	let tableList = document.getElementById('tablaEstatus');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/EstatusAjax.php', '#tablaEstatus');

	// Confirmar la eliminación del Estatus
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Estatus (Descripción: '+folio+') ?',
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

	//color picker with addon
    $('.my-colorpicker2').colorpicker();

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
		let elementIcono = this.querySelector('.fa-square');
		let elementInputColorPicker = this.querySelector('input');
		let style = ( elementInputColorPicker.getAttribute('name') === 'colorTexto') ? 'color' : 'background-color';
		let elementDescripcion = document.getElementById('descripcion');

    	if ( event.color === null ) {
			elementIcono.style.removeProperty('color');
			elementDescripcion.style.removeProperty(style);
    	} else {
			$(elementIcono).css('color', event.color.toString());
			$(elementDescripcion).css(style, event.color.toString());
    	}
	})

	// $('.my-colorpicker2').colorpicker().on('colorpickerChange', function(event) {
	// 	let elementIcono = this.querySelector('.fa-square');
	// 	$(elementIcono).css('color', event.color.toString());

});
