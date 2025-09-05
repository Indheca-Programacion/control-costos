<?php

	if (isset($inventario->id)) {

		$ordenCompra = isset($old["ordenCompra"]) ? $old["ordenCompra"] : $inventario->ordenCompra;
		$almacenId = isset($old["almacen"]) ? $old["almacen"] : $inventario->almacen;
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $inventario->observaciones;
		$numeroContrato = isset($old["numeroContrato"]) ? $old["numeroContrato"] : $inventario->numeroContrato;
		$entrega = isset($old["entrega"]) ? $old["entrega"] : $inventario->entrega;
		$inventarioId = $inventario->id;
		$fechaEntrega = $inventario->fechaCreacion;
		$firma=$inventario->firma;
		$folio = isset($obra->prefijo) ? $obra->prefijo.'-'.$requisicion->folio : '';
		$requisicionId = isset($id) ? $id : $requisicion->id;

	} else {
		$requisicionId = isset($id) ? $id : 0;
		$numeroContrato = isset($old["numeroContrato"]) ? $old["numeroContrato"] : '';
		$ordenCompra = isset($old["ordenCompra"]) ? $old["ordenCompra"] : '';
		$almacenId = isset($old["almacen"]) ? $old["almacen"] : '';
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : '';
		$entrega = isset($old["entrega"]) ? $old["entrega"] : '';
		$fechaEntrega = fFechaLarga(date('Y-m-d'));
		$inventarioId = 0;
		$folio = Isset($id) ? $obra->prefijo.'-'.$requisicion->folio : '';
	}

?>

<input type="hidden" name="_token" id="token" value="<?php echo createToken(); ?>">
<input type="hidden" id="requisicionId" value="<?= $requisicionId ?>">
<input type="hidden" id="inventarioId" value="<?= $inventarioId ?>">
<input type="hidden" id="firma" name="firma">

