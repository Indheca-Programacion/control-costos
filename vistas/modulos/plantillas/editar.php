<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Plantillas <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('plantillas.index')?>"> <i class="fas fa-list-alt"></i> Plantillas</a></li>
	            <li class="breadcrumb-item active">Editar plantilla</li>
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
							Editar plantilla
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<input type="hidden" id="plantilla" value="<?= $plantilla->id ?>">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('plantillas.update', $plantilla->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/plantillas/formulario.php"; ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<button type="button" id="" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#importarMaterialesModal">
								<i class="fas fa-file-import"></i> Importar
							</button>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			<div class="col-md-6">
				<div class="card card-info card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-plus"></i>
							Agregar Directo/Indirecto
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<div class="alert alert-danger error-validacion mb-2 d-none" id="error-validacion">
							<ul class="mb-0">
								<!-- <li></li> -->
							</ul>
						</div>
						<div class="row">
						
							<input type="hidden" id="fk_plantillaId" value="<?= $plantilla->id ?>">

							<div class="col-md-6 form-group">
								<label for="tipo">Seleccione:</label>
								<select name="tipo" id="tipo" class="custom-control select2">
									<option value="0">Seleccione un tipo</option>
									<option value="1">Indirecto</option>
									<option value="2">Directo</option>
								</select>
							</div>

							<div class="col-md-6 form-group directo d-none">
								<label for="directo">Directos:</label>
								<select class="custom-select select2" id="directoId" name="directo">
									<option value="" selected>Selecciona un directo</option>
									<?php foreach($Insumos as $value) { ?>
									<option value="<?php echo $value["id"]; ?>">
									<?php echo mb_strtoupper(fString($value["descripcion"])); ?>
									</option>
									<?php } ?>
								</select>
							</div>

							<div class="col-md-6 form-group indirecto d-none">
								<label for="indirecto">Indirectos:</label>
								<select class="custom-select select2" id="indirectoId" name="indirecto">
									<option value="" selected>Selecciona un indirecto</option>
									<?php foreach($Indirectos as $value) { ?>
									<option value="<?php echo $value["id"]; ?>">
									<?php echo mb_strtoupper(fString($value["descripcion"])); ?>
									</option>
									<?php } ?>
								</select>
							</div>

							<div class="col-md-6 form-group">
								<label for="">Cantidad:</label>
								<input type="text" id="cantidad" class="campoConDecimal form-control form-control-sm">
							</div>

							<div class="col-md-6 form-group">
								<label for="">Presupuesto:</label>
								<input type="text" id="presupuesto" class="campoConDecimal form-control form-control-sm">
							</div>

						</div>

						<button type="button" id="btnAgregar" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Agregar
							</button>
					</div> <!-- /.card-body -->
				</div>
			</div> <!-- /.col -->
			<div class="col-12">
				<div class="card card-warning card-outline">
					<div class="card-header">
						<h3 class="card-title">
							Directos Indirectos
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">

						<table class="table table-bordered table-striped" id="tablaPlantillasDetalles" width="100%">
                            
                            <thead>
                                <tr>
									<th>Tipo</th>
                                    <th>Descripcion</th>
                                    <th style="width:200px">Cantidad</th>
                                    <th style="width:200px">Presupuestos</th>
                                    <th style="width:10px">Acciones</th>
                                </tr> 
                            </thead>

                            <tbody>
                            </tbody>

                        </table>

					</div> <!-- /.card-body -->
				</div>
			</div>
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

	<!-- Modal -->
	<div class="modal fade" id="importarMaterialesModal" tabindex="-1" role="dialog" aria-labelledby="importarMaterialesModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="importarMaterialesModalTitle">Importar Materiales</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-12">
						<label for="">Obra</label>
						<select name="obra" id="obra" class="custom-select form-controls select2">
							<option value="">Selecciona una obra</option>
							<?php foreach($obras as $obra) { ?>
							<option value="<?php echo $obra["id"]; ?>"
								><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" id="impMateriales" class="btn btn-primary btnimpMateriales">Importar</button>
			</div>
			</div>
		</div>
	</div>
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/plantillas.js?v=1.00');
?>
