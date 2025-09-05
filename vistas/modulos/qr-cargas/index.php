<?php use App\Route; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                <h1>Registro de QR <small class="font-weight-light">Listado</small></h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                    <li class="breadcrumb-item active">Registro de QR</li>
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
                        <i class="fas fa-list-ol ml-1"></i> 
                        <h3 class="card-title">
                        Listado de QR
                    </h3>
                    <div class="card-tools">
                        <!-- <a href="<?=Route::names('qr-cargas.create')?>" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Asignar QR
                        </a>                 -->
                    </div>
                    </div>

                    <div class="card-body">
                
                    <table class="table text-uppercase table-bordered table-striped" id="tablaQrCargas" width="100%">
                        
                        <thead>
                            <tr>
                                <th style="width:10px">#</th>
                                <th>Id</th>
                                <th>Placa</th>
                                <th>Operador</th>
                                <th>Estatus</th>
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

        <!-- Modal -->
        <div class='modal fade' id='qrModal' tabindex='-1' role='dialog' aria-labelledby='qrModalLabel' aria-hidden='true'>
            <div class='modal-dialog' role='document'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='qrModalLabel'>QR Code</h5>
                        <button type='button' class='close' data-dismiss='modal' aria-label='Close'>
                            <span aria-hidden='true'>&times;</span>
                        </button>
                    </div>
                    <div class='modal-body text-center'>
                        <img src='' alt='QR Code' class='img-fluid' id='imgQr'>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        
    </section>
</div>




<?php 
  array_push($arrayArchivosJS, 'vistas/js/qr-cargas.js?v=1.01');
?>