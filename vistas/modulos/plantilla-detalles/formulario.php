<?php 
    if(isset($plantilla->id)){
        $tipo = isset($old["tipo"]) ? $old["tipo"] : $plantilla->tipo;
        $descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $plantilla->descripcion;
        $presupuesto = isset($old["presupuesto"]) ? $old["presupuesto"] : $plantilla->presupuesto;
        $cantidad = isset($old["cantidad"]) ? $old["cantidad"] : $plantilla->cantidad;
        $plantilla = isset($old["plantilla"]) ? $old["plantilla"] : $plantilla->plantilla;
    }
?>
<input type="hidden" name="_token" id="_token" value="<?php echo createToken(); ?>">
<input type="hidden" id="plantilla" name="plantilla" value="<?= $plantilla ?>">

<div class="row">
    <div class="col-md-6 form-group">
        <label for="descripcion">Directo/Indirecto:</label>
        <input disabled type="text" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una descripcion">
    </div>
    <div class="col-md-6 form-group">
        <label for="tipo">Tipo:</label>
        <input disabled type="text" value="<?php echo fString($tipo); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una nombre corto para la plantilla">
    </div>
    <div class="col-md-6 form-group">
        <label for="cantidad">Cantidad:</label>
        <input type="text" name="cantidad" value="<?php echo fString($cantidad); ?>" class="form-control form-control-sm text-uppercase campoConDecimal" placeholder="Ingresa una nombre corto para la plantilla">
    </div>
    <div class="col-md-6 form-group">
        <label for="presupuesto">Presupuesto:</label>
        <input type="text" name="presupuesto" value="<?php echo fString($presupuesto); ?>" class="form-control form-control-sm text-uppercase campoConDecimal" placeholder="Ingresa una nombre corto para la plantilla">
    </div>
</div>