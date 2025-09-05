<?php
	if ( isset($movimiento->id) ) {
		$obraId = isset($movimiento->obraId) ? $movimiento->obraId : "";
		$tipoId = isset($movimiento->tipoId) ? $movimiento->tipoId : "";
	} else {
		$obraId = isset($obraId) ? $obraId : "";
		$tipoId = isset($tipoId) ? $tipoId : "";
	}
?>

<div class="row">

	<div class="col-md-6 form-group">

		<label for="ubicacion">Ubicacion:</label>
		<select class="form-control from-control-sm select2" id="ubicacion" name="nIdObra">
			<option value="0">Seleccione una ubicacion</option>
			<?php foreach($obras as $obra) { ?>
			<option value="<?php echo $obra["id"]; ?>"
				<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
			</option>
			<?php } ?>
		</select>

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-12 form-group">

		<label for="tipo">Tipo:</label>
		<input type="checkbox" class="form-control form-control-sm text-uppercase" name="nTipo" id="tipo" value="1" <?php echo $tipoId == 1 ? ' checked' : ''; ?>>

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6 form-group">
		
	</div>
		

</div> <!-- <div class="row"> -->
