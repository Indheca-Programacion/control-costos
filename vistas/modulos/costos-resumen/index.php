<?php use App\Route;
?>

<div class="content-wrapper position-relative">


	<div id="scrollBtnContainer" class="position-fixed" style="bottom: 1.5rem; right: 1.5rem; z-index: 100; display: none;">
		<button id="scrollBtn" type="button" class="btn btn-primary btn-lg">
			<i class="fas fa-angle-double-down text-lg"></i>
		</button>
	</div>

	<section class="content-header">
		<input type="hidden" id="usuarioRoal" value="<?php echo $usuarioRoal; ?>">

		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>Resumen de Costos <small class="font-weight-light">Visor</small></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
						<li class="breadcrumb-item active">Resumen de Costos</li>
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
					<div class="card card-secondary card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-binoculars"></i>
								Visor de Resumen de Costos
							</h3>
							<div class="card-tools">
								<div class="input-group input-group-sm " style="flex-wrap: nowrap;">
									<div class="input-group-prepend">
										<label class="input-group-text" for="filtroDivisas">Divisa</label>
									</div>
									<select class="custom-select select2" id="filtroDivisas">
										<?php foreach($divisas as $divisa) { ?>
											<option value="<?php echo $divisa["id"]; ?>">
											<?php echo mb_strtoupper(fString($divisa["nombreCorto"])); ?>
											</option>
										<?php } ?>
									</select>
								</div>
								<button type="button" id="btnFiltrar" class="btn btn-outline-info mt-2">
									<i class="fas fa-sync-alt"></i> Listado
								</button>   
							</div>
						</div>

						<div class="collapse show" id="collapseFiltros">
							<div class="card card-body mb-0">
								<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
								<div class="row">

									<div class="col-md-6">

										<div class="input-group input-group-sm mb-0" style="flex-wrap: nowrap;">
											<div class="input-group-prepend">
												<label class="input-group-text" for="filtroObraId">Obra</label>
											</div>
											<select class="custom-select select2" id="filtroObraId" <?php if ($usuarioRoal ) echo 'disabled'; ?>>
												<option value="0" selected>Selecciona una Obra</option>
												<?php foreach($obras as $obra) { ?>
												<option value="<?php echo $obra["id"]; ?>" <?php if ($usuarioRoal && $obra["id"] == 109 ) echo 'selected'; ?>>
												<?php echo '[ ' . mb_strtoupper(fString($obra["empresas.nombreCorto"])) . ' ] ' . mb_strtoupper(fString($obra["descripcion"])); ?>
												</option>
												<?php } ?>
											</select>
										</div>

									</div><!-- <div class="col-md-6"> -->
									<div class="col-md-6 d-flex justify-content-end">
										
										<!-- Year Filter -->
										<div id="yearFilterWrapper" class="col-md-6 input-group input-group-sm mb-3 d-none form-group" style="flex-wrap: nowrap;">
											<div class="input-group-prepend">
												<label  class="input-group-text" for="filterYear">Año:</label>
											</div>
											<select id="filterYear" class="custom-select select2">
												<option value="all">Todos los años</option>
												<option value="2025">2025</option>
												<option value="2024">2024</option>
												<option value="2023">2023</option>
											</select>
										</div>

										<!-- <div id="monthFilterWrapper" class="col-md-6 input-group input-group-sm mb-3 d-none form-group" style="flex-wrap: nowrap;">
											<div class="input-group-prepend">
												<label class="input-group-text"  for="filterMonth">Mes:</label>
											</div>

											<select id="filterMonth" class="custom-select select2">
												<option value="all">Todos los meses</option>
												<option value="1">Enero</option>
												<option value="2">Febrero</option>
												<option value="3">Marzo</option>
												<option value="4">Abril</option>
												<option value="5">Mayo</option>
												<option value="6">Junio</option>
												<option value="7">Julio</option>
												<option value="8">Agosto</option>
												<option value="9">Septiembre</option>
												<option value="10">Octubre</option>
												<option value="11">Noviembre</option>
												<option value="12">Diciembre</option>
											</select>

										</div> -->
										<!-- <button type="button" id="btnAddSemanas" data-toggle="modal" data-target="#modalAgregarSemana" class="btn btn-outline-info m-1 d-none">Añadir Semanas</button>
										<button type="button" class="btn btn-info m-1 d-none" id="btnImportPlantilla"  data-toggle="modal" data-target="#modalImportarPlantilla"><i class="fas fa-file-import"></i> Importar Plantilla</button>
										<a class="btn btn-info btn m-1 d-none" target="_blank" id="btnImprimir"><i class='fas fa-print'></i> Imprimir</a> -->
									</div> <!-- <div class="col-md-6 d-flex justify-content-end"> -->
										
								</div> <!-- <div class="row"> -->
							</div> <!-- <div class="card card-body mb-0"> -->
						</div> <!-- <div class="collapse" id="collapseFiltros"> -->

            			<div class="card-body px-0 pb-0">

							<div class="card card-primary card-outline card-outline-tabs mb-0 d-none">
								<div class="card-header">
									<div class="input-group float-right" style="width: 300px; padding-bottom: 10px;">
										<select id="filtroPresupuesto" class="select2 form-control" <?php if ($usuarioRoal ) echo 'disabled'; ?>>
											<option value="0" selected>General</option>
										</select>
										<div class="input-group-append">
											<button type="button" class="btn btn-sm btn-outline-primary" id="btnAgregarPresupuesto" <?php if ($usuarioRoal ) echo 'disabled'; ?>>
												<i class="fas fa-plus"></i>
											</button>
										</div>
									</div>

									<ul class="nav nav-tabs" id="tabCostosResumen" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" id="resumen-costos-tab" data-toggle="pill" href="#resumen-costos" role="tab" aria-controls="resumen-costos" aria-selected="true">Resumen</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="listado-insumos-tab" data-toggle="pill" href="#listado-insumos" role="tab" aria-controls="listado-insumos" aria-selected="false">Directos</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" id="listado-indirectos-tab" data-toggle="pill" href="#listado-indirectos" role="tab" aria-controls="listado-indirectos" aria-selected="false">Indirectos</a>
										</li>
									</ul>
									
								</div>
								<div class="card-body">
									<div class="tab-content" id="tabCostosResumenContent">
										<div class="tab-pane fade show active" id="resumen-costos" role="tabpanel" aria-labelledby="resumen-costos-tab">
											<?php include "vistas/modulos/costos-resumen/form-section-resumen.php"; ?>
										</div>
										<div class="tab-pane fade" id="listado-insumos" role="tabpanel" aria-labelledby="listado-insumos-tab">
											<?php include "vistas/modulos/costos-resumen/form-section-insumos.php"; ?>
										</div>
										<div class="tab-pane fade" id="listado-indirectos" role="tabpanel" aria-labelledby="listado-indirectos-tab">
											<?php include "vistas/modulos/costos-resumen/form-section-indirectos.php"; ?>
										</div>
									</div>
								</div> <!-- /.card-body -->
							</div> <!-- /.card -->

						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->
		
		<!-- Directos -->

		<!-- Modal id="modalAgregarInsumo" -->
		<div class="modal fade" id="modalAgregarInsumo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalAgregarInsumoLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<!-- <div class="modal-content" style="min-height: 30rem;"> -->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalAgregarInsumoLabel"><i class="fas fa-plus"></i> Agregar Directo</h5>
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

						<form id="formInsumosSend">
							<input name="insumoId" id="modalAgregarInsumo_insumoId" hidden>
							<div class="row">
								<div class="col-md-6 form-group">
									<label for="modalAgregarInsumo_insumoTipoId">Tipo de Directo:</label>
									<div class="input-group">
										<select disabled name="insumoTipoId" id="modalAgregarInsumo_insumoTipoId" class="custom-select form-controls select2ModalAgregarInsumo">
											<option value="">Selecciona un Tipo de Directo</option>
										</select>
										<div class="input-group-append">
											<button type="button" id="btnBuscarInsumo" class="btn btn-sm btn-outline-primary" title="Buscar Insumo">
												<i class="fas fa-search"></i>
											</button>
										</div>
									</div>
								</div>
								<div class="col-md-6 form-group">
									<label for="modalAgregarInsumo_codigo">Código:</label>
									<input disabled type="text" id="modalAgregarInsumo_codigo" name="codigo" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del directo">
								</div>
							</div>

							<div class="form-group">
								<label for="modalAgregarInsumo_descripcion">Descripción:</label>
								<textarea disabled id="modalAgregarInsumo_descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del directo"></textarea>
							</div>

							<div class="row">
								<div class="col-md-6 form-group">
									<label for="modalAgregarInsumo_unidadId">Unidad:</label>
									<select disabled name="unidadId" id="modalAgregarInsumo_unidadId" class="custom-select form-controls select2ModalAgregarInsumo">
										<option value="">Selecciona una Unidad</option>
									</select>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 form-group">
									<label for="modalAgregarInsumo_unidadId">Cantidad:</label>
									<input type="number" id="modalAgregarInsumo_cantidad" name="cantidad" value="" class="form-control form-control-sm text-uppercase" min="0" placeholder="Ingresa la cantidad del directo">
								</div>
								<div class="col-md-6 form-group">
									<label for="modalAgregarInsumo_presupuesto">Presupuesto:</label>
									<input type="number" id="modalAgregarInsumo_presupuesto" name="presupuesto" value="" class="form-control form-control-sm text-uppercase" min="0" placeholder="Ingresa el presupuesto del directo">
								</div>
								<div class="col-md-6 form-group">
									<label for="modalAgregarInsumo_presupuesto_dolares">Presupuesto Dolares:</label>
									<input type="number" id="modalAgregarInsumo_presupuesto_dolares" name="presupuesto_dolares" value="" class="form-control form-control-sm text-uppercase" min="0" placeholder="Ingresa el presupuesto del directo">
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnAgregar">
							<i class="fas fa-save"></i> Agregar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal id="modalCrearInsumo" -->
		<div class="modal fade" id="modalCrearInsumo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalCrearInsumoLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<!-- <div class="modal-content" style="min-height: 30rem;"> -->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalCrearInsumoLabel"><i class="fas fa-plus"></i> Crear Directo</h5>
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

						<form id="formInsumosSendCreate">
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="modalCrearInsumo_insumoTipoId">Tipo de Directo:</label>
								<div class="input-group">
									<select name="insumoTipoId" id="modalCrearInsumo_insumoTipoId" class="custom-select form-controls select2ModalCrearInsumo">
										<option value="">Selecciona un Tipo de Directo</option>
									</select>
								</div>
							</div>
							<div class="col-md-6 form-group">
								<label for="modalCrearInsumo_codigo">Código:</label>
								<input type="text" id="modalCrearInsumo_codigo" name="codigo" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del insumo">
							</div>
						</div>

						<div class="form-group">
							<label for="modalCrearInsumo_descripcion">Descripción:</label>
							<textarea id="modalCrearInsumo_descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del insumo"></textarea>
						</div>

						<div class="row">
							<div class="col-md-6 form-group">
								<label for="modalCrearInsumo_unidadId">Unidad:</label>
								<select name="unidadId" id="modalCrearInsumo_unidadId" class="custom-select form-controls select2ModalCrearInsumo">
									<option value="">Selecciona una Unidad</option>
								</select>
							</div>
						</div>
						</form>
						<!-- <div class="alert alert-danger error-validacion mb-0 d-none">
							<ul class="mb-0">
								<li></li>
							</ul>
						</div> -->
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnGuardar">
							<i class="fas fa-save"></i> Guardar
						</button>
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
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div>

						<table class="table table-sm table-bordered" id="tablaSeleccionarInsumos" width="100%">
							<thead>
								<tr>
									<th style="width: 10px;">#</th>
									<th>Tipo</th>
									<th>Código</th>
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
		<button id="openModalDetails" class="d-none" data-toggle="modal" data-target="#myModal"></button>
		<!--  -->
		<div id="myModal" class="modal fade" data-keyboard="false" tabindex="-1" aria-labelledby="myModalLabel">
			<div class="modal-dialog  modal-dialog-centered" role="document">
				<div class="modal-content" >
				<div class="modal-header">
					<h5 class="modal-title" id="ModalDetallesLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modalDetallesContent">
					
				</div>
				</div>
			</div>
		</div>
		
		<!-- Indirectos -->

		<!-- Modal id="modalAgregarIndirecto" -->
		<div class="modal fade" id="modalAgregarIndirecto" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalAgregarIndirectoLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<!-- <div class="modal-content" style="min-height: 30rem;"> -->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalAgregarIndirectoLabel"><i class="fas fa-plus"></i> Agregar Indirecto</h5>
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

						<form id="formIndirectoSendCreate">
						<div class="row">
							<input hidden id="modalAgregarIndirecto_indirectoId">
							<div class="col-md-6 form-group">
								<label for="modalAgregar_indirectoTipoId">Tipo de Indirecto:</label>
								<div class="input-group">
									<select disabled name="indirectoTipoId" id="modalAgregarIndirecto_indirectoTipoId" class="custom-select form-controls select2ModalAgregarIndirecto">
										<option value="">Selecciona un Tipo de Indirecto</option>
									</select>
									<div class="input-group-append">
										<button type="button" id="btnBuscarIndirecto" class="btn btn-sm btn-outline-primary" title="Buscar Indirecto">
											<i class="fas fa-search"></i>
										</button>
									</div>
								</div>
							</div>
							<div class="col-md-6 form-group">
								<label for="modalAgregarIndirecto_codigo">Código:</label>
								<input disabled type="text" id="modalAgregarIndirecto_codigo" name="codigo" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del indirecto">
							</div>
						</div>
						<div class="form-group">
							<label for="modalAgregarIndirecto_descripcion">Descripción:</label>
							<textarea disabled id="modalAgregarIndirecto_descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del indirecto"></textarea>
						</div>

						<div class="row">
							<div class="col-md-6 form-group">
								<label for="modalAgregarIndirecto_unidadId">Unidad:</label>
								<select disabled name="unidadId" id="modalAgregarIndirecto_unidadId" class="custom-select form-controls select2ModalAgregarIndirecto">
									<option value="">Selecciona una Unidad</option>
								</select>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="modalAgregarIndirecto_cantidad">Cantidad:</label>
								<input type="number" id="modalAgregarIndirecto_cantidad" name="cantidad" value="" class="form-control form-control-sm text-uppercase" min="0" placeholder="Ingresa la cantidad del indirecto">
							</div>
							<div class="col-md-6 form-group">
								<label for="modalAgregarIndirecto_presupuesto">Presupuesto:</label>
								<input type="number" id="modalAgregarIndirecto_presupuesto" name="presupuesto" value="" class="form-control form-control-sm text-uppercase" min="0" placeholder="Ingresa el presupuesto del indirecto">
							</div>
							<div class="col-md-6 form-group">
								<label for="modalAgregarIndirecto_presupuesto_dolares">Presupuesto Dolares:</label>
								<input type="number" id="modalAgregarIndirecto_presupuesto_dolares" name="presupuesto_dolares" value="" class="form-control form-control-sm text-uppercase" min="0" placeholder="Ingresa el presupuesto del indirecto">
							</div>
						</div>						
						
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnAgregar">
							<i class="fas fa-save"></i> Guardar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal id="modalCrearIndirecto" -->
		<div class="modal fade" id="modalCrearIndirecto" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalCrearIndirectoLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<!-- <div class="modal-content" style="min-height: 30rem;"> -->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalCrearIndirectoLabel"><i class="fas fa-plus"></i> Crear Indirecto</h5>
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

						<form id="formIndirectosSend">
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="modalCrearIndirecto_indirectoTipoId">Tipo de Indirecto:</label>
								<select name="indirectoTipoId" id="modalCrearIndirecto_indirectoTipoId" class="custom-select form-controls select2">
									<option value="">Selecciona un Tipo de Indirecto</option>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="modalCrearIndirecto_numero">Número:</label>
								<input type="text" id="modalCrearIndirecto_numero" name="numero" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número del indirecto" data-inputmask='"mask": "9.9{1,3}.9{1,3}"' data-mask>
							</div>
						</div>

						<!-- <div class="form-group">
							<label for="modalCrearIndirecto_descripcion">Descripción:</label>
							<input type="text" id="modalCrearIndirecto_descripcion" name="descripcion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del indirecto">
						</div> -->

						<div class="form-group">
							<label for="modalCrearIndirecto_descripcion">Descripción:</label>
							<textarea id="modalCrearIndirecto_descripcion" name="descripcion" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa la descripción del indirecto"></textarea>
						</div>

						<div class="row">
							<div class="col-md-6 form-group">
								<label for="modalCrearIndirecto_unidadId">Unidad:</label>
								<select name="unidadId" id="modalCrearIndirecto_unidadId" class="custom-select form-controls select2">
									<option value="">Selecciona una Unidad</option>
								</select>
							</div>
						</div>
						</form>

						<!-- <div class="alert alert-danger error-validacion mb-0 d-none">
							<ul class="mb-0">
								<li></li>
							</ul>
						</div> -->
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnGuardar">
							<i class="fas fa-save"></i> Guardar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal id="modalBuscarIndirecto" -->
		<div class="modal fade" id="modalBuscarIndirecto" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalBuscarIndirectoLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<!-- <div class="modal-content" style="min-height: 30rem;"> -->
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalBuscarIndirectoLabel"><i class="fas fa-search"></i> Buscar Directo</h5>
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

						<table class="table table-sm table-bordered" id="tablaSeleccionarIndirectos" width="100%">
							<thead>
								<tr>
									<th style="width: 10px;">#</th>
									<th>Tipo</th>
									<th>Código</th>
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

		<!-- Modal Requisicion -->

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
							<div class="row d-none" data-tipo="insumo">
								<div class="col-md-6 form-group">
									<label for="modalAgregarPartida_insumoTipoId">Tipo de Directo:</label>
									<input type="text" id="modalAgregarPartida_insumoTipoId" name="insumoTipoId" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el tipo de insumo" disabled>
								</div>
								<div class="col-md-6 form-group">
									<label for="modalAgregarPartida_codigo">Código:</label>
									<input disabled type="text" id="modalAgregarPartida_codigo" name="codigo" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del insumo" disabled>
								</div>
							</div>

							<div class="row d-none" data-tipo="indirecto">
								<div class="col-md-6 form-group">
									<label for="modalAgregarPartida_indirectoTipoId">Tipo de Indirecto:</label>
									<input type="text" id="modalAgregarPartida_indirectoTipoId" name="indirectoTipoId" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el tipo de indirecto" disabled>
								</div>
								<div class="col-md-6 form-group">
									<label for="modalAgregarPartida_numero">Número:</label>
									<input type="text" id="modalAgregarPartida_numero" name="numero" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número del indirecto" data-inputmask='"mask": "9.9{1,3}.9{1,3}"' data-mask disabled>
								</div>
							</div>

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
									<input type="text" id="modalAgregarPartida_costo" value="1" min="1" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el costo" readonly>
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

							<!-- <div class="row">
								<div class="form-group col-md-6">
									<label for="modalAgregarPartida_ISR">Retencion ISR:</label>
									<input type="number" name="" id="modalAgregarPartida_ISR" value="0" class="form-control form-control-sm">
								</div>
								
								<div class="form-group col-md-6">
									<label for="modalAgregarPartida_IVA_retencion">Retencion IVA:</label>
									<select id="modalAgregarPartida_IVA_retencion" class="custom-select form-controls select2">
										<option value="0">0%</option>
										<option value="4">4%</option>
										<option value="10.6667">10.6667%</option>
									</select>
								</div>

								<div class="form-group col-md-6">
									<label for="modalAgregarPartida_IVA">IVA:</label>
									<select id="modalAgregarPartida_IVA" class="custom-select form-controls select2">
										<option value="0">0%</option>
										<option value="16">16%</option>
									</select>
								</div>

								<div class="form-group col-md-6">
									<label for="modalAgregarPartida_descuento">Descuento:</label>
									<input type="text" id="modalAgregarPartida_descuento" value="0" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el descuento">
								</div>
							</div> -->

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

						<!-- <div class="alert alert-danger error-validacion mb-0 d-none">
							<ul class="mb-0">
								<li></li>
							</ul>
						</div> -->
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

		<!-- Modal id="modalCrearRequisicion" -->
		<div class="modal fade" id="modalCrearRequisicion" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalCrearRequisicionLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalCrearRequisicionLabel"><i class="fas fa-plus"></i> Crear Requisición</h5>
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

						<form id="formCrearRequisicionSend">
						<div class="table-responsive">
							<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%">
								<thead>
									<tr>
										<th class="text-right" style="min-width: 80px;">Partida</th>
										<th>Tipo</th>
										<th style="min-width: 160px;">Tipo Directo / Indirecto</th>
										<th style="min-width: 128px;">Código / Número</th>
										<th style="min-width: 192px;">Descripción</th>
										<th class="text-right" style="min-width: 64px;">Cant.</th>
										<th style="min-width: 112px;">Unidad</th>
										<th style="min-width: 160px;">Num. de Parte</th>
										<th style="min-width: 320px;">Concepto</th>
									</tr>
								</thead>
								<tbody class="text-uppercase">
								</tbody>
							</table>
						</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnGuardar" disabled>
							<i class="fas fa-save"></i> Guardar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal id="modalCrearRequiInsumoIndirecto" -->
		<div class="modal fade" id="modalCrearRequiInsumoIndirecto" data-backdrop="static" data-keyboard="false" aria-labelledby="modalCrearRequiInsumoIndirectoLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalCrearRequiInsumoIndirectoLabel"><i class="fas fa-plus"></i> Crear Requisición </h5>
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

						<div class="row">

							<div class="col-sm-6 form-group">
								<label for="modalCrearRequiInsumoIndirecto_folio">Folio</label>
								<input type="text" class="form-control form-control-sm text-uppercase" id="modalCrearRequiInsumoIndirecto_folio" name="folio">
							</div>
							<div class="col-md-6 form-group">
								<label for="divisas">Divisa:</label>
								<select class="custom-select select2" id="divisa">
									<?php foreach($divisas as $divisa) { ?>
										<option value="<?php echo $divisa["id"]; ?>">
										<?php echo mb_strtoupper(fString($divisa["nombreCorto"])); ?>
										</option>
									<?php } ?>
								</select>
							</div>

						</div>

						<div class="row">		

							<div class="col-md-6 form-group">
								<label>Rango:</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="far fa-clock"></i></span>
									</div>
									<input disabled type="text" class="form-control float-right form-control form-control-sm" id="reservationdate">
								</div>

							</div>

							<div class="col-md-6 form-group">
								<label for="modalCrearRequiInsumoIndirecto_tipoReq">Tipo de Requisicion:</label>
								<select class="custom-select select2" id="modalCrearRequiInsumoIndirecto_tipoReq">
									<option value="0">Programada</option>
									<option value="1">Urgente</option>
								</select>
							</div>

							<div class="col-md-6 form-group">
								<label for="modalCrearRequiInsumoIndirecto_fechaRequerida">Fecha Requerida:</label>						
								<div class="input-group date" data-target-input="nearest">
									<input type="text" name="fechaRequerida" id="modalCrearRequiInsumoIndirecto_fechaRequerida" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#modalCrearRequiInsumoIndirecto_fechaRequerida">
									<div class="input-group-append" data-target="#modalCrearRequiInsumoIndirecto_fechaRequerida" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
									</div>
								</div>
							</div>

							<div class="col-md-6 form-group">
								<label for="modalCrearRequiInsumoIndirecto_direccion">Direccion:</label>
								<input type="text" id="modalCrearRequiInsumoIndirecto_direccion" name="direccion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la direccion">
							</div>

							<div class="col-md-6 form-group">
								<label for="modalCrearRequiInsumoIndirecto_especificaciones">Especificaciones:</label>
								<textarea id="modalCrearRequiInsumoIndirecto_especificaciones" name="especificaciones" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa las especificaciones"></textarea>
							</div>

							<div class="col-md-6 form-group">
								<label for="modalCrearRequiInsumoIndirecto_categoriaOrden">Categoría :</label>
								<select name="categoriaId" id="categoriaId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
									<option value="">Selecciona una Categoría</option>
									<?php foreach($categoriasOrdenCompra as $categoria) : ?>
										<option value="<?php echo $categoria['id']; ?>">
											<?php echo $categoria['nombre']; ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-outline-primary btnGuardar">
							<i class="fas fa-save"></i> Guardar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal id="modalCrearRequisicionPersonal" -->
		<div class="modal fade" id="modalCrearRequisicionPersonal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalCrearRequisicionPersonalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalAgregarPartidaLabel"><i class="fas fa-plus"></i> Agregar Nomina</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<!-- Errores -->
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div>
						
						<form id="formAgregarRequisicionPersonalSend">
							<!-- Directos -->
							<div class="row d-none" data-tipo="insumo">
								<div class="col-md-6 form-group">
									<label for="modalCrearRequisicionPersonal_insumoTipoId">Tipo de Directo:</label>
									<input type="text" id="modalCrearRequisicionPersonal_insumoTipoId" name="insumoTipoId" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el tipo de directo" disabled>
								</div>
								<div class="col-md-6 form-group">
									<label for="modalCrearRequisicionPersonal_codigo">Código:</label>
									<input disabled type="text" id="modalCrearRequisicionPersonal_codigo" name="codigo" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del directo" disabled>
								</div>
							</div>
							<!-- Indirectos -->
							<div class="row d-none" data-tipo="indirecto">
								<div class="col-md-6 form-group">
									<label for="modalCrearRequisicionPersonal_indirectoTipoId">Tipo de Indirecto:</label>
									<input type="text" id="modalCrearRequisicionPersonal_indirectoTipoId" name="indirectoTipoId" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el tipo de indirecto" disabled>
								</div>
								<div class="col-md-6 form-group">
									<label for="modalCrearRequisicionPersonal_numero">Número:</label>
									<input type="text" id="modalCrearRequisicionPersonal_numero" name="numero" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número del indirecto" data-inputmask='"mask": "9.9{1,3}.9{1,3}"' data-mask disabled>
								</div>
							</div>
							<!-- Cantidad y Salario semanal -->
							<div class="row">
								<div class="col-sm-6 form-group">
									<label for="modalCrearRequisicionPersonal_descripcion">Descripción:</label>
									<input type="text" id="modalCrearRequisicionPersonal_descripcion" name="descripcion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del Directo" disabled>
								</div>
								<input type="text" id="modalCrearRequisicionPersonal_unidadId" hidden>
								<div class="col-sm-6 form-group">
									<label for="modalCrearRequisicionPersonal_unidad">Unidad:</label>
									<input type="text" id="modalCrearRequisicionPersonal_unidad" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la unidad" disabled>
								</div>
								<div class="col-sm-6 form-group">
									<label for=" ">Cantidad:</label>
									<input name="cantidad" type="number" id="modalCrearRequisicionPersonal_cantidad" min="1" class="form-control form-control-sm campoConDecimal" value="1" placeholder="Ingresa la cantidad">
								</div>
								<div class="col-6 form-group">
									<label for="modalCrearRequisicionPersonal_costo">Salario Semanal:</label>
									<input name="salario_semanal" type="number" id="modalCrearRequisicionPersonal_costo" min="1" value="" class="form-control form-control-sm" value="1" placeholder="Ingresa el salario semanal">
								</div>
							</div>
							<!-- Duracion de contrato -->
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="modalCrearRequisicionPersonal_fechaInicio">Inicia:</label>
										<input name="fecha_inicio" type="date" id="modalCrearRequisicionPersonal_fechaInicio" class="form-control form-control-sm">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="modalCrearRequisicionPersonal_fechaTermina">Termina:</label>
										<input name="fecha_fin" type="date" id="modalCrearRequisicionPersonal_fechaTermina" class="form-control form-control-sm">
									</div>	
								</div>
							</div>
							<!-- Viaticos y otros -->
							<!-- <div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="modalCrearRequisicionPersonal_viaticos">Viaticos:</label>
										<input name="viaticos" type="number" id="modalCrearRequisicionPersonal_viaticos" placeholder="Ingrese los Viaticos" min="0" value="0" class="form-control form-control-sm">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="">Otros:</label>
										<input name="otros" value="0" type="number" id="modalCrearRequisicionPersonal_otros" min="0" class="form-control form-control-sm">
									</div>
								</div>
							</div> -->
							<!-- Costo Neto -->
							<!-- <div class="row flex justify-content-center">
								<div class="col-6 form-group">
									<label for="modalCrearRequisicionPersonal_costoneto">Costo Neto</label>
									<span>(incluye finiquito y Costo Social)</span>
									<input name="costo_neto" value="0" type="number" id="modalCrearRequisicionPersonal_costoneto" min="0" class="form-control form-control-sm">
								</div>
							</div> -->
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary btnCrearRequisicionPersonal" data-tipo>
							<i class="fas fa-plus"></i> Agregar Nomina
						</button>
					</div>
				</div>
			</div>
		</div>
		
		<!-- Modal id="modalAgregarSemana" -->
		<div class="modal fade" id="modalAgregarSemana" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalAgregarSemanaLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalAgregarPartidaLabel"><i class="fas fa-plus"></i> Agregar Semana</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<!-- Errores -->
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div>
						<div class="row d-flex justify-content-center">
							<div class="col-md-6">
								<label for="modalAgregarSemana_semana">Semanas</label>
								<input type="number" name="semana" min="1" id="modalAgregarSemana_semana" class="form-control form-control-sm">
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary btnAgregarSemana" data-tipo>
							<i class="fas fa-plus"></i> Agregar Semana
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal id="modalImportarPlantilla" -->
		<div class="modal fade" id="modalImportarPlantilla" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalImportarPlantillaLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="modalAgregarPartidaLabel"><i class="fas fa-file-import"></i> Importar Partida</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">

						<!-- Errores -->
						<div class="alert alert-danger error-validacion mb-2 d-none">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div>
						<div class="row d-flex justify-content-center">
							<div class="col-md-6">
								<label for="modalImportarPlantilla_plantilla">Plantilla:</label>
								<select name="plantilla" id="modalImportarPlantilla_plantilla" class="custom-select form-controls form-control-sms select2">
									<option>Seleccione una plantilla</option>
									<?php foreach($plantillas as $plantilla) { ?>
									<option value="<?php echo $plantilla["id"]; ?>"
										><?php echo mb_strtoupper(fString($plantilla["nombreCorto"])); ?>
									</option>
									<?php } ?>
								</select>	
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-primary btnImportarPartida" data-tipo>
							<i class="fas fa-plus"></i> Importar Partida
						</button>
					</div>
				</div>
			</div>										
		</div>
	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/costos-resumen.js?v=2.4.2');
