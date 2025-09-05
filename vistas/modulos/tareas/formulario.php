<?php
	if ( isset($tarea->id) ) {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $tarea->descripcion;
		$responsable = isset($old["responsable"]) ? $old["responsable"] : $tarea->responsable;
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : $tarea->fecha_inicio;
		$fecha_limite = isset($old["fecha_limite"]) ? $old["fecha_limite"] : $tarea->fecha_limite;
		$estatus = isset($old["estatus"]) ? $old["estatus"] : $tarea->estatus;
	} else {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$responsable = isset($old["responsable"]) ? $old["responsable"] : "";
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : "";
		$fecha_limite = isset($old["fecha_limite"]) ? $old["fecha_limite"] : "";
		$estatus = isset($old["estatus"]) ? $old["estatus"] : "";
		$permitirEditar = true;
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<div class="row">
	<!-- Descripcion -->
	<div class="col form-group">
		<label for="descripcion">Descripción:</label>
		<textarea type="text" rows="3" id="descripcion" <?php if(!$permitirEditar) echo 'readonly'; ?> name="descripcion" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción de la tarea"><?php echo fString($descripcion); ?></textarea>
	</div>
</div>
<div class="row ">
	<!-- Responsable -->
	<div class="col-md-6 form-group">
		<label for="responsable">Responsable:</label>
		<select id="responsable" <?php if(!$permitirEditar) echo 'disabled'; ?> name="fk_usuario" class="custom-select form-controls select2">
			<option value="">Selecciona un responsable</option>
			<?php foreach($usuarios as $usuario) { ?>
				<option value="<?php echo $usuario["id"]; ?>" 
				<?php echo $responsable == $usuario["id"] ? ' selected' : ''; ?>
				>
				<?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>
	<!-- Fecha de inicio -->
	<div class="col-md-6 form-group">
		<label for="fecha_inicio">Fecha de Inicio:</label>
		<div class="input-group date" id="fecha_inicio" data-target-input="nearest">
			<input <?php if(!$permitirEditar) echo 'disabled'; ?> type="text" name="fecha_inicio" id="fecha_inicio" value="<?= $fecha_inicio; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fecha_inicio">
			<div class="input-group-append" data-target="#fecha_inicio" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
			</div>
		</div>
	</div>
	<!-- Fecha estimada -->
	<div class="col-md-6 form-group">
		<label for="fecha_estimada">Fecha Estimada de Finalizacion:</label>
		<div class="input-group date" id="fecha_estimada" data-target-input="nearest">
			<input <?php if(!$permitirEditar) echo 'disabled'; ?> type="text" name="fecha_limite" id="fecha_estimada" value="<?= $fecha_limite; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fecha_estimada">
			<div class="input-group-append" data-target="#fecha_estimada" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
			</div>
		</div>
	</div>

</div>