<?php

	if ( isset($cargas->id) ) {
		$idCarga = sprintf("C%04d", $cargas->id);
		$idObra = isset($old['idObra']) ? $old['idObra'] : $cargas->idObra;
		$materialCarga = isset($old['nombreMaterial']) ? $old['nombreMaterial'] : $cargas->nombreMaterial;
		$pesoCarga = isset($old['pesoCarga']) ? $old['pesoCarga'] : $cargas->pesoCarga;
		$fechaHoraCarga = isset($old['fechaHoraCarga']) ? $old['fechaHoraCarga'] : $cargas->fechaHoraCarga;
		$folioCarga = isset($old['folioCarga']) ? $old['folioCarga'] : $cargas->folioCarga;

	} else {
		
		$idCarga =  sprintf("C%04d", 0);
		$idObra = isset($old['idObra']) ? $old['idObra'] : "";
		$materialCarga = isset($old['nombreMaterial']) ? $old['nombreMaterial'] : "";
		$pesoCarga = isset($old['pesoCarga']) ? $old['pesoCarga'] : "";
		$fechaHoraCarga = isset($old['fechaHoraCarga']) ? $old['fechaHoraCarga'] : "";
		$folioCarga = isset($old['folioCarga']) ? $old['folioCarga'] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" name="idCarga" id="idCarga" value="<?php echo $cargas->id;?>">

<div class="row">
	
	<div class="col-md-6 form-group">
		<label for="idObra">Obra:</label>
		<select <?php if(isset($cargas->id)) echo 'disabled' ?> name="idObra" id="idObra" class="custom-select form-controls select2">
			<option value="">Seleccione una obra</option>
			<?php foreach($obras as $obra) { ?>
			<option value="<?php echo $obra["id"]; ?>"
				<?php echo $idObra == $obra["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
			</option>
			<?php } ?>

			
		</select>
	</div>		

	<div class="col-md-6 form-group">
		<label for="operador">Material:</label>
		<input type="" id="operador" name="operador" value="<?php echo fString($materialCarga); ?>"   <?php echo $cargas->id ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre del material">
	</div>

	<div class="col-md-6 form-group">
		<label for="nPeso">Peso:</label>
		<input type="number" id="nPeso" name="nPeso" value="<?php echo fString($pesoCarga); ?>"   <?php echo $cargas->id ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa el peso del material">
	</div>

	<!-- Fecha Y Hora -->
	<div class="col-md-6 form-group">
		<label for="dfechaHora">Fecha Y Hora</label>
		<input type="datetime-local" 
			name="dFechaHora" 
			id="dFechaHora" 
			value="<?php echo $fechaHoraCarga; ?>" 
			class="form-control form-control-sm" 
			placeholder="Ingresa la Fecha y Hora de la carga" 
			<?php if(isset($cargas->id)) echo 'disabled'; ?>>
	</div>


	<div class="col-md-6 form-group">
		<label for="sFolio">Folio de Carga:</label>
		<input type="text" id="sFolio" name="sFolio" value="<?php echo fString($folioCarga); ?>"    <?php echo $cargas->id ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa el folio de la carga">
	</div>

	
</div>
