<?php
use App\Route;
?>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-info">
        <div class="inner">
            <h3><?php echo $cantidadNominas; ?></h3>
            <p>Nominas</p>
        </div>    
        <div class="icon">
            <i class="fas fa-money-check-alt"></i>
        </div>
        <a href="<?php echo Route::names('nominas.index'); ?>" class="small-box-footer">
            Más info <i class="fa fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-green">
        <div class="inner">
            <h3><?php echo $cantidadObras; ?></h3>
            <p>Obras</p>
        </div>
        <div class="icon">
            <i class="fas fa-vest"></i>
        </div>
        <a href="<?php echo Route::names('costos-resumen.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-warning">
        <div class="inner">
            <h3><?php echo $cantidadRequisiciones; ?></h3>
            <p>Requisiciones</p>
        </div>
        <div class="icon">
            <i class="fas fa-tools"></i>
        </div>
        <a href="<?php echo Route::names('requisiciones.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-danger">
        <div class="inner">
            <h3><?php echo $cantidadEmpleados; ?></h3>
            <p>Empleados</p>
        </div>
        <div class="icon">
            <i class="fas fa-user-tie"></i>
        </div>
        <a href="<?php echo Route::names('empleados.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>
