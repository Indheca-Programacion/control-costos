<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Requisiciones <small class="font-weight-light">Crear</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('requisiciones.index')?>"> <i class="fas fa-tools"></i> Requisiciones</a></li>
	            <li class="breadcrumb-item active">Crear requisición</li>
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
			<div class="col-md-12">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-plus"></i>
							Crear requisición
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('requisiciones.store'); ?>" enctype="multipart/form-data">
							<?php include "vistas/modulos/requisiciones/formulario-step.php"; ?>
									
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	<!-- Modal id="modalBuscarIndirecto" -->
	<div class="modal fade" id="modalBuscarIndirecto" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalBuscarIndirectoLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<!-- <div class="modal-content" style="min-height: 30rem;"> -->
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalBuscarIndirectoLabel"><i class="fas fa-search"></i> Buscar Indirecto</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<span class="text-muted mb-2 d-block">
						(Si no aparece su concepto favor de comunicarse con el Encargado de materiales al <strong>+52 229 145 4660</strong> para que lo agregue y después volver a buscarlo)
					</span>
					<div class="alert alert-danger error-validacion mb-2 d-none">
						<ul class="mb-0">
							<!-- <li></li> -->
						</ul>
					</div>

					<table class="table table-sm table-bordered" id="tablaSeleccionarIndirectos" width="100%">
						<thead>
							<tr>
								<th style="width: 10px;">#</th>
								<th style="min-width: 128px;">Descripción</th>
								<th>Unidad</th>
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

	<!-- Modal id="modalBuscarInsumo" -->
	<div class="modal fade" id="modalBuscarInsumo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalBuscarInsumoLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<!-- <div class="modal-content" style="min-height: 30rem;"> -->
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalBuscarInsumoLabel"><i class="fas fa-search"></i> Buscar Directo</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<span class="text-muted mb-2 d-block">
						(Si no aparece su concepto favor de comunicarse con el Encargado de materiales al <strong>+52 229 145 4660</strong> para que lo agregue y después volver a buscarlo)
					</span>
					<div class="alert alert-danger error-validacion mb-2 d-none">
						<ul class="mb-0">
							<!-- <li></li> -->
						</ul>
					</div>

					<table class="table table-sm table-bordered" id="tablaSeleccionarInsumos" width="100%">
						<thead>
							<tr>
								<th style="width: 10px;">#</th>
								<th style="min-width: 128px;">Descripción</th>
								<th>Unidad</th>
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

	<!-- Modal id="modalAgregarPartida" -->
	<div class="modal fade" id="modalAgregarPartida" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalAgregarPartidaLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalAgregarPartidaLabel"><i class="fas fa-plus"></i> Agregar Partida</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">

					<input type="text" hidden id="modalAgregarPartida_obraDetalleId">
					<div class="alert alert-danger error-validacion mb-2 d-none">
						<ul class="mb-0">
							<!-- <li></li> -->
						</ul>
					</div>

					<form id="formAgregarPartidaSend">

						<input type="text" id="modalAgregarPartida_insumoId" hidden>
						<input type="text" id="modalAgregarPartida_indirectoId" hidden>

						<div class="form-group">
							<label for="modalAgregarPartida_descripcion">Descripción:</label>
							<input type="text" id="modalAgregarPartida_descripcion" name="descripcion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del insumo" disabled>
						</div>

						<div class="row">
							<div class="col-sm-6 form-group">
								<label for=" ">Cantidad:</label>
								<input type="number" id="modalAgregarPartida_cantidad" value="1" min="1" class="form-control form-control-sm" placeholder="Ingresa la cantidad">
							</div>
							<div class="col-6 form-group">
								<label for="modalAgregarPartida_costo">Costo:</label>
								<input type="text" id="modalAgregarPartida_costo" value="1" min="1" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el costo">
							</div>
							<div class="col-6 form-group">
								<label for="modalAgregarPartida_costo_unitario">Costo Unitario:</label>
								<input type="number" id="modalAgregarPartida_costo_unitario" value="1" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el costo unitario" >
							</div>
							<input type="text" id="modalAgregarPartida_unidadId" hidden>
							<div class="col-sm-6 form-group">
								<label for="modalAgregarPartida_unidad">Unidad:</label>
								<input type="text" id="modalAgregarPartida_unidad" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la unidad" disabled>
							</div>
						</div>

						<div class="form-group">
							<label for="modalAgregarPartida_concepto">Concepto:</label>
							<textarea id="modalAgregarPartida_concepto" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa el concepto de la partida"></textarea>
						</div>

						<div class="subir-fotos mb-1">
							<button type="button" class="btn btn-info" id="btnSubirFotos">
								<i class="fas fa-images"></i> Subir Fotos
							</button>
							<!-- <span class="lista-fotos">
							</span> -->
							<span class="previsualizar">
							</span>
							<!-- <input type="file" class="form-control form-control-sm d-nones" id="fotos" name="fotos[]" multiple> -->
							<input type="file" class="form-control form-control-sm d-none" id="modalAgregarPartida_fotos" multiple>
						</div>
						<div class="mb-1 text-muted">Archivos permitidos JPG O PNG (con capacidad máxima de 1MB)</div>
					</form>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-primary btnAgregarPartida" data-tipo>
						<i class="fas fa-plus"></i> Agregar partida
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Seleccionar Proveedor -->
	<div id="modalSeleccionarProveedor" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalSeleccionarProveedorLabel">
		<div class="modal-dialog modal-lg  modal-dialog-centered">
			<div class="modal-content" >
				<div class="modal-header">
					<h5 class="modal-title" id="modalSeleccionarProveedorLabel">Seleccionar proveedor</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<a target="_blank" href="<?=Route::names('proveedores.create')?>" class="btn btn-primary ml-3">
						Agregar Proveedor
					</a>
				</div>
				<div class="modal-body" id="modalDetallesContent">
					<table class="table table-sm table-bordered" id="tablaProveedores" >

						<thead>
							<tr>
								<th style="width:10px">#</th>
								<th>Proveedor</th>
								<th>Telefono</th>
								<th>Direccion</th>
								<th>Email</th>
								<th>Calificacion</th>
							</tr>
						</thead>

					</table>
				</div>
			</div>
		</div>
	</div>

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/crear-requisicion.js?v=2.00');
?>
