<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Puestos <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Puestos</li>
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
                Listado de Puestos 
              </h3> 
              <div class="card-tools">
                <a href="<?=Route::names('puestos.create')?>" class="btn btn-outline-primary">
                  <i class="fas fa-plus"></i> Crear puesto
                </a>                
              </div>
            </div>

            <div class="card-body">
        
              <table class="table table-bordered table-striped" id="tablaPuestos" width="100%">
                 
                <thead>
                 <tr>
                   <th style="width:10px">#</th>
                   <th>Nombre Corto</th>
                   <th>Descripción</th>
                   <th>Acciones</th>
                 </tr> 
                </thead>

                <tbody class="text-uppercase">
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
  array_push($arrayArchivosJS, 'vistas/js/puestos.js');
?>
