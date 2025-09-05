<div class="row">

  <?php
  use App\Route;
  ?>

	<div class="col-12">

		<?php if ( count($cotizaciones)>0 ) : ?>
			
		<div class="card card-info card-outline">

      <?php if ( $formularioEditable ) : ?>
      <div class="card-tools pt-1">
        <button type="button" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#crearCotizacionModal">
          <i class="fas fa-plus"></i> Crear cotizacion
        </button>
      </div>
      <?php endif; ?>

			<!-- <div class="card-body table-responsive p-0"> -->
      <div class="card-body pt-0">
        <input type="file" id="fileCotizacion" name="fileCotizacion" class="d-none">
  				<table class="table table-hover text-nowrap" id="tablaRequisicionCotizaciones" width="100%">
          <!-- <table class="table table-bordered table-striped" id="tablaMaquinarias" width="100%"> -->
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Proveedor</th>
                      <th>Estatus</th>
                      <th>Fecha Cotizacion</th>
                      <th>Fecha Limite</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>

                  	<?php foreach($cotizaciones as $key => $value) { ?>
                    <tr>
                      <td><?php echo $key+1; ?></td>
                      <td class="text-uppercase"><?php echo fString($value['proveedores.razonSocial']); ?></td>
                      <td class="text-uppercase"><?php echo fString($value['estatus.descripcion']); ?></td>
                      <td><?php echo fFechaLarga($value["fechaCreacion"]); ?></td>
                      <td><?php echo fFechaLarga($value["fechaLimite"]); ?></td>
                      <td>
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-upload-document" data-id="<?php echo $value['proveedores.id']; ?>">
                          <i class="fas fa-upload"></i> Subir cotizacion
                        </button>
                      </td>
                    </tr>
                    <?php } ?>

                  </tbody>
          </table>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php else: ?>

    <?php if ( $formularioEditable ) : ?>
      <button type="button" class="btn btn-outline-primary float-right mt-2 mr-2 " data-toggle="modal" data-target="#crearCotizacionModal">
        <i class="fas fa-plus"></i> Crear cotizacion
      </button>
    <?php endif; ?>
		<div class="jumbotron text-center">
			<p class="display-4">Requisicion sin Cotizaciones</p>
		</div>

    <?php endif; ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->
