<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Resguardos <small class="font-weight-light">Crear</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?php echo Route::routes('inicio'); ?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('resguardos.index')?>"> <i class="fas fa-building"></i> Resguardos</a></li>
	            <li class="breadcrumb-item active">Crear Resguardo</li>
	          </ol>
	        </div>
	      </div>
	    </div><!-- /.container-fluid -->

	</section>

	<section class="content">

	<?php if ( !is_null(flash()) ) : ?>
      <div class="d-none" id="msgToast" clase="<?=flash()->clase?>" titulo="<?=flash()->titulo?>" subtitulo="<?=flash()->subTitulo?>" mensaje="<?=flash()->mensaje?>"></div>
    <?php endif; ?>

    <div class="container-fluid">
		<div class="row">
			<div class="col-md-6">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-plus"></i>
							Crear resguardo
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('resguardos.store'); ?>"  enctype="multipart/form-data">
							<?php include "vistas/modulos/resguardos/formulario.php"; ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Guardar
							</button>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

		<!-- Modal id="modalBuscarInsumo" -->
		<div class="modal fade" id="modalBuscarInventario" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalBuscarInventarioLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<!-- <div class="modal-content" style="min-height: 30rem;"> -->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalBuscarInventarioLabel"><i class="fas fa-search"></i> Buscar Inventario</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div>
		
						<table class="table table-sm table-bordered" id="tablaSeleccionarInventario" width="100%">
							<thead>
								<tr>
									<th style="width: 10px;">#</th>
									<th style="min-width: 128px;">Descripción</th>
									<th>Cantidad</th>
									<th>Cantidad Disponible</th>
									<th>Unidad</th>
									<th>Almacen</th>
									<th>Modelo</th>
									<th>Género</th>
									<th>SubGénero</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<!-- <button type="button" class="btn btn-outline-primary btnSeleccionar">
							<i class="fas fa-check"></i> Seleccionar
						</button> -->
					</div>
				</div>
			</div>
		</div>
	</section>

</div>


<?php
	array_push($arrayArchivosJS, 'vistas/js/resguardos.js?v=1.1');
?>