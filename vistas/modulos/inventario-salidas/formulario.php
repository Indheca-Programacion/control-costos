<?php
if (isset($inventarioSalida->id)) {

	$almacenId = $inventario->almacen;
	$entrega = $inventarioSalida->usuarioEntrega;
	$inventarioSalidaId = $inventarioSalida->id;
	$observaciones = $inventarioSalida->observaciones;
	$folio = $inventarioSalida->folio;

}

?>

<input type="hidden" name="_token" id="token" value="<?php echo createToken(); ?>">
<input type="hidden" id="inventarioSalida" value="<?= $inventarioSalidaId ?>">
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

					<!-- Folio -->
					<div class="col-md-6 form-group">

						<label for="folio">Folio:</label>
						<input type="text" name="folio" class="form-control form-control-sm text-uppercase" value="<?php echo $folio ?>" disabled>

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
					
					<!-- Observaciones -->
					<div class="col-md-6 form-group">
				
						<label for="observaciones">Observaciones:</label>
						<input type="text" <?php if(isset($inventario->id)) echo 'disabled' ?> name="observaciones" class="form-control form-control-sm text-uppercase" value="<?php echo $observaciones ?>" placeholder="ingrese las observaciones">
				
					</div>

					<!-- Entrega -->
					<div class="col-md-6 form-group">

						<label for="entrega">Entrega</label>
						<input type="text" <?php if(isset($inventario->id)) echo 'disabled' ?> name="entrega" class="form-control form-control-sm text-uppercase"value="<?php echo $entrega ?>" placeholder="Ingrese el nombre que entrega">

					</div>

					<!-- Recibe -->
					<div class="col-md-6 form-group">

						<label for="recibe">Recibe</label>
						<input type="text" name="recibe" class="form-control form-control-sm text-uppercase" placeholder="Ingrese el nombre que recibe">

					</div>

				</div>			

			</div><!-- <div class="card-body"> -->

		</div><!-- <div class="card card-warning card-outline"> -->
	
	</div><!-- <div class="col-md-6"> -->

	<div class="col-12">
		<div class="card card-success card-outline">
		
			<div class="card-body">
		
				<div class="table-responsive">
				<input type="file" id="archivoSubir" style="display: none;" multiple>
		
					<table class="table table-sm table-bordered table-striped mb-0 tablaDetalle" id="tablaSalidaDetalles" width="100%">
		
						<thead>
		
							<tr>
								
								<th class="text-right" style="width: 10px;">Partida</th>
								<th style="width: 120px;">Cant.</th>
								<th style="width: 120px;">Unidad</th>
								<th style="width: 120px;">Num. de Parte</th>
								<th>Descripción</th>

								
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