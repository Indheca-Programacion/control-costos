<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Nominas <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('nominas.index')?>"> <i class="fas fa-list-alt"></i> Nominas</a></li>
	            <li class="breadcrumb-item active">Editar nomina</li>
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
							Editar nomina
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
							<?php include "vistas/modulos/nominas/formulario.php"; ?>

						<div class="card card-success card-outline">

							<div class="card-body">
								
								<div class="table-responsive">

									<table class="table table-sm table-bordered table-striped mb-0" id="tablaNominasDetalles" width="100%">

										<thead>
											<tr>
												<th class="text-center" style="min-width: 40px;">#</th>
												<th class="text-center" style="min-width: 160px;">Nombre</th>
												<th class="text-center" style="min-width: 200px;">Puesto</th>
												<th class="text-center" style="min-width: 160px;">Salario</th>
												<th class="text-center" style="min-width: 160px;">Hrs Extras</th>
												<th class="text-center" style="min-width: 160px;">Prima vacacional</th>
												<th class="text-center" style="min-width: 160px;">Pago Comida</th>
												<th class="text-center" style="min-width: 160px;">Prestamos</th>
												<th class="text-center" style="min-width: 160px;">Descuentos</th>
												<th class="text-center" style="min-width: 160px;">Pension</th>
												<th class="text-center" style="min-width: 160px;">Neto</th>
											</tr>
										</thead>

										<tbody class="text-uppercase">
                                            <?php foreach($nominas_detalles as $key=>$value) : ?>
                                                <tr>
                                                    <td class="text-right"><?php echo $key+1; ?></td>
                                                    <td class="text-right"><?php echo fString($value['nombreCompleto']); ?></td>
                                                    <td class=""><?php echo mb_strtoupper(fString($value['puesto'])); ?></td>
                                                    <td class="text-right"><?php echo formatMoney($value['salario']); ?></td>
                                                    <td class="text-right"><?php echo $value['hrsExtras']; ?></td>
                                                    <td class="text-right"><?php echo formatMoney($value['primas']); ?></td>
                                                    <td class="text-right"><?php echo formatMoney($value['comida']); ?></td>
                                                    <td class="text-right"><?php echo formatMoney($value['prestamos']); ?></td>
                                                    <td class="text-right"><?php echo formatMoney($value['descuentos']); ?></td>
                                                    <td class="text-right"><?php echo formatMoney($value['pension']); ?></td>
                                                    <td class="text-right"><?php echo formatMoney($value['neto']); ?></td>
                                                </tr>
											<?php endforeach; ?>
										</tbody>
                                    </table>

								</div>
								
							</div> <!-- <div class="card-body"> -->
						</div> <!-- /.card-body -->
				</div> <!-- /.card -->
            </div> <!-- /.col -->
        </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/nominas.js?v=1.1');
?>
