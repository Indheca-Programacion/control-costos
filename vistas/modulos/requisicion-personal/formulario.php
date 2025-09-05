<?php
	$archivo = '';
	if ( isset($requisicion->id) ) {
		$contrato = isset($requisicion->contrato) ? $requisicion->contrato : "";
		$observacion = isset($requisicion->observacion) ? $requisicion->observacion : "";
		$orden_trabajo = isset($requisicion->orden_trabajo) ? $requisicion->orden_trabajo : "";
		$cargo = isset($requisicion->cargo) ? $requisicion->cargo : "";
		$area = isset($requisicion->area) ? $requisicion->area : "";
		$departamento = isset($requisicion->departamento) ? $requisicion->departamento : "";
		$jefe_inmediato = isset($requisicion->jefe_inmediato) ? $requisicion->jefe_inmediato : "";
		$funciones = isset($requisicion->funciones) ? $requisicion->funciones : "";		 
		$categoria = isset($requisicion->categoria) ? $requisicion->categoria :"";
		$origen = isset($requisicion->origen) ? $requisicion->origen :"";
		$razon = isset($requisicion->razon) ? $requisicion->razon :"";
		$fecha_cubrir = isset($requisicion->fecha_cubrir) ? $requisicion->fecha_cubrir :"";
		$edad_init = isset($requisicion->edad_init) ? $requisicion->edad_init :"";
		$edad_end = isset($requisicion->edad_end) ? $requisicion->edad_end :"";
		$especialidad = isset($requisicion->especialidad) ? $requisicion->especialidad :"";
		$postgrado = isset($requisicion->postgrado) ? $requisicion->postgrado :"";
		$licenciatura = isset($requisicion->licenciatura) ? $requisicion->licenciatura :"";
		$carrera = isset($requisicion->carrera) ? $requisicion->carrera :"";
		$otros_estudios = isset($requisicion->otros_estudios) ? $requisicion->otros_estudios :"";
		$dedicacion = isset($requisicion->dedicacion) ? $requisicion->dedicacion :"";
		$horario = isset($requisicion->horario) ? $requisicion->horario :"";
		$puesto = isset($requisicion->puesto) ? $requisicion->puesto :"";
		$trabajadores = isset($requisicion->trabajadores) ? json_decode($requisicion->trabajadores) :array();

	}
	
	// $json = json_encode($requisicion, JSON_PRETTY_PRINT);
	// echo '<pre>' . $json . '</pre>';
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">
					<!-- Folio -->
					<div class="col-6 form-group">
						<label for="folio">Folio:</label>
						<input type="text" name="folio" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->folio ?>" disabled>
					</div>
					<!-- Fecha de Requisicion -->
					<div class="col-6 form-group">
						<label for="fecha_requisicion">Fecha de Requisicion:</label>
						<input type="date" name="fecha_requisicion" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->fecha_requisicion ?>" disabled>
					</div>
					<!-- Cantidad -->
					<div class="col-6 form-group">
						<label for="cantidad">Cantidad:</label>
						<input type="text" name="cantidad" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->cantidad ?>">
					</div>
					<!-- Salario Semanal -->
					<div class="col-6 form-group">
						<label for="salario_semanal">Salario Semanal:</label>
						<input type="text" name="salario_semanal" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->salario_semanal ?>">
					</div>
					<!-- Viaticos -->
					<!-- <div class="col-6 form-group">
						<label for="viaticos">Viaticos:</label>
						<input type="text" name="viaticos" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->viaticos ?>">
					</div> -->
					<!-- Otros -->
					<!-- <div class="col-6 form-group">
						<label for="otros">Otros:</label>
						<input type="text" name="otros" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->otros ?>">
					</div> -->
					<!-- Fecha inicio -->
					<div class="col-6 form-group">
						<label for="fecha_inicio">Fecha de Inicio:</label>
						<input type="date" name="fecha_inicio" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->fecha_inicio ?>">
					</div>
					<!-- Fecha termino -->
					<div class="col-6 form-group">
						<label for="fecha_fin">Fecha de Termino:</label>
						<input type="date" name="fecha_fin" class="form-control form-control-sm text-uppercase" value="<?= $requisicion->fecha_fin ?>">
					</div>
					<!-- Numero de contrato -->
					<div class="col-6 form-group">
						<label for="contrato">Numero de Contrato:</label>
						<input type="text" name="contrato" class="form-control form-control-sm text-uppercase" placeholder="Ingrese el numero de contrato" value="<?= $contrato ?>" >
					</div>
					<!-- Orden de Trabajo -->
					<div class="col-6 form-group">
						<label for="orden_trabajo">Orden de trabajo:</label>
						<input type="text" name="orden_trabajo" class="form-control form-control-sm text-uppercase" placeholder="Ingrese el orden de trabajo" value="<?= $orden_trabajo ?>" >
					</div>
					<!-- Jefe Inmediato -->
					<div class="col-6 form-group">
						<label for="jefe_inmediato">Jefe Inmediato (solicitante):</label>
						<input type="text" name="jefe_inmediato" class="form-control form-control-sm text-uppercase" placeholder="Ingrese el jefe inmediato" value="<?= $jefe_inmediato ?>" >
					</div>
					<!-- Cargo que desempeña -->
					<div class="col-6 form-group">
						<label for="cargo">Cargo que desempeña:</label>
						<input type="text" name="cargo" class="form-control form-control-sm text-uppercase" placeholder="Ingrese el cargo que desempeña" value="<?= $cargo ?>" >
					</div>
					<!-- Area -->
					<div class="col-6 form-group">
						<label for="area">Area:</label>
						<input type="text" name="area" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el area" value="<?= $area ?>" >
					</div>
					<!-- Departamento -->
					<div class="col-6 form-group">
						<label for="departamento">Departamento:</label>
						<input type="text" name="departamento" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el departamento" value="<?= $departamento ?>" >
					</div>
					<!-- Categoria -->
					<div class="col-12">
						<label for="">Categoria:</label>
						<div class="row">
							<div class="col-6">
								<div class="custom-control custom-radio">
									<input type="radio" id="obra" name="categoria" value="1" class="custom-control-input" <?php if ($categoria == 1) echo 'checked' ?>>
									<label class="custom-control-label" for="obra">Obra</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="administracion" value="2" name="categoria" class="custom-control-input" <?php if ($categoria == 2) echo 'checked' ?>>
									<label class="custom-control-label" for="administracion">Administracion</label>
								</div>
							</div>
							<div class="col-6">
								<div class="custom-control custom-radio">
									<input type="radio" id="taller" name="categoria" value="3" class="custom-control-input" <?php if ($categoria == 3) echo 'checked' ?>>
									<label class="custom-control-label" for="taller">Taller</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="arrendamiento" value="4" name="categoria" class="custom-control-input" <?php if ($categoria == 4) echo 'checked' ?>>
									<label class="custom-control-label" for="arrendamiento">Arendamiento</label>
								</div>
							</div>
						</div>
					</div>
					<!-- Observacion -->
					<div class="col-12 form-group">
						<label for="observacion">Observacion:</label>
						<textarea  name="observacion" class="form-control form-control-sm text-uppercase" rows="5" placeholder="" value=""><?php echo $observacion; ?> </textarea>
					</div>
					<!-- Trabajadores -->
					<div class="col-12 form-group">
						<label for="">Trabajadores:</label>
						<select name="trabajadores[]" id="trabajadores" class="custom-select form-controls form-control-sms select2" multiple="multiple" style="width: 100%.">
							<?php foreach($empleados as $empleado) { ?>
							<option value="<?php echo $empleado["id"]; ?>"
								<?php echo in_array($empleado["id"], $trabajadores) ? ' selected' : ''; ?>

								><?php echo mb_strtoupper(fString($empleado["nombreCompleto"])); ?>
							</option>
							<?php } ?>
						</select>	
					</div>
				</div> <!-- <div class="row"> -->

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> --> 

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-body">
				
				<div class="row">

					<!-- Informacion sobre la vacante -->
					<h4 class="col-12">I. Informacion sobre la vacante</h4>
					<div class="col-6 form-group">
						<div class="custom-control custom-radio">
							<input type="radio" id="customRadio1" value="1" name="razon" class="custom-control-input" <?php if ($razon == 1) echo 'checked' ?>>
							<label class="custom-control-label" for="customRadio1">Creacion de cargo</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="customRadio2" value="2" name="razon" class="custom-control-input" <?php if ($razon == 2) echo 'checked' ?>>
							<label class="custom-control-label" for="customRadio2">Reemplazo temporal</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="customRadio3" value="3" name="razon" class="custom-control-input" <?php if ($razon == 3) echo 'checked' ?>>
							<label class="custom-control-label" for="customRadio3">Reemplazo definitivo</label>
						</div>
					</div>
					<!-- Origen vacante -->
					<div class="col-12 form-group">
						<label for="">La vacante se produjo por:</label>
						<div class="row">
							<div class="col-6">
								<div class="custom-control custom-radio">
									<input type="radio" id="renuncia" value="1" name="origen" class="custom-control-input"  <?php if ($origen == 1) echo 'checked' ?>>
									<label class="custom-control-label" for="renuncia">Renuncio el titular</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="cancelacion" value="2" name="origen" class="custom-control-input" <?php if ($origen == 2) echo 'checked' ?>>
									<label class="custom-control-label" for="cancelacion">Cancelacion de contrato</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="incrementoAct" value="3" name="origen" class="custom-control-input" <?php if ($origen == 3) echo 'checked' ?>>
									<label class="custom-control-label" for="incrementoAct">Incremento de Actividades</label>
								</div>
							</div>
							<div class="col-6">
								<div class="custom-control custom-radio">
									<input type="radio" id="incapacidad" value="4" name="origen" class="custom-control-input" <?php if ($origen == 4) echo 'checked' ?>>
									<label class="custom-control-label" for="incapacidad">Incapacidad</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="vacaciones" value="5" name="origen" class="custom-control-input" <?php if ($origen == 5) echo 'checked' ?>>
									<label class="custom-control-label" for="vacaciones">Vacaciones</label>
								</div>
								<div class="custom-control custom-radio">
									<input type="radio" id="promociontraslado" value="6" name="origen" class="custom-control-input" <?php if ($origen == 6) echo 'checked' ?>>
									<label class="custom-control-label" for="promociontraslado">Promocion o traslado</label>
								</div>
							</div>
						</div>
					</div>
					<!-- Fecha para cubrir  -->
					<div class="col-6 form-group">
						<label for="fecha_cubrir">Fecha para cubrir la vacante</label>
						<input type="date" name="fecha_cubrir" class="form-control form-control-sm text-uppercase" value="<?= $fecha_cubrir ?>" >
					</div>
					<!-- Edad sugerida -->
					<div class="col-12 form-group">
						<label for="">Edad sugerida:</label>
						<div class="row">
							<div class="col-5">
								<input type="text" name="edad_init" class="form-control form-control-sm text-uppercase" value="<?= $edad_init ?>">
							</div>
							<div class="col-2 d-flex align-items-center justify-content-center">
								<label for=""> Y </label>
							</div>
							<div class="col-5">
								<input type="text" name="edad_end" class="form-control form-control-sm text-uppercase" value="<?= $edad_end ?>">
							</div>
						</div>
					</div>
					<!--II. Perfil de Educacion -->
					<h4 class="col-12">II. Perfil de Educacion</h4>
					<!-- Especialidad -->
					<div class="col-6 form-group">
						<label for="">Especialidad:</label>
						<input type="text" name="especialidad" class="form-control form-control-sm text-uppercase"  value="<?= $especialidad ?>">
					</div>
					<!-- Postgrado -->
					<div class="col-6 form-group">
						<label for="">Post-Grado:</label>
						<input type="text" name="posgrado" class="form-control form-control-sm text-uppercase" value="<?= $postgrado ?>">
					</div>
					<!-- Licenciatura -->
					<div class="col-6 form-group">
						<label for="">Licenciatura:</label>
						<input type="text" name="licenciatura" class="form-control form-control-sm text-uppercase" value="<?= $licenciatura ?>">
					</div>
					<!-- Carrera Tecnica -->
					<div class="col-6 form-group">
						<label for="">Carrera Tecnica:</label>
						<input type="text" name="carrera" class="form-control form-control-sm text-uppercase" value="<?= $carrera ?>">
					</div>
					<!-- Otras -->
					<div class="col-6 form-group">
						<label for="">Otras:</label>
						<input type="text" name="otros_estudios" class="form-control form-control-sm text-uppercase" value="<?= $otros_estudios ?>">
					</div>
					<!-- III. Informacion sobre el cargo -->
					<h4 class="col-12">III. Informacion sobre el cargo</h4>
					<!-- Nombre del Puesto -->
					<div class="row col-12 form-group">
						<div class="col-6 form-group">
							<label for="">Nombre del puesto:</label>
							<input type="text" name="puesto" class="form-control form-control-sm text-uppercase" value="<?= $descripcion ?>" disabled>
						</div>
					</div>
					<!-- Dedicacion -->
					<div class="col-6 form-group">
						<label for="">Dedicacion</label>
						<div class="custom-control custom-radio">
							<input type="radio" id="turnocompleto" value="1" name="dedicacion" class="custom-control-input" <?php if ($dedicacion == 1) echo 'checked' ?>>
							<label class="custom-control-label" for="turnocompleto">Turno Completo</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="mediotiempo" value="2" name="dedicacion" class="custom-control-input" <?php if ($dedicacion ==2) echo 'checked' ?>>
							<label class="custom-control-label" for="mediotiempo">Medio Tiempo</label>
						</div>
					</div>
					<!-- Horario -->
					<div class="col-6 form-group">
						<label for="">Horario</label>
						<div class="custom-control custom-radio">
							<input type="radio" id="diurno" value="1" name="horario" class="custom-control-input" <?php if ($horario == 1) echo 'checked' ?>>
							<label class="custom-control-label" for="diurno">Diurno</label>
						</div>
						<div class="custom-control custom-radio">
							<input type="radio" id="nocturno" value="2" name="horario" class="custom-control-input" <?php if ($horario == 2) echo 'checked' ?>>
							<label class="custom-control-label" for="nocturno">Nocturno</label>
						</div>
					</div>
					<!-- funciones -->
					<div class="col form-group">
						<label for="funciones">Funciones a realizar en el puesto</label>
						<textarea name="funciones" id="funciones" class="form-control" rows="5"><?php echo $funciones ?></textarea>
					</div>
				</div>

			</div>

		</div>

	</div>

</div>
