<?php
	if ( isset($configuracionOrdenes->id) ) {
		$inicialEstatusId = isset($old["inicialEstatusId"]) ? $old["inicialEstatusId"] : $configuracionOrdenes->inicialEstatusId;

		$usuarioCreacionEliminarPartidas = isset($old["inicialEstatusId"]) ? ( isset($old["usuarioCreacionEliminarPartidas"]) && $old["usuarioCreacionEliminarPartidas"] == "on" ? true : false ) : $configuracionOrdenes->usuarioCreacionEliminarPartidas;
	} else {
		$inicialEstatusId = isset($old["inicialEstatusId"]) ? $old["inicialEstatusId"] : "";

		$usuarioCreacionEliminarPartidas = isset($old["usuarioCreacionEliminarPartidas"]) && $old["usuarioCreacionEliminarPartidas"] == "on" ? true : false;
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-header">
              <h3 class="card-title">Configuración general</h3>
            </div>

            <div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="form-group">
					<label for="inicialEstatusId">Estatus Inicial:</label>
					<select name="inicialEstatusId" id="inicialEstatusId" class="custom-select form-controls form-control-sms select2" style="width: 100%">
					<?php if ( isset($configuracionOrdenes->id) ) : ?>
					<!-- <select id="inicialEstatusId" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled> -->
					<?php else: ?>
						<option value="">Selecciona un Estatus</option>
					<?php endif; ?>
						<?php foreach($Status as $Estatus) { ?>
						<?php if ( $Estatus["ordenCompraAbierta"] ) : ?>
						<option value="<?php echo $Estatus["id"]; ?>"
							<?php echo $inicialEstatusId == $Estatus["id"] ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($Estatus["descripcion"])); ?>
						</option>
						<?php endif; ?>
						<?php } ?>
					</select>	
				</div>

				<hr class="mt-0">

				<div class="col form-group">
					<label for="usuarioCreacionEliminarPartidas">Permitir solo al usuario que crea la Requisición:</label>
					<!-- <label>Permitir solo al usuario que crea la Requisición:</label> -->
					<div class="input-group">
						<input type="text" class="form-control form-control-sm" value="Eliminar partidas" readonly>
						<div class="input-group-append">
							<div class="input-group-text">
								<input type="checkbox" name="usuarioCreacionEliminarPartidas" id="usuarioCreacionEliminarPartidas" <?php echo $usuarioCreacionEliminarPartidas ? "checked" : ""; ?>>
							</div>
						</div>
					</div>
				</div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info card-outline"> -->

		<!-- <button type="button" id="btnSend" class="btn btn-outline-primary">
			<i class="fas fa-save"></i> Actualizar
		</button>
		<div id="msgSend"></div> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-header">
			<h3 class="card-title">Perfiles con Permiso <span class="font-weight-bold">Modificar Estatus</span></h3>
			</div>

			<div class="card-body">

				<div class="accordion" id="accordionPerfiles">
					<?php foreach($perfilesPermisoModificarEstatus as $key => $perfil) { ?>

					<div class="card mb-0">
						<div class="card-header px-0 py-2" id="heading-<?php echo $key+1; ?>">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left font-weight-bold collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $key+1; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $key+1; ?>">
								<?php echo mb_strtoupper(fString($perfil["perfiles.nombre"])); ?>
								</button>
							</h2>
						</div>
						
						<div id="collapse-<?php echo $key+1; ?>" class="collapse" aria-labelledby="heading-<?php echo $key+1; ?>" data-parent="#accordionPerfiles">
							<div class="card-body p-2">

								<table class="table table-bordered table-striped" width="100%">

									<thead>
										<tr>
											<th>Estatus</th>
											<th class="text-center" style="width:55px;">Modificar</th>
											<th class="text-center" style="width:55px;">Automático</th>
										</tr> 
									</thead>

									<tbody>

									<?php foreach($Status as $servicioEstatus) { ?>
										
										
										<?php if ( ( $servicioEstatus["ordenCompraAbierta"] ) ) : ?>
											
											
										<?php $checkModificar = $configuracionOrdenes->checkPerfil($perfil["perfiles.nombre"], $servicioEstatus["descripcion"], "modificar");?>
										<tr>
											<td class="text-capitalize" style="padding: 4px 8px;"><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?></td>
											<td class="text-center" style="padding: 2px;">
												<input type="checkbox" name="perfiles[<?php echo $perfil["perfiles.nombre"]; ?>][<?php echo $servicioEstatus["descripcion"]; ?>][]" value="modificar" <?php echo $configuracionOrdenes->checkPerfil($perfil["perfiles.nombre"], $servicioEstatus["descripcion"], "modificar") ? "checked" : ""; ?>>
											</td>
											<td class="text-center" style="padding: 2px;">
												<input type="checkbox" name="perfiles[<?php echo $perfil["perfiles.nombre"]; ?>][<?php echo $servicioEstatus["descripcion"]; ?>][]" value="automatico" <?php echo $configuracionOrdenes->checkPerfil($perfil["perfiles.nombre"], $servicioEstatus["descripcion"], "automatico") ? "checked" : ""; ?> <?php echo ( !$checkModificar ) ? 'disabled' : '' ?>>
											</td>
										</tr>

										<?php endif; ?>
										<?php } ?>

									</tbody>

								</table>

							</div> <!-- <div class="card-body p-2"> -->
						</div>
					</div> <!-- <div class="card mb-0"> -->

					<?php } ?>
				</div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-warning card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->				

	<div class="col-md-6">

		<div class="card card-primary card-outline">

			<div class="card-header">
              <h3 class="card-title">Flujo</h3>
            </div>

            <div class="card-body">

            	<div class="accordion" id="accordionFlujo">
            		<?php foreach($Status as $key => $servicioEstatus) { ?>
					<?php if ( $servicioEstatus["ordenCompraAbierta"] ) : ?>

					<div class="card mb-0">
						<div class="card-header px-0 py-2" id="heading-flujo-<?php echo $key+1; ?>">
							<h2 class="mb-0">
								<button class="btn btn-link btn-block text-left font-weight-bold collapsed" type="button" data-toggle="collapse" data-target="#collapse-flujo-<?php echo $key+1; ?>" aria-expanded="false" aria-controls="collapse-flujo-<?php echo $key+1; ?>">
          						<?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
								</button>
							</h2>
    					</div>
    					<div id="collapse-flujo-<?php echo $key+1; ?>" class="collapse" aria-labelledby="heading-flujo-<?php echo $key+1; ?>" data-parent="#accordionFlujo">
							<div class="card-body p-2">

							<?php foreach($Status as $siguienteServicioEstatus) { ?>
							<?php if ( ( $siguienteServicioEstatus["ordenCompraAbierta"] || $siguienteServicioEstatus["requisicionCerrada"] ) &&  $servicioEstatus != $siguienteServicioEstatus ) : ?>

								<div class="form-group mb-1">
									<div class="input-group">
										<input type="text" class="form-control form-control-sm" value="<?php echo mb_strtoupper(fString($siguienteServicioEstatus["descripcion"])); ?>" readonly>
										<div class="input-group-append">
											<div class="input-group-text">
												<input type="checkbox" name="flujo[<?php echo $servicioEstatus["descripcion"]; ?>][]" value="<?php echo $siguienteServicioEstatus["descripcion"]; ?>" <?php echo $configuracionOrdenes->checkFlujo($servicioEstatus["descripcion"], $siguienteServicioEstatus["descripcion"]) ? "checked" : ""; ?>>
											</div>
										</div>
									</div>
								</div>

							<?php endif; ?>
							<?php } ?>

							</div>
						</div>

					</div>

					<?php endif; ?>
					<?php } ?>
            	</div>

            </div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-primary card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->


	
</div> <!-- <div class="row"> -->