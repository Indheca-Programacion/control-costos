<?php 

    if ( !isset($puestoSEI->id) ) $tagsSuperiores = isset($old["tagsSuperiores"]) ? $old["tagsSuperiores"] : array();
    else $tagsSuperiores = $puestoSEI->puestoSuperior;
    if ( !isset($puestoSEI->id) ) $tagsInferiores = isset($old["tagsInferiores"]) ? $old["tagsInferiores"] : array();
    else $tagsInferiores = $puestoSEI->puestoInferior;

?>

<input type="hidden" id="idPuesto" value=<?php echo $puesto->id?>>

<div class="form-group">
	<label for="tagsSuperiores">Puestos Superiores:</label>
	<select name="tagsSuperiores[]" id="tagsSuperiores" class="custom-select form-controls select2" multiple>
	<?php if ( isset($puesto->id) ) : ?>
		<!-- <select id="tagsSuperiores" class="custom-select form-controls select2" style="width: 100%" disabled> -->
	<?php else: ?>
		<!-- <option value="">Selecciona un Tag</option> -->
	<?php endif; ?>
		<?php foreach($puestos as $puesto) { ?>
		<option value="<?php echo $puesto["id"]; ?>"
			<?php echo in_array($puesto["id"], $tagsSuperiores) ? ' selected' : ''; ?>>
			<?php echo mb_strtoupper(fString($puesto["nombreCorto"])); ?>
		</option>
		<?php } ?>
	</select>
</div>

<div class="form-group">
	<label for="tagsInferiores">Puestos Inferiores:</label>
	<select name="tagsInferiores[]" id="tagsInferiores" class="custom-select form-controls select2" multiple>
	<?php if ( isset($maquinaria->id) ) : ?>
		<!-- <select id="tagsInferiores" class="custom-select form-controls select2" style="width: 100%" disabled> -->
	<?php else: ?>
		<!-- <option value="">Selecciona un Tag</option> -->
	<?php endif; ?>
		<?php foreach($puestos as $puesto) { ?>
		<option value="<?php echo $puesto["id"]; ?>"
			<?php echo in_array($puesto["id"], $tagsInferiores) ? ' selected' : ''; ?>>
			<?php echo mb_strtoupper(fString($puesto["nombreCorto"])); ?>
		</option>
		<?php } ?>
	</select>
</div>