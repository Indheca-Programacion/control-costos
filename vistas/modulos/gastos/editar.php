<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Gastos <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('gastos.index')?>"> <i class="fas fa-list-alt"></i> Gastos</a></li>
	            <li class="breadcrumb-item active">Editar gastos</li>
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
							<i class="fas fa-edit"></i>
							Editar gastos
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body" id="gasto-section">
						<div class="alert alert-danger error-validacion d-none">
							<ul class="mb-0">
								<li></li>
							</ul>
						</div>
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('gastos.update', $gastos->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/gastos/formulario.php"; ?>
							<div class="row">
								<div class="col">
									<button type="button" id="btnSend" class="btn btn-outline-primary <?php if($gastos->requisicionId !== null) echo 'd-none' ;?>">
										<i class="fas fa-save"></i> Actualizar
									</button>
									<button type="button" id="btnDownload" class="btn btn-outline-primary"><i class="fas fa-download"></i> Descargar Archivos</button>
									<?php if($permisoAutorizar && is_null($gastos->usuarioIdAutorizacion)) : ?>
										<button type="button" id="btnAutorizarGasto" class="btn btn-success">
											<i class="fas fa-check"></i> Autorizar
										</button>
									<?php endif; ?>
									<?php if( $gastos->cerrada == 0 && $gastos->requisicionId == null) : ?>
										<button type="button" id="btnBuscarRequisicion" class="btn btn-warning" data-toggle="modal" data-target="#modalBuscarRequisicion">
											<i class="fas fa-plus"></i> Enlazar Requisición
										</button>
									<?php endif; ?>
									<?php if($gastos->procesado == 0) : ?>
										<button type="button" id="btnEnProceso" class="btn btn-primary">
											<i class="fas fa-location-arrow"></i> Marcar como en Proceso
										</button>
									<?php endif; ?>
									<?php if(  $gastos->procesado == 1) : ?>
										<button type="button" id="btnProcesado" class="btn btn-success">
											<i class="fas fa-check"></i> Marcar Procesado
										</button>
									<?php endif; ?>
									<?php if( $gastos->procesado == 2) : ?>
										<button type="button" id="btnPagado" class="btn btn-success">
											<i class="fas fa-money-check-alt"></i> Marcar como Pagado
										</button>
									<?php endif; ?>
									<a href="<?php echo Route::names('gastos.print', $gastos->id); ?>" target="_blank" class="btn btn-info float-right"><i class="fas fa-print"></i> Imprimir</a>
								</div>
							</div>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			<?php if($gastos->cerrada == 0) : ?>
				<div class="col-md-6">
					<div class="card card-success card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-edit"></i>
								Añadir Gastos
							</h3>
						</div><!-- <div class="card-header"> -->
						<div class="card-body">
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<?php include "vistas/modulos/gastos/form-add.php"; ?>
						</div><!-- /.card-body -->
					</div><!-- /.card -->
				</div> <!-- /.col -->
			<?php endif ?>
			<div class="col-12">
				<div class="card card-warning card-outline">
					<div class="card-header">
						<h3 class="card-title">
							Gastos
						</h3>
					</div><!-- <div class="card-header"> -->
                    <div class="card-body">
					<input type="file" id="archivoSubir" style="display: none;" multiple>
                        <table class="table table-sm table-bordered table-striped" id="tablaDetallesGastos" width="100%">
							<thead>
								<tr>
									<th style="width:10px">#</th>
									<th>Fecha</th>
									<th>Tipo de Gasto</th>
									<th>Total</th>
									<th>Obra</th>
									<th>Numero Economico</th>
									<th>Descripcion</th>
									<th>Observaciones</th>
									<th>Acciones</th>
								</tr> 
							</thead>
						</table>

						<button class="btn btn-info mt-2 float-right" id="btnDescargarFacturas">
							<i class="fas fa-download"></i> Descargar Facturas
						</button>
                    </div><!-- /.card-body -->
                </div><!-- /.card -->
			</div>
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->


	<!-- Modal id="modalBuscarRequisicion" -->
	<div id="modalBuscarRequisicion" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalBuscarRequisicionLabel">
		<div class="modal-dialog modal-lg  modal-dialog-centered">
			<div class="modal-content" >
				<div class="modal-header">
					<h5 class="modal-title" id="modalBuscarRequisicionLabel">Seleccionar Requisicion</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modalDetallesContent">
					<table class="table table-sm table-bordered text-uppercase" id="tablaRequisiciones" >

						<thead>
						</thead>

					</table>
				</div>
			</div>
		</div>
	</div>

	</section>

	<!-- Modal id="modalVerArchivos" -->
	<div class="modal fade" id="modalVerArchivos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerArchivosLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVerArchivosLabel">Evidencia Documental <span></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="accordion" id="accordionArchivos">
					</div>
					<div class="alert alert-danger error-validacion d-none">
						<ul class="mb-0">
							<li></li>
						</ul>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal id="modalCrearRequisicion" -->
	<div class="modal fade" id="modalCrearRequisicion" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalCrearRequisicionLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalCrearRequisicionLabel"><i class="fas fa-plus"></i> Crear Requisición </h5>
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

					<div class="row justify-content-sm-center">
						<div class="col-sm-6 form-group">
							<label for="modalCrearRequiInsumoIndirecto_folio">Folio</label>
							<input disabled type="text" class="form-control form-control-sm text-uppercase" id="modalCrearRequiInsumoIndirecto_folio" name="folio">
						</div>
					</div>

					<div class="row justify-content-sm-center">
						<div class="col-sm-6 form-group">
							<label for="modalCrearRequiInsumoIndirecto_periodos">Semana:</label>
							<select id="modalCrearRequiInsumoIndirecto_periodos" name="periodo" class="custom-select select2">
								<option value="">Selecciona una Semana</option>
								<!-- <option value="1">Período 1</option>
								<option value="2">Período 2</option>
								<option value="3">Período 3</option> -->
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-outline-primary" id="btnCrearRequisicion">
						<i class="fas fa-save"></i> Guardar
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal id="modalEditarGasto" -->
	<div class="modal fade" id="modalEditarGasto" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalEditarGastoLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalEditarGastoLabel"><i class="fas fa-edit"></i> Editar Detalles de Gasto</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="alert alert-danger error-validacion mb-2 d-none">
						<ul class="mb-0">
							<li></li>
						</ul>
					</div>
					<form id="formEditarGasto">
						<input type="hidden" name="id" id="gastoDetalleId" value="">
						<input type="hidden" name="_token" value="<?php echo token(); ?>">
						<div class="form-group">
							<label for="editarFechaGasto">Fecha</label>
							<input type="text" class="form-control form-control-sm datetimepicker-input" id="editarFechaGasto" name="fecha" required data-toggle="datetimepicker" data-target="#editarFechaGasto">
						</div>
						<div class="form-group">
							<label for="editarTipoGasto">Tipo de Gasto</label>
							<select class="form-control select2 form-control-sm" id="editarTipoGasto" name="tipoGasto" required>
								<option value="" selected>Selecciona un tipo de gasto</option>
								<?php foreach($gastosTipo as $value) { ?>
								<option value="<?php echo $value["id"]; ?>">
								<?php echo mb_strtoupper(fString($value["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="editarObraGasto">Obra</label>
							<select class="custom-select select2" id="editarObraGastoo" name="obra" required>
								<option value="" selected>Selecciona una Obra</option>
								<?php foreach($obras as $obra) { ?>
								<option value="<?php echo $obra["id"]; ?>">
								<?php echo '[ ' . mb_strtoupper(fString($obra["empresas.nombreCorto"])) . ' ] ' . mb_strtoupper(fString($obra["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="editarSolicito">Solicito:</label>
							<input type="text" class="form-control form-control-sm text-uppercase" id="editarSolicito" name="solicito">
						</div>
						<div class="form-group">
							<label for="editarDescripcionGasto">Descripción</label>
							<select class="custom-select select2" id="editarDescripcionGasto" name="obraDetalle">
								<option value="" selected>Selecciona una descripcion</option>
								<?php foreach($obra_detalles as $detalle) { ?>
								<option value="<?php echo $detalle["id"]; ?>">
								<?php echo mb_strtoupper(fString($detalle["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>
						</div>
						<div class="form-group">
							<label for="editarNumeroEconomico">Número Económico</label>
							<input type="text" class="form-control form-control-sm text-uppercase" id="editarNumeroEconomico" name="economico">
						</div>
						<div class="form-group">
							<label for="editarCosto">Costo</label>
							<input type="number" step="0.01" class="form-control form-control-sm" id="editarCosto" name="costo" required>
						</div>
						<div class="form-group">
							<label for="editarCantidad">Cantidad</label>
							<input type="number" step="0.01" class="form-control form-control-sm" id="editarCantidad" name="cantidad" required>
						</div>
						<div class="form-group">
							<label for="editarFactura">Factura</label>
							<input type="text" class="form-control form-control-sm text-uppercase" id="editarFactura" name="factura">
						</div>
						<div class="form-group">
							<label for="editarProveedor">Proveedor</label>
							<input type="text" class="form-control form-control-sm text-uppercase" id="editarProveedor" name="proveedor">
						</div>
						<div class="form-group">
							<label for="editarObservacionesGasto">Observaciones</label>
							<textarea class="form-control form-control-sm text-uppercase" id="editarObservacionesGasto" name="observaciones" rows="3"></textarea>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-outline-primary" id="btnActualizarGasto">
						<i class="fas fa-save"></i> Guardar Cambios
					</button>
				</div>
			</div>
		</div>
	</div>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/gastos.js?v=1.9');
?>
