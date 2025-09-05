<?php use App\Route; ?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
					<h1>NOM35 <small class="font-weight-light">Visor</small></h1>
				</div>
				<div class="col-sm-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
						<li class="breadcrumb-item active">NOM35</li>
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
								Generador de reporte NOM35
							</h3>
						</div>

            			<div class="card-body justify-content-center">
							
							<div class="row form-group">

								<div class="col-12">
									<form class="dropzone needsclick" id="nom35" action="">
										<div id="dropzone">
					
											<div class="dz-message needsclick">    
												Suelta el archivo aquí o haz clic para subirlo.
												
											</div>
										</div>
									</form>
								</div>

								<div class="col-12 text-center">
									<h3>
										Ultimo reporte
									</h3>
								</div>

								<div class="col">
									<iframe id="reporte" src="<?php echo Route::rutaServidor();?>reportes/tmp/reporteNom35.pdf" style="width:100%; height:600px;" frameborder="0"></iframe>
								</div>
							</div>

						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/nom35.js?v=1.0');
?>