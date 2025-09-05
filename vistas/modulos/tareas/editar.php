<?php
	$old = old();

	use App\Route;
?>
<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Tareas <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('tareas.index')?>"> <i class="fas fa-clipboard-check"></i> Tareas</a></li>
	            <li class="breadcrumb-item active">Editar Tarea</li>
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
							Editar tarea
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('tareas.update', $tarea->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/tareas/formulario.php"; ?>
							<?php if ($permitirEditar) : ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<?php endif ?>
							<?php if (!is_null($tarea->requisicionId) && $tarea->estatus < 10 ) : ?>
								<input type="hidden" id="requisicionId" value="<?php echo $tarea->requisicionId; ?>">
								<input type="hidden" id="tareaId" value="<?php echo $tarea->id; ?>">
								<button type="button" id="verRequisicion" class="btn btn-outline-info">
									<i class="fas fa-eye"></i> Ver Requisición
								</button>
							<?php endif ?>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			<div class="col-md-6">
				<div class="card card-warning card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Cambiar Estatus
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSendObservacicones" action="<?php echo Route::names('tarea-observaciones.store'); ?>" method="post" enctype="multipart/form-data">
							<input type="hidden" name="_method" value="PUT">
							<input type="hidden" name="_token" value="<?php echo token(); ?>">
							<input type="hidden" name="fk_tarea" id="fk_tarea" value="<?= $tarea->id ?>">
							<div class="row">
								<!-- Estatus -->
								<div class="col-md-6 form-group">
									<label for="estatus">Progreso de Tarea</label>
									<input min="1" max="10" value="<?= $estatus ?>" <?php if ($estatus == 10) echo 'disabled' ; ?> name="estatus" type="range" class="custom-range" id="estatus">
								</div>
								<!-- Observaciones -->
								<div class="col-md-6 form-group">
									<label for="observacion">Observaciones:</label>
									<textarea name="observacion" rows="3" id="observacion" class="form-control form-control-sm text-uppercase"></textarea>
								</div>
								<!-- Documentos -->
								<div class="col-md-6 form-group subir-archivos">
									<button type="button" class="btn btn-info" id="btnSubirArchivos">
										<i class="fas fa-folder-open"></i> Cargar Documentos
									</button>
									<?php if ( isset($tarea->id) ) : ?>
										<?php foreach($tarea->archivos as $key=>$vale) : ?>
										<p class="text-info mb-0"><?php echo $vale['archivo']; ?>
											<?php if ( $permitirEliminarArchivos ) : ?>
											<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $vale['id']; ?>" folio="<?php echo $vale['archivo']; ?>"></i>
											<?php endif; ?>
										</p>
										<?php endforeach; ?>
									<?php endif; ?>
									<span class="lista-archivos">
									</span>
									<input type="file" class="form-control form-control-sm d-none" id="archivos" multiple>
								</div>
							</div>
							<?php if ( isset($tarea->id) && count($tarea->observaciones) > 0 ) : ?>
							<div class="row">
								<div class="col-12">
									<ul class="list-group pb-3">
										<?php foreach($tarea->observaciones as $observacion) { ?>
										<?php
											$leyenda = "[{$observacion["fechaCreacion"]}] ";
											$leyenda .= mb_strtoupper(fString($observacion["observacion"]));
										?>
										<li class="list-group-item list-group-item-success py-2 px-3"><?php echo $leyenda; ?></li>
										<?php } ?>
									</ul>
								</div>
							</div>
							<?php endif; ?>
							<button type="button" id="btnSendObs" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar Estatus
							</button>
							<div class="btn-group descargar-archivos">
								<button type="button" class="btn btn-outline-info" id="btnDescargarArchivos" <?php if ( $cantidadArchivos == 0 ) echo "disabled"; ?>>
									<i class="fas fa-download"></i> Descargar
								</button>
							</div>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div><!-- /.card-body -->
				</div> <!-- /.card -->
			</div> <!-- /.col -->
    	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/tareas.js?v=1.4');
?>
