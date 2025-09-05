<?php
	$old = old();

	use App\Route;

?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Puesto <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('puestos.index')?>"> <i class="fas fa-truck"></i> Puestos</a></li>
	            <li class="breadcrumb-item active">Editar puesto</li>
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
			<div class="col-md-12 col-lg-6 col-xl-5">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Editar puesto
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('puestos.update', $puesto->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/puestos/formulario.php"; ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->

			<div class="col-md-12 col-lg-6 col-xl-5 ">
				<div class="card card-primary card-outline">
					<div class="card-header d-flex flex-column">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Asignar Puestos
						</h3>
						<p class="mt-2">
							Asignación de puestos superiores e inferiores del Puesto.
						</p>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/puestos/form-puesto-asignacion.php"; ?>
						<button type="button" id="btnSendAsignacion" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Asignar
						</button>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->

      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/puestos.js');
	array_push($arrayArchivosJS, 'vistas/js/puestos-asignacion.js');

?>
