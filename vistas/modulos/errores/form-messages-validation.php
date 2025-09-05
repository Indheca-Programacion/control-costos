<?php if ( errors() ) : ?>
<script>
	let element = null;
	let newDiv = null;
	let newContent = null;
	<?php foreach(errors() as $key => $error) { ?>
	element = document.getElementsByName('<?=$key?>');
	if ( element.length > 0 ) {
		element[0].classList.add("is-invalid");
		elementPadre = element[0].parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("<?=$error?>");
		newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);
	}			
	<?php } ?>
</script>
<?php endif; ?>
