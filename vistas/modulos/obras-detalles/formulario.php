<?php
	$tiposSelect = $obradetalle->Tipo == 'Directo' ? $insumoTipos : $indirectoTipos;
	$tipoSelectId = $obradetalle->Tipo == 'Directo' ? $insumos->insumoTipoId : $indirectos->indirectoTipoId;

	$descripcion = $obradetalle->Tipo == 'Directo' ? $insumos->descripcion : $indirectos->descripcion;

	$codigo = $obradetalle->Tipo == 'Directo' ? $insumos->codigo : $indirectos->numero;

	$unidadId = $obradetalle->Tipo == 'Directo' ? $insumos->unidadId : $indirectos->unidadId;

	$presupuesta_dolares = isset($old["presupuesta_dolares"]) ? $old["presupuesta_dolares"] : $obradetalle->presupuesto_dolares;
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="row">

	<div class="col-md-6 form-group">

		<label for="TipoId">Tipo de  <?php echo $obradetalle->Tipo ?>:</label>
		<select disabled name="TipoId" id="TipoId" class="custom-select form-controls select2">
			<option value="">Selecciona un Tipo de  <?php echo $obradetalle->Tipo ?></option>
			<?php foreach($tiposSelect as $tipoSelect) { ?>
			<option value="<?php echo $tipoSelect["id"]; ?>"
				<?php echo $tipoSelectId == $tipoSelect["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($tipoSelect["descripcion"])); ?>
			</option>
			<?php } ?>
		</select>

	</div>

	<div class="col-md-6 form-group">
		<label for="codigo">Código:</label>
		<input disabled type="text" id="codigo" name="codigo" value="<?php echo fString($codigo); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del directo">
	</div>
	
	<div class="col-12 form-group">
		<label  for="descripcion">Descripción:</label>
		<textarea disabled id="descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del insumo"><?php echo mb_strtoupper(fString($descripcion)) ?></textarea>
	</div>
	
	<div class="col-md-6 form-group">
	
		<label for="unidadId">Unidad:</label>
		<select disabled name="unidadId" id="unidadId" class="custom-select form-controls select2">
			<option value="">Selecciona una Unidad</option>
			<?php foreach($unidades as $unidad) { ?>
			<option value="<?php echo $unidad["id"]; ?>"
				<?php echo $unidadId == $unidad["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($unidad["descripcion"])) . ' [ ' . mb_strtoupper(fString($unidad["nombreCorto"])) . ' ]'; ?>
			</option>
			<?php } ?>
		</select>
	
	</div>

</div>

<div class="row">
	<div class="col-md-6 form-group">
		<label for="cantid">Cantidad:</label>
		<input type="text" id="cantidad" name="cantidad" value="<?php echo $obradetalle->cantidad; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el cantidad del directo">
	</div>
	<div class="col-md-6 form-group">
		<label for="presupuesto">Presupuesto:</label>	
		<input type="text" id="presupuesto" name="presupuesto" value="<?php echo $obradetalle->presupuesto; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el presupuesto del directo">
	</div>
	<div class="col-md-6 form-group">
		<label for="presupuesto_dolares">Presupuesto de Dolares:</label>	
		<input type="text" id="presupuesto_dolares" name="presupuesto_dolares" value="<?php echo $obradetalle->presupuesto_dolares; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el presupuesto del directo">
	</div>
</div>