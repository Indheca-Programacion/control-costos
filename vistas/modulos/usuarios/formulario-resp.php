<?php
	if ( isset($usuario->id) ) {
		$usuarioField = $usuario->usuario;
		// $activo = isset($old["nombre"]) ? ( isset($old["activo"]) && $old["activo"] == "on" ? true : false ) : $usuario->activo;
		$contrasena = isset($old["contrasena"]) ? $old["contrasena"] : "";
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $usuario->nombre;
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : $usuario->apellidoPaterno;
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : $usuario->apellidoMaterno;
		$correo = isset($old["correo"]) ? $old["correo"] : $usuario->correo;
		$fotoAnterior = $usuario->foto;
		$foto = $usuario->foto;
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $usuario->empresaId;
	} else {
		$usuarioField = isset($old["usuario"]) ? $old["usuario"] : "";
		// $activo = isset($old["activo"]) && $old["activo"] == "on" ? true : false;
		$contrasena = isset($old["contrasena"]) ? $old["contrasena"] : "";
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : "";
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : "";
		$correo = isset($old["correo"]) ? $old["correo"] : "";
		// $fotoAnterior = null;
		$foto = null;
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="box box-info">

			<div class="box-header with-border">
				<h3 class="box-title">Datos generales</h3>
			</div>

			<div class="box-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">
				
					<div class="col-md-6">

					<!-- <div class="row"> -->
						<div class="form-group">
							<label for="usuario">Usuario:</label>
							<?php if ( isset($usuario->id) ) : ?>
								<input type="text" value="<?php echo fString($usuarioField); ?>" class="form-control form-control-sm text-uppercase" disabled>
							<?php else: ?>
								<input type="text" name="usuario" value="<?php echo fString($usuarioField); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el usuario">
							<?php endif; ?>
						</div>

					<!-- 	<div class="col-md-6 form-group form-check">
							<label for="activo">Activo:</label>
							<div class="input-group">
								<input type="checkbox" name="activo" <?php echo $activo ? "checked" : ""; ?> class="form-check-input">
							</div>
						</div> -->
					<!-- </div> -->

						<div class="form-group">
							<label for="nombre">Nombre(s):</label>
							<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre(s) del usuario">
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
							$previsual = App\Route::rutaServidor()."vistas/img/usuarios/default/anonymous.png";
						} else {
							$previsual = App\Route::rutaServidor().$foto;
						}
					?>
					<div class="col-md-6 form-group">
						<label for="foto">Imágen:</label>
						<figure style="height: 18rem;">
							<img src="<?php echo $previsual; ?>" id="imgFoto" class="img-thumbnail previsualizar" style="height: 100%;">
						</figure>
						<span class="help-block">Presione sobre la imágen si desea cambiarla (Resolución recomendada 500 x 500 pixeles)</span>
						<?php if ( isset($usuario->id) ) : ?>
							<input type="hidden" name="fotoAnterior" value="<?php echo $fotoAnterior; ?>">
						<?php endif; ?>
						<input type="file" class="form-control form-control-sm" id="foto" name="foto" style="display: none">
					</div>

				</div>

				<div class="form-group">
					<label for="correo">Correo Electrónico:</label>
					<input type="email" name="correo" value="<?php echo fString($correo); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el correo electrónico">
				</div>

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="contrasena">Contraseña:</label>
						<input type="password" name="contrasena" value="<?php echo $contrasena; ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la contraseña">
						<?php if ( isset($usuario->id) ) : ?>
							<span class="help-block">Dejar en blanco para no cambiar la contraseña</span>
						<?php endif; ?>
					</div>
				</div>

				<div class="form-group">
					<label for="empresaId">Empresa:</label>
					<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
					<?php if ( $usuarioAutenticado->checkAdmin() || App\Controllers\Autorizacion::permiso($usuarioAutenticado, "usuarios", "actualizar") ) : ?>
					<select name="empresaId" id="empresaId" class="custom-select form-controls form-control-sms select2" style="width: 100%">
						<option value="">Selecciona una Empresa</option>
					<?php else: ?>
					<select id="empresaId" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>
					<?php endif; ?>
						<?php foreach($empresas as $empresa) { ?>
						<option value="<?php echo $empresa["id"]; ?>"
							<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
						</option>
						<?php } ?>
					</select>	
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="box box-warning">

			<div class="box-header with-border">
				<h3 class="box-title">Perfiles</h3>
			</div>

			<div class="box-body">

				<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
			    <?php if ( $usuarioAutenticado->checkAdmin() || App\Controllers\Autorizacion::permiso($usuarioAutenticado, "usuarios", "actualizar") ) : ?>

					<?php foreach($perfiles as $perfil) { ?>
					<div class="form-check">
						<div class="input-group perfiles">
							<?php if ( mb_strtolower($usuarioField) == mb_strtolower(CONST_ADMIN) && mb_strtolower($perfil["nombre"]) == mb_strtolower(CONST_ADMIN) ) : ?>
								<input type="hidden" name="perfiles[]" value="<?php echo fString($perfil["nombre"]); ?>" checked>
								<input type="checkbox" value="<?php echo fString($perfil["nombre"]); ?>" <?php echo in_arrayi($perfil["nombre"], $usuario->perfiles) ? "checked" : "" ; ?> class="form-check-input" disabled>
								<span class="text-uppercase">
									<?php echo fString($perfil["nombre"]); ?>
								</span>
							<?php else: ?>
								<input type="checkbox" name="perfiles[]" value="<?php echo fString($perfil["nombre"]); ?>" <?php echo in_arrayi($perfil["nombre"], $usuario->perfiles) ? "checked" : "" ; ?> class="form-check-input">
								<span class="text-uppercase">
									<?php echo fString($perfil["nombre"]); ?>
								</span>
							<?php endif; ?>
						</div>
					</div>
					<?php } ?>

				<?php else: ?>

					<ul class="list-group">
						<?php if ( $usuario->perfiles ) : ?>
							<?php foreach($usuario->perfiles as $perfil) { ?>
								<li class="list-group-item text-uppercase"><?php echo fString($perfil); ?></li>
							<?php } ?>
						<?php else: ?>
							<li class="list-group-item">No tiene perfiles asignados</li>
						<?php endif; ?>
					</ul>

			    <?php endif; ?>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
