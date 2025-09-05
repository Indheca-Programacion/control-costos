<?php
	if ( isset($nominas->id) ) {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $obra->empresaId;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $obra->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $obra->nombreCorto;
		$observacion = isset($old["observacion"]) ? $old["observacion"] : $obra->observacion;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : $obra->estatusId;
		$periodos = isset($old["periodos"]) ? $old["periodos"] : $obra->periodos;
		$fechaInicio = isset($old["fechaInicio"]) ? $old["fechaInicio"] : fFechaLarga($obra->fechaInicio);
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : ( is_null($obra->fechaFinalizacion) ? "" : fFechaLarga($obra->fechaFinalizacion) );
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : "";
		$fechaInicio = isset($old["fechaInicio"]) ? $old["fechaInicio"] : fFechaLarga(date("Y-m-d"));
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : "";
		$periodos = isset($old["periodos"]) ? $old["periodos"] : "1";
		$empleado = isset($old["empleado"]) ? $old["empleado"] : "1";
	}
?>

<div class="row">
	<div class="col-md-8">
		<div class="card card-warning card-outline">
			<div class="card-body">
				<!-- Datos -->
				<div class="row my-2">
					<div class="col-md-6 form-group">
						<div class="form-group">
							<label for="trabajadorId">Nombre del Trabajador:</label>
							<select name="trabajadorId" id="trabajadorId" class="custom-select form-controls select2">
								<option value="0">Seleccione un empleado</option>
								<?php foreach($empleados as $empleado) { ?>
								<option value="<?php echo $empleado["id"]; ?>"
									<?php echo $empleado == $empleado["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($empleado["nombreCompleto"])); ?>
								</option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="col-md-6 form-group">
						<label for="">Fecha</label>
	  		        	<input type="date" name="fecha" id="fecha" value="<?php echo date('Y-m-d'); ?>" class="form-control form-control-sm">
	    			</div>

					<div class="col-md-6 form-group">
						<label for="">Horario Entrada</label>
					    <input type="time" name="horaEntrada" id="horaEntrada" value="08:00" class="form-control form-control-sm">
					</div>

					<div class="col-md-6 form-group">
						<label for="">Horario Salida</label>
					    <input type="time" name="horaSalida" id="horaSalida" value="18:00" class="form-control form-control-sm">
					</div>

					<div class="col-md-6 form-group">
						<label for="horasExtras">Horas Extras:</label>
					    <input type="text" name="horasExtras" id="horasExtras" value="0" class="form-control form-control-sm campoConDecimal">
					</div>

					<div class="col-12 row">

						<div class="col-md-4 form-group">
							<label for="falta">Faltas:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Faltas" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="falta" id="falta">
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-4 form-group">
							<label for="incapacidad">Incapacidad:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Incapacidad" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="incapacidad" id="incapacidad">
									</div>
								</div>
							</div>
						</div>
	
						<div class="col-md-4 form-group">
							<label for="vacaciones">Vacaciones:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Vacaciones" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="vacaciones" id="vacaciones">
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- OBSERVACION -->
					<div class="col-md-12 form-group">
						<label for="observacion">Observacion:</label>
						<textarea id="observacion" name="observacion" rows="4" class="form-control form-control-sm text-uppercase"></textarea>
					</div>

				</div>
				<!-- Boton -->
				<div class="col d-flex justify-content-end">
					<button type="button" class="btn btn-info btnAgregarJornada" id="btnAgregarJornada">
						<i class="fas fa-plus"></i> Agregar jornada
					</button>
				</div>
			</div>
		</div>
	</div>
</div>