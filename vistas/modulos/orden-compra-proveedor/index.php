<?php use App\Route; ?>

<div class="content-wrapper">

  <!-- Encabezado -->
  <section class="content-header bg-white py-3 mb-4 border-bottom shadow-lg">
    <div class="container-fluid">
      <div class="row align-items-center">
        <div class="col-sm-6">
          <h1 class="h3 mb-0">Órdenes de Compra</h1>
          <small class="text-muted">Listado</small>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right mb-0">
            <li class="breadcrumb-item">
              <a href="<?=Route::routes('inicio')?>">
                <i class="fas fa-tachometer-alt"></i> Inicio
              </a>
            </li>
            <li class="breadcrumb-item active">Órdenes de Compra</li>
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
                <i class="fas fa-list-ol mr-2"></i>Listado de Órdenes de Compra
              </h3>
              <div class="card-tools">
                <button type="button" id="btnFiltrar" class="btn btn-outline-info">
                  <i class="fas fa-sync-alt"></i> Actualizar
                </button>
              </div>

            </div>

            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover table-sm table-bordered table-striped text-center align-middle" id="tablaOrdenes" width="100%">
                  <thead class="thead-light">
                    <tr>
                      <th>#</th>
                      <th>Folio</th>
                      <!-- <th>Obra</th> -->
                      <th>Fecha Creación</th>
                      <th>Estatus</th>
                      <th>SubTotal</th>
                      <th>Importe</th>
                      <th>Moneda</th>
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

    <!-- Modal Adjuntar Factura -->
    <div class="modal fade" id="modalAgregarFactura" tabindex="-1" role="dialog" aria-labelledby="modalAgregarFacturaLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="modalAgregarFacturaLabel">
              <i class="fas fa-file-upload mr-1"></i> Adjuntar Facturas
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
              <span>&times;</span>
            </button>
          </div>

          <form id="formAgregarFactura" method="POST" enctype="multipart/form-data">
            <div class="modal-body">
              <div class="form-group">
                <label for="archivoFactura">Archivo(s) de Factura (.pdf, .xml)</label>
                <input type="file" class="form-control-file" id="archivoFactura" name="archivoFactura[]" accept=".pdf,.xml" multiple required>
              </div>
              <div class="form-group">
                <label>Archivos seleccionados:</label>
                <ul id="listaArchivos" class="list-group list-group-flush shadow-sm"></ul>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
              <button type="button" id="btnSubirFacturas" class="btn btn-primary">Guardar</button>
            </div>
          </form>

          <!-- Script para eliminar archivos seleccionados -->
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const input = document.getElementById('archivoFactura');
              const lista = document.getElementById('listaArchivos');

              input.addEventListener('change', function() {
                lista.innerHTML = '';
                Array.from(input.files).forEach((file, idx) => {
                  const li = document.createElement('li');
                  li.className = 'list-group-item d-flex justify-content-between align-items-center';
                  li.textContent = file.name;

                  const btn = document.createElement('button');
                  btn.type = 'button';
                  btn.className = 'btn btn-danger btn-sm ml-2';
                  btn.textContent = 'Eliminar';
                  btn.onclick = function() {
                    const dt = new DataTransfer();
                    Array.from(input.files).forEach((f, i) => {
                      if (i !== idx) dt.items.add(f);
                    });
                    input.files = dt.files;
                    input.dispatchEvent(new Event('change'));
                  };

                  li.appendChild(btn);
                  lista.appendChild(li);
                });
              });
            });
          </script>

        </div>
      </div>
    </div>

    <!-- Modal Ver Pagos -->
    <div class="modal fade" id="modalVerPagos" role="dialog" aria-labelledby="modalVerPagosLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header bg-dark text-white">
            <h5 class="modal-title" id="modalVerPagosLabel">
              <i class="fas fa-money-check-alt mr-1"></i> Pagos de la Orden de Compra
            </h5>
            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
              <span>&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="archivosPagos"></div>
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
  array_push($arrayArchivosJS, 'vistas/js/ordenes-compra-proveedor.js?v=2.00');
?>
