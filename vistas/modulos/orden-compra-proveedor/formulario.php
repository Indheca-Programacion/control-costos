<?php
    if( isset($ordenCompra->id) ) {
		$proveedorSeleccionado = $ordenCompra->proveedorId;
		$fechaRequerida = fFechaLarga($ordenCompra->fechaRequerida);
		$monedaId = isset($old["monedaId"]) ? $old["monedaId"] : $ordenCompra->monedaId;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : $ordenCompra->estatusId;
		$condicionPagoId = isset($old["condicionPagoId"]) ? $old["condicionPagoId"] : $ordenCompra->condicionPagoId;
		$folioOC = $ordenCompra->id;
		$direccion = isset($old["direccion"]) ? $old["direccion"] : $ordenCompra->direccion;
		$especificaciones = isset($old["especificaciones"]) ? $old["especificaciones"] : $ordenCompra->especificaciones;
		$retencionIva = isset($old["retencionIva"]) ? $old["retencionIva"] : $ordenCompra->retencionIva;
		$retencionIsr = isset($old["retencionIsr"]) ? $old["retencionIsr"] : $ordenCompra->retencionIsr;
		$descuento = isset($old["descuento"]) ? $old["descuento"] : $ordenCompra->descuento;
		$iva = isset($old["iva"]) ? $old["iva"] : $ordenCompra->iva;
	}else{
		$proveedorSeleccionado = $requisicion->proveedorId;
		$fechaRequerida = fFechaLarga(date('Y-m-d'));
		$monedaId = isset($old["monedaId"]) ? $old["monedaId"] : 1;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : 8;
		$condicionPagoId = isset($old["condicionPagoId"]) ? $old["condicionPagoId"] : 1;
		$folioOC = "";
		$direccion = isset($old["direccion"]) ? $old["direccion"] : '';
		$especificaciones = isset($old["especificaciones"]) ? $old["especificaciones"] : '';
		$retencionIva = isset($old["retencionIva"]) ? $old["retencionIva"] : 0;
		$retencionIsr = isset($old["retencionIsr"]) ? $old["retencionIsr"] : 0;
		$descuento = isset($old["descuento"]) ? $old["descuento"] : 0;
		$iva = isset($old["iva"]) ? $old["iva"] : 0;
	}
?>
<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
				<?php if ( isset($requisicion->id) ) : ?>
				<input type="hidden" name="requisicionId" value="<?php echo $requisicion->id; ?>">
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
								<input type="text" value="<?= $folioOC; ?>" class="form-control form-control-sm" placeholder="Folio (se genera al crearla)" disabled="">
							</div>

							<div class="col-md-6 form-group">
								<label for="fechaRequerida">Fecha Requerida:</label>
								<div class="input-group date" id="fechaRequeridaDTP" data-target-input="nearest">
									<input type="text" name="fechaRequerida" id="fechaFinalfechaRequeridaizacion" value="<?php echo $fechaRequerida; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de finalización" data-target="#fechaFinalizacionDTP">
									<div class="input-group-append" data-target="#fechaRequeridaDTP" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
									</div>
								</div>
							</div>

						</div>

						<div class="row">

							<div class="col-md-6 form-group">
								<label for="estatusId">Estatus:</label>
								<select name="estatusId" id="estatusId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
									<option value="">Selecciona un Estatus</option>
									<?php foreach($estatuses as $estatus) : ?>
										<?php if ( $estatus["ordenCompraAbierta"] ) : ?>
										<option value="<?php echo $estatus['id'] ?>" <?php echo ($estatus['id'] == $estatusId) ? 'selected' : ''; ?>><?php echo $estatus['descripcion'] ?></option>
										<?php endif; ?>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-md-6 form-group">
								<label for="monedaId">Moneda:</label>
								<select name="monedaId" id="monedaId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
									<option value="">Selecciona una Moneda</option>
									<?php foreach($divisas as $moneda) : ?>
										<option value="<?php echo $moneda['id'] ?>" <?php echo ($moneda['id'] == $monedaId) ? 'selected' : ''; ?>><?php echo $moneda['nombreCorto'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>

						</div>

						<div class="form-group">
							<label for="proveedorId">Proveedor:</label>
							<select name="proveedorId" id="proveedorId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
								<option value="">Selecciona un Proveedor</option>
								<?php foreach($proveedores as $proveedor) : ?>
									<option value="<?php echo $proveedor['id'] ?>" <?php echo ($proveedor['id'] == $proveedorSeleccionado) ? 'selected' : ''; ?>><?php echo $proveedor['razonSocial'] ?></option>
								<?php endforeach; ?>
							</select>
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

							<div class="col-md-6 form-group">
								<label for="especificaciones">Especificaciones Adjuntas:</label>
								<input name="especificaciones" type="text" id="especificaciones" class="form-control form-control-sm" placeholder="Ingresa las especificaciones de entrega" value="<?php echo $especificaciones; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

						</div> <!-- <div class="row"> -->

						<div class="row">
							
							<div class="col-md-6 form-group">
								<label for="retencionIva">Retencion I.V.A.:</label>
								<select id="retencionIva" name="retencionIva" class="custom-select form-controls select2">
									<option value="0" <?php echo ($retencionIva == 0) ? 'selected' : ''; ?>>0%</option>
									<option value="4" <?php echo ($retencionIva == 4) ? 'selected' : ''; ?>>4%</option>
									<option value="10.6667" <?php echo ($retencionIva == 10.6667) ? 'selected' : ''; ?>>10.6667%</option>
								</select>
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-md-6 form-group">
								<label for="retencionIsr">Retencion I.S.R.:</label>
								<input name="retencionIsr" type="text" id="retencionIsr" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa la retencion de IVA" value="<?php echo $retencionIsr; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-md-6 form-group">
								<label for="descuento">Descuentos:</label>
								<input name="descuento" type="text" id="descuento" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa la retencion de IVA" value="<?php echo $descuento; ?>">
							</div> <!-- <div class="col-md-6 form-group"> -->

							<div class="col-md-6 form-group">
								<label for="iva">I.V.A.:</label>
								<select name="iva" id="iva" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
									<option value="0" <?php echo ($iva == 0) ? 'selected' : ''; ?>>0%</option>
									<option value="16" <?php echo ($iva == 16) ? 'selected' : ''; ?>>16%</option>
								</select>
							</div> <!-- <div class="col-md-6 form-group"> -->

						</div> <!-- <div class="row"> -->

					</div> <!-- <div class="box-body"> -->

				</div> <!-- <div class="box box-info"> -->

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> --> 

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
										<input type="hidden" class="precioCompra" value="<?php echo $detalle['costo']; ?>">
									</td>
									<td class="cantidad">
										<?php echo $detalle['cantidad']; ?>
									</td>
									<td unidad>
										<?php echo fString($detalle['unidad']); ?>
									</td>
									<td class="costoUnitario">
										<?php echo number_format($detalle['costo_unitario'],2); ?>
									</td>
									<td class="descripcion">
										<?php echo $detalle['concepto'] . ' | '.$detalle['descripcion'];; ?>
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
							<?php echo $detalle['concepto'].' | '. $detalle['descripcion']; ?>
						</td>
						<td>
							$ <?php echo $detalle['importeUnitario']; ?>
						</td>
						<td>
							$ <?php echo $detalle['importeUnitario']*$detalle['cantidad']; ?>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody> <!-- <tbody class="text-uppercase"> -->

			</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaOrdenCompraDetalles" width="100%"> -->

		</div> <!-- <div class="table-responsive"> -->

	</div> <!-- <div class="card-body"> -->

</div> <!-- <div class="card card-info card-outline"> -->