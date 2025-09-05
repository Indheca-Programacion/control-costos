<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Ordenes de Compra <small class="font-weight-light">Listado</small></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Ordenes de Compra</li>
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
                Listado de Ordenes de Compra
              </h3>
              <div class="card-tools">
                <!-- <button type="button" id="btnFiltrar" class="btn btn-outline-info ml-1 float-right">
                  <i class="fas fa-sync-alt"></i> Listado
                </button>
                <button type="button" id="btnVerFiltros" class="btn btn-info float-right" data-toggle="collapse" data-target="#collapseFiltros" aria-expanded="false" aria-controls="collapseFiltros">
                  <i class="fas fa-eye"></i> Filtros
                </button> -->
              </div>
            </div>

            <div class="collapse" id="collapseFiltros">
              <div class="card card-body mb-0">
                <div class="row">

                  <div class="col-md-6">

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroEstatusId">Estatus</label>
                      </div>
                      <select class="custom-select select2" id="filtroEstatusId">                      
                        <option value="0">Selecciona un Estatus</option>
                        <?php foreach($estatuses as $estatus) : ?>
                          <?php if ( $estatus["ordenCompraAbierta"] ) : ?>
                          <option value="<?php echo $estatus['id'] ?>" ><?php echo $estatus['descripcion'] ?></option>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2" style="flex-wrap: nowrap;">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroObraId">Obra</label>
                      </div>
                      <select class="custom-select select2" id="filtroObraId">
                        <option value="0" selected>Selecciona una obra</option>
                        <?php foreach($obras as $obra) { ?>
                          <option value="<?php echo $obra["id"]; ?>">
                            <?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
                          </option>
                        <?php } ?>
                      </select>
                    </div>

                  </div>

                  <div class="col-md-6">

                    <!-- <div class="input-group input-group-sm mb-2 mb-md-0" style="flex-wrap: nowrap;"> -->
                    <div class="input-group input-group-sm mb-2 mb-md-0 date" id="fechaInicialDTP" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroFechaInicial">Fecha Inicial:</label>
                      </div>
                      <input type="text" id="filtroFechaInicial" class="form-control form-control-sms datetimepicker-input" placeholder="Ingresa la fecha inicial" data-target="#fechaInicialDTP">
                      <div class="input-group-append" data-target="#fechaInicialDTP" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                      </div>
                    </div>
                    <!-- </div> -->

                  </div>

                  <div class="col-md-6">

                    <div class="input-group input-group-sm date mb-2 mb-md-0" id="fechaFinalDTP" data-target-input="nearest">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="filtroFechaFinal">Fecha Final:</label>
                      </div>
                      <input type="text" id="filtroFechaFinal" class="form-control form-control-sms datetimepicker-input" placeholder="Ingresa la fecha final" data-target="#fechaFinalDTP">
                      <div class="input-group-append" data-target="#fechaFinalDTP" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                      </div>
                    </div>

                  </div>

                </div> <!-- <div class="row"> -->
              </div> <!-- <div class="card card-body mb-0"> -->
            </div> <!-- <div class="collapse" id="collapseFiltros"> -->

            <div class="card-body">

              <table class="table table-sm table-bordered table-striped" id="tablaOrdenes" width="100%">

                <thead>
                 <tr>
                   <th style="width:10px">#</th>
                   <th style="width:100px">Folio</th>
                   <th>Servicio</th>
                   <th style="width:200px">Estatus</th>
                   <th style="width:200px">Fecha Creacion</th>
                   <th style="width:100px">Requisicion</th>
                   <th>Solicitó</th>
                   <th style="width:100px">Acciones</th>
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
  array_push($arrayArchivosJS, 'vistas/js/OrdenCompra.js?v=2.00');
?>
