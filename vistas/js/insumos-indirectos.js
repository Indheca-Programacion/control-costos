$(function(){

	let tableList = document.getElementById('tablaInsumosIndirectos');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/InsumoIndirectoAjax.php', '#tablaInsumosIndirectos');

	// Confirmar la eliminación del Insumo o Indirecto
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var tipo = $(this).attr("tipo");
	    var form = $(this).parents('form');

	    Swal.fire({
			// title: '¿Estás Seguro de querer eliminar este Indirecto (Descripción: '+folio+') ?',
			title: '¿Estás seguro de querer eliminar este '+tipo+' (Descripción: '+folio+') ?',
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

	// Activar el elemento Inputmask
    $('[data-mask]').inputmask();

    let elementInsumoTipoId = $('#insumoTipoId.select2.is-invalid');
	let elementIndirectoTipoId = $('#indirectoTipoId.select2.is-invalid');
	let elementUnidadId = $('#unidadId.select2.is-invalid');
	if ( elementInsumoTipoId.length == 1 ) {
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-insumoTipoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementIndirectoTipoId.length == 1 ) {
		$('span[aria-labelledby="select2-indirectoTipoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-indirectoTipoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-indirectoTipoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-indirectoTipoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-indirectoTipoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementUnidadId.length == 1) {
		$('span[aria-labelledby="select2-unidadId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-unidadId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

	$("input[type='radio'][name='tipo']").change(function(event) {
		let elementDivInsumo = document.querySelector("div.row[data-tipo='insumo']");
		let elementDivIndirecto = document.querySelector("div.row[data-tipo='indirecto']");
		if ( this.value == 'insumo' ) {
			elementDivIndirecto.classList.add("d-none");
			elementDivInsumo.classList.remove("d-none");
		} else {
			elementDivInsumo.classList.add("d-none");
			elementDivIndirecto.classList.remove("d-none");
		}
	});

	$("#insumoTipoId,#indirectoTipoId").change(function(event) {
		let tipoId = this.value
		let text = $("#"+this.id+" option:selected").text();
		let numerosYPuntos = text.match(/[0-9.]+/g);
		// Concatenamos los números y puntos encontrados en un string
		let resultado = numerosYPuntos ? numerosYPuntos.join("") : "";
		let tipo = this.id
		$.ajax({
			url: `${rutaAjax}app/Ajax/InsumoIndirectoAjax.php?id=${tipoId}&tipo=${tipo}`,
			method: 'GET',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
		}).done(function(respuesta) {
			if (respuesta !== '') {
				if(tipo == 'insumoTipoId'){
					$("#codigo").val(respuesta)
				}else{
					$("#numero").val(respuesta)
				}
			}else{
				if(tipo == 'insumoTipoId'){
					$("#codigo").val(resultado+".1")
				}else{
					$("#numero").val(resultado+".1")
				}
			}
		}).fail(function(error){
			console.log(error)
		});
	})

});
