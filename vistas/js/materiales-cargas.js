$(function(){
	let tablaMaterialesCargas = document.getElementById('tablaMaterialesCargas')

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tablaMaterialesCargas != null ) fAjaxDataTable(rutaAjax+'app/Ajax/MaterialCargaAjax.php', '#tablaMaterialesCargas');

	// Envio del formulario para Crear o Editar registros
	function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);

		formulario.submit();
	}

	$(tablaMaterialesCargas).on("click", "button.eliminar", function (e) {

		e.preventDefault();
		var folio = $(this).attr("folio");
		var form = $(this).parents('form');
	
		Swal.fire({
		  title: '¿Estás Seguro de querer eliminar este QR (Id: '+folio+') ?',
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

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});

	$("[name='owner']").bootstrapSwitch({
		onText: 'Propio',
		offText: 'Externo',
	}).on('switchChange.bootstrapSwitch', function (event, state) {
		let divMaquinaria = document.getElementById('divMaquinaria');
		if (divMaquinaria) {
			if (state) {
				divMaquinaria.classList.remove('d-none');
			} else {
				divMaquinaria.classList.add('d-none');
			}
		}
	});
});
