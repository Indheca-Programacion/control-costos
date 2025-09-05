<?php
	if ( isset($indirectoTipo->id) ) {
		$numero = isset($old["numero"]) ? $old["numero"] : $indirectoTipo->numero;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $indirectoTipo->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $indirectoTipo->nombreCorto;
		$perfilesCrearRequis = isset($old["perfilesCrearRequis"]) ? $old["perfilesCrearRequis"] : $indirectoTipo->perfilesCrearRequis;
	} else {
		$numero = isset($old["numero"]) ? $old["numero"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$perfilesCrearRequis = isset($old["perfilesCrearRequis"]) ? $old["perfilesCrearRequis"] : array();
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="numero">Número:</label>
	<input type="text" id="numero" name="numero" value="<?php echo fString($numero); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número del tipo de indirecto" data-inputmask='"mask": "9.9{1,3}"' data-mask>
</div>

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del tipo de indirecto">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
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