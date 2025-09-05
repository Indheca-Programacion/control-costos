<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Resguardos <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?php echo Route::routes('inicio'); ?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Resguardos</li>
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
                Listado de Resguardos
              </h3>
            </div>
            <!-- <div class="card-body pad table-responsive"> -->
            <div class="card-body">
        
             <!-- <table class="table table-bordered table-striped dt-responsive tablas" width="100%"> -->
             <!-- <table class="table table-bordered table-striped tablas" width="100%"> -->
             <table class="table table-bordered table-striped" id="tablaResguardos" width="100%">
               
              <thead>
               <tr>
                 <th style="width:10px">#</th>
                 <th>Asignado</th>
                 <th>Entregó</th>
                 <th>Fecha de Asignación</th>
                 <th>Descripcion</th>
                 <th>Obra</th>
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
  array_push($arrayArchivosJS, 'vistas/js/resguardos.js?v=1.2');
?>