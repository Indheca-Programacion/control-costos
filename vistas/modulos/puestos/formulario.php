<?php
	if ( isset($puesto->id) ) {
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $puesto->nombreCorto;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $puesto->descripcion;

	} else {
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
	}
?>

<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="nombreCorto">Nombre:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre ">
</div>

<div class="form-group">
	<label for="descripcion">Descripción</label>

	<textarea id="descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción" rows="5" cols="33"><?php echo !empty($descripcion) ? fString($descripcion) : ''; ?></textarea>


</div>