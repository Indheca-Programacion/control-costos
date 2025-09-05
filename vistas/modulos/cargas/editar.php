<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

<section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>Detalle Cargas <small class="font-weight-light">Listado</small></h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
					<li class="breadcrumb-item"><a href="<?=Route::names('cargas.index')?>"> <i class="fas fa-truck-loading mr-1"></i> Cargas</a></li>
					<li class="breadcrumb-item active">Detalles Cargas</li>
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
							Detalles Cargas
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('cargas.update', $cargas->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/cargas/formulario.php"; ?>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			<div class="col-md-6">
				<div class="card card-dark card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Ticket de carga
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">


                    <?php if ($cargas->urlTicket) : ?>
						<img clasS="w-100 rounded-lg" src="<?php  echo  $cargas->urlTicket ?>" alt="">
					<?php else: ?>
						<h4>Sin Ticket</h4>
					<?php endif; ?>
					
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			<div class="col-md-12">
				<div class="card card-dark card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Detalles de los Movimientos
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<table class="table table-bordered table-striped" id="tablaMovimietoCarga" width="100%">
							<thead>
							<tr>
								<th>ID</th>
								<th>Obra</th>
								<th>Maquinaria</th>
								<th>Entrada/Salida</th>
								<th>Estatus</th>
								<th>Operador</th>
								<th>Fehca/Hora</th>
							</tr> 
							</thead>
							<tbody class="text-uppercase">
							</tbody>	
						</table>
					</div>
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/cargas.js?v=2.00');
?>
