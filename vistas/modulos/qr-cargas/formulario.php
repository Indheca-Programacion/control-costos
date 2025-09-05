<?php

	if ( isset($qrCargas->idMaquinaria) ) {
		$id = sprintf("C%04d", $qrCargas->id);
		$operadorId = isset($old['nIdOperador03Operador']) ? $old['nIdOperador03Operador'] : $qrCargas->operadorId;
		$marca = isset($old['sMarca']) ? $old['sMarca'] : $qrCargas->marca;
		$modelo = isset($old['sModelo']) ? $old['sModelo'] : $qrCargas->modelo;
		$año = isset($old['sYear']) ? $old['sYear'] : $qrCargas->año;
		$placa = isset($old['placa']) ? $old['placa'] : $qrCargas->placa;
		$capacidad = isset($old['sCapacidad']) ? $old['sCapacidad'] : $qrCargas->capacidad;
		$numeroEconomico = isset($old['sNumeroEconomico']) ? $old['sNumeroEconomico'] : $qrCargas->numeroEconomico;
		$owner = ($qrCargas->numeroEconomico == '') ? true : false;

		// DATOS DE REGISTRO DE CARGAS

		$idCarga =  sprintf("C%04d", 0);
		$idObra = isset($old['idObra']) ? $old['idObra'] : "";
		$idUbicacion = isset($old['idUbicacion']) ? $old['idUbicacion'] : "";

		$materialCargaId = isset($old['nombreMaterial']) ? $old['nombreMaterial'] : "";
		$pesoCarga = isset($old['pesoCarga']) ? $old['pesoCarga'] : "";
		$fechaHoraCarga = isset($old['fechaHoraCarga']) ? $old['fechaHoraCarga'] : "";
		$folioCarga = isset($old['folioCarga']) ? $old['folioCarga'] : "";

		$polizaSeguro = isset($qrCargas->polizaSeguro) ?  $qrCargas->polizaSeguro : "";
		$condiciones = isset($qrCargas->condiciones) ?  $qrCargas->condiciones : "";
		$arrendadorEquipo = isset($qrCargas->arrendadorEquipo) ?  $qrCargas->arrendadorEquipo : "";
		$cumpleNoCumple = isset($qrCargas->cumpleNoCumple) ?  $qrCargas->cumpleNoCumple : "";

		$acuerdo = isset($old['acuerdo']) ? $old['acuerdo'] : "";
		
		$idMaquinaria = isset($qrCargas->idMaquinaria) ?  $qrCargas->idMaquinaria : "";

		$options = ['CUMPLE', 'NO CUMPLE'];

	} else {
		$id = sprintf("C%04d", $qrCargas->id);
		$operadorId = isset($old["nIdOperador03Operador"]) ? $old["nIdOperador03Operador"] : "";
		$marca = isset($old['sMarca']) ? $old['sMarca'] : "";
		$modelo = isset($old['sModelo']) ? $old['sModelo'] : "";
		$año = isset($old['sYear']) ? $old['sYear'] : "";
		$placa = isset($old['sPlaca']) ? $old['sPlaca'] : "";
		$capacidad = isset($old['sCapacidad']) ? $old['sCapacidad'] : "";
		$numeroEconomico = isset($old['sNumeroEconomico']) ? $old['sNumeroEconomico'] : "";
		$owner = (isset($old['sNumeroEconomico']) && $old['sNumeroEconomico'] == '') ? true : false;

		$polizaSeguro = isset($old['polizaSeguro']) ? $old['polizaSeguro'] : "";
		$arrendadorEquipo = isset($old['arrendadorEquipo']) ? $old['arrendadorEquipo'] : "";
		$acuerdo = isset($old['acuerdo']) ? $old['acuerdo'] : "";
		$cumpleNoCumple = isset($old['cumpleNoCumple']) ? $old['cumpleNoCumple'] : "";
		$condiciones = isset($old['condiciones']) ? $old['condiciones'] : "";
		
		$idMaquinaria = isset($old['idMaquinaria']) ? $old['idMaquinaria'] : "";

		$pesoCarga = isset($old['pesoCarga']) ? $old['pesoCarga'] : "";
		$fechaHoraCarga = isset($old['fechaHoraCarga']) ? $old['fechaHoraCarga'] : "";
		$materialCargaId = isset($old['nombreMaterial']) ? $old['nombreMaterial'] : "";

		$options = ['CUMPLE', 'NO CUMPLE'];
		
	}
?>
<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" name="nId01Qr" id="idQrCarga" value="<?php echo $qrCargas->id ?>">