<div class="row">

	<div class="col-md-6">
		
		<div class="card card-warning card-outline">

			<div class="card-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
						<!-- <li></li> -->
					</ul>
				</div>
				<div class="row">
				
					<!-- Requisicion -->
					<div class="col-md-6 form-group">

						<label for="requisicion">Requisicion:</label>
						<input type="text" disabled name="requisicion" class="form-control form-control-sm text-uppercase" value="<?php echo $folio ?>" >
					</div>

					<!-- OC -->
					<div class="col-md-6 form-group">

						<label for="ordenCompra">Orden de Compra:</label>
						<input type="text" <?php if(isset($inventario->id) || $id==null ) echo 'disabled' ?> name="ordenCompra" class="form-control form-control-sm text-uppercase" value="<?php echo $ordenCompra ?>" placeholder="ingrese la orden de compra">

					</div>

					<!-- Almacen -->
					<div class="col-md-6 form-group">

						<label for="almacen">Almacen:</label>
						<select <?php if(isset($inventario->id)) echo 'disabled' ?> name="almacen" id="almacen" class="custom-select form-controls select2">
							<option value="">Seleccione una almacen</option>
							<?php foreach($almacenes as $almacen) { ?>
							<option value="<?php echo $almacen["id"]; ?>"
								<?php echo $almacenId == $almacen["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($almacen["nombreCorto"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>
					
					<div class="col-md-6 form-group">
						<label for="numeroContrato">Numero de Contrato:</label>
						<select <?php if(isset($inventario->id)) echo 'disabled' ?> name="numeroContrato" id="numeroContrato" class="custom-select form-controls select2">
							<option value="">Seleccione un numero de contrato</option>
							<?php foreach($tiposContratoInventario as $tipoContratoInventario) { ?>
							<option value="<?php echo $tipoContratoInventario["id"]; ?>"
								<?php echo $numeroContrato == $tipoContratoInventario["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($tipoContratoInventario["nombreCorto"])); ?>
							</option>
							<?php } ?>
						</select>
					</div>
					
					<!-- Observaciones -->
					<div class="col-md-12 form-group">
						<label for="observaciones">Observaciones:</label>
						<input type="text" <?php if(isset($inventario->id)) echo 'disabled' ?> name="observaciones" class="form-control form-control-sm text-uppercase" value="<?php echo $observaciones ?>" placeholder="ingrese las observaciones">
					</div>

					<!-- Entrega -->
					<div class="col-md-6 form-group">

						<label for="entrega">Entrega</label>
						<input type="text" <?php if(isset($inventario->id)) echo 'disabled' ?> name="entrega" class="form-control form-control-sm text-uppercase"value="<?php echo $entrega ?>" placeholder="Ingrese el nombre que entrega">

					</div>

					<!-- Fecha de Entrega -->
					<div class="col-md-6 form-group">
						<label for="fechaEntrega">Fecha de Entrega</label>
						<div class="input-group date" id="fechaEntregaDTP" data-target-input="nearest">
							<input type="text" name="fechaEntrega" id="fechaEntrega" value="<?php echo $fechaEntrega; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de entrega" data-target="#fechaEntregaDTP" <?php  if(isset($inventario->id)) echo 'disabled' ?>>
							<div class="input-group-append" data-target="#fechaEntregaDTP" data-toggle="datetimepicker">
								<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
							</div>
						</div>
					</div>

				</div>			

			</div><!-- <div class="card-body"> -->

		</div><!-- <div class="card card-warning card-outline"> -->
	
	</div><!-- <div class="col-md-6"> -->

	<div class="col-md-6">
		<?php if(!isset($inventario->id)) : ?>
			<div class="row">
				<?php if($id ==null) : ?>
					<input type="hidden" name="directo" id="directo">
					<input type="hidden" name="indirecto" id="indirecto">

					<div class="col-md-6 form-group">
						<label for="descripcion">Descripcion:</label>
						<input type="text" name="descripcion" id="descripcion" disabled class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-md-6 form-group">
						<label for="unidad">Unidad:</label>
						<input type="text" name="unidad" id="unidad" disabled class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-md-6 form-group">
						<label for="cantidad">Cantidad</label>
						<input type="number" id="cantidad" name="cantidad" class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-md-6 form-group">
						<label for="numParte">Num. de Parte</label>
						<input type="text" name="numParte" id ="numeroParte" value="NA" class="form-control form-control-sm text-uppercase">
					</div>
					<div class="col-12 form-group">
						<button type="button" id="btnBuscarInsumo" class="btn btn-outline-primary" title="Buscar Directo">
							<i class="fas fa-search">Buscar Directo</i>
						</button>
						<button type="button" id="btnBuscarIndirecto" class="btn btn-outline-primary" title="Buscar Indirecto">
							<i class="fas fa-search">Buscar Indirecto</i>
						</button>
					</div>
				<?php endif ?>
				<!-- Button trigger modal -->
				<div class="col-12 form-group">
					<?php if($id ==null) : ?>	
						<button type="button" id="btnAgregarPartida" class="btn btn-outline-info">
							<i class="fas fa-plus"></i> Añadir partida
						</button>
					<?php endif ?>
					<button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#firmaModal">
						Firmar
					</button>
				</div>
			</div>
		<?php endif ?>
	</div><!-- <div class="col-md-6"> -->

	<div class="col-12">
		<div class="card card-success card-outline">
		
			<div class="card-body">
		
				<div class="table-responsive">
				<input type="file" id="archivoSubir" style="display: none;" multiple>
		
					<table class="table table-sm table-bordered table-striped mb-0 tablaDetalle" id="tablaInventarioDetalles" width="100%">
		
						<thead>
		
							<tr>
								<?php if(!isset($inventario->id)) : ?>
								<th style="width: 10px;"></th>
								<?php endif ?>
								<th class="text-right" style="width: 10px;">Partida</th>
								<th style="width: 120px;">Cant.</th>
								<?php if(isset($inventario->id)) : ?>
								<th style="width: 120px;">Cant. Disponible</th>
								<?php endif ?>
								<th style="width: 120px;">Unidad</th>
								<th style="width: 120px;">Num. de Parte</th>
								<th>Descripción</th>
								<?php if(isset($inventario->id)) : ?>
								<th style="width: 10px;">Acciones</th>
								<?php endif ?>
							</tr>
		
						</thead>
		
						<tbody class="text-uppercase">
							
						</tbody>
		
					</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaInventarioDetalles" width="100%"> -->
		
				</div> <!-- <div class="table-responsive"> -->
		
			</div> <!-- <div class="card-body"> -->
		
		</div> <!-- <div class="card card-info card-outline"> -->
	</div><!-- <div class="col-12"> -->
</div>
