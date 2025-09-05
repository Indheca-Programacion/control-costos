<?php

	if ( isset($operador->id) ) {
		$sNombre = isset($old['sNombre']) ? $old['sNombre'] : $operador->nombre;
	} else {
		$sNombre = isset($old['sNombre']) ? $old['sNombre'] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="row">
	
	<div class="col-md-6 form-group">	
		<label for="idOperador">Id:</label>
		<input type="" id="idOperador" name="idOperador" value="<?php echo fString($operador->id); ?>"  class="form-control form-control-sm text-uppercase" >
	</div>

	<div class="col-md-6 form-group">
		<label for="sNombre">Nombre del operador:</label>
		<input type="" id="sNombre" name="sNombre" value="<?php echo fString($sNombre); ?>"  class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre del operador">
	</div>
	
</div>
