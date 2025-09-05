<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Inventarios <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Inventarios</li>
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
                <ul class="nav nav-tabs" id="tabInventario" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="inventarios-tab" data-toggle="pill" href="#inventarios" role="tab" aria-controls="inventarios" aria-selected="true">Inventario</a>
                  </li> 
                  <li class="nav-item">
                    <a class="nav-link" id="entradas-tab" data-toggle="pill" href="#entradas" role="tab" aria-controls="listado-entradas" aria-selected="false">Entradas</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="salidas-tab" data-toggle="pill" href="#salidas" role="tab" aria-controls="listado-salidas" aria-selected="false">Salidas</a>
                  </li>
                </ul> <!-- <ul class="nav nav-tabs" id="tabInventario" role="tablist"> -->
            <div class="card-body">
              <div class="tab-content" id="tabInventario">
                <div class="tab-pane fade show active" id="inventarios" role="tabpanel" aria-labelledby="inventarios-tab">
                  <a class="btn btn-outline-primary float-right" href="<?=Route::names('inventarios.create')?>"><i class="fas fa-plus"></i> Nueva entrada</a>
                  <table class="table table-bordered table-striped" id="tablaInventarioGeneral" width="100%">
                    
                    <thead>
                        <tr>
                          <th style="width:10px">#</th>
                          <th>Almacen</th>                
                          <th>Cantidad</th>
                          <th>Unidad</th>
                          <th class="text-left">Folio Entrada</th>
                          <th>Descripcion</th>
                          <th>OC</th>
                          <th>Requisicion</th>
                          <th>Acciones</th>
                          
                        </tr> 
                    </thead>

                    <tbody>
                    </tbody>

                  </table> <!-- <table class="table table-bordered table-striped" id="tablaInventarioGeneral" width="100%"> -->
                </div> <!-- <div class="tab-pane fade show active" id="inventarios" role="tabpanel" aria-labelledby="inventarios-tab"> -->
                <div class="tab-pane fade" id="entradas" role="tabpanel" aria-labelledby="entradas-tab">
                    <a class="btn btn-outline-primary float-right" href="<?=Route::names('inventarios.create')?>"><i class="fas fa-plus"></i> Nueva entrada</a>
                  <table class="table table-bordered table-striped" id="tablaInventarios" width="100%">
                    
                    <thead>
                        <tr>
                        <th style="width:10px">#</th>
                        <th style="width:20px text-left">Folio Entrada</th>                
                        <th>Almacen</th>                
                        <th>Entregó</th>
                        <th>Orden de Compra</th>
                        <th>Recibió</th>
                        <th>Acciones</th>
                      </tr> 
                    </thead>

                    <tbody>
                    </tbody>

                  </table> <!-- <table class="table table-bordered table-striped" id="tablaInventarios" width="100%"> -->
                </div> <!-- <div class="tab-pane fade" id="entradas" role="tabpanel" aria-labelledby="entradas-tab"> -->
                <div class="tab-pane fade" id="salidas" role="tabpanel" aria-labelledby="salidas-tab">
                    <table class="table table-bordered table-striped" id="tablaSalidasDeEntrada" width="100%">
                    <thead>
                        <tr>
                        <th style="width:10px">#</th>
                        <th style="width:20px text-left">Folio Salida</th>                
                        <th>Almacen</th>                
                        <th>Entregó</th>
                        <th>Fecha Entregado</th>
                        <th>Recibió</th>
                        <th>Acciones</th>
                      </tr> 
                    </thead>

                    <tbody>
                    </tbody>

                  </table> <!-- <table class="table table-bordered table-striped" id="tablaInventarios" width="100%"> -->
                </div> <!-- <div class="tab-pane fade" id="entradas" role="tabpanel" aria-labelledby="entradas-tab"> -->
              </div> <!-- <div class="tab-content" id="tabInventario"> -->
            </div> <!-- /.card-body -->
          </div> <!-- /.card -->
        </div> <!-- /.col -->
      </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

  </section>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/inventarios.js?v=3.5');
?>
