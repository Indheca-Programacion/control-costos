<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Programacion de Pagos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Programacion de Pagos</li>
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
                Programacion de Pagos
              </h3>
              <div class="card-tools">
                <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalCrearBloque">
                  <i class="fas fa-plus"></i> Crear Bloque
                </button>

                <!-- Modal -->
                <div class="modal fade" id="modalCrearBloque" tabindex="-1" role="dialog" aria-labelledby="modalCrearBloqueLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <form id="formCrearBloque" method="POST" action="<?=Route::names('programacion-pagos.store')?>">
                      <input type="hidden" name="_token" value="<?= token() ?>">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalCrearBloqueLabel">Crear Bloque de Pago</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <div class="form-group">
                            <label for="fecha_programada">Fecha Programada</label>
                            <input type="text" class="form-control form-control-sm datetimepicker-input" id="fecha_programada" name="fecha_programada" data-toggle="datetimepicker" data-target="#fecha_programada" autocomplete="off" required>
                          </div>

                          <div class="form-group">
                            <label for="prioridad">Prioridad</label>
                            <input type="number" class="form-control form-control-sm" id="prioridad" name="prioridad" value="<?=count($programacionPagos->bloques)+1?>" min="<?= count($programacionPagos->bloques)+1 ?>" required>
                          </div>
                          
                          <div class="form-group">
                            <label for="tipo">Tipo de Pago</label>
                            <select class="form-control select2" id="tipo" name="tipo" required>
                              <option value="" disabled selected>Seleccione un tipo</option>
                              <option value="0">Contado</option>
                              <option value="1">Crédito</option>
                            </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                          <button type="submit" class="btn btn-primary">Crear</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card card-secondary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-list-ol"></i> 
                                    Ordenes de Compra
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" id="btnAsignarBloque" disabled>
                                      <i class="fas fa-arrow-right"></i> Asignar a Bloque
                                    </button>
                                    <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                      // Habilitar/deshabilitar el botón según selección de checkboxes
                                      const btnAsignar = document.getElementById('btnAsignarBloque');
                                      const checkboxes = document.querySelectorAll('.check-orden');
                                      const checkAll = document.getElementById('checkAllOrdenes');

                                      function toggleBtnAsignar() {
                                        btnAsignar.disabled = !Array.from(checkboxes).some(cb => cb.checked);
                                      }

                                      checkboxes.forEach(cb => cb.addEventListener('change', toggleBtnAsignar));
                                      if (checkAll) checkAll.addEventListener('change', toggleBtnAsignar);

                                      btnAsignar.addEventListener('click', function() {
                                        // Generar opciones de bloques
                                        const bloques = <?= json_encode(array_map(function($b) {
                                          return [
                                            'id' => $b['id'],
                                            'texto' => $b['tipo'].' '.$b['id'].' ('.fFechaLarga($b['fecha']).')',
                                            'pagado' => intval($b['pagado'])
                                          ];
                                        }, $programacionPagos->bloques)) ?>;

                                        let optionsHtml = '<select id="bloqueSelect" class="swal2-select" style="width:100%">';
                                        bloques.forEach(b => {
                                          if (b.pagado) return; // No mostrar bloques pagados
                                          optionsHtml += `<option value="${b.id}">${b.texto}</option>`;
                                        });
                                        optionsHtml += '</select>';

                                        Swal.fire({
                                          title: 'Asignar a Bloque',
                                          html: optionsHtml,
                                          showCancelButton: true,
                                          confirmButtonText: 'Asignar',
                                          cancelButtonText: 'Cancelar',
                                          preConfirm: () => {
                                            const bloqueId = document.getElementById('bloqueSelect').value;
                                            if (!bloqueId) {
                                              Swal.showValidationMessage('Seleccione un bloque');
                                            }
                                            return bloqueId;
                                          }
                                        }).then(result => {
                                          if (result.isConfirmed) {
                                            const bloqueId = result.value;
                                            const ordenIds = Array.from(checkboxes)
                                              .filter(cb => cb.checked)
                                              .map(cb => cb.value);

                                            // AJAX insert
                                            $.ajax({
                                              url: rutaAjax + 'app/Ajax/ProgramacionPagosAjax.php',
                                              method: 'POST',
                                              dataType: 'json',
                                              headers: {
                                              'X-CSRF-TOKEN': '<?= token() ?>'
                                              },
                                              data: {
                                              programacion_pago: bloqueId,
                                              ordenes: ordenIds,
                                              accion: 'asignarOrdenes'
                                              },
                                              success: function(data) {
                                              if (!data.error) {
                                                Swal.fire('¡Asignado!', 'Las órdenes fueron asignadas.', 'success')
                                                .then(() => location.reload());
                                              } else {
                                                Swal.fire('Error', data.errorMessage || 'No se pudo asignar.', 'error');
                                              }
                                              },
                                              error: function() {
                                              Swal.fire('Error', 'Ocurrió un error en la petición.', 'error');
                                              }
                                            });
                                          }
                                        });
                                      });
                                    });
                                    </script>
                                </div>
                            </div>
                            <div class="card-body">
                                <div style="overflow-x:auto;">
                                  <table class="table table-bordered table-striped" id="tablaProgramacionPagos" style="min-width:900px;">
                                    <thead>
                                      <tr>
                                        <th><input type="checkbox" id="checkAllOrdenes"></th>
                                        <th>Folio</th>
                                        <th>Categoria</th>
                                        <th>Tipo</th>
                                        <th>Proveedor</th>
                                        <th>Obra</th>
                                        <th>Bloque</th>
                                        <th>Total</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php foreach($ordenes as $orden) : ?>
                                          <tr>
                                          <td>
                                            <?php if (!isset($orden["Bloque"])) : ?>
                                              <input type="checkbox" class="check-orden" value="<?= $orden["id"] ?>">
                                            <?php endif; ?>
                                          </td>
                                          <td><?= $orden["folio"] ?></td>
                                          <td><?= mb_strtoupper($categoriasOrdenCompra[$orden["categoriaId"]] ?? "otros") ?></td>
                                          <td>
                                            <?php
                                            if ($orden["condicionPagoId"] == 1) {
                                            echo "Contado";
                                            } elseif ($orden["condicionPagoId"] == 2) {
                                            echo "30 DIAS";
                                            } elseif ($orden["condicionPagoId"] == 3) {
                                            echo "CRÉDITO";
                                            } elseif ($orden["condicionPagoId"] == 4) {
                                            echo "CRÉDITO 15 DÍAS";
                                            } else {
                                            echo $orden["condicionPagoId"];
                                            }
                                            ?>
                                          </td>
                                          <td><?= mb_strtoupper($orden["proveedor"]) ?></td>
                                          <td><?= mb_strtoupper($orden["obra.nombreCorto"]) ?></td>
                                          <td>
                                            <?php if (isset($orden["Bloque"])) : ?>
                                            <?= $orden["Bloque"] ?>
                                            <?php else : ?>
                                            <span class="text-muted">Sin Asignar</span>
                                            <?php endif; ?>
                                          </td>
                                          <td style="white-space:nowrap;">$<?= number_format($orden["total"], 4) ?></td>
                                          </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                  </table>
                                </div>
                                <script>
                                  document.addEventListener('DOMContentLoaded', function() {
                                    // Checkbox select all
                                    document.getElementById('checkAllOrdenes').addEventListener('change', function() {
                                      document.querySelectorAll('.check-orden').forEach(cb => cb.checked = this.checked);
                                    });

                                    // SweetAlert button
                                    document.querySelectorAll('.btn-sweet-alert').forEach(btn => {
                                      btn.addEventListener('click', function() {
                                        const folio = this.getAttribute('data-folio');
                                        Swal.fire({
                                          title: 'Detalle de Orden',
                                          text: 'Folio: ' + folio,
                                          icon: 'info',
                                          confirmButtonText: 'Cerrar'
                                        });
                                      });
                                    });
                                  });
                                </script>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card card-primary card-outline">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-list-ol"></i> 
                                    Pagos Programados
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                  <?php foreach($programacionPagos->bloques as $bloque) : ?>
                                  <?php if ($bloque["pagado"] == 0): ?>
                                    <div class="col-12">
                                      <div class="card card-outline card-secondary draggable-bloque" data-bloque-id="<?= $bloque["id"] ?>">
                                        <div class="card-header">
                                          <h3 class="card-title">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?= $bloque["tipo"].' '.$bloque["id"] ?>
                                          </h3>

                                          <div class="card-tools">
                                            <button class="btn btn-tool" data-card-widget="collapse">
                                              <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-toggle="modal" data-target="#modalEditarBloque<?= $bloque["id"] ?>">
                                              <i class="fas fa-edit"></i> 
                                            </button>
                                            <button type="button" class="btn btn-tool btn-marcar-pagado" data-bloque-id="<?= $bloque["id"] ?>" title="Marcar como pagado">
                                              <i class="fas fa-check-circle text-success"></i>
                                            </button>
                                            <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                              
                                            });
                                            </script>
                                            <form method='POST' action='<?=Route::names('programacion-pagos.destroy', $bloque["id"])?>' style='display: inline'>
                                              <input type='hidden' name='_method' value='DELETE'>
                                              <input type='hidden' name='_token' value='<?= token() ?>'>
                                                <button type='button' class='btn btn-tool eliminar'>
                                                  <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            <!-- Modal Editar Bloque -->
                                            <div class="modal fade" id="modalEditarBloque<?= $bloque["id"] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditarBloqueLabel<?= $bloque["id"] ?>" aria-hidden="true">
                                              <div class="modal-dialog" role="document">
                                              <form method="POST" action="<?=Route::names('programacion-pagos.update', $bloque["id"])?>">
                                                <input type="hidden" name="_method" value="PUT">
                                                <input type="hidden" name="_token" value="<?= token() ?>">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                  <h5 class="modal-title" id="modalEditarBloqueLabel<?= $bloque["id"] ?>">Editar Bloque de Pago</h5>
                                                  <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                  <span aria-hidden="true">&times;</span>
                                                  </button>
                                                </div>
                                                <div class="modal-body">
                                                  <div class="form-group">
                                                    <label for="fecha_programada_<?= $bloque["id"] ?>">Fecha Programada</label>
                                                    <input type="text" class="form-control form-control-sm datetimepicker-input" id="fecha_programada_<?= $bloque["id"] ?>" name="fecha_programada" data-toggle="datetimepicker" data-target="#fecha_programada_<?= $bloque["id"] ?>" autocomplete="off" value="<?= fFechaLarga( $bloque["fecha"]) ?>" required>
                                                  </div>

                                                  <div class="form-group">
                                                    <label for="prioridad_<?= $bloque["id"] ?>">Prioridad</label>
                                                    <input type="number" class="form-control form-control-sm" id="prioridad_<?= $bloque["id"] ?>" name="prioridad" value="<?= $bloque["prioridad"] ?>" min="1" required>
                                                  </div>

                                                  <div class="form-group">
                                                    <label for="tipo_<?= $bloque["id"] ?>">Tipo de Pago</label>
                                                    <select class="form-control select2" id="tipo_<?= $bloque["id"] ?>" name="tipo" required>
                                                      <option value="" disabled>Seleccione un tipo</option>
                                                      <option value="0" <?= $bloque["tipo"] == 0 ? "selected" : "" ?>>Contado</option>
                                                      <option value="1" <?= $bloque["tipo"] == 1 ? "selected" : "" ?>>Crédito</option>
                                                    </select>
                                                  </div>
                                                </div>

                                                <div class="modal-footer">
                                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                  <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                </div>
                                                </div>
                                              </form>
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="card-body">
                                          <p><strong>Fecha Programada:</strong> <?= fFechaLarga($bloque["fecha"]) ?></p>
                                          <p><strong>Total Programado:</strong> $<?= number_format($bloque["total"], 4) ?></p>
                                          <p><strong>Prioridad:</strong> <span class="prioridad-num"><?= $bloque["prioridad"] ?></span></p>
                                          <p><strong>Ordenes de Compra:</strong></p>
                                          <ul>
                                            <?php foreach($bloque["ordenesCompra"] as $orden) : ?>
                                              <li>
                                                <?= $orden["folio"] ?> - <?= $orden["obra"] ?>
                                                  <button type="submit" class="btn btn-sm btn-danger" data-bloque-id="<?= $bloque["id"] ?>" data-orden-id="<?= $orden["id"] ?>">
                                                    <i class="fas fa-trash"></i>
                                                  </button>
                                              </li>
                                            <?php endforeach; ?>
                                          </ul>
                                        </div>
                                      </div>
                                    </div>
                                    <script>
                                      document.addEventListener('DOMContentLoaded', function() {
                                        const container = document.querySelector('.col-md-6 .row');
                                        let dragged;

                                        container.querySelectorAll('.draggable-bloque').forEach(el => {
                                          el.setAttribute('draggable', true);

                                          el.addEventListener('dragstart', function(e) {
                                            dragged = this;
                                            e.dataTransfer.effectAllowed = 'move';
                                            this.classList.add('dragging');
                                          });

                                          el.addEventListener('dragend', function() {
                                            this.classList.remove('dragging');
                                          });

                                          el.addEventListener('dragover', function(e) {
                                            e.preventDefault();
                                          });

                                          el.addEventListener('dragenter', function(e) {
                                            e.preventDefault();
                                            if (this !== dragged) this.classList.add('drag-over');
                                          });

                                          el.addEventListener('dragleave', function() {
                                            this.classList.remove('drag-over');
                                          });

                                          el.addEventListener('drop', function(e) {
                                            e.preventDefault();
                                            this.classList.remove('drag-over');
                                            if (this !== dragged) {
                                              if (this.parentNode) {
                                                this.parentNode.insertBefore(dragged, this);
                                              }
                                              actualizarPrioridades();
                                            }
                                          });
                                        });

                                        function actualizarPrioridades() {
                                          const bloques = Array.from(container.querySelectorAll('.draggable-bloque'));
                                          const prioridades = bloques.map((bloque, idx) => {
                                          // Actualiza el número en la UI
                                          bloque.querySelector('.prioridad-num').textContent = idx + 1;
                                            return {
                                              id: bloque.dataset.bloqueId,
                                              prioridad: idx + 1
                                            };
                                          });

                                          // Enviar prioridades por AJAX al backend
                                          $.ajax({
                                          url: rutaAjax + 'app/Ajax/ProgramacionPagosAjax.php',
                                          method: 'POST',
                                          dataType: 'json',
                                          headers: {
                                            'X-CSRF-TOKEN': '<?= token() ?>'
                                          },
                                          data: {
                                            accion: 'actualizarPrioridades',
                                            _token: $('input[name="_token"]').val(),
                                            prioridades: prioridades
                                          },
                                          success: function(data) {
                                            if (!data.error) {
                                            // Opcional: mostrar mensaje de éxito
                                            // Swal.fire('Prioridades actualizadas', '', 'success');
                                            } else {
                                            Swal.fire('Error', data.errorMessage || 'No se pudo actualizar prioridades.', 'error');
                                            }
                                          },
                                          error: function() {
                                            Swal.fire('Error', 'Ocurrió un error en la petición.', 'error');
                                          }
                                          });
                                        }
                                      });
                                    </script>
                                    <style>
                                      .draggable-bloque.dragging {
                                        opacity: 0.5;
                                      }
                                      .draggable-bloque.drag-over {
                                        border: 2px dashed #007bff;
                                      }
                                    </style>
                                  <?php endif; ?>

                                  <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- /.card-body -->
          </div> <!-- /.card -->
        </div> <!-- /.col -->
      </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

  </section>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/programacion-pagos.js');
?>
