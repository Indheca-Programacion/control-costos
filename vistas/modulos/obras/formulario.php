<?php
	if ( isset($obra->id) ) {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $obra->empresaId;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $obra->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $obra->nombreCorto;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : $obra->estatusId;
		$periodos = isset($old["periodos"]) ? $old["periodos"] : $obra->periodos;
		// $fechaInicio = fFechaLarga($obra->fechaInicio);
		$fechaInicio = isset($old["fechaInicio"]) ? $old["fechaInicio"] : fFechaLarga($obra->fechaInicio);
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : ( is_null($obra->fechaFinalizacion) ? "" : fFechaLarga($obra->fechaFinalizacion) );
		$compradores = isset($old["usuariosCompras"]) ? $old["usuariosCompras"] :$obra->usuariosCompras;
		$prefijo = isset($old["prefijo"]) ? $old["prefijo"] : $obra->prefijo;
		// $ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : $obra->ubicacionId;
		$almacenId = isset($old["almacen"]) ? $old["almacen"] : $obra->almacen;

	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : "";
		$fechaInicio = isset($old["fechaInicio"]) ? $old["fechaInicio"] : fFechaLarga(date("Y-m-d"));
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : "";
		$periodos = isset($old["periodos"]) ? $old["periodos"] : "1";
		$compradores = isset($old["usuariosCompras"]) ? $old["usuariosCompras"] : array();
		$prefijo = isset($old["prefijo"]) ? $old["prefijo"] : "";
		// $ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : "";
		$almacenId = isset($old["almacen"]) ? $old["almacen"] : "";

	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">


<div class="form-group">

	<label for="empresaId">Empresa:</label>
	<select name="empresaId" id="empresaId" class="custom-select form-controls select2" <?php echo ( !$formularioEditable || isset($obra->id) ) ? ' disabled' : ''; ?>>
	<?php if ( isset($obra->id) ) : ?>
	<!-- <select id="empresaId" class="custom-select form-controls select2" style="width: 100%" disabled> -->
	<?php else: ?>
		<option value="">Selecciona una Empresa</option>
	<?php endif; ?>
		<?php foreach($empresas as $empresa) { ?>
		<option value="<?php echo $empresa["id"]; ?>"
			<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
			><?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
		</option>
		<?php } ?>
	</select>

</div>

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción de la obra" <?php echo ( !$formularioEditable ) ? ' disabled' : ''; ?>>
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto" <?php echo ( !$formularioEditable ) ? ' disabled' : ''; ?>>
</div>

<div class="row">

	<div class="col-md-6 form-group">

		<label for="estatusId">Estatus:</label>
		<select <?php echo ( $formularioEditable ) ? 'name="estatusId"' : ''; ?> id="estatusId" class="custom-select form-controls select2" <?php echo ( !$formularioEditable ) ? ' disabled' : ''; ?>>
		<?php if ( isset($obra->id) ) : ?>
		<!-- <select id="estatusId" class="form-control select2" style="width: 100%" disabled> -->
		<?php else: ?>
		<!-- <select name="estatusId" id="estatusId" class="form-control select2Add" style="width: 100%"> -->
			<option value="">Selecciona un Estatus</option>
		<?php endif; ?>
			<?php foreach($status as $estatus) { ?>
			<?php if ( $estatus["obraAbierta"] || ( $estatus["obraCerrada"] && isset($obra->id) ) ) : ?>
			<option value="<?php echo $estatus["id"]; ?>"
				<?php echo $estatusId == $estatus["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($estatus["descripcion"])); ?>
			</option>
			<?php endif; ?>
			<?php } ?>
		</select>

	</div>

	<div class="col-md-6 form-group">
		<label for="periodos">Semana:</label>
		<input type="text" id="periodos" name="periodos" min=1 value="<?php echo fString($periodos); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingrese las Semanas">
	</div>

</div>

<div class="row">

	<div class="col-md-6 form-group">
		<label for="fechaInicio">Fecha de Inicio:</label>
		<div class="input-group date" id="fechaInicioDTP" data-target-input="nearest">
			<?php if ( $formularioEditable ) : ?>
			<input type="text" name="fechaInicio" id="fechaInicio" value="<?php echo $fechaInicio; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de inicio" data-target="#fechaInicioDTP">
			<?php else: ?>
			<input type="text" id="fechaInicio" value="<?php echo $fechaInicio; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de inicio" data-target="#fechaInicioDTP" disabled>
			<?php endif; ?>
			<div class="input-group-append" data-target="#fechaInicioDTP" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
            </div>
		</div>
	</div>

	<?php if ( isset($obra->id) ) : ?>
	<div class="col-md-6 form-group">
		<label for="fechaFinalizacion">Fecha de Finalización:</label>
		<div class="input-group date" id="fechaFinalizacionDTP" data-target-input="nearest">
			<input type="text" name="fechaFinalizacion" id="fechaFinalizacion" value="<?php echo $fechaFinalizacion; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de finalización" data-target="#fechaFinalizacionDTP" <?php echo ( !$formularioEditable ) ? ' disabled' : ''; ?>>
			<div class="input-group-append" data-target="#fechaFinalizacionDTP" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
            </div>
		</div>
	</div>
	<?php endif; ?>
	
	<div class="col-md-6 form-group">
		<label for="prefijo">Prefijo de Obra:</label>
		<input type="text" name="prefijo" id="prefijo" value="<?php echo $prefijo; ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el prefijo">
	</div>

	<div class="col-md-6 form-group">
		<label for="">Compradores:</label>
		<select name="usuariosCompras[]" id="usuariosCompras" class="custom-select form-controls form-control-sms select2" multiple="multiple" style="width: 100%.">
			<?php foreach($usuarios as $usuario) { ?>
			<option value="<?php echo $usuario["id"]; ?>"
				<?php echo in_array($usuario["id"], $compradores) ? ' selected' : ''; ?>

				><?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
			</option>
			<?php } ?>
		</select>	
	</div>
	<?php if(!isset($obra->id)) : ?>
		<div class="col-md-6 form-group">
			<label for="">Crear desde:</label>
			<select name="plantilla" id="plantilla" class="custom-select form-controls form-control-sms select2">
				<option value='0'>Dejar en vacio si no se ocupa</option>
				<?php foreach($plantillas as $plantilla) { ?>
				<option value="<?php echo $plantilla["id"]; ?>"
					><?php echo mb_strtoupper(fString($plantilla["nombreCorto"])); ?>
				</option>
				<?php } ?>
			</select>	
		</div>
	<?php endif ?>

	<!-- Almacen -->
	<div class="col-md-6 form-group">
		<label for="almacen">Almacen:</label>
		<select <?php if(isset($inventario->id)) echo 'disabled' ?> name="almacen" id="almacen" class="custom-select form-controls select2">
			<option value="">Seleccione una almacen</option>
			<?php foreach($almacenes as $almacen) { ?>
			<option value="<?php echo $almacen["id"]; ?>"
			<?php echo $almacenId == $almacen["id"] ? ' selected' : ''; ?>
			><?php echo mb_strtoupper(fString($almacen["nombreCorto"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>
</div>
