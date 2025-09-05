<?php
    if( isset($ordenCompra->id) ) {

		//******* DATOS PROVEEDOR ********/
		$proveedorSeleccionado = isset($old["proveedorId"]) ? $old["proveedorId"] : $ordenCompra->proveedorId;

		//******* DATOS ORDEN DE COMPRA ********/
		$folioOC = $ordenCompra->folio;
		$fechaRequeridaOrdenCompra = fFechaLarga($ordenCompra->fechaRequerida);
		$monedaId = isset($old["monedaId"]) ? $old["monedaId"] : $ordenCompra->monedaId;
		$condicionPagoId = isset($old["condicionPagoId"]) ? $old["condicionPagoId"] : $ordenCompra->condicionPagoId;
		$direccion = isset($old["direccion"]) ? $old["direccion"] : $ordenCompra->direccion;
		$especificaciones = isset($old["especificaciones"]) ? $old["especificaciones"] : $ordenCompra->especificaciones;
		$justificacion = isset($old["justificacion"]) ? $old["justificacion"] : $ordenCompra->justificacion;
		$retencionIva = isset($old["retencionIva"]) ? $old["retencionIva"] : $ordenCompra->retencionIva;
		$retencionIsr = isset($old["retencionIsr"]) ? $old["retencionIsr"] : $ordenCompra->retencionIsr;
		$descuento = isset($old["descuento"]) ? $old["descuento"] : $ordenCompra->descuento;
		$iva = isset($old["iva"]) ? $old["iva"] : $ordenCompra->iva;
		$total = isset($old["total"]) ? $old["total"] : $ordenCompra->total;
		$subtotal = isset($old["subtotal"]) ? $old["subtotal"] : $ordenCompra->subtotal;
		$datoBancarioId = isset($old['datoBancarioId']) ? $old['datoBancarioId'] : $ordenCompra->datoBancarioId;

		//******* DATOS REQUISICIÓN ********/
		$tipoRequisicion = isset($requisicion->tipoRequisicion) ? $requisicion->tipoRequisicion : '0';

		//******* ESTATUS DE LA ORDEN DE COMPRA ********/
		$actualServicioEstatusId = $ordenCompra->estatus;
		$servicioEstatusId = isset($old["estatusId"]) ? $old["estatusId"] : $ordenCompra->estatus;

		//******* OBSERVACIÓN DE LA ORDEN DE COMPRA ********/
		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";

	}else{

		//******* DATOS PROVEEDOR ********/
		$proveedorSeleccionado = isset($old["proveedorId"]) ? $old["proveedorId"] : '';

		//******* DATOS ORDEN DE COMPRA ********/
		$fechaRequeridaOrdenCompra = fFechaLarga(date('Y-m-d'));
		$monedaId = isset($old["monedaId"]) ? $old["monedaId"] : 1;
		$condicionPagoId = isset($old["condicionPagoId"]) ? $old["condicionPagoId"] : 1;
		$folioOC = "";
		$direccion = isset($old["direccion"]) ? $old["direccion"] : "";
		$especificaciones = isset($old["especificaciones"]) ? $old["especificaciones"] : "";
		$justificacion = isset($old["justificacion"]) ? $old["justificacion"] : "";
		$retencionIva = isset($old["retencionIva"]) ? $old["retencionIva"] : 0;
		$retencionIsr = isset($old["retencionIsr"]) ? $old["retencionIsr"] : 0;
		$descuento = isset($old["descuento"]) ? $old["descuento"] : 0;
		$iva = isset($old["iva"]) ? $old["iva"] : 0;
		$total = isset($old["total"]) ? $old["total"] : 0;
		$subtotal = isset($old["subtotal"]) ? $old["subtotal"] : 0;
		$datoBancarioId = isset($old['datoBancarioId']) ? $old['datoBancarioId'] : "";

		//******* DATOS REQUISICIÓN ********/
		$tipoRequisicion = $requisicion->tipoRequisicion ?? '0';

		//******* ESTATUS DE LA ORDEN DE COMPRA ********/
		$actualServicioEstatusId = '';
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : 8;

		//******* OBSERVACIÓN DE LA ORDEN DE COMPRA ********/
		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";
	}

