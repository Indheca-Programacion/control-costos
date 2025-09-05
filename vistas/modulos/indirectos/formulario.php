<?php
	if ( isset($indirecto->id) ) {
		$indirectoTipoId = isset($old["indirectoTipoId"]) ? $old["indirectoTipoId"] : $indirecto->indirectoTipoId;
		$numero = isset($old["numero"]) ? $old["numero"] : $indirecto->numero;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $indirecto->descripcion;
		$unidadId = isset($old["unidadId"]) ? $old["unidadId"] : $indirecto->unidadId;
		$resguardos = isset($old["resguardos"]) ? $old["resguardos"] : $indirecto->resguardos;
	} else {
		$indirectoTipoId = isset($old["indirectoTipoId"]) ? $old["indirectoTipoId"] : "";
		$numero = isset($old["numero"]) ? $old["numero"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$unidadId = isset($old["unidadId"]) ? $old["unidadId"] : "";
		$resguardos = isset($old["resguardos"]) ? $old["resguardos"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="row">

	<div class="col-md-6 form-group">

		<label for="indirectoTipoId">Tipo de Indirecto:</label>
		<?php if ( isset($indirecto->id) ) : ?>
		<!-- <select id="indirectoTipoId" class="custom-select form-controls select2" style="width: 100%" disabled> -->
		<select id="indirectoTipoId" class="custom-select form-controls select2" disabled>
		<?php else: ?>
		<select name="indirectoTipoId" id="indirectoTipoId" class="custom-select form-controls select2">
			<option value="">Selecciona un Tipo de Indirecto</option>
		<?php endif; ?>
			<?php foreach($indirectoTipos as $indirectoTipo) { ?>
			<option value="<?php echo $indirectoTipo["id"]; ?>"
				<?php echo $indirectoTipoId == $indirectoTipo["id"] ? ' selected' : ''; ?>
				><?php echo '[ ' . mb_strtoupper(fString($indirectoTipo["numero"])) . '  ] ' . mb_strtoupper(fString($indirectoTipo["descripcion"])); ?>
			</option>
			<?php } ?>
		</select>

	</div>

	<div class="col-md-6 form-group">
		<label for="numero">Número:</label>
		<input type="text" id="numero" name="numero" value="<?php echo fString($numero); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número del indirecto" data-inputmask='"mask": "9.9{1,3}.9{1,3}"' data-mask>
	</div>

</div>

<!-- <div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del indirecto">
</div> -->

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<textarea id="descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del indirecto"><?php echo fString($descripcion); ?></textarea>
</div>

<div class="row">

	<div class="col-md-6 form-group">

		<label for="unidadId">Unidad:</label>
		<select name="unidadId" id="unidadId" class="custom-select form-controls select2">
		<?php if ( isset($indirecto->id) ) : ?>
		<!-- <select id="unidadId" class="form-control select2" style="width: 100%" disabled> -->
		<?php else: ?>
		<!-- <select name="unidadId" id="unidadId" class="form-control select2Add" style="width: 100%"> -->
			<option value="">Selecciona una Unidad</option>
		<?php endif; ?>
			<?php foreach($unidades as $unidad) { ?>
			<option value="<?php echo $unidad["id"]; ?>"
				<?php echo $unidadId == $unidad["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($unidad["descripcion"])) . ' [ ' . mb_strtoupper(fString($unidad["nombreCorto"])) . ' ]'; ?>
			</option>
			<?php } ?>
		</select>

	</div>

	<div class="col-md-6 form-group">

		<label for="resguardos">Resguardos:</label>
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Resguardos" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="resguardos" id="resguardos" <?php echo $resguardos == 1 ? "checked" : ""; ?>>
				</div>
			</div>
		</div>

	</div>

</div>
