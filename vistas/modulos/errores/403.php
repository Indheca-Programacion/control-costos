<?php use App\Route; ?>

<div class="content-wrapper">

  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Página no autorizada</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"><i class="fas fa-tachometer-alt"></i> Inicio</a></li>
            <li class="breadcrumb-item active">Página no autorizada</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <section class="content">
    <div class="error-page">
      <h2 class="headline text-primary">403</h2> 
      <div class="error-content">
        <h3>
          <i class="fa fa-warning text-primary"></i> 
          Ooops! Página no autorizada.
        </h3>
        <p>
           Ingresa al menú lateral y allí podrás encontrar las páginas disponibles. También puedes regresar haciendo <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : Route::routes('inicio'); ?>">click aquí.</a>
        </p>
      </div>
    </div>  
  </section>

</div>