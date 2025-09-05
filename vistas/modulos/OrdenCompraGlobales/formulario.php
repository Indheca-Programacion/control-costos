<?php
	if( isset($ordenCompraGlobal->id) ) {
		$proveedorSeleccionado = isset($old["proveedorId"]) ? $old["proveedorId"] : $ordenCompraGlobal->proveedorId;
		$fechaRequerida = fFechaLarga($ordenCompraGlobal->fechaRequerida);
		$monedaId = isset($old["monedaId"]) ? $old["monedaId"] : $ordenCompraGlobal->monedaId;
		$EstatusId = isset($old["EstatusId"]) ? $old["EstatusId"] : $ordenCompraGlobal->estatus;
		$condicionPagoId = isset($old["condicionPagoId"]) ? $old["condicionPagoId"] : $ordenCompraGlobal->condicionPagoId;
		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";
		$folioOC = $ordenCompraGlobal->folio;
		$actualEstatusId = $ordenCompraGlobal->estatus;
		$direccion = isset($old["direccion"]) ? $old["direccion"] : $ordenCompraGlobal->direccion;
		$especificaciones = isset($old["especificaciones"]) ? $old["especificaciones"] : $ordenCompraGlobal->especificaciones;
		$justificacion = isset($old["justificacion"]) ? $old["justificacion"] : $ordenCompraGlobal->justificacion;
		$retencionIva = isset($old["retencionIva"]) ? $old["retencionIva"] : $ordenCompraGlobal->retencionIva;
		$retencionIsr = isset($old["retencionIsr"]) ? $old["retencionIsr"] : $ordenCompraGlobal->retencionIsr;
		$descuento = isset($old["descuento"]) ? $old["descuento"] : $ordenCompraGlobal->descuento;
		$iva = isset($old["iva"]) ? $old["iva"] : $ordenCompraGlobal->iva;
		$reposicionGastos = isset($old["reposicion_gastos"]) ? $old["reposicion_gastos"] : $ordenCompraGlobal->reposicion_gastos;
		$categoriaId = isset($old['categoriaId']) ? $old['categoriaId'] : $ordenCompraGlobal->categoriaId;
		$tiempoEntrega = isset($old["tiempoEntrega"]) ? $old["tiempoEntrega"] : $ordenCompraGlobal->tiempoEntrega;
		$tipoRequisicion = isset($old["tipoRequisicion"]) ? $old["tipoRequisicion"] : $ordenCompraGlobal->tipoRequisicion;

		$datoBancarioId = isset($old['datoBancarioId']) ? $old['datoBancarioId'] : $ordenCompraGlobal->datoBancarioId;
		$ordenCompraGlobalId = isset($old['ordenCompraGlobalId']) ? $old['ordenCompraGlobalId'] : $ordenCompraGlobal->id;

		$total = isset($old["total"]) ? $old["total"] : $ordenCompraGlobal->total;
		$subtotal = isset($old["subtotal"]) ? $old["subtotal"] : $ordenCompraGlobal->subtotal;


	}else{
		$proveedorSeleccionado = isset($old["proveedorId"]) ? $old["proveedorId"] : '';
		$fechaRequerida = fFechaLarga(date('Y-m-d'));
		$monedaId = isset($old["monedaId"]) ? $old["monedaId"] : 1;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] :8;
		$condicionPagoId = isset($old["condicionPagoId"]) ? $old["condicionPagoId"] : 1;
		$folioOC = isset($old["folio"]) ? $old["folio"] : '';
		$actualEstatusId = '';
		$direccion = isset($old["direccion"]) ? $old["direccion"] : "";
		$especificaciones = isset($old["especificaciones"]) ? $old["especificaciones"] : "";
		$justificacion = isset($old["justificacion"]) ? $old["justificacion"] :"";
		$retencionIva = isset($old["retencionIva"]) ? $old["retencionIva"] : 0;
		$retencionIsr = isset($old["retencionIsr"]) ? $old["retencionIsr"] : 0;
		$descuento = isset($old["descuento"]) ? $old["descuento"] : 0;
		$iva = isset($old["iva"]) ? $old["iva"] : 0;
		$reposicionGastos = isset($old["reposicion_gastos"]) ? $old["reposicion_gastos"] : 0;
		$tiempoEntrega = isset($old["tiempoEntrega"]) ? $old["tiempoEntrega"] : "";
		$tipoRequisicion = isset($old["tipoRequisicion"]) ? $old["tipoRequisicion"] : "";

		$categoriaId = isset($old['categoriaId']) ? $old['categoriaId'] : null;
		$datoBancarioId = isset($old['datoBancarioId']) ? $old['datoBancarioId'] : "";

		$total = isset($old["total"]) ? $old["total"] : 0;
		$subtotal = isset($old["subtotal"]) ? $old["subtotal"] : 0;

	}

	$tiposRequisicion = [
		"0" => "Programado",
		"1" => "Urgente"
	];

	use App\Route;
?>

