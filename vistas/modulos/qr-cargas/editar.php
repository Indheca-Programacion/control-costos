<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

<section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>Detalle Del Equipo De Carga <small class="font-weight-light">Listado</small></h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?=Route::names('qr-cargas.index')?>"> <i class="fas fa-list-alt"></i> Qr</a></li>
					<li class="breadcrumb-item active">Detalle Del Equipo De Carga</li>
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
			<div class="col-xl-8">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Detalle Del Equipo De Carga
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('maquinaria-traslados.store'); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/qr-cargas/formulario.php"; ?>
							<?php if ( !isset($qrCargas->idMaquinaria) ) : ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Asignar QR
							</button>
							<?php else : ?>
							<div class="mt-3">

								<?php if (isset($qrCargas->placa)) : ?>

									<?php if (App\Controllers\Autorizacion::permiso($usuarioAutenticado, "baja-qr", "actualizar")) : ?>
										<button type='button' class="btn btn-outline-danger" id="btnDarBaja">
											<i class="fas fa-eject"></i> Dar de Baja
										</button>
									<?php endif ?>

									<?php if (  App\Controllers\Autorizacion::permiso($usuarioAutenticado, "cargas", "crear") ) : ?>
										<button  type='button' class='btn btn-outline-primary' data-toggle='modal' data-target='#modalRegistrarCarga'>
											<i class="fas fa-truck-loading"></i>Registrar Carga
										</button> 	
									<?php endif ?>

									<?php if (  App\Controllers\Autorizacion::permiso($usuarioAutenticado, "movimientos", "actualizar") ) : ?>
										<button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#modalRegistrarMovimiento">
											<i class="fas fa-save"></i> Registrar Movimiento
										</button>
									<?php endif ?>


									<?php elseif (!isset($qrCargas->placa)) : ?>
										<button type='button' class="btn btn-outline-success" id="btnAsignar">
											<i class="fas fa-plus"></i> Asignar QR
										</button>
									<?php endif ?>
							</div>
							<?php endif ?>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->

			<div class="col-xl-4">
				<?php if (isset($qrCargas->placa)) : ?>
					<div class="card card-primary card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-edit"></i>
								Archivos
							</h3>
						</div> <!-- <div class="card-header"> -->
						<div class="card-body">
							<form 
							class="row"
							id="formSend" method="POST" action="<?php echo Route::names('qr-cargas.update',$qrCargas->id); ?>"  enctype="multipart/form-data">
								<input type="hidden" name="_method" value="PUT">
								<?php include "vistas/modulos/qr-cargas/form-archivos.php"; ?>

								<?php if (isset($qrCargas->placa)) : ?>
									<button type='submit' class="btn btn-outline-success" >
										<i class="fas fa-eject"></i> Actualizar Archivos
									</button>
								<?php endif; ?>

								<div id="msgSend"></div>
							</form>
						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				<?php endif ?>
        	</div> <!-- /.col -->

      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->
</section>
</div>

<?php array_push($arrayArchivosJS, 'vistas/js/qr-cargas.js?v=2.00');?>

