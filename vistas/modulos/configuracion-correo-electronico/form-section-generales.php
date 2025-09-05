<div class="row">

	<div class="col-xl-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="servidor">Servidor:</label>
						<input type="text" name="servidor" value="<?php echo fString($servidor); ?>" class="form-control form-control-sm" placeholder="Ingresa el servidor (SMTP)">
					</div>

					<div class="col-md-3 form-group">
						<label for="puerto">Puerto:</label>
						<input type="text" name="puerto" value="<?php echo fString($puerto); ?>" class="form-control form-control-sm campoSinDecimal" placeholder="Ingresa el puerto" maxlength="4">
					</div>

					<div class="col-md-3 form-group">
						<label for="puertoSSL">Seguridad:</label>
						<div class="input-group">
							<input type="text" class="form-control form-control-sm" value="SSL" readonly>
							<div class="input-group-append">
								<div class="input-group-text">
									<input type="checkbox" name="puertoSSL" id="puertoSSL" <?php echo $puertoSSL ? "checked" : ""; ?>>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="usuario">Usuario:</label>
						<input type="text" name="usuario" value="<?php echo fString($usuario); ?>" class="form-control form-control-sm" placeholder="Ingresa el usuario">
					</div>

					<div class="col-md-6 form-group">
						<label for="contrasena">Contraseña:</label>
						<input type="password" name="contrasena" value="<?php echo $contrasena; ?>" class="form-control form-control-sm" placeholder="Ingresa la contraseña">
						<span class="text-muted">Dejar en blanco para no cambiar la contraseña</span>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="visualizacionCorreo">Correo de Visualización:</label>
						<input type="email" name="visualizacionCorreo" value="<?php echo fString($visualizacionCorreo); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el correo de visualización">
					</div>

					<div class="col-md-6 form-group">
						<label for="visualizacionNombre">Nombre de Visualización:</label>
						<input type="text" name="visualizacionNombre" value="<?php echo fString($visualizacionNombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre de visualización">
					</div>
				</div>

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="respuestaCorreo">Correo de Respuesta:</label>
						<input type="email" name="respuestaCorreo" value="<?php echo fString($respuestaCorreo); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el correo de respuesta">
					</div>

					<div class="col-md-6 form-group">
						<label for="respuestaNombre">Nombre de Respuesta:</label>
						<input type="text" name="respuestaNombre" value="<?php echo fString($respuestaNombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre de respuesta">
					</div>
				</div>

				<hr class="my-2">

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="comprobacionCorreo">Correo de Comprobación:</label>
						<div class="input-group input-group-sm">
							<input type="email" name="comprobacionCorreo" value="<?php echo fString($comprobacionCorreo); ?>" class="form-control text-lowercase" placeholder="Ingresa el correo de comprobación">
							<div class="input-group-append">
								<button type="button" id="btnComprobar" class="btn btn-info float-right" <?php if ( is_null($configuracionCorreoElectronico->comprobacionCorreo) ) echo 'disabled'; ?>>
									<i class="fas fa-envelope"></i> Enviar
								</button>
							</div>
						</div>
						<div id="msgComprobar"></div>
					</div>
				</div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
