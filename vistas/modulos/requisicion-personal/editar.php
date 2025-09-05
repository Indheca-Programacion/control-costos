<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Requisiciones <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('requisicion-personal.index')?>"> <i class="fas fa-tools"></i> Requisiciones de Personal</a></li>
	            <li class="breadcrumb-item active">Editar Requisicion de personal</li>
	          </ol>
	        </div>
	      </div>
	    </div><!-- /.container-fluid -->

	</section>

	<section class="content requisiciones">

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
								Editar requisición
							</h3>
						</div> <!-- <div class="card-header"> -->
						<div class="card-body">
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<form id="formSend" method="POST" action="<?php echo Route::names('requisicion-personal.update', $requisicion->id); ?>" enctype="multipart/form-data">
								<input type="hidden" name="_method" value="PUT">
								<input type="hidden" value="<?= $requisicion->id ?>" id="id_requisicion">
								<?php include "vistas/modulos/requisicion-personal/formulario.php"; ?>

								<button type="button" id="btnSend" class="btn btn-outline-primary" >
									<i class="fas fa-save"></i> Actualizar
								</button>

								<?php if($permiso_authorizar){ ?>
									<button type="button" id="btnAuth" class="btn btn-outline-primary" >
										<i class="fas fa-clipboard-check"></i> Autorizar
									</button>
								<?php } ?>
								<a href="<?php echo Route::names('requisicion-personal.print', $requisicion->id); ?>" target="_blank" class="btn btn-info float-right"><i class="fas fa-print"></i> Imprimir</a>
								
								<div id="msgSend"></div>
							</form>
							<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/requisiciones-personal.js?v=1.01');
?>