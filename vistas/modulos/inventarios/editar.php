<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Inventarios <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('inventarios.index')?>"> <i class="fas fa-boxes"></i> Inventarios</a></li>
	            <li class="breadcrumb-item active">Editar inventario</li>
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
				<div class="col-12">
					<div class="card card-primary card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-edit"></i>
								Editar Inventario
							</h3>
						</div> <!-- <div class="card-header"> -->
						<ul class="nav nav-tabs" id="tabInventario" role="tablist">
							<li class="nav-item">
								<a class="nav-link active" id="entradas-tab" data-toggle="pill" href="#entradas" role="tab" aria-controls="entradas" aria-selected="true">Entradas</a>
							</li> 
							<li class="nav-item">
								<a class="nav-link" id="salidas-tab" data-toggle="pill" href="#salidas" role="tab" aria-controls="listado-salidas" aria-selected="false">Salidas</a>
							</li>
						</ul> <!-- <ul class="nav nav-tabs" id="tabInventario" role="tablist"> -->
						<div class="card-body">
							<div class="tab-content" id="tabInventario">
								<div class="tab-pane fade show active" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
									<?php include "vistas/modulos/errores/form-messages.php"; ?>
									<form id="formSend" method="POST" action="<?php echo Route::names('inventarios.update', $inventario->id); ?>">
										<input type="hidden" name="_method" value="PUT">
										<?php include "vistas/modulos/inventarios/formulario.php"; ?>
										<a target="_blank" href="<?php echo Route::names('inventarios.print',$inventario->id); ?>" class="btn btn-outline-primary">
											<i class="fas fa-print"></i> Imprimir
										</a>
										<div id="msgSend"></div>
									</form>
									<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
								</div> <!-- <div class="tab-pane fade show active" id="inventarios" role="tabpanel" aria-labelledby="inventarios-tab"> -->
								<div class="tab-pane fade show" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
									<div class="card card-info card-outline">
		
										<div class="card-body">

											<button type="button" class="btn btn-outline-primary float-right mb-2" data-toggle="modal" data-target="#modalCrearSalida"> Generar Salida </button>
									
											<div class="table-responsive">
									
												<table class="table table-sm table-bordered table-striped mb-0 tablaDetalle" id="tablaSalidas" width="100%">
									
													<thead>
									
														<tr>
															<th></th>
															<th class="text-right" style="width: 10px;">#</th>									
															<th style="width: 50px;">Folio</th>
															<th style="width: 120px;">Fecha Salida</th>
															<th>Entregó</th>
															<th>Estatus</th>
															<th style="width: 100px;">Acciones</th>
														</tr>
									
													</thead>
									
													<tbody class="text-uppercase">
														

													</tbody>
									
												</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaSalidas" width="100%"> -->
									
											</div> <!-- <div class="table-responsive"> -->
									
										</div> <!-- <div class="card-body"> -->
									
									</div> <!-- <div class="card card-info card-outline"> -->
									
								</div> <!-- <div class="tab-pane fade show active" id="inventarios" role="tabpanel" aria-labelledby="inventarios-tab"> -->
						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->

		<!-- Modal id="modalCrearSalida" -->
		<div class="modal fade" id="modalCrearSalida" data-backdrop="static" data-keyboard="false" aria-labelledby="modalCrearSalidaLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalCrearSalidaLabel"><i class="fas fa-plus"></i> Crear Salida</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div> <!-- <div class="modal-header"> -->
					<div class="modal-body">
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div> <!-- <div class="alert alert-danger error-validacion mb-2 d-none"> -->

						<form id="formSalidaSend">

							<table class="table table-sm table-bordered table-striped mb-0 tablaDetalle display" id="tablaSalidasDetalles" width="100%">
			
								<thead>
				
									<tr>
										<th class="text-right" style="width: 10px;">#</th>									
										<th style="width: 80px;">Cant.</th>
										<th style="width: 80px;">Cant. disponible</th>
										<th style="width: 120px;">Unidad</th>
										<th>Numero de Parte</th>
										<th>Descripción</th>
									</tr>
				
								</thead>
			
								<tbody class="text-uppercase">
									
									<tr>
										
									</tr>

								</tbody>
			
							</table> <!-- <table class="table table-sm table-bordered table-striped mb-0 tablaDetalle" id="tablaSalidas" width="100%"> -->

						</form> <!-- <form id="formSalidaSend"> -->

						<div class="row">
							<div class="col-12" role="alert">
								<p id="resguardo"><strong></strong></p>
								<p id="recuperacion"><strong></strong></p>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnGuardarSalida">
							<i class="fas fa-save"></i> Crear
						</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Modal id="modalVerSalida" -->
		<div class="modal fade" id="modalVerImagenes" data-backdrop="static" data-keyboard="false" aria-labelledby="modalVerImagenesLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalVerImagenesLabel">Fotos de la Partida <span></span></h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div> <!-- <div class="modal-header"> -->
					<div class="modal-body">
						<div class="row row-cols-1 row-cols-lg-2 imagenes">
						</div>
						<div class="alert alert-danger error-validacion d-none">
							<ul class="mb-0">
								<li></li>
							</ul>
						</div> <!-- <div class="alert alert-danger error-validacion d-none"> -->
					</div> <!-- <div class="modal-body"> -->
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div> <!-- <div class="modal-footer"> -->
				</div> <!-- <div class="modal-content"> -->
			</div> <!-- <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"> -->
		</div> <!-- <div class="modal fade" id="modalVerImagenes" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerImagenesLabel" aria-hidden="true"> -->
		
		<!-- Modal id="modalFirmarSalida" -->
		<div class="modal fade modal-fullscreen" id="modalFirmarSalida" data-backdrop="static" data-keyboard="false" aria-labelledby="modalFirmarSalidaLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalFirmarSalidaLabel"><i class="fas fa-signature"></i> Firmar Salida</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div> <!-- <div class="modal-header"> -->
					<div class="modal-body">
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div> <!-- <div class="alert alert-danger error-validacion mb-2 d-none"> -->
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="usuarioIdRecibe">Recibe:</label>
									<select name="usuarioIdRecibe" id="usuarioIdRecibe" class="custom-select form-controls select2" style="width: 50%">
										<option value="">Seleccione la persona quien recibe</option>
										<?php foreach($usuarios as $usuario) { ?>
										<option value="<?php echo $usuario["id"]; ?>"
											><?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
										</option>
										<?php } ?>
									</select>
								</div>
								<div class="text-center" role="alert">
									<strong>Nota:</strong> Para firmar, dibuje su firma en el recuadro de abajo.
								</div> <!-- <div class="" role="alert"> -->
							</div>
							<div class="col-md-12 text-center">
								<canvas class="border" id="canvasFirma" ></canvas>
							</div>
							<div class="col-md-12 form-group">
								<button id="btnLimpiarFirmaSalida" type="button" class="btn btn-outline-info"><i class="fas fa-broom"></i>Limpiar</button>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary" id="btnFirmarSalida">
							<i class="fas fa-save"></i> Firmar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal id="modalCrearResguardo" -->
		<div class="modal fade" id="modalCrearResguardo" data-backdrop="static" data-keyboard="false" aria-labelledby="modalCrearResguardoLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalCrearResguardoLabel"><i class="fas fa-plus"></i> Crear Resguardo</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div> <!-- <div class="modal-header"> -->
					<div class="modal-body">
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div> <!-- <div class="alert alert-danger error-validacion mb-2 d-none"> -->

						<form id="formSalidaSend">

							<table class="table table-sm table-bordered table-striped mb-0  display" id="tablaSalidasResguardo" width="100%">
								<thead>
									<tr>
										<th class="" style="width: 10px;">#</th>	
										<th style="width: 80px;">Partida</th>
										<th class="" style="width: 80px;">Cantidad Salidas</th>									
										<th style="width: 80px;">Cantidad Disponibles</th>
										<th style="width: 120px;">Unidad</th>
										<th>Numero de Parte</th>
										<th>Descripción</th>
									</tr>
				
								</thead>
			
								<tbody class="text-uppercase">
									
									<tr>
										
									</tr>

								</tbody>
			
							</table> <!-- <table class="table table-sm table-bordered table-striped mb-0 tablaDetalle" id="tablaSalidas" width="100%"> -->

						</form> <!-- <form id="formSalidaSend"> -->


						<input type="hidden" name="salidaId" id="salidaId" class="form-control form-control-sm text-uppercase" value="">

						<div class="row align-items-start">
							<div class="form-group col-6">										
								<label for="recibe">Recibe:</label>
									<select name="recibeResguardo" id="recibeResguardo" class="custom-select form-controls select2Add">
										<option value="0">Seleccione la persona quien retira</option>
										<?php foreach($usuarios as $usuario) { ?>
										<option value="<?php echo $usuario["id"]; ?>">
											<?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
										</option>
										<?php } ?>
									</select>
								</div>

								<div class="form-group col-6">
									<label for="observacion">Observaciones:</label>
									<input type="text" name="observacionesResguardo" id="observacionesResguardo" class="form-control form-control-sm text-uppercase" value="" placeholder="ingrese las observaciones">
								</div>
						</div>

						<div class="row">

						<div class="col-sm-6 form-group">
							<label for="fecha">Fecha:</label>
							<input class="form-control form-control-sm" 
								type="datetime-local" 
								id="fecha" 
								name="fecha" 
								value="" 
								placeholder="Ingresa la fecha" 
								required>
						</div>

						</div>

						<div class="row">
							<div class="col-12">
							
								<div class="text-center" role="alert">
									<strong>Nota:</strong> Para firmar, dibuje su firma en el recuadro de abajo.
								</div> <!-- <div class="" role="alert"> -->
							</div>
							<div class="col-md-12 text-center">
								<canvas class="border" id="canvasFirmaResguardo" ></canvas>
							</div>
							<div class="col-md-12 form-group">
								<button id="btnLimpiarResguardo" type="button" class="btn btn-outline-info"><i class="fas fa-broom"></i>Limpiar</button>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnGuardarResguardo">
							<i class="fas fa-save"></i> Crear
						</button>
					</div>
				</div>
			</div>
		</div>

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/inventarios.js?v=5');
?>