?>
<div class="row">



	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<input type="hidden" id="datoBancarioInput" name="datoBancarioInput" value="<?php echo $datoBancarioId; ?>">

				<?php if ( isset($requisicion->id) ) : ?>
					<input type="hidden" name="requisicionId" value="<?php echo $requisicion->id; ?>">
				<?php endif; ?>

				<?php if ( isset($ordenCompra->id) ) : ?>
					<input type="hidden" name="ordenCompraId" value="<?php echo $ordenCompra->id; ?>">
				<?php endif; ?>

				<div class="box box-info">

					<div class="box-header with-border">
						<h3 class="box-title">Datos Generales</h3>
					</div>

					<div class="box-body">

						<div class="row">

							<div class="col-md-6 form-group">
								<label for="folio">Folio Requisicion:</label>
								<input type="text" value="<?php echo $requisicion->folio ?>" class="form-control form-control-sm" disabled="">
							</div>

							<div class="col-md-6 form-group">
								<label for="codigo">Folio:</label>
								<input type="text" name="folio" value="<?= $folioOC; ?>" class="form-control form-control-sm" placeholder="Folio (vacio para generar automatico)">
							</div>

							<div class="col-md-6 form-group">
								<label for="fechaRequerida">Fecha Requerida:</label>
								<div class="input-group date" id="fechaRequeridaDTP" data-target-input="nearest">
									<input type="text" name="fechaRequerida" id="fechaFinalfechaRequeridaizacion" value="<?php echo $fechaRequeridaOrdenCompra; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de finalización" data-target="#fechaFinalizacionDTP">
									<div class="input-group-append" data-target="#fechaRequeridaDTP" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
									</div>
								</div>
							</div>

							<div class="col-md-6 form-group">
								<input type="hidden" name="actualServicioEstatusId" id="actualServicioEstatusId" value="<?php echo $actualServicioEstatusId; ?>">
								<label for="estatusId">Estatus:</label>
								<?php if ( !isset($ordenCompra->id) || ( $formularioEditable && $permitirModificarEstatus ) ) : ?>
									<select name="estatusId" id="estatusId" class="custom-select form-controls select2">
								<?php else: ?>
									<select id="estatusId" class="custom-select form-controls select2" disabled>
								<?php endif; ?>
								<?php foreach($servicioStatus as $servicioEstatus) { ?>
									<?php if ( $servicioEstatus["ordenCompraAbierta"] || ( $servicioEstatus["requisicionCerrada"] && isset($ordenCompra->id) ) ) : ?>
										<option value="<?php echo $servicioEstatus["id"]; ?>"
											<?php echo $servicioEstatusId == $servicioEstatus["id"] ? ' selected' : ''; ?>
											><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
										</option>
									<?php endif; ?>
								<?php } ?>
									</select>
							</div>

						</div>

						<div class="row">

							<div class="col-md-6 form-group">
								<label for="monedaId">Moneda:</label>
								<select name="monedaId" id="monedaId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
									<option value="">Selecciona una Moneda</option>
									<?php foreach($divisas as $moneda) : ?>
										<option value="<?php echo $moneda['id'] ?>" <?php echo ($moneda['id'] == $monedaId) ? 'selected' : ''; ?>><?php echo $moneda['nombreCorto'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>

							<?php if ( isset($ordenCompra->id)): ?>
								<div class="col-md-6 form-group">
									<label for="tipoRequisicion">Tipo de Requisición:</label>
									<select name="tipoRequisicion" id="tipoRequisicion" class="custom-select form-controls select2">
										<option value="0"<?php echo ( (int)($tipoRequisicion ?? 0) === 0 ) ? ' selected' : ''; ?>>PROGRAMADA</option>
										<option value="1"<?php echo ( (int)($tipoRequisicion ?? 0) === 1 ) ? ' selected' : ''; ?>>URGENTE</option>
									</select>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( isset($ordenCompra->id) && $permitirAgregarObservaciones ) : ?>
							<div class="row <?php echo ( $actualServicioEstatusId == $servicioEstatusId && !$cambioAutomaticoEstatus ) ? 'd-none' : '' ?>">
								<div class="col-12">
									<div class="form-group">
										<label for="observacion">Observación:</label>
										<input type="text" id="observacion" name="observacion" value="<?php echo fString($observacion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una observación" <?php echo ( $actualServicioEstatusId == $servicioEstatusId && !$cambioAutomaticoEstatus ) ? 'disabled' : '' ?>>
									</div>
								</div>
							</div>
						<?php endif; ?>


							<?php if ( isset($ordenCompra->id) && count($ordenCompra->observaciones) > 0 ) : ?>
							<div class="row">
								<div class="col-12">
									<ul class="list-group pb-3">
										<?php foreach($ordenCompra->observaciones as $observacion) { ?>
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

							<div class="form-group">
								<label for="proveedorId">Proveedor:</label>
								<select name="proveedorId" id="proveedorId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
									<option value="">Selecciona un Proveedor</option>
									<?php foreach($proveedores as $proveedor) : ?>
										<option value="<?php echo $proveedor['id'] ?>" <?php echo ($proveedor['id'] == $proveedorSeleccionado) ? 'selected' : ''; ?>>
											<?php
												echo !empty($proveedor['razonSocial'])
													? $proveedor['razonSocial']
													: trim($proveedor['nombre'] . ' ' . $proveedor['apellidoPaterno'] . ' ' . ($proveedor['apellidoMaterno'] ?? ''));
											?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- Contenedor para el segundo select -->
							<div class="row d-none" id="container-dato-bancario">
								<div class="form-group col-md-12 ">
									<label for="datoBancarioId">Dato bancario:</label>
									<div class="input-group">
										<select name="datoBancarioId" id="datoBancarioId" class="form-control select2" style="width: 100%">
											<option value="">Selecciona una opción</option>
										</select>
									</div>
								</div>
							</div>
						
						<div class="row">

							<div class="col-md-6 form-group">
								<label for="condicionPagoId">Condición de Pago:</label>
								<select name="condicionPagoId" id="condicionPagoId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
									<option value="">Selecciona una Condición de Pago</option>
									<option value="1" <?php echo ($condicionPagoId == 1) ? 'selected' : ''; ?>>CONTADO</option>
									<option value="2" <?php echo ($condicionPagoId == 2) ? 'selected' : ''; ?>>30 DIAS</option>
								</select>
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-md-6 form-group">
								<label for="direccion">Direccion de entrega:</label>
								<input name="direccion" type="text" id="direccion" class="form-control form-control-sm" placeholder="Ingresa la direccion de entrega" value="<?php echo $direccion; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-12 form-group">
								<label for="especificaciones">Especificaciones Adjuntas:</label>
								<input name="especificaciones" type="text" id="especificaciones" class="form-control form-control-sm" placeholder="Ingresa las especificaciones de entrega" value="<?php echo $especificaciones; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-12 form-group">
								<label for="justificacion">Justificacion:</label>
								<textarea name="justificacion" id="justificacion" class="form-control form-control-sm" rows="3" placeholder="Ingresa la justificacion"><?php echo $justificacion; ?></textarea>
							</div>

						</div> <!-- <div class="row"> -->

						<div class="row">
							
							<div class="col-md-6 form-group">
								<label for="retencionIva">Retencion I.V.A.:</label>
								<input type="number" id="retencionIva" name="retencionIva" class="form-control form-control-sm" placeholder="Ingresa la retención de IVA" value="<?php echo $retencionIva; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-md-6 form-group">
								<label for="retencionIsr">Retencion I.S.R.:</label>
								<input name="retencionIsr" type="number" id="retencionIsr" class="form-control form-control-sm" placeholder="Ingresa la retencion de IVA" value="<?php echo $retencionIsr; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-md-6 form-group">
								<label for="descuento">Descuentos:</label>
								<input name="descuento" type="number" id="descuento" class="form-control form-control-sm" placeholder="Ingresa la retencion de IVA" value="<?php echo $descuento; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

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
							</div>

							<div class="col-md-6 form-group">
								<label for="subtotal">Subtotal</label>
								<input type="number" name="subtotal" id="subtotal" class="form-control form-control-sm" placeholder="Ingresa el subtotal de la orden de compra" value="<?php echo $subtotal; ?>">
							</div>

							<div class="col-md-6 form-group">
								<label for="total">Total:</label>
								<input type="number" name="total" id="total" class="form-control form-control-sm" placeholder="Ingresa el total de la orden de compra" value="<?= $total ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->
						</div> <!-- <div class="row"> -->

					</div> <!-- <div class="box-body"> -->

				</div> <!-- <div class="box box-info"> -->

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> --> 

	<?php if ( isset($ordenCompra->id) ) : ?>
	<div class="col-lg-3">

		<div class="card card-info card-outline">

			<div class="card-body">
				<!-- COMPROBANTES DE PAGO -->
				<div class="subir-comprobantes mb-3">
					<label for="">Listado comprobantes de pago</label>
					<br>
					<button type="button" class="btn btn-info" id="btnSubirComprobantes">
						<i class="fas fa-folder-open"></i> Cargar Comprobantes de Pago
					</button>
					<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->comprobantesPago as $key=>$comprobante) : ?>
							<p class="text-info mb-0">
								<?php echo $comprobante['archivo']; ?>
								<i  class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo "https://cm.atibernal.com/".$comprobante['ruta']?>" style="cursor: pointer;" ></i>
							</p>
						<?php endforeach; ?>
					<?php endif; ?>
					
					<span class="lista-archivos">
					</span>
					<input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" multiple>
					<div class="text-muted mt-1">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>
				</div>

				<hr>

				<div class="">
					<label for="">Ordenes de compra</label>
					<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->ordenesCompra as $key=>$orden) : ?>
							<p class="text-info mb-0 "><?php echo $orden['archivo']; ?>
								<i  class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo "https://cm.atibernal.com/".$orden['ruta']?>" style="cursor: pointer;" ></i>
							</p>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				<hr>
				<div class="">
					<label for="">Facturas</label>
					<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->facturas as $key=>$factura) : ?>
							<p class="text-info mb-0 "><?php echo $factura['archivo']; ?>
								<i  class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo "https://cm.atibernal.com/".$factura['ruta']?>" style="cursor: pointer;" ></i>
							</p>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

				<hr>

				<div class="">
					<label for="">Cotizaciones</label>
					<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->cotizaciones as $key=>$cotizacion) : ?>
							<p class="text-info mb-0 "><?php echo $cotizacion['archivo']; ?>
								<i  class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo "https://cm.atibernal.com/".$cotizacion['ruta']?>" style="cursor: pointer;" ></i>
							</p>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> 
	<?php endif; ?>
	<?php if ( !isset($ordenCompra->id) ) : ?>
	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-header with-border">
				<h3 class="card-title">Seleccionar Productos de Requisicion</h3>
			</div>

			<div class="card-body">

				<div class="table-responsive">

					<table class="table table-sm table-bordered table-striped mb-0 listaProductos" id="tablaRequisicionDetalles" width="100%">

						<thead>
							<tr>
								<th class="text-right" style="width: 10px;">#</th>
								<th>Cantidad</th>
								<th>Unidad</th>
								<th>Costo Unitario</th>
								<th style="min-width: 320px;">Descripcion</th>
								<th style="width: 100px;">Acciones</th>
							</tr>
						</thead>

						<tbody class="text-uppercase">
							<?php if ( isset($requisicion->id) ) : ?>
								<?php foreach($requisicion->detalles as $key=>$detalle) : ?>
								<tr>
									<td productoId="<?php echo $detalle['id'] ?>" class="text-right">
										<span><?php echo ($key + 1); ?></span>
										<!-- <input type="hidden" class="precioCompra" value="<?php echo $detalle['costo']; ?>"> -->
									</td>
									<td class="cantidad">
										<?php echo $detalle['cantidad']; ?>
									</td>
									<td unidad>
										<?php echo fString($detalle['unidad']); ?>
									</td>
									<td class="costoUnitario">
										<?php echo $detalle['costo']??0; ?>
									</td>
									<td class="descripcion">
										<?php 
										echo $detalle['concepto'] 
										// . 
										// ' | '.
										// $detalle['descripcion'];; 
										?>
									</td>
									<td>
										<button type="button" class="btn btn-xs btn-success btnAgregarDetalle" productoId="<?php echo $detalle['id'] ?>">
											<i class="fa fa-plus-circle"></i> Agregar
										</button>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php endif; ?>
							
						</tbody> <!-- <tbody class="text-uppercase"> -->

					</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%"> -->

				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div>
	<?php endif; ?>
