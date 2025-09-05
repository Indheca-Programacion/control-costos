<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">

    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Sistema De Gestión Integral <small class="font-weight-light"></small></h1>
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
            <div class="card-body">

              <div id="accordion">

                <?php
                $consecutivo = 1;
                  foreach ($documentos as $key => $archivo) {
                ?>
                
                <div class="card card-primary card-outline">
                  <div class="card-header" id="heading-<?=$key?>">
                    <h5 class="mb-0">
                      <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?=$consecutivo?>" aria-expanded="false" aria-controls="collapse-<?=$consecutivo?>">
                        <?=$key?>
                      </button> <!-- /.btn -->
                    </h5> <!-- h5 -->
                  </div> <!-- /.card-header -->

                  <div id="collapse-<?=$consecutivo?>" class="collapse" aria-labelledby="heading-<?=$consecutivo?>" data-parent="#accordion">
                    <div class="card-body">
                      <div class="accordion-<?=$consecutivo?>">
                        <?php $consecutivo2 = 1; foreach ($archivo as $key2 => $doc) { ?>
                          <?php if( !is_numeric($key2) ) { ?>
                            <div class="card card-primary card-outline">
                              <div class="card-header" id="heading-<?=$consecutivo?>-<?=$consecutivo2?>">
                                <h5 class="mb-0">
                                  <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?=$consecutivo?>-<?=$consecutivo2?>" aria-expanded="false" aria-controls="collapse-<?=$consecutivo?>-<?=$consecutivo2?>">
                                    <?=$key2?>
                                  </button> <!-- /.btn -->
                                </h5> <!-- h5 -->
                              </div> <!-- /.card-header -->
                              <div id="collapse-<?=$consecutivo?>-<?=$consecutivo2?>" class="collapse" aria-labelledby="heading-<?=$consecutivo?>-<?=$consecutivo2?>" data-parent=".accordion-<?=$consecutivo?>">
                                <div class="card-body">
                                  <div class="accordion-<?= $consecutivo?>-<?= $consecutivo2 ?>">
                                    <?php $consecutivo3 = 1; foreach ($doc as $key3 => $doc2) { ?>
                                      <div class="card card-warning card-outline">
                                        <div class="card-header" id="heading-<?=$consecutivo?>-<?=$consecutivo2?>-<?=$consecutivo3?>">
                                          <h5 class="mb-0">
                                            <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?=$consecutivo?>-<?=$consecutivo2?>-<?=$consecutivo3?>" aria-expanded="false" aria-controls="collapse-<?=$consecutivo?>-<?=$consecutivo2?>-<?=$consecutivo3?>">
                                              <?=$doc2['titulo']?>
                                            </button> <!-- /.btn -->
                                          </h5> <!-- h5 -->
                                        </div> <!-- /.card-header -->
                                        <div id="collapse-<?=$consecutivo?>-<?=$consecutivo2?>-<?=$consecutivo3?>" class="collapse" aria-labelledby="heading-<?=$consecutivo?>-<?=$consecutivo2?>-<?=$consecutivo3?>" data-parent=".accordion-<?= $consecutivo?>-<?= $consecutivo2 ?>">
                                          <div class="card-body">
                                            <iframe src="<?php echo $doc2['ruta']?>" width="100%" height="500px"> 
                                            </iframe> 
                                          </div> <!-- /.card-body -->
                                        </div> <!-- /.collapse -->
                                      </div> <!-- /.card -->
                                    <?php $consecutivo3++; } ?>
                                  </div>
                                </div> <!-- /.card-body -->
                              </div> <!-- /.collapse -->
                            </div> <!-- /.card -->
                          <?php }else{ ?>
                            <div class="card card-warning card-outline">
                              <div class="card-header" id="heading-<?=$consecutivo?>-<?=$consecutivo2?>">
                                <h5 class="mb-0">
                                  <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-<?=$consecutivo?>-<?=$consecutivo2?>" aria-expanded="false" aria-controls="collapse-<?=$consecutivo?>-<?=$consecutivo2?>">
                                    <?=$doc['titulo']?>
                                  </button> <!-- /.btn -->
                                </h5> <!-- h5 -->
                              </div> <!-- /.card-header -->
                              <div id="collapse-<?=$consecutivo?>-<?=$consecutivo2?>" class="collapse" aria-labelledby="heading-<?=$consecutivo?>-<?=$consecutivo2?>" data-parent=".accordion-<?=$consecutivo?>">
                                <div class="card-body">
                                  <iframe src="<?php echo $doc['ruta']?>" width="100%" height="500px"> 
                                  </iframe>                            
                                </div> <!-- /.card-body -->
                              </div> <!-- /.collapse -->
                            </div> <!-- /.card -->
                          <?php }; ?>
                        <?php $consecutivo2++; } ?>
                      </div>
                    </div> <!-- /.card-body -->
                  </div> <!-- /.collapse -->
                </div> <!-- /.card -->
                <?php $consecutivo++; } ?>

              </div> <!-- /.accordion -->
        
            </div> <!-- /.card-body -->
          </div> <!-- /.card -->
        </div> <!-- /.col -->
      </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

  </section>

</div> <!-- /.content-wrapper -->