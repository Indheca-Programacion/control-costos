<div class="row">

  <?php
  use App\Route;
  
  ?>

	<div class="col-12">

		<?php if ( $requisicion->ordenes_compra ) : ?>
			
		<div class="card card-info card-outline">

      <?php if ( $formularioEditable ) : ?>
      <div class="card-tools pt-1">
        <a href="<?=Route::routes('requisiciones.crear-orden-compra', $requisicion->id)?>" target='_blank' class="btn btn-outline-primary float-right">
          <i class="fas fa-plus"></i> Crear Orden de Compra
        </a>
      </div>
      <?php endif; ?>

			<!-- <div class="card-body table-responsive p-0"> -->
      <div class="card-body pt-0">

  				<table class="table table-hover text-nowrap" id="tablaRequisicionOrdenes" width="100%">
          <!-- <table class="table table-bordered table-striped" id="tablaMaquinarias" width="100%"> -->
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Folio</th>
                      <th>Estatus</th>
                      <th>Fecha Requisición</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>

                  	<?php foreach($requisicion->ordenes_compra as $key => $value) { ?>
                    <tr>
                      <td><?php echo $key+1; ?></td>
                      <td class="text-uppercase"><?php echo fString($value['folio']); ?></td>
                      <td class="text-uppercase"><?php echo fString($value['estatus.descripcion']); ?></td>
                      <td><?php echo fFechaLarga($value["fechaCreacion"]); ?></td>
                      <td>
                        <a href="<?php echo Route::names(
                          $value['tipo_orden'] === 'ordencompra' ? 'orden-compra.edit' : 'orden-compra-globales.edit',
                          $value['id']
                        ); ?>" class='btn btn-xs btn-warning'>
                          <i class='fas fa-pencil-alt'></i>
                        </a>
                      </td>
                    </tr>
                    <?php } ?>

                  </tbody>
          </table>

          <?php
            // $comandoJS = "fDataTable('#tablaRequisicionOrdenes');"
          ?>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php else: ?>

    <?php if ( $formularioEditable ) : ?>
    <a href="<?=Route::routes('requisiciones.crear-orden-compra', $requisicion->id)?>" target='_blank' class="btn btn-outline-primary mt-2 mr-2 float-right">
      <i class="fas fa-plus"></i> Crear Orden de Compra
    </a>
    <?php endif; ?>
		<div class="jumbotron text-center">
			<p class="display-4">Requisicion sin Orden de Compra</p>
		</div>

    <?php endif; ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->
