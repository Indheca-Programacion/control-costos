<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Cotizaciones <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?php echo Route::routes('inicio'); ?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('cotizaciones.index')?>"> <i class="fas fa-building"></i> Cotizaciones</a></li>
	            <li class="breadcrumb-item active">Editar cotizacion</li>
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
							<i class="fas fa-edit"></i>
							Editar cotizacion
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('cotizaciones.update', $cotizacion->id); ?>" enctype="multipart/form-data">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/cotizaciones/formulario.php"; ?>
							<button type="button" id="btnSend" class="btn btn-success">
								<i class="fas fa-save"></i> Aprobar
							</button>
							<button type="button" class="btn btn-danger">
								<i class="fas fa-times"></i> Rechazar
							</button>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	<!-- MODAL DOCUMENTOS -->
	<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="pdfModalLabel">Visualizador de PDF</h5>
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
	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/cotizaciones.js?v=1.00');
?>
