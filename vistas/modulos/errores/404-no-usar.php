<?php

  include "vistas/modulos/plantilla/encabezado.php";
  use App\Route;

?>

<div class="content-wrapper">

  <section class="content-header">
    
    <h1>
      
      Página no encontrada
    
    </h1>

    <ol class="breadcrumb">
      
      <li><a href="<?php echo Route::routes('inicio'); ?>"><i class="fa fa-dashboard"></i> Inicio</a></li>
      
      <li class="active">Página no encontrada</li>
    
    </ol>

  </section>

  <section class="content">

    <div class="error-page">
      
      <h2 class="headline text-primary">404</h2> 

      <div class="error-content">

        <h3>

          <i class="fa fa-warning text-primary"></i> 

          Ooops! Página no encontrada.

        </h3>

        <p>
        
           Ingresa al menú lateral y allí podrás encontrar las páginas disponibles. También puedes regresar haciendo <a href="<?php echo Route::routes('inicio'); ?>">click aquí.</a>
        
        </p>

      </div>

    </div>  

  </section>

</div>

<?php
  
  // $archivoJS = "vistas/js/fileName.js";
  include "vistas/modulos/plantilla/pie-de-pagina.php";

?>