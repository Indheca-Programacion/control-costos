<?php
use App\Route;
?>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-info">
        <div class="inner">
            <h3><?php echo $datosFiscales; ?></h3>
            <p class="font-weight-bold">Mis Datos Generales</p>
        </div>    
        <div class="icon">
            <i class="fas fa-file-invoice"></i>
        </div>
        <a href="<?php echo Route::names('datos-generales.index'); ?>" class="small-box-footer">
            Más info <i class="fa fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-green">
        <div class="inner">
            <h3><?php echo $cantidadOrdenes; ?></h3>
            <p class="font-weight-bold">Ordenes de Compra</p>
        </div>
        <div class="icon">
            <i class="fas fa-wallet"></i>
        </div>
        <a href="<?php echo Route::names('ordenes-compra.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-warning">
        <div class="inner">
            <h3><?php echo $cantidadPagadas; ?></h3>
            <p class="font-weight-bold">Estado de Cuenta</p>
        </div>
        <div class="icon">
            <i class="fas fa-money-check-alt"></i>
        </div>
        <a href="<?php echo Route::names('estados-cuenta.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>


<div class="col-sm-6 col-lg-3">
    <div class="small-box bg-danger">
        <div class="inner">
            <h3><?php echo $cantidadDebidaDiligencia; ?></h3>
            <p class="font-weight-bold">Debida Diligencia</p>
        </div>
        <div class="icon">
            <i class="fas fa-balance-scale"></i>
        </div>
        <a href="<?php echo Route::names('debida-diligencia.index'); ?>" class="small-box-footer">
            Más info <i class="fas fa-arrow-circle-right"></i>
        </a>
    </div>
</div>

