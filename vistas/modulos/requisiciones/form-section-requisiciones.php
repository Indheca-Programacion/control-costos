<?php

	use App\Route;
?>
<input type="hidden" id="subtotal" value="<?php echo $subtotal; ?>">
<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="proveedorId" id="proveedorId" value="<?= $proveedorSeleccionado ?>">
				<input type="hidden" name="_token" id="_token" value="<?php echo createToken(); ?>">
				<?php if ( isset($requisicion->id) ) : ?>
				<input type="hidden" id="requisicionId" value="<?php echo $requisicion->id; ?>">
				<?php endif; ?>

				<div class="row">

					<div class="col-md-6 form-group">

						<label for="empresaId">Empresa:</label>
						<select id="empresaId" class="custom-select form-controls select2" disabled>
						<?php if ( isset($requisicion->id) ) : ?>
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

					<div class="col-md-6 form-group">
							<label for="">Obra:</label>
							<input type="text" name="" id="" class="form-control form-control-sm text-uppercase" disabled value="<?= $obraNombre ?>">
							<?php if ( $permitirEditar && ( is_null($requisicion->usuarioIdAutorizacion) || $actualEstatusId["id"] == 16 ) ) : ?>
								<button type="button" class="btn btn-warning w-100 mt-2" id="btnSolicitudCambioObra"  data-toggle="modal" data-target="#modalSolicitudCambioObra">
									Solicitar cambio de obra
								</button>
							<?php endif; ?>
					</div>
				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="folio">Folio de Requisición:</label>						
						<input type="text" id="folio" name="folio" value="<?php echo fString($folio); ?>" class="form-control form-control-sm text-uppercase" placeholder="">
					</div>

					<div class="col-md-6 form-group">

						<input type="hidden" name="actualEstatusId" id="actualEstatusId" value="<?php echo $actualEstatusId["id"]; ?>">

						<label for="estatusId">Estatus:</label>
						<?php if ( !isset($requisicion->id) || ( $formularioEditable && $permitirModificarEstatus ) ) : ?>
						<select name="estatusId" id="estatusId" class="custom-select form-controls select2">
						<?php else: ?>
						<select id="estatusId" class="custom-select form-controls select2" disabled>
						<?php endif; ?>
							<?php if ( !isset($requisicion->id) ) : ?>
							<!-- <option value="">Selecciona un Estatus</option> -->
							<?php endif; ?>
							<?php foreach($servicioStatus as $servicioEstatus) { ?>
							<?php if ( $servicioEstatus["requisicionAbierta"] || ( $servicioEstatus["requisicionCerrada"] && isset($requisicion->id) ) ) : ?>
							<option value="<?php echo $servicioEstatus["id"]; ?>"
								<?php echo $actualEstatusId["id"] == $servicioEstatus["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
							</option>
							<?php endif; ?>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">
						<label for="fechaRequerida">Fecha de Requisición:</label>						
						<div class="input-group date" id="fechaRequerida" data-target-input="nearest">
							<input type="text" name="fechaRequerida" id="fechaRequerida" value="<?= $fechaRequerida; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaRequerida">
							<div class="input-group-append" data-target="#fechaRequerida" data-toggle="datetimepicker">
								<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
							</div>
						</div>
					</div>

					<div class="col-md-6 form-group">
						<label for="tipoRequisicion">Tipo de Requisicion:</label>
						<select class="custom-select select2" id="tipoRequisicion" name="tipoRequisicion">
							<option value="0" <?php echo ($tipoRequisicion == 0) ? 'selected' : ''; ?>>Programada</option>
							<option value="1" <?php echo ($tipoRequisicion == 1) ? 'selected' : ''; ?>>Urgente</option>
						</select>
					</div>

					<div class="col-md-6 form-group">
						<label for="divisa">Divisa:</label>
						<select class="custom-select select2" name="divisa" id="divisa">
							<?php foreach($divisas as $divisa) { ?>
								<option value="<?php echo $divisa["id"]; ?>" <?php echo ($divisa["id"] == $divisaId) ? 'selected' : ''; ?>>
								<?php echo mb_strtoupper(fString($divisa["nombreCorto"])); ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<div class="col-md-6 form-group">
						<label for="presupuesto">Presupuesto:</label>
						<select class="custom-select select2" id="presupuesto" name="presupuesto">
							<option value="0" selected>General</option>
							<?php foreach($presupuestos as $presupuesto) { ?>
								<option value="<?php echo $presupuesto["id"]; ?>" <?php echo ($presupuesto["id"] == $presupuestoId) ? 'selected' : ''; ?>>
								<?php echo mb_strtoupper(fString($presupuesto["descripcion"])); ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<div class="col-md-6 form-group">
						<label for="direccion">Direccion de Entrega:</label>
						<input type="text" id="direccion" name="direccion" value="<?= $direccion ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la dirección">
					</div>

					<div class="col-md-6 form-group">
						<label for="especificaciones">Especificaciones:</label>
						<input type="text" id="especificaciones" name="especificaciones" value="<?= $especificaciones ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa las especificaciones">
					</div>

					<div class="col-md-6 form-group">
						<label for="categoriaOrden">Categoría de Orden de Compra:</label>
						<select name="categoriaId" id="categoriaId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
							<option value="">Selecciona una Categoría</option>
							<?php foreach($categoriasOrdenCompra as $categoria) : ?>
								<option value="<?php echo $categoria['id']; ?>" <?php echo ($categoria['id'] == $categoriaId) ? 'selected' : ''; ?>>
									<?php echo $categoria['nombre']; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="col-md-6 form-group">
						<label for="iva">I.V.A.:</label>
						<div class="input-group">
							<input type="number" name="iva" id="iva" class="form-control form-control-sm" placeholder="Ingresa el IVA" value="<?php echo $iva; ?>">
							<div class="input-group-append">
								<select id="ivaPorcentaje" class="form-control form-control-sm" style="width: 80px;">
									<option value="0" <?php echo ($iva == 0) ? 'selected' : ''; ?>>0%</option>
									<option value="16" <?php echo ($iva > 0) ? 'selected' : ''; ?>>16%</option>
								</select>
							</div>
						</div>
					</div> <!-- <div class="col-md-6 form-group"> -->

					<div class="col-md-6 form-group">
						<label for="retencionIva">Retencion I.V.A.:</label>
						<input type="number" id="retencionIva" name="retencionIva" class="form-control form-control-sm" placeholder="Ingresa la retención de IVA" value="<?php echo $retencionIva; ?>">
					</div> <!-- <div class="col-md-6 form-group"> -->

					<div class="col-md-6 form-group">
						<label for="retencionIsr">Retencion I.S.R.:</label>
						<input name="retencionIsr" type="text" id="retencionIsr" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa la retencion de IVA" value="<?php echo $retencionIsr; ?>">
					</div> <!-- <div class="col-md-6 form-group"> -->

					<div class="col-md-6 form-group">
						<label for="descuento">Descuentos:</label>
						<input name="descuento" type="text" id="descuento" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa la retencion de IVA" value="<?php echo $descuento; ?>">
					</div> <!-- <div class="col-md-6 form-group"> -->

				</div>

				<?php if ( isset($requisicion->id) && $permitirAgregarObservaciones ) : ?>
					<div class="row <?php echo ( $actualEstatusId["id"] == $EstatusId["id"] && !$cambioAutomaticoEstatus ) ? 'd-none' : '' ?>">
						<div class="col-12">
							<div class="form-group">
								<label for="observacion">Observación:</label>
								<input type="text" id="observacion" name="observacion" value="<?php echo fString($observacion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una observación" <?php echo ( $actualEstatusId["id"] == $EstatusId["id"] && !$cambioAutomaticoEstatus ) ? 'disabled' : '' ?>>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( isset($requisicion->id) && count($requisicion->observaciones) > 0 ) : ?>
					<div class="row">
						<div class="col-12">
							<ul class="list-group pb-3">
								<?php foreach($requisicion->observaciones as $observacion) { ?>
								<?php
									$leyenda = "[{$observacion["fechaCreacion"]}] Requisición fue cambiada a estado '";
									$leyenda .= mb_strtoupper(fString($observacion["servicio_estatus.descripcion"]));
									$leyenda .= "' por ";
									$leyenda .= mb_strtoupper(fString($observacion["usuarios.nombre"]));
									$leyenda .= " ";
									$leyenda .= mb_strtoupper(fString($observacion["usuarios.apellidoPaterno"]));
									if ( !is_null($observacion["usuarios.apellidoMaterno"]) ) {
										$leyenda .= " ";
										$leyenda .= mb_strtoupper(fString($observacion["usuarios.apellidoMaterno"]));
									}
									$leyenda .= " (";
									$leyenda .= mb_strtoupper(fString($observacion["observacion"]));
									$leyenda .= ")";
								?>
								<li class="list-group-item list-group-item-success py-2 px-3"><?php echo $leyenda; ?></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				<?php endif; ?>


				<?php if ( $formularioEditable && $permitirSubirArchivos ) : ?>
					<div class="row">

						<!-- COMPROBANTES DE PAGO -->
						<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-comprobantes">
						
							<button type="button" class="btn btn-success" id="btnSubirComprobantes" requisicionId="<?php echo $requisicion->id; ?>">
								<i class="fas fa-folder-open"></i> Cargar Comprobantes de Pago
							</button>

							<?php if ( isset($requisicion->id) ) : ?>
							<?php foreach($requisicion->comprobantesPago as $key=>$comprobante) : ?>
							<p class="text-info mb-0"><?php echo $comprobante['archivo']; ?>
							<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $comprobante['ruta']?>" style="cursor: pointer;" ></i>
								<?php if ( $permitirEliminarArchivos ) : ?>
								<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $comprobante['id']; ?>" folio="<?php echo $comprobante['archivo']; ?>"></i>
								<?php endif; ?>
							</p>
							<?php endforeach; ?>
							<?php endif; ?>
							<span class="lista-archivos">
							</span>

							<!-- <input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" name="comprobanteArchivos[]" multiple> -->
							<input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" multiple>
							
						</div>

						<!-- ORDENES DE COMPRA-->

						<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-ordenes d-flex flex-column align-items-end">

							<button type="button" class="btn btn-warning float-right" id="btnSubirOrdenes">
								<i class="fas fa-folder-open"></i> Cargar Órdenes de Compra
							</button>

							<?php if ( isset($requisicion->id) ) : ?>
							<?php foreach($requisicion->ordenesCompra as $key=>$orden) : ?>
							<p class="text-info mb-0 text-right"><?php echo $orden['archivo']; ?>
							<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $orden['ruta']?>" style="cursor: pointer;" ></i>
								<?php if ( $permitirEliminarArchivos ) : ?>

								<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $orden['id']; ?>" folio="<?php echo $orden['archivo']; ?>"></i>
								<?php endif; ?>
							</p>
							<?php endforeach; ?>
							<?php endif; ?>
							<span class="lista-archivos">
							</span>

							<!-- <input type="file" class="form-control form-control-sm d-none" id="ordenesArchivos" name="ordenesArchivos[]" multiple> -->
							<input type="file" class="form-control form-control-sm d-none" id="ordenesArchivos" multiple>

						</div>

						<div class="col-12 text-muted">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>

					</div>
				<?php elseif ( isset($requisicion->id) ) : ?>
					<div class="row">
						<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1">
							<?php if ( count($requisicion->comprobantesPago) > 0 ) : ?>
							<p class="text-info font-weight-bold mb-0">Comprobantes de Pago:</p>
							<?php foreach($requisicion->comprobantesPago as $key=>$comprobante) : ?>
							<p class="text-info mb-0"><?php echo $comprobante['archivo']; ?></p>
							<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $comprobante['ruta']?>" style="cursor: pointer;" ></i>
							<?php endforeach; ?>
							<?php endif; ?>
						</div>
						<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 d-flex flex-column align-items-end">
							<?php if ( count($requisicion->ordenesCompra) > 0 ) : ?>
							<p class="text-info font-weight-bold mb-0 text-right">Órdenes de Compra:</p>
							<?php foreach($requisicion->ordenesCompra as $key=>$orden) : ?>
							<p class="text-info mb-0 text-right"><?php echo $orden['archivo']; ?></p>
							<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $orden['ruta']?>" style="cursor: pointer;" ></i>
							<?php endforeach; ?>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<hr>

				<div class="row">

					<!-- FACTURAS-->

					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-facturas">
					
						<button type="button" class="btn btn-info" id="btnSubirFacturas">
							<i class="fas fa-folder-open"></i> Cargar Facturas
						</button>

						<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->facturas as $key=>$factura) : ?>
						<p class="text-info mb-0"><?php echo $factura['archivo']; ?>
						<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $factura['ruta']?>" style="cursor: pointer;" ></i>
							<?php if ( $permitirEliminarArchivos ) : ?>
							<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $factura['id']; ?>" folio="<?php echo $factura['archivo']; ?>"></i>
							<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<span class="lista-archivos">
						</span>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="facturaArchivos" name="facturaArchivos[]" multiple> -->
						<input type="file" class="form-control form-control-sm d-none" id="facturaArchivos" multiple>

						<div class="text-muted mt-1">Archivos permitidos PDF y XML (con capacidad máxima de 4MB)</div>
						
					</div>

					<?php if ( $formularioEditable ) : ?>

						<!-- COTIZACIONES-->

						<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-cotizaciones d-flex flex-column align-items-end mt-1">

							<button type="button" class="btn btn-info float-right" id="btnSubirCotizaciones">
								<i class="fas fa-folder-open"></i> Cargar Cotizaciones
							</button>

							<?php if ( isset($requisicion->id) ) : ?>
							<?php foreach($requisicion->cotizaciones as $key=>$cotizacion) : ?>
							<p class="text-info mb-0 text-right"><?php echo $cotizacion['archivo']; ?>
							<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $cotizacion['ruta']?>" style="cursor: pointer;" ></i>
								<?php if ( $permitirEliminarArchivos ) : ?>
								<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cotizacion['id']; ?>" folio="<?php echo $cotizacion['archivo']; ?>"></i>
								<?php endif; ?>
							</p>
							<?php endforeach; ?>
							<?php endif; ?>
							<span class="lista-archivos">
							</span>

							<!-- <input type="file" class="form-control form-control-sm d-none" id="cotizacionArchivos" name="cotizacionArchivos[]" multiple> -->
							<input type="file" class="form-control form-control-sm d-none" id="cotizacionArchivos" multiple>

							<div class="text-muted mt-1 text-right">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>

						</div>

					<?php elseif ( isset($requisicion->id) && count($requisicion->cotizaciones) > 0 ) : ?>

						<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 d-flex flex-column align-items-end mt-1">
							<p class="text-info font-weight-bold mb-0 text-right">Cotizaciones:</p>
							<?php foreach($requisicion->cotizaciones as $key=>$cotizacion) : ?>
							<p class="text-info mb-0 text-right"><?php echo $cotizacion['archivo']; ?></p>
							<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $cotizacion['ruta']?>" style="cursor: pointer;" ></i>
							<?php endforeach; ?>
						</div>

					<?php endif; ?>

				</div>

				<hr>


				<div class="row">

					<div class="col-6 col-sm-6 col-md-12 col-xl-6 mb-1">
					
						<p class="text-info font-weight-bold mb-0 ">Entradas de Almacen:</p>

						<?php foreach($entradasAlmacen as $key=>$vale) : ?>
						<a target="_blank" class="text-info mb-0" href="<?php echo Route::names('inventarios.edit',$vale["id"]); ?>" >Entrada Folio <?php echo $vale['folio']; ?></a>
						<br>
						<?php endforeach; ?>

					</div>

					<!-- SOPORTES -->
					<div class="col-6 col-sm-6 col-md-12 col-xl-6 mb-1 flex d-flex flex-column align-items-end soporte">
					
						<button type="button" class="btn btn-info" id="btnSubirSoporte">
							<i class="fas fa-folder-open"></i> Soportes
						</button>

						<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->soportes as $key=>$soporte) : ?>
						<p class="text-info mb-0"><?php echo $soporte['archivo']; ?>
						<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $soporte['ruta']?>" style="cursor: pointer;" ></i>
							<?php if ( $permitirEliminarArchivos ) : ?>
							<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $soporte['id']; ?>" folio="<?php echo $soporte['archivo']; ?>"></i>
							<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<span class="lista-archivos">
						</span>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="facturaArchivos" name="facturaArchivos[]" multiple> -->
						<input type="file" class="form-control form-control-sm d-none" id="soporte" multiple>
								
						<div class="text-muted mt-1">Archivos permitidos PDF y IMG (con capacidad máxima de 4MB)</div>
					
					</div>
				</div>

				<hr>

				<!-- COMPROBAR EXISTENCIAS -->
				<button type="button" class="btn btn-danger" id="btnComprobarExistencias" data-toggle="modal" data-target="#modalComprobarExistencias">
					<i class="fas fa-box"></i> Comprobar Existencias
				</button>

				<!-- ENTRADA -->
				<a target="_blank" class="btn btn-info float-right"  href="<?php echo Route::routes('inventarios.crear', $requisicion->id); ?>">Añadir Entrada</a>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">
		 <div class="card card-warning card-outline">
			 
			 <div class="card-body">

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="proveedor">Proveedor:</label>
						<input type="text" disabled  id="proveedor" value="<?= $proveedorName ?>" class="form-control form-control-sm">
					</div>

					<div class="col-md-6 form-group">
						<label for="telefono">Telefono:</label>
						<input type="text" disabled id="telefono" value="<?= $telefono ?>" pattern="[0-9]{10}" class="form-control form-control-sm">
					</div>

					<div class="col-12 form-group">
	
						<button type="button" class="btn btn-info" data-toggle="modal" data-target="#modalSeleccionarProveedor">
							<i class="fas fa-truck-loading"></i> Seleccionar Proveedor
						</button>
					</div> <!-- <div class="col-12 justify-content-between d-flex"> -->
						
					<div class="col-md-12 form-group">
						<label for="justificacion">Justificacion:</label>
						<textarea id="justificacion" name="justificacion" value="<?= $justificacion ?>" rows="4" class="form-control form-control-sm"><?php echo $justificacion ?></textarea>
					</div> <!-- <div class="col-md-12 form-group"> -->
						
				</div> <!-- <div class="row"> -->
				
				<?php if ( !$permitirEditar ) : ?>
					<button type="button" class="btn btn-warning float-right" id="btnSolicitudCambio" data-toggle="modal" data-target="#modalSolicitudCambio">
						Solicitar modificar Requisición
					</button>
				<?php endif; ?>

				<?php if ( $permitirAutorizar) : ?>
					<button type="button" class="btn btn-success float-right mr-2" id="btnAutorizarRequisicion">
						<i class="fas fa-check"></i> VB Requisición Adicional
					</button>
				<?php endif; ?>

			</div> 

		</div> 
	</div>  

</div>

<div class="card card-success card-outline">

	<div class="card-body">
		
		<?php if ( $permitirEditar && ( is_null($requisicion->usuarioIdAutorizacion) || $actualEstatusId["id"] == 16 ) ) : ?>
			<div class="mb-2">
				<button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#modalAgregarPartidas">Agregar Partida</button>
			</div>
		<?php endif; ?>
		<div class="table-responsive">

			<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%">

				<thead>
					<tr>
						<th class="text-right" style="min-width: 80px;">Partida</th>
						<th class="text-right">Cant.</th>
						<th class="text-right">Cant. Faltante</th>
						<th>Unidad</th>
						<th style="min-width: 140px;">Costo</th>
						<th style="min-width: 140px;">Costo Unitario</th>
						<th style="min-width: 140px;">Costo Final</th>
						<th style="min-width: 320px;">Concepto</th>
						<th style="min-width: 320px;">Descripcion</th>
					</tr>
				</thead>

				<tbody class="text-uppercase">
					<?php if ( isset($requisicion->id) ) : ?>
					<?php foreach($requisicion->detalles as $key=>$detalle) : ?>
					<tr>
						<td partida class="text-right">
							<span><?php echo ($key + 1); ?></span>
							<?php if ( $detalle['cant_imagenes'] == 0 ) : ?>
							<i class="mx-1 fas fa-eye text-secondary"></i>
							<?php else: ?>
							<i class="mx-1 fas fa-eye text-info verImagenes" style="cursor: pointer;" detalleId="<?php echo $detalle['id']; ?>" data-toggle="modal" data-target="#modalVerImagenes"></i>
							<?php endif; ?>
							<?php if ( $permitirEditar && ( is_null($requisicion->usuarioIdAutorizacion) || $actualEstatusId["id"] == 16 ) ) : ?>
								<button type="button" class="btn btn-sm btn-outline-danger ml-2 float-right" id="btnEliminarDetalle"
									data-detalle-id="<?php echo $detalle['id']; ?>">
									<i class="fas fa-trash-alt"></i>
								</button>
							<?php endif; ?>
						</td>
						<td class="text-right"><?php echo floatval($detalle['cantidad']); ?></td>
						<td class="text-right"><?php echo floatval($detalle['cantidad'] - $detalle['cantidadEntrada']) ; ?></td>
						<td><?php echo fString($detalle['unidad']); ?></td>
						<td><?php echo formatMoney($detalle['costo']); ?></td>
						<td><?php echo formatMoney($detalle['costo_unitario']); ?></td>
						<td><?php echo formatMoney($detalle['costo_total']); ?></td>
						<td><?php echo $detalle['concepto']; ?></td>
						<td><?php echo $detalle['descripcion']; ?>
							<?php if ( $permitirEditar && ( is_null($requisicion->usuarioIdAutorizacion) || $actualEstatusId["id"] == 16 ) ) : ?>
								<button type="button" class="btn btn-sm btn-outline-primary ml-2 btnEditarDetalle float-right"
									data-toggle="modal"
									data-target="#modalModificarPartidas"
									data-partida-id="<?php echo $detalle['id']; ?>"
									data-detalle-id="<?php echo $detalle['obraDetalleId']; ?>"
									data-cantidad="<?php echo floatval($detalle['cantidad']); ?>"
									data-concepto="<?php echo htmlspecialchars($detalle['concepto']); ?>"
									data-descripcion="<?php echo htmlspecialchars($detalle['descripcion']); ?>"
									data-unidad="<?php echo htmlspecialchars($detalle['unidadId']); ?>"
									data-costo_unitario="<?php echo formatMoney($detalle['costo_unitario']); ?>"
								>
									<i class="fas fa-edit"></i>
								</button>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>

			</table>

		</div>

	</div> <!-- <div class="card-body"> -->

</div> <!-- <div class="card card-info card-outline"> -->

<?php if ( $formularioEditable ) : ?>
<button type="button" id="btnSend" class="btn btn-outline-primary">
	<i class="fas fa-save"></i> Actualizar
</button>
<?php else: ?>
<button type="button" id="btnSend" class="btn btn-outline-primary cargar-facturas d-none" disabled>
	<i class="fas fa-save"></i> Actualizar
</button>
<?php endif; ?>

<div class="btn-group descargar-archivos">
	<button type="button" class="btn btn-outline-info" <?php if ( $cantidadComprobantes == 0 && $cantidadOrdenes == 0 && $cantidadFacturas == 0 && $cantidadCotizaciones == 0 && $cantidadVales == 0 ) echo "disabled"; ?>>
		<i class="fas fa-download"></i> Descargar
	</button>
	<button type="button" class="btn btn-outline-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false" <?php if ( $cantidadComprobantes == 0 && $cantidadOrdenes == 0 && $cantidadFacturas == 0 && $cantidadCotizaciones == 0 && $cantidadVales == 0 ) echo "disabled"; ?>>
		<span class="sr-only">Alternar Menú Desplegable</span>
	</button>
	<div class="dropdown-menu">
		<a class="dropdown-item <?php if ( $cantidadComprobantes == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarComprobantes">Comprobantes de Pago</a>
		<a class="dropdown-item <?php if ( $cantidadOrdenes == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarOrdenes">Órdenes de Compra</a>
		<a class="dropdown-item <?php if ( $cantidadFacturas == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarFacturas">Facturas</a>
		<a class="dropdown-item <?php if ( $cantidadCotizaciones == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarCotizaciones">Cotizaciones</a>
		<a class="dropdown-item <?php if ( $cantidadVales == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarVales">Vales de Almacén</a>
		<a class="dropdown-item <?php if ( $cantidadResguardos == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarResguardos">Resguardos</a>
		<a class="dropdown-item <?php if ( $cantidadDocs < 1 ) echo "disabled-link"; ?>" href="#" id="btnDescargarTodo">Descargar Todo</a>
	</div>
</div>

<!-- Botón para ver todo -->
<button type="button" id="btnVerTodo" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalVerPDF">
	<i class="fas fa-eye"></i> Ver Todo
</button>

<a href="<?php echo Route::names('requisiciones.print', $requisicion->id); ?>" target="_blank" class="btn btn-info float-right"><i class="fas fa-print"></i> Imprimir</a>
<div id="msgSend"></div>