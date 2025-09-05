<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<!-- <div class="card-header"> -->
              <!-- <h3 class="card-title">Perfiles que reciben un correo al <span class="font-weight-bold">Crear Requisición</span></h3> -->
              <!-- <h3 class="card-title">Perfiles con Permiso <span class="font-weight-bold">Actualizar</span></h3> -->
            <!-- </div> -->

			<div class="card-body">

				<div class="form-group">
					<label for="perfilesCrear">Perfiles que reciben un correo al crear la Requisición:</label>
					<select name="perfilesCrear[]" id="perfilesCrear" class="custom-select form-controls form-control-sms select2" multiple="multiple" style="width: 100%">
						<?php foreach($perfiles as $perfil) { ?>
						<option value="<?php echo $perfil["id"]; ?>"
							<?php echo in_array($perfil["id"], $configuracionCorreoElectronico->perfilesCrear) ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($perfil["nombre"])); ?>
						</option>
						<?php } ?>
					</select>
				</div>

				<hr>

				<div class="form-group">
					<label for="estatusModificarUsuarioCreacion">El usuario que crea la Requisición recibe un correo al cambiar al estatus:</label>
					<select name="estatusModificarUsuarioCreacion[]" id="estatusModificarUsuarioCreacion" class="custom-select form-controls form-control-sms select2" multiple="multiple" style="width: 100%">
						<?php foreach($Estatus as $servicioEstatus) { ?>
						<?php if ( $servicioEstatus["requisicionAbierta"] || $servicioEstatus["requisicionCerrada"] ) : ?>
						<option value="<?php echo $servicioEstatus["id"]; ?>"
							<?php echo in_array($servicioEstatus["id"], $configuracionCorreoElectronico->estatusModificarUsuarioCreacion) ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
						</option>
						<?php endif; ?>
						<?php } ?>
					</select>	
				</div>

				<label>Perfiles que reciben un correo al cambiar al estatus</label>

				<?php foreach($Estatus as $servicioEstatus) { ?>
				<?php if ( $servicioEstatus["requisicionAbierta"] || $servicioEstatus["requisicionCerrada"] ) : ?>
				<div class="form-group">
					<label for="estatusModificarPerfiles-<?php echo $servicioEstatus["id"]; ?>" style="color: #007bff;"><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>:</label>
					<select name="estatusModificarPerfiles[<?php echo $servicioEstatus["id"]; ?>][]" id="estatusModificarPerfiles-<?php echo $servicioEstatus["id"]; ?>" class="custom-select form-controls form-control-sms select2 estatusModificarPerfiles" multiple="multiple" data-estatus-id="<?php echo $servicioEstatus["id"]; ?>" style="width: 100%">
						<?php foreach($perfiles as $perfil) { ?>
						<option value="<?php echo $perfil["id"]; ?>" <?php echo ( isset($configuracionCorreoElectronico->estatusModificarPerfiles[$servicioEstatus["id"]]) && in_array($perfil["id"], $configuracionCorreoElectronico->estatusModificarPerfiles[$servicioEstatus["id"]]) ) ? 'selected' : ''; ?>>
							<?php echo mb_strtoupper(fString($perfil["nombre"])); ?>
						</option>
						<?php } ?>
					</select>
				</div>
				<?php endif; ?>
				<?php } ?>

				<button type="button" id="btnActualizarAvisos" class="btn btn-outline-primary" disabled>
					<i class="fas fa-envelope"></i> Actualizar avisos
				</button>
				<div class="mt-2" id="msgActualizarAvisos"></div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-body">

				<label>Enviar un correo al subir un Documento:</label>
				<div class="form-group mb-1">
					<div class="input-group">
						<input type="text" class="form-control form-control-sm" value="Comprobante de Pago" readonly>
						<div class="input-group-append">
							<div class="input-group-text">
								<input type="checkbox" name="uploadDocumentos[]" class="uploadDocumentos" value="1" <?php echo in_array(1, $configuracionCorreoElectronico->documentos->uploadDocumentos) ? "checked" : ""; ?>>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group mb-1">
					<div class="input-group">
						<input type="text" class="form-control form-control-sm" value="Orden de Compra" readonly>
						<div class="input-group-append">
							<div class="input-group-text">
								<input type="checkbox" name="uploadDocumentos[]" class="uploadDocumentos" value="2" <?php echo in_array(2, $configuracionCorreoElectronico->documentos->uploadDocumentos) ? "checked" : ""; ?>>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group mb-1">
					<div class="input-group">
						<input type="text" class="form-control form-control-sm" value="Factura" readonly>
						<div class="input-group-append">
							<div class="input-group-text">
								<input type="checkbox" name="uploadDocumentos[]" class="uploadDocumentos" value="3" <?php echo in_array(3, $configuracionCorreoElectronico->documentos->uploadDocumentos) ? "checked" : ""; ?>>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group mb-1">
					<div class="input-group">
						<input type="text" class="form-control form-control-sm" value="Cotización" readonly>
						<div class="input-group-append">
							<div class="input-group-text">
								<input type="checkbox" name="uploadDocumentos[]" class="uploadDocumentos" value="4" <?php echo in_array(4, $configuracionCorreoElectronico->documentos->uploadDocumentos) ? "checked" : ""; ?>>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="input-group">
						<input type="text" class="form-control form-control-sm" value="Vale de Almacén" readonly>
						<div class="input-group-append">
							<div class="input-group-text">
								<input type="checkbox" name="uploadDocumentos[]" class="uploadDocumentos" value="5" <?php echo in_array(5, $configuracionCorreoElectronico->documentos->uploadDocumentos) ? "checked" : ""; ?>>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="usuarioUploadDocumento">El usuario recibe un correo cuando:</label>
					<div class="input-group">
						<input type="text" class="form-control form-control-sm" value="Sube un Documento" readonly>
						<div class="input-group-append">
							<div class="input-group-text">
								<input type="checkbox" name="usuarioUploadDocumento" id="usuarioUploadDocumento" <?php echo $configuracionCorreoElectronico->documentos->usuarioUploadDocumento ? "checked" : ""; ?>>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="perfilesUploadDocumento">Perfiles que reciben un correo cuando se sube un Documento:</label>
					<select name="perfilesUploadDocumento[]" id="perfilesUploadDocumento" class="custom-select form-controls form-control-sms select2" multiple="multiple" style="width: 100%">
						<?php foreach($perfiles as $perfil) { ?>
						<option value="<?php echo $perfil["id"]; ?>"
							<?php echo in_array($perfil["id"], $configuracionCorreoElectronico->documentos->perfilesUploadDocumento) ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($perfil["nombre"])); ?>
						</option>
						<?php } ?>
					</select>
				</div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-warning card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->