<!-- MODALE REGISTRO DE MOVIMIENTOS -->
<div class="modal fade modal-fullscreen" id="modalRegistrarMovimiento" data-backdrop="static" data-keyboard="false" aria-labelledby="modalRegistrarMovimientoLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalRegistrarMovimientoLabel"><i class="fas fa-truck-loading mr-2"></i>Registrar Movimiento</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div> <!-- <div class="modal-header"> -->
					
				<input type="hidden" class="" value="<?php echo isset($verificarCargaActiva["idCarga"]) ? $verificarCargaActiva["idCarga"] : ''; ?>" id="idCarga">

			<div class="modal-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
						<!-- <li></li> -->
					</ul>
				</div> <!-- <div class="alert alert-danger error-validacion mb-2 d-none"> -->
				<div class="row">

					<div class="col-md-6 form-group">
						<label for="obraId">Obra:</label>
						<select id="obraId" class="custom-select form-controls select2">
							<option value="">Seleccione una obra</option>
							<?php foreach($obras as $obra) { ?>

							<option value="<?php echo $obra["id"]; ?>"
								<?php echo $usuario->ubicacionId == $obra["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>
					</div>				

					<div class="col-6 form-group">

						<label for="tipo">Tipo:</label>
						<br>
						<input type="checkbox" class="form-control form-control-sm text-uppercase" name="nTipo" id="tipo" value="1">

					</div> <!-- <div class="col-md-6"> -->

					<div class="col-md-6 form-group">
						<label for="cargado">¿Está cargado?</label>
						<select id="cargado" class="custom-select form-controls select2">
							<option value="">Seleccione una opción</option>
							<option value="1">Sí</option>
							<option value="0">No</option>
						</select>
					</div>

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-outline-primary" id="btnRegistrarMovimiento">
					<i class="fas fa-save"></i> Guardar Movimiento
				</button>
			</div>
		</div>
	</div>
</div>

<!-- MODAL REGISTRO DE CARGAS -->
<div class="modal fade modal-fullscreen" id="modalRegistrarCarga" data-backdrop="static" data-keyboard="false" aria-labelledby="modalRegistrarCargaLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalRegistrarCargaLabel"><i class="fas fa-truck-loading mr-2"></i>Registrar Carga</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
			</div> <!-- <div class="modal-header"> -->
					
			<div class="modal-body">

				<?php if (isset($verificarCargaActiva['estatus']) && $verificarCargaActiva['estatus'] === 'ACTIVADO'): ?>
					<div class="alert alert-warning error-validacion ">
						<h6>La carga está activa</h6>
					</div>
				<?php endif; ?>


				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
						<!-- <li></li> -->
					</ul>
				</div> <!-- <div class="alert alert-danger error-validacion mb-2 d-none"> -->
				<div class="row">

					<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
					<input type="hidden" id="idMaquinaria"  name="idMaquinaria" value="<?php echo fString($qrCargas->idMaquinaria); ?>"  >

					<div class="col-md-6 form-group">
						<label for="idObra">Obra:</label>
						<select name="idObra" id="idObra"  class="custom-select form-controls select2">
							<option value="">Seleccione una obra</option>
							<?php foreach($obras as $obra) { ?>
							<option value="<?php echo $obra["id"]; ?>"
								<?php echo $usuario->ubicacionId == $obra["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>
					</div>				

					<div class="col-md-6 form-group">
						<label for="modeloId">Material:</label>
						<div class="input-group">
							<select name="materialId" id="materialId"  class="custom-select form-controls select2Add">
								<?php if (isset($qrCargas->placa) ) : ?>
									<option value="">Selecciona un material</option>
								<?php endif; ?>

								<?php foreach($materialesCarga as $material) { ?>
									<option value="<?php echo $material["id"]; ?>"
										<?php echo $materialCargaId == $material["id"] ? ' selected' : ''; ?>>
											<?php echo mb_strtoupper(fString($material["descripcion"])); ?>
									</option>
								<?php } ?>
							</select>
							<div class="input-group-append">
								<button type="button" id="btnAddMaterialId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>
						</div>
					</div>

					<?php 
					$atributoMax = isset($qrCargas->numeroEconomico) ? 'max="' . $qrCargas->capacidad . '"' : '';
					?>

					<div class="col-md-6 form-group">
						<label for="nPeso">Peso:</label>
						<input type="number" 
							id="nPeso" 
							name="nPeso" 
							<?php echo $atributoMax; ?>
							value="<?php echo fString($pesoCarga); ?>"  
							class="form-control form-control-sm text-uppercase" 
							placeholder="Ingresa el peso del material">
					</div>

						<!-- Fecha Y Hora -->
					<div class="col-md-6 form-group">
						<label for="dfechaHora">Fecha Y Hora</label>
						<input type="datetime-local" 
							name="dFechaHora" 
							id="dFechaHora" 
							value="<?php echo $fechaHoraCarga; ?>" 
							class="form-control form-control-sm" 
							placeholder="Ingresa la Fecha y Hora de la carga" 
							>
					</div>

					<div class="col-md-6 form-group">
						<label for="sFolio">Folio de Carga:</label>
						<input type="text" id="sFolio" name="sFolio" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el folio de la carga">
					</div>

					<div class="col-md-6 form-group">
						<label for="idUbicacion">Ubicación:</label>
						<select name="idUbicacion" id="idUbicacion"  class="custom-select form-controls select2">
							<option value="">Seleccione una ubicación</option>
							<?php foreach($ubicaciones as $ubicacion) { ?>
							<option value="<?php echo $ubicacion["id"]; ?>"
								><?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>
					</div>			

					<div class="form-group col-md-12">
						<label for="inputFiles">Subir Imagen</label>
						<div class="input-group">
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="inputFiles" accept="image/*">
								<label class="custom-file-label" for="inputFiles">Selecciona la imagen</label>
							</div>
						</div>
					</div>


				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

				<?php if (!isset($verificarCargaActiva['estatus']) || $verificarCargaActiva['estatus'] === 'DESACTIVADO'): ?>
					<button type="button" class="btn btn-outline-primary" id="btnRegistrarCarga">
						<i class="fas fa-save"></i> Guardar Carga
					</button>
				<?php endif; ?>

			</div>
		</div>
	</div>
</div>

<!-- MODAL VER ARCHIVOS -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Visualizador de Archivos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
				<iframe id="pdfViewer" src="" width="100%" height="600px"></iframe>
            </div>
        </div>
    </div>
</div>