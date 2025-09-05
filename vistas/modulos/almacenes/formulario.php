<?php
	if ( isset($almacen->id) ) {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $almacen->nombre;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $almacen->nombreCorto;
	} else {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="nombre">Nombre:</label>
	<input type="text" id="nombre" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del almacen">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
</div>
