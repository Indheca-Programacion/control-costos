<?php use App\Route; ?>

<div class="content-wrapper">

  <!-- Encabezado -->
  <section class="content-header bg-white py-3 mb-4 border-bottom shadow-lg">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-6">
          <h1 class="h3 mb-0">Cotizaciones</h1>
          <small class="text-muted">Listado</small>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item">
              <a href="<?=Route::routes('inicio')?>">
                <i class="fas fa-tachometer-alt"></i> Inicio
              </a>
            </li>
            <li class="breadcrumb-item active">Cotizaciones</li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Contenido principal -->
  <section class="content">

    <?php if (!is_null(flash())) : ?>
      <div class="d-none" id="msgToast"
           clase="<?=flash()->clase?>"
           titulo="<?=flash()->titulo?>"
           subtitulo="<?=flash()->subTitulo?>"
           mensaje="<?=flash()->mensaje?>">
      </div>
    <?php endif; ?>

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

          <div class="card shadow-sm border-left-info">
            <div class="card-header bg-light">
              <h3 class="card-title text-info mb-0">
                <i class="fas fa-file-alt mr-2"></i>Listado de Cotizaciones
              </h3>
              <div class="card-tools">
                <button type="button" id="btnFiltrarCotizaciones" class="btn btn-outline-info">
                  <i class="fas fa-sync-alt"></i> Actualizar
                </button>
              </div>
            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm table-bordered table-striped text-center align-middle" id="tablaCotizaciones" width="100%">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th>Fecha de Requisición</th>
                      <th>Fecha Límite</th>
                      <th>Estatus</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody class="text-uppercase">
                    <!-- Cuerpo dinámico -->
                  </tbody>
                </table>
              </div>
            </div>
          </div> <!-- /.card -->

        </div>
      </div>
    </div>

    <!-- Modal Ver Archivos de Cotización -->
    <div class="modal fade" id="modalVerArchivosCotizacion" role="dialog" aria-labelledby="modalVerCotizacionLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="modalVerCotizacionLabel">
              <i class="fas fa-file-pdf mr-1"></i> Archivos de la Cotización
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="archivosCotizacion"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

  </section>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/cotizaciones.js?v=1.0');
?>
