<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Obras <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Obras</li>
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
                <i class="fas fa-list-ol"></i> 
                Listado de Obras
              </h3>
              <div class="card-tools">
                <a href="<?=Route::names('obras.create')?>" class="btn btn-outline-primary">
                  <i class="fas fa-plus"></i> Crear obra
                </a>                
              </div>
            </div>

            <div class="card-body">
        
              <table class="table table-bordered table-striped" id="tablaObras" width="100%">
                 
                <thead>
                 <tr>
                   <th style="width:10px">#</th>
                   <th>Empresa</th>
                   <th>Descripción</th>
                   <th>Nombre Corto</th>
                   <th>Estatus</th>
                   <th>Semanas</th>
                   <th>Fecha Inicio</th>
                   <th>Fecha Finalización</th>
                   <th>Creó</th>
                   <th>Acciones</th>
                 </tr> 
                </thead>

                <tbody>
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
  array_push($arrayArchivosJS, 'vistas/js/obras.js?v=1.00');
?>