</div>

<div class="card card-success card-outline">

	<div class="card-body">

		<div class="table-responsive">

			<table class="table table-bordered table-striped dt-responsivex tablaSimplex detalleOrden" id="tablaOrdenCompraDetalles" width="100%">

				<thead>
					<tr>
						<th style="vertical-align:middle"><div style="width: 10px;">#</div></th>							
						<th style="vertical-align:middle"><div style="width: 70px;">Cantidad</div></th>
						<th style="vertical-align:middle"><div style="width: 250px;">Descripción</div></th>
						<th style="vertical-align:middle"><div style="width: 140px;">Valor Unitario</div></th>
						<th style="vertical-align:middle"><div style="width: 140px;">Importe</div></th>
						<?php if (!isset($ordenCompra->id)) : ?>
						<th style="vertical-align:middle"><div style="width: 100px;">Acciones</div></th>
						<?php endif; ?>
					</tr>
				</thead>

				<tbody class="text-uppercase">
					<?php if ( isset($ordenCompra->id) ) : ?>
					<?php foreach($ordenCompra->detalles as $key=>$detalle) : ?>
					<tr>
						<td class="text-right">
							<span><?php echo ($key + 1); ?></span>
						</td>
						<td>
							<?php echo $detalle['cantidad']; ?>
						</td>
						<td>
							<?php echo $detalle['concepto']
							// .' | '. 
							// $detalle['descripcion']; 
							?>
						</td>
						<td>
							<?php echo $detalle['importeUnitario']; ?>
						</td>
						<td>
							$ <span class="importe"><?php echo number_format($detalle['importeUnitario']*$detalle['cantidad'],6); ?></span>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody> <!-- <tbody class="text-uppercase"> -->

			</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaOrdenCompraDetalles" width="100%"> -->

		</div> <!-- <div class="table-responsive"> -->

	</div> <!-- <div class="card-body"> -->

</div> <!-- <div class="card card-info card-outline"> -->