<div class="row">
	<div class="col-lg-6">
		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
				<input type="hidden" id="datoBancarioInput" name="datoBancarioInput" value="<?php echo $datoBancarioId; ?>">
				<input type="hidden" id="detalles" name="detalles">
				
				<input type="hidden" id="requisicionIds" value="<?php echo $ids ?>" name="requisicionIds">
				 
				<input type="hidden" id="ordenCompraId" value="<?php echo $ordenCompraGlobalId ?>" name="ordenCompraId">

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
								<label for="codigo">Folio:</label>
								<input type="text" name="folio" value="<?= $folioOC; ?>" class="form-control form-control-sm" placeholder="Folio (vacio para generar automatico)" <?= isset($ordenCompra->id) ? 'disabled' : ''; ?>>
							</div>
							
							<div class="col-md-6 form-group">
								<div class="form-check mt-4">
									<input class="form-check-input" type="checkbox" name="reposicion_gastos" id="reposicion_gastos" <?php echo $reposicionGastos == 1 ? 'checked' : '' ?>>
									<label for="reposicion_gastos">
										Reposición de gastos
									</label>
								</div>
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

							<div class="col-md-6 form-group">

								<?php if (isset($ordenCompraGlobal->id)): ?>
									<input type="hidden" name="actualEstatusId" id="actualEstatusId" value="<?= $actualEstatusId['id'] ?>">
								<?php endif; ?>

								<label for="estatusId">Estatus:</label>
								<?php if ( !isset($ordenCompraGlobal->id) || ( $formularioEditable && $permitirModificarEstatus ) ) : ?>
								<select name="estatusId" id="estatusId" class="custom-select form-controls select2">
								<?php else: ?>
								<select id="estatusId" class="custom-select form-controls select2" disabled>
								<?php endif; ?>
									<?php foreach($servicioStatus as $servicioEstatus) { ?>
									<?php if ( $servicioEstatus["ordenCompraAbierta"] || ( $servicioEstatus["requisicionCerrada"] && isset($ordenCompraGlobal->id) ) ) : ?>
									<option value="<?php echo $servicioEstatus["id"]; ?>"
										<?php echo $actualEstatusId == $servicioEstatus["id"] ? ' selected' : ''; ?>
										><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
									</option>
									<?php endif; ?>
									<?php } ?>
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

							<div class="col-md-6 form-group">
								<label for="categoriaId">Categoría de Orden de Compra:</label>
								<select name="categoriaId" id="categoriaId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true" required>
									<option value="">Selecciona una Categoría</option>
									<?php foreach($categoriasOrdenCompra as $categoria) : ?>
										<option value="<?php echo $categoria['id']; ?>" <?php echo ($categoria['id'] == $categoriaId) ? 'selected' : ''; ?>>
											<?php echo $categoria['nombre']; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-md-6 form-group">
								<label for="tiempoEntrega">Tiempo de entrega:</label>
								<input type="text" name="tiempoEntrega" id="tiempoEntrega" class="form-control form-control-sm" placeholder="Ingresa el tiempo de entrega" value="<?php echo $tiempoEntrega;?>">
							</div>

							<!-- TIPO DE REQUISICION -->
							<div class="col-md-6 form-group">
								<label for="tipoRequisicion">Tipo de Requisición:</label>
								<select name="tipoRequisicion" id="tipoRequisicion" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true" required>
									<option value="">Seleccione una opción</option>
									<?php foreach ($tiposRequisicion as $valor => $nombre): ?>
										<option value="<?php echo $valor; ?>" <?php echo ($tipoRequisicion == $valor) ? 'selected' : ''; ?>>
											<?php echo $nombre; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<?php if ( isset($ordenCompraGlobal->id) && $permitirAgregarObservaciones ) : ?>
								<div class="col-md-6 form-group <?php echo ( $actualEstatusId["id"] == $EstatusId["id"] && !$cambioAutomaticoEstatus ) ? 'd-none' : '' ?>">
									<div class="">
										<div class="form-group">
											<label for="observacion">Observación:</label>
											<input type="text" id="observacion" name="observacion" value="<?php echo fString($observacion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una observación" <?php echo ( $actualEstatusId["id"] == $EstatusId["id"] && !$cambioAutomaticoEstatus ) ? 'disabled' : '' ?>>
										</div>
									</div>
								</div>
							<?php endif; ?>

						</div>

							<?php if ( isset($ordenCompraGlobal->id) && count($ordenCompraGlobal->observaciones) > 0 ) : ?>
							<div class="row">
								<div class="col-12">
									<ul class="list-group pb-3">
										<?php foreach($ordenCompraGlobal->observaciones as $observacion) { ?>
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
								<div class="input-group">
									<select name="proveedorId" id="proveedorId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
										<option value="">Selecciona un Proveedor</option>
										<?php foreach($proveedores as $proveedor) : ?>
											<option value="<?php echo $proveedor['id'] ?>" <?php echo ($proveedor['id'] == $proveedorSeleccionado) ? 'selected' : ''; ?>><?php echo $proveedor['proveedor'] ?></option>
										<?php endforeach; ?>
									</select>
									<div class="input-group-append">
											<a href="<?= Route::names('proveedores.create') ?>" target="_blank" class="btn btn-success btn-sm">
												<i class="fa fa-plus"></i>
											</a>
										</div>
								</div>
							</div>

						<!-- Contenedor para el segundo select -->
						<div class="row d-none" id="container-dato-bancario">
							<div class="form-group col-md-12 ">
								<label for="datoBancarioId">Dato bancario:</label>
								<div class="input-group">
									<select name="datoBancarioId" id="datoBancarioId" class="form-control select2" style="width: 100%">
										<option value="">Selecciona una opción</option>
									</select>
									<div class="input-group-append">
										<a href="" target="_blank" class="btn btn-success btn-sm" id="addBancario" title="Agregar dato bancario">
											<i class="fa fa-plus"></i>
										</a>
									</div>
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
									<option value="3" <?php echo ($condicionPagoId == 3) ? 'selected' : ''; ?>>CRÉDITO</option>
									<option value="4" <?php echo ($condicionPagoId == 4) ? 'selected' : ''; ?>>CRÉDITO 15 DÍAS</option>
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

  	<div class="card card-secondary card-outline col-6">

      <div class="card-body">
			<div class="box-header with-border">
				<h3 class="box-title">Requisiciones</h3>
				<?php if ( !isset($ordenCompraGlobal->id) ) : ?>
					<p>
						* Seleccione las requisiciones para la Orden de Compra
					</p>
				<?php else: ?>
					<p>
						* Requisiciones seleccionadas
					</p>
				<?php endif; ?>

			</div>
		<table class="table table-sm table-bordered table-striped" 
       id="<?= isset($ordenCompraGlobal->id) ? 'tablaRequisicionesDetalles' : 'tablaRequisiciones' ?>" 
       width="100%">
          <thead>
            <tr>
            </tr>
          </thead>
          <tbody class="text-uppercase">
          </tbody>
      </table>
    </div> <!-- /.card-body -->
  </div> <!-- /.card -->
</div>

<div class="row">

	<?php if ( isset($ordenCompraGlobal->id) ) : ?>
	<div class="col-lg-3">

		<div class="card card-info card-outline">

			<div class="card-body">
				<!-- COMPROBANTES DE PAGO -->
				<div class="subir-comprobantes mb-3">
					<label>Listado comprobantes de pago</label>
					
					<button type="button" class="btn btn-info" id="btnSubirComprobantes">
						<i class="fas fa-folder-open"></i> Cargar Comprobantes de Pago
					</button>

					<?php if (isset($ordenCompraGlobal->id)) : ?>
						<?php foreach ($ordenCompraGlobal->comprobantesPago as $comprobante) : ?>
							<p class="text-info mb-0">
								<?= $comprobante['archivo']; ?>
								<i class="ml-1 fas fa-eye text-warning verArchivo" 
								style="cursor: pointer;" 
								data-ruta="<?= $comprobante['ruta']; ?>"></i>
							</p>
						<?php endforeach; ?>
					<?php endif; ?>

					<!-- Archivos cargados dinámicamente -->
					<span class="lista-archivos"></span>

					<!-- Input oculto para subir archivos -->
					<input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" multiple>

					<!-- Ayuda -->
					<div class="text-muted mt-1">
						Archivos permitidos PDF (con capacidad máxima de 4MB)
					</div>
				</div>
			</div>

		</div> <!-- <div class="box box-info"> -->

	</div> 
	<?php endif; ?>

	<div class="col-lg-9 card card-success card-outline">

		<div class="card-body">
			<div class="box-header with-border">
				<h3 class="box-title">Partidas</h3>
				<?php if ( !isset($ordenCompraGlobal->id) ) : ?>
					<p>
						* Seleccione las partidas para la Orden de Compra
					</p>
				<?php else: ?>
					<p>
						* Partidas seleccionadas
					</p>
				<?php endif; ?>
			</div>
			<div class="table-responsive">

				<table class="table table-sm table-bordered table-striped" 
		id="<?= isset($ordenCompraGlobal->id) ? 'tablaPartidasDetalles' : 'tablaPartidas' ?>" 
		width="100%">

					<thead>
							<th style="vertical-align:middle"><div style="width: 10px;">#</div></th>							
							<th style="vertical-align:middle"><div style="width: 70px;">Cantidad</div></th>
							<th style="vertical-align:middle"><div style="width: 250px;">Descripción</div></th>
							<th style="vertical-align:middle"><div style="width: 140px;">Valor Unitario</div></th>
							<th style="vertical-align:middle"><div style="width: 140px;">Importe</div></th>
					</thead>

					<tbody class="text-uppercase">

					</tbody> <!-- <tbody class="text-uppercase"> -->

				</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaOrdenCompraDetalles" width="100%"> -->

			</div> <!-- <div class="table-responsive"> -->

		</div> <!-- <div class="card-body"> -->

	</div> <!-- <div class="card card-info card-outline"> -->

</div>
