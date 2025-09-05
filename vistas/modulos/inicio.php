<?php
  use App\Controllers\Autorizacion;
?>

<div class="content-wrapper">

  <section class="content-header">
    
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <!-- font-weight-light -->
            <h1>Tablero <small class="font-weight-light">Panel de Control</small></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item active"><i class="fas fa-tachometer-alt"></i> Inicio</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->

  </section>

  <section class="content">

    <div class="card card-success">

      <div class="card-header">

        <h2 class="text-capitalize mb-0">Bienvenid@ <?=fString($usuarioAutenticado->nombreCompleto)?></h2>

      </div>

    </div>

  </section>
 
</div>
