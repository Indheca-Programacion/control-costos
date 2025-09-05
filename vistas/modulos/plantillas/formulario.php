<?php 
    if(isset($plantilla->id)){
        $descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $plantilla->descripcion;
        $nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $plantilla->nombreCorto;
    }else{
        $descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
        $nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
    }
?>
<input type="hidden" name="_token" id="_token" value="<?php echo createToken(); ?>">

<div class="row">
    <div class="col-md-6 form-group">
        <label for="descripcion">Descripcion de plantilla:</label>
        <input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una descripcion">
    </div>
    <div class="col-md-6 form-group">
        <label for="nombreCorto">Nombre Corto:</label>
        <input type="text" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una nombre corto para la plantilla">
    </div>
</div>