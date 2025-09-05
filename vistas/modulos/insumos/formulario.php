<?php
	if ( isset($insumo->id) ) {
		$insumoTipoId = isset($old["insumoTipoId"]) ? $old["insumoTipoId"] : $insumo->insumoTipoId;
		$codigo = isset($old["codigo"]) ? $old["codigo"] : $insumo->codigo;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $insumo->descripcion;
		$unidadId = isset($old["unidadId"]) ? $old["unidadId"] : $insumo->unidadId;
		$resguardos = isset($old["resguardos"]) ? $old["resguardos"] : $insumo->resguardos;
	} else {
		$insumoTipoId = isset($old["insumoTipoId"]) ? $old["insumoTipoId"] : "";
		$codigo = isset($old["codigo"]) ? $old["codigo"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$unidadId = isset($old["unidadId"]) ? $old["unidadId"] : "";
		$resguardos = isset($old["resguardos"]) ? $old["resguardos"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="row">

	<div class="col-md-6 form-group">

		<label for="insumoTipoId">Tipo de Insumo:</label>
		<?php if ( isset($insumo->id) ) : ?>
		<!-- <select id="insumoTipoId" class="custom-select form-controls select2" style="width: 100%" disabled> -->
		<select id="insumoTipoId" class="custom-select form-controls select2" disabled>
		<?php else: ?>
		<select name="insumoTipoId" id="insumoTipoId" class="custom-select form-controls select2">
			<option value="">Selecciona un Tipo de Insumo</option>
		<?php endif; ?>
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

<!-- <div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del insumo">
</div> -->

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<textarea id="descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del insumo"><?php echo fString($descripcion); ?></textarea>
</div>

<div class="row">

	<div class="col-md-6 form-group">

		<label for="unidadId">Unidad:</label>
		<select name="unidadId" id="unidadId" class="custom-select form-controls select2">
		<?php if ( isset($insumo->id) ) : ?>
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
