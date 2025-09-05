<?php

	if ( isset($material->id) ) {
		$sDescripcion = isset($old['sDescripcion']) ? $old['sDescripcion'] : $material->descripcion;
	} else {
		$sDescripcion = isset($old['sDescripcion']) ? $old['sDescripcion'] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="row">
	

	<div class="col-md-6 form-group">
		<label for="materialId">Id:</label>
		<input type="text" disabled id="materialId" name="materialId" value="<?php echo fString($material->id); ?>"  class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre del material">
	</div>

	<div class="col-md-6 form-group">
		<label for="nPeso">Descripción:</label>
		<input type="text" id="sDescripcion" name="sDescripcion" value="<?php echo fString($sDescripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripcion">
	</div>

	
</div>
