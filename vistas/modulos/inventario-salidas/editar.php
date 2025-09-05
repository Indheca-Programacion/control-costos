<?php
	$old = old();

	use App\Route;
	use App\Controllers\Autorizacion;
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
						<div class="card-body">
								<?php include "vistas/modulos/errores/form-messages.php"; ?>
								<form id="formSend" method="POST" action="<?php echo Route::names('inventario-salidas.update', $inventarioSalida->id); ?>">
									<input type="hidden" name="_method" value="PUT">
									<?php include "vistas/modulos/inventario-salidas/formulario.php"; ?>
									<a target="_blank" href="<?php echo Route::names('inventario-salidas.print',$inventarioSalida->id); ?>" class="btn btn-outline-primary">
										<i class="fas fa-print"></i> Imprimir
									</a>
									<?php if (Autorizacion::permiso($usuario,'auth-salida','actualizar') && is_null($inventarioSalida->usuarioIdAutoriza)) : ?>
										<button type="submit" class="btn btn-primary">
											<i class="fas fa-save"></i> Autorizar
										</button>
									<?php endif; ?>
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
	array_push($arrayArchivosJS, 'vistas/js/inventario-salidas.js?v=1.0');
?>
