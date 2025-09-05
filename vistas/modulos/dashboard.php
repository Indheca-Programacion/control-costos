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

    <div class="row">      
    <?php
      include "inicio/cajas-superiores.php";
    ?>
    </div> 

    <div class="row">
      
      <?php if ( false && count($horasTrabajadasCentro) ) : ?>
      <div class="col-lg-12">
        <?php
          // include "reportes/grafico-horas-trabajadas.php";
        ?>
      </div>
      <?php endif; ?>

      <div class="col-md-4">
        <?php
          include "reportes/tareas.php";
        ?>
      </div>

      <div class="col-md-8">
      <?php
        include "inicio/asistente.php";
      ?>
      </div>

    </div>

  </section>
 
</div>
