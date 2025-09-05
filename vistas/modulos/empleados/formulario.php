<?php
	if ( isset($empleado->id) ) {
		// var_dump($empleado);
		$activo = isset($old["nombre"]) ? ( isset($old["activo"]) && $old["activo"] == "on" ? true : false ) : $empleado->activo;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $empleado->nombre;
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : $empleado->apellidoPaterno;
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : $empleado->apellidoMaterno;
		$correo = isset($old["correo"]) ? $old["correo"] : $empleado->correo;
		$rfc = isset($old["rfc"]) ? $old["rfc"] : $empleado->rfc;
		$seguro = isset($old["seguro"]) ? $old["seguro"] : $empleado->seguro;
		$cbanco = isset($old["cbanco"]) ? $old["cbanco"] : $empleado->cbanco;
		$curp = isset($old["curp"]) ? $old["curp"] : $empleado->curp;


		$fotoAnterior = $empleado->foto;
		$foto = $empleado->foto;
	} else {
		$activo = isset($old["activo"]) && $old["activo"] == "on" ? true : false;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : "";
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : "";
		$correo = isset($old["correo"]) ? $old["correo"] : "";
		$rfc = isset($old["rfc"]) ? $old["rfc"] : "";
		$seguro = isset($old["seguro"]) ? $old["seguro"] : "";
		$curp = isset($old["curp"]) ? $old["curp"] : "";
		$cbanco = isset($old["cbanco"]) ? $old["cbanco"] : "";

		$foto = null;
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-header">
              <h3 class="card-title">Datos generales</h3>
            </div>

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">
				
					<div class="col-md-6">

						<div class="form-group">
							<label for="activo">Activo:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Empleado Activo" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="activo" id="activo" <?php echo $activo ? "checked" : ""; ?>>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<label for="nombre">Nombre(s):</label>
							<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre(s) del empleado">
						</div>

						<div class="form-group">
							<label for="apellidoPaterno">Apellido Paterno:</label>
							<input type="text" name="apellidoPaterno" value="<?php echo fString($apellidoPaterno); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el apellido paterno">
						</div>

						<div class="form-group">
							<label for="apellidoMaterno">Apellido Materno:</label>
							<input type="text" name="apellidoMaterno" value="<?php echo fString($apellidoMaterno); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el apellido materno">
						</div>
					</div>

					<?php
						if ( is_null($foto) ) {
							$previsual = App\Route::rutaServidor()."vistas/img/empleados/default/anonymous.jpg";
						} else {
							$previsual = App\Route::rutaServidor().$foto;
						}
					?>
					<div class="col-md-6 form-group">
						<label for="foto">Imágen:</label>
						<picture>
							<img src="<?php echo $previsual; ?>" id="imgFoto" class="img-fluid img-thumbnail previsualizar" style="width: 100%">
						</picture>
						<span class="text-muted">Presione sobre la imágen si desea cambiarla (Resolución recomendada 500 x 500 pixeles)</span>
						<?php if ( isset($empleado->id) ) : ?>
							<input type="hidden" name="fotoAnterior" value="<?php echo $fotoAnterior; ?>">
						<?php endif; ?>
						<input type="file" class="form-control form-control-sm" id="foto" name="foto" style="display: none">
					</div>

				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="correo">Correo Electrónico:</label>
							<input type="email" name="correo" value="<?php echo fString($correo); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el correo electrónico">
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="rfc">RFC:</label>
							<input type="text" name="rfc" value="<?php echo fString($rfc); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el rfc">
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="seguro">Seguro Social:</label>
							<input type="number" name="seguro" value="<?php echo fString($seguro); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el numero de seguro social">
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="cbanco">Cuenta Bancaria:</label>
							<input type="number" name="cbanco" value="<?php echo fString($cbanco); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la cuenta Bancaria">
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="curp">Curp:</label>
							<input type="text" name="curp" value="<?php echo fString($curp); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el curp">
						</div>
					</div>
				</div>
			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-header">
              <h3 class="card-title">Funciones</h3>
            </div>

			<div class="card-body">

				<?php foreach($empleadoFunciones as $funcion) { ?>
				<div class="form-check">
					<input type="checkbox" name="funciones[]" id="<?=fString($funcion["nombreCorto"])?>" value="<?php echo fString($funcion["id"]); ?>" <?php echo in_arrayi($funcion["id"], $empleado->funciones) ? "checked" : "" ; ?> class="form-check-input">
					<label class="form-check-label text-uppercase" for="<?=fString($funcion["nombreCorto"])?>">
						<?php echo fString($funcion["descripcion"]); ?>
					</label>
				</div>
				<?php } ?>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
