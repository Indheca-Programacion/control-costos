<?php use App\Route; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>Asistencias <small class="font-weight-light">Crear</small></h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?=Route::names('asistencias.index')?>"> <i class="fas fa-list-alt"></i> Asistencias</a></i>
                    <li class="breadcrumb-item active">Crear Asistencias</li>
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
                                <i class="fas fa-plus"></i>
                                Crear Asistencias
                            </h3>
                        </div> <!-- <div class="card-header"> -->
                        <div class="card-body">
                                <?php include "vistas/modulos/asistencias/formulario.php"; ?>
                                
                            <div class="card card-success card-outline">

                                <div class="card-body">
                                <form id="formCreaAsistenciaSend" method="POST" action="<?php echo Route::names('asistencias.store'); ?>">
                                    <div class="table-responsive">
				                        <input type="hidden" name="_token" value="<?php echo createToken(); ?>">

                                        <table class="table table-sm table-bordered table-striped mb-0" id="tablaAsistenciasDetalles" width="100%">

                                            <thead>
                                                <tr>
                                                    <th class="text-right" style="min-width: 40px;">#</th>
                                                    <th class="text-center" style="min-width: 160px;">Nombre</th>
                                                    <th class="text-center" style="min-width: 160px;">Fecha</th>
                                                    <th class="text-center" style="min-width: 160px;">Hora de Entrada</th>
                                                    <th class="text-center" style="min-width: 160px;">Hora de Salida</th>
                                                    <th class="text-center" style="min-width: 160px;">Hora Extras</th>
                                                    <th class="text-center" style="min-width: 160px;">Incidencias</th>
                                                </tr>
                                            </thead>

                                            <tbody class="text-uppercase">
                                            </tbody>

                                        </table>

                                    </div>
                                </form>

                                </div> <!-- <div class="card-body"> -->

                            </div> <!-- <div class="card card-info card-outline"> -->

                            <button type="button" id="btnSend" class="btn btn-outline-primary">
                                    <i class="fas fa-save"></i> Guardar
                                </button>										
                                <div id="msgSend"></div>
                        </div> <!-- /.card-body -->
                    </div> <!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- ./row -->
        </div><!-- /.container-fluid -->
    </section>
</div>
<?php
	array_push($arrayArchivosJS, 'vistas/js/asistencias.js?v=1.2');
?>