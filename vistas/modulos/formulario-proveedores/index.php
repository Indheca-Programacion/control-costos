<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content pt-5">
    
    <div class="container-fluid text-center mb-5 text-uppercase">
        <h3>
            <b>
                Información complementaria para la alta de proveedores en compras
            </b>
        </h3>
    </div>


    <?php if ( !is_null(flash()) ) : ?>
      <div class="d-none" id="msgToast" clase="<?=flash()->clase?>" titulo="<?=flash()->titulo?>" subtitulo="<?=flash()->subTitulo?>" mensaje="<?=flash()->mensaje?>"></div>
    <?php endif; ?>

    <div class="container-fluid ">
      <div class="row ">
        <div class="col-md-7 col-lg-10 col-xl-7  mx-auto">
          <div class="card card-secondary card-outline">
            <div class="card-header text-uppercase d-flex flex-column">
              <h3 class="card-title ">
                <b>
                    Datos generales del proveedor
                </b>
              </h3>
              <h5 class="card-title text-sm">
                 (para el uso del área de compras indheca)
              </h5>
            </div>
            <div class="card-body">
                <!-- <?php include "vistas/modulos/errores/form-messages.php"; ?> -->
                    <form id="formSend" method="POST" action="<?php echo Route::names('formulario-proveedor.store'); ?>" enctype="multipart/form-data">
                        <?php include "vistas/modulos/formulario-proveedores/formulario.php"; ?>
                            <button type="button" id="btnSend" class="btn btn-outline-primary">
                                <i class="fas fa-save"></i> Enviar
                            </button>										
                        <div id="msgSend"></div>
                    </form>
                <?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
            </div> <!-- /.card-body -->
          </div> <!-- /.card -->
        </div> <!-- /.col -->
      </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

  </section>

</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/formulario-proveedores.js?v=1.6');
?>
