<?php use App\Route; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>Registro de Cargas <small class="font-weight-light">Listado</small></h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                    <li class="breadcrumb-item active">Registro de Cargas</li>
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
                        <i class="fas fa-list-ol ml-2"></i> 
                        <h3 class="card-title">
                        Listado de Cargas
                        </h3>
                        <div class="card-tools">
							<a href="<?=Route::names('cargas.print',1)?>" class="btn btn-info" data-toggle="tooltip" title="Volver">
                                <i class="fas fa-print"></i> Imprimir
							</a>
						</div>
                    </div>

                    <div class="card-body">
                
                    <table class="table table-bordered table-striped" id="tablaCargas" width="100%">
                        
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Codigo</th>
                                <th>Obra</th>
                                <th>Material</th>
                                <th>Peso</th>
                                <th>Fecha Y Hora</th>
                                <th>Folio De Carga</th>
                                <th>Maquinaria</th>
                                <th>Acciones</th>
                            </tr> 
                        </thead>
                        <tbody>
                        <tr>
                        </tr>
                        </tbody>

                    </table>

                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- ./row -->
        </div><!-- /.container-fluid -->
    </section>
</div>

<?php 
  array_push($arrayArchivosJS, 'vistas/js/cargas.js?v=1.01');
?>