<?php
	if ( isset($insumoTipo->id) ) {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $insumoTipo->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $insumoTipo->nombreCorto;
		$orden = isset($old["orden"]) ? $old["orden"] : $insumoTipo->orden;
		$perfilesCrearRequis = isset($old["perfilesCrearRequis"]) ? $old["perfilesCrearRequis"] : $insumoTipo->perfilesCrearRequis;

	} else {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$orden = isset($old["orden"]) ? $old["orden"] : "0";
		$perfilesCrearRequis = isset($old["perfilesCrearRequis"]) ? $old["perfilesCrearRequis"] : array();

	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del tipo de insumo">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
</div>

<div class="col form-group">
	<label for="orden">Orden:</label>
	<input type="text" id="orden" name="orden" value="<?php echo fString($orden); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el Orden">
</div>

<div class="form-group">
	<label for="perfilesCrearRequis">Perfiles que crean Requisiciones:</label>
	<select name="perfilesCrearRequis[]" id="perfilesCrearRequis" class="custom-select form-controls form-control-sms select2" multiple="multiple" style="width: 100%">
		<?php foreach($perfiles as $perfil) { ?>
		<option value="<?php echo $perfil["id"]; ?>"
			<?php echo in_array($perfil["id"], $perfilesCrearRequis) ? ' selected' : ''; ?>
			><?php echo mb_strtoupper(fString($perfil["nombre"])); ?>
		</option>
		<?php } ?>
	</select>
</div>