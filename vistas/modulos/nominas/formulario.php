<?php
	if ( isset($nominas["id"]) ) {
		$nominaId = $nominas["id"];
		$obraId = $nominas["fk_obraId"];
	} else {
		$nominaId = '';
		$obraId = '';
	}
?>

<div class="row">
	<!-- Fechas -->
	<div class="col">
		<!-- OBRA -->
		<div class="col-md-4 form-group">
			<label for="filtroObraId">Obra:</label>
			<select class="custom-select select2" id="filtroObraId" name="obraId" <?php echo $nominaId !== '' ? ' disabled' : ''; ?>>
				<option value="0" selected>Selecciona una Obra</option>
				<?php foreach($obras as $obra) { ?>
				<option value="<?php echo $obra["id"]; ?>"
				<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>>
					<?php echo '[ ' . mb_strtoupper(fString($obra["empresas.nombreCorto"])) . ' ] ' . mb_strtoupper(fString($obra["descripcion"])); ?>
				</option>
				<?php } ?>
			</select>
		</div>
		<!-- SEMANA -->
		<div class="col-md-4 form-group">
			<label for="">Semana</label>
			<select class="custom-select select2" id="semana" name="semana" <?php echo $nominaId !== '' ? ' disabled' : ''; ?>>
				<?php if(!isset($nominas["id"])){?>
					<option value="">Seleccione una semana</option>
				<?php } else {?>
					<option value="<?php echo $nominas["semana"]; ?>">SEMANA <?php echo $nominas["semana"]; ?> </option>
				<?php }?>
				
			</select>
		</div>
	</div>
</div>