<div class="row">
	<?php if ( isset($qrCargas->id) ) : ?>
		<div class="col-md-6 form-group">
			<label for="modeloId">ID:</label>
			<input type="text" id="id" name="id" value="<?php echo $id; ?>" disabled class="form-control form-control-sm text-uppercase" placeholder="ID del vehiculo">
		</div>
	<?php endif; ?>
	<div class="col-md-6 form-group">
		<label for="modeloId">Nombre Operado:</label>
		<div class="input-group">
			<select name="nIdOperador03Operador" id="operadorId" <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?>  class="custom-select form-controls select2Add">
				<?php if ( !isset($qrCargas->id) ) : ?>
					<option value="">Selecciona un Operador</option>
				<?php endif; ?>

				<?php foreach($operadoresMaquinaria as $operadorMaquinaria) { ?>
					<option value="<?php echo $operadorMaquinaria["id"]; ?>"
						<?php echo $operadorId == $operadorMaquinaria["id"] ? ' selected' : ''; ?>>
							<?php echo mb_strtoupper(fString($operadorMaquinaria["nombreOperador"])); ?>
					</option>
				<?php } ?>
			</select>
			<div class="input-group-append">
				<button type="button" id="btnAddOperadorId" class="btn btn-sm btn-success" disabled>
					<i class="fas fa-plus-circle"></i>
				</button>
			</div>
		</div>
	</div>
					
	<div class="col-md-6 form-group">
		<label for="sPlaca">Placa:</label>
		<input type="text" id="sPlaca" name="sPlaca" value="<?php echo fString($placa); ?>"    <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa la placa del vehiculo">
	</div>

	<div class="col-md-6 form-group">
		<label for="sMarca">Marca de Camión:</label>
		<input type="text" id="sMarca" name="sMarca" value="<?php echo fString($marca); ?>"   <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa la marca del vehiculo">
	</div>

	<div class="col-md-6 form-group">
		<label for="sModelo">Modelo:</label>
		<input type="text" id="sModelo" name="sModelo" value="<?php echo fString($modelo); ?>"   <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa el modelo del vehiculo">
	</div>

	<div class="col-md-6 form-group">
		<label for="sYear">Año:</label>
		<input type="text" id="sYear" name="sYear" value="<?php echo fString($año); ?>"   <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa el año del vehiculo">
	</div>

	<div class="col-md-6 form-group">
		<label for="sCapacidad">Capacida de Carga / Volumen </label>
		<input type="text" id="sCapacidad" name="sCapacidad" value="<?php echo fString($capacidad); ?>"   <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa la capacidad de carga del vehiculo">
	</div>

	<div class="col-md-6 form-group ">
		<label for="owner">Camion:</label>
		<br>
		<input type="checkbox"  class="form-control form-control-sm text-uppercase" 
			<?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?> 
			<?php echo $owner ? "" : "checked"; ?>
			name="owner" id="owner"  >
	</div>

	<div class="col-md-6 form-group" >
		<label for="sNumeroEconomico">Numero Economico:</label>
		<input type="text" id="sNumeroEconomico" name="sNumeroEconomico" value="<?php echo fString($numeroEconomico); ?>"   <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?> class="form-control form-control-sm text-uppercase" placeholder="Ingresa el numero economico del vehiculo">
	</div>

	<?php if ( isset($qrCargas->id) ) : ?>
		
			<div class="col-md-6 form-group">
				<label for="condiciones">Condiciones:</label>
				<input type="text" id="condiciones" name="condiciones" value="<?php echo $condiciones ?>" class="form-control form-control-sm text-uppercase"  <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?>   placeholder="Ingresa las condiciones">
			</div>

			<div class="col-md-6 form-group">
				<label for="polizaSeguro">Póliza de Seguro:</label>
				<input type="text" id="polizaSeguro" name="polizaSeguro" value="<?php echo $polizaSeguro ?>" class="form-control form-control-sm text-uppercase"  <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?>   placeholder="Ingresa el número de póliza de seguro">
			</div>

			<div class="col-md-6 form-group">
				<label for="arrendadorEquipo">Nombre Arrendadora:</label>
				<input type="text" id="arrendadorEquipo" name="arrendadorEquipo" value="<?php echo $arrendadorEquipo ?>" class="form-control form-control-sm text-uppercase"  <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?>   placeholder="Ingresa el nombre de a la arrendadora">
			</div>

			<div class="col-md-6 form-group">
				<label for="cumpleNoCumple">Cumple/No cumple:</label>
				<select id="cumpleNoCumple" name="cumpleNoCumple" class="form-control form-control-sm text-uppercase" <?php echo $qrCargas->idMaquinaria ? "disabled" : ""; ?>   >
					<option value="">-- Selecciona una opción --</option>
					<?php foreach ($options as $option): ?>
						<option value="<?= $option ?>" <?= $cumpleNoCumple === $option ? 'selected' : '' ?>><?= $option ?></option>
					<?php endforeach; ?>
				</select>
			</div>

	<?php endif; ?>
	
</div>
