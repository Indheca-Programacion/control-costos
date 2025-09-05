if (document.getElementById('nom35') !== null) Dropzone.autoDiscover = false;
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
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});

    

    if (document.getElementById('nom35') !== null) {

		myDropzone = new Dropzone('#nom35', {
			url: rutaAjax+'app/Ajax/Nom35Ajax.php',
			acceptedFiles: ".xlsx,.xls",
			addRemoveLinks: true,
			dictRemoveFile: 'Eliminar Archivo',
			dictUpload: "Subiendo",
			dictCancelUpload: "Cancelar",
			dictInvalidFileType: "No se permite este tipo de archivo",
			maxFiles: 1,
			maxfilesexceeded: function(file) {
			crearToast('bg-danger', 'Error', '', 'Solo se permite subir un archivo');
			this.removeFile(file); // Eliminar el archivo que excede el límite
			},
			maxFilesize: 4 // Tamaño máximo en MB
		});

		myDropzone.on('success', function(file, response) {
			crearToast('bg-success', 'Éxito', '', 'Archivo subido correctamente');
			let iframeReporte = document.getElementById('iframeReporte');
			if (iframeReporte) {
			iframeReporte.contentWindow.location.reload();
			}
		});
	
		// myDropzone.on('addedfile', file => {
		// 	arrPDF.push(file);
		// })

		// myDropzone.on('removedfile', file => {
		// 	let i = arrPDF.indexOf(file);
		// 	arrPDF.splice(i, 1);
		// })
		
	}


});
