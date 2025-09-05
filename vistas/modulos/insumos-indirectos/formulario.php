<?php
	$tipo = isset($old["tipo"]) ? $old["tipo"] : "insumo";

	$insumoTipoId = isset($old["insumoTipoId"]) ? $old["insumoTipoId"] : "";
	$codigo = isset($old["codigo"]) ? $old["codigo"] : "";

	$indirectoTipoId = isset($old["indirectoTipoId"]) ? $old["indirectoTipoId"] : "";
	$numero = isset($old["numero"]) ? $old["numero"] : "";

	$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
	$unidadId = isset($old["unidadId"]) ? $old["unidadId"] : "";
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label>Tipo:</label>
	<br>
	<div class="btn-group btn-group-toggle" data-toggle="buttons">
		<label class="btn btn-outline-info" <?php echo ( $tipo == 'insumo' ) ? 'active' : ''; ?>>
			<input type="radio" name="tipo" value="insumo" <?php echo ( $tipo == 'insumo' ) ? 'checked' : ''; ?>> Directo
		</label>
		<label class="btn btn-outline-info ml-1" <?php echo ( $tipo == 'indirecto' ) ? 'active' : ''; ?>>
			<input type="radio" name="tipo" value="indirecto" <?php echo ( $tipo == 'indirecto' ) ? 'checked' : ''; ?>> Indirecto
		</label>
	</div>
</div>

<div class="row <?php echo ( $tipo != 'insumo' ) ? 'd-none' : ''; ?>" data-tipo="insumo">

	<div class="col-md-6 form-group">

		<label for="insumoTipoId">Tipo de Insumo:</label>
		<select name="insumoTipoId" id="insumoTipoId" class="custom-select form-controls select2">
			<option value="">Selecciona un Tipo de Insumo</option>
			<?php foreach($insumoTipos as $insumoTipo) { ?>
			<option value="<?php echo $insumoTipo["id"]; ?>"
				<?php echo $insumoTipoId == $insumoTipo["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($insumoTipo["descripcion"])); ?>
			</option>
			<?php } ?>
		</select>

	</div>

	<div class="col-md-6 form-group">
		<label for="codigo">Código:</label>
		<input type="text" id="codigo" name="codigo" value="<?php echo fString($codigo); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del insumo">
	</div>

</div>

<div class="row <?php echo ( $tipo != 'indirecto' ) ? 'd-none' : ''; ?>" data-tipo="indirecto">

	<div class="col-md-6 form-group">

		<label for="indirectoTipoId">Tipo de Indirecto:</label>
		<select name="indirectoTipoId" id="indirectoTipoId" class="custom-select form-controls select2">
			<option value="">Selecciona un Tipo de Indirecto</option>
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
	<input type="text" id="descripcion" name="descripcion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del insumo o indirecto">
</div> -->

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<textarea id="descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del insumo o indirecto"><?php echo fString($descripcion); ?></textarea>
</div>

<div class="row">

	<div class="col-md-6 form-group">

		<label for="unidadId">Unidad:</label>
		<select name="unidadId" id="unidadId" class="custom-select form-controls select2">
			<option value="">Selecciona una Unidad</option>
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
					<input type="checkbox" name="resguardos" id="resguardos">
				</div>
			</div>
		</div>

	</div>

</div>
