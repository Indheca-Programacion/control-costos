<?php
use App\Route;
    if ( isset($gastos->id) ) {
		$obraId = isset($old["obra"]) ? $old["obra"] : $gastos->obra;
		$tipoGasto = isset($old["tipoGasto"]) ? $old["tipoGasto"] : $gastos->tipoGasto;
		$banco = isset($old["banco"]) ? $old["banco"] : $gastos->banco;
		$cuenta = isset($old["cuenta"]) ? $old["cuenta"] : $gastos->cuenta;
		$clave = isset($old["clave"]) ? $old["clave"] : $gastos->clave;
		$encargado = isset($old["encargado"]) ? $old["encargado"] : $gastos->encargado;
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : $gastos->fecha_inicio;
		$fecha_fin = isset($old["fecha_fin"]) ? $old["fecha_fin"] : $gastos->fecha_fin;
		$fecha_envio = isset($old["fecha_envio"]) ? $old["fecha_envio"] : $gastos->fecha_envio;
        $periodos = $ubicacion["periodos"] + $ubicacion["semanaExtra"];
        $requisicion = $gastos->requisicionId;
        $encargado = isset($old["encargado"]) ? $old["encargado"] : $gastos->encargado;
        $folio = $requisiciones->folio;
	} else {
		$obraId = isset($old["obra"]) ? $old["obra"] : "";
		$tipoGasto = isset($old["tipoGasto"]) ? $old["tipoGasto"] : "";
		$banco = isset($old["banco"]) ? $old["banco"] : "";
		$cuenta = isset($old["cuenta"]) ? $old["cuenta"] : "";
		$clave = isset($old["clave"]) ? $old["clave"] : "";
		$encargado = isset($old["encargado"]) ? $old["encargado"] : "";
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : "0000-00-00";
		$fecha_fin = isset($old["fecha_fin"]) ? $old["fecha_fin"] : "0000-00-00";
		$fecha_envio = isset($old["fecha_envio"]) ? $old["fecha_envio"] : "0000-00-00";
        $encargado = isset($old["encargado"]) ? $old["encargado"] : usuarioAutenticado()["id"];
        $requisicion = null;
        $requisicion = null;
        $periodos = '';
	}
    //echo json_encode($old);
?>

<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" id="gastoId" value="<?= $gastos->id ?>">

<div class="row">
    <!-- OBRA -->
    <div class="col-md-6 form-group">
        <input type="hidden" id="periodos" value="<?= $periodos  ?>">
        <label for="obra">Obra:</label>
        <select class="custom-select select2" id="obra" name="obra" <?php if(isset($gastos->id)) echo 'disabled'; ?>>
            <option value="" selected>Selecciona una Obra</option>
            <?php foreach($obras as $obra) { ?>
            <option value="<?php echo $obra["id"]; ?>"
            <?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>>
            <?php echo '[ ' . mb_strtoupper(fString($obra["empresas.nombreCorto"])) . ' ] ' . mb_strtoupper(fString($obra["descripcion"])); ?>
            </option>
            <?php } ?>
        </select>
    </div>
    <!-- TIPO DE GASTO -->
    <div class="col-md-6 form-group">
        <label for="tipoGasto">Tipo de Gasto:</label>
        <select name="tipoGasto" id="tipoGasto" class="custom-select select2" <?php if(isset($gastos->id)) echo 'disabled'; ?>>
            <option value="">Seleccione un tipo</option>
            <option value="1" <?php echo $tipoGasto == 1 ? ' selected' : ''; ?>>Deducible</option>
            <option value="2" <?php echo $tipoGasto == 2 ? ' selected' : ''; ?>>No Deducible</option>
        </select>
    </div>
</div>
    
<div class="row">
    <!-- FECHA INICIO -->
    <div class="col-md-6 form-group">
        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="text" class="form-control form-control-sm datetimepicker-input" id="datetimepicker5" name="fecha_inicio" value="<?= $fecha_inicio ?>" data-toggle="datetimepicker" data-target="#datetimepicker5"/>
    </div>
    <!-- FECHA FINALIZACION -->
    <div class="col-md-6 form-group">
        <label for="fecha_fin">Fecha de Finalizacion:</label>
        <input type="text" class="form-control form-control-sm" id="datetimepicker1" name="fecha_fin" value="<?= $fecha_fin ?>" data-toggle="datetimepicker" data-target="#datetimepicker1"/>
    </div>
    <!-- ENCARGADO -->
    <div class="col-md-6 form-group">
        <label for="encargado">Encargado:</label>
        <select name="encargado" id="encargado" class="custom-select select2">
            <option value="">Selecciona un Encargado</option>
            <?php foreach($usuarios as $usuario) { ?>
            <option value="<?php echo $usuario["id"]; ?>"
            <?php echo $encargado == $usuario["id"] ? ' selected' : ''; ?>>
            <?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
            </option>
            <?php } ?>
        </select>
    </div>
</div>

<div class="row" id="bancoSection" >
    <!-- BANCO -->
    <div class="col-md-6 form-group">
        <label for="banco">Banco:</label>
        <input type="text" id="banco" name="banco" value="<?php echo mb_strtoupper(fString($banco)); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el banco">
    </div>
    <!-- CUENTA -->
    <div class="col-md-6 form-group">
        <label for="cuenta">Cuenta:</label>
        <input type="text" id="cuenta" maxlength="14" name="cuenta" value="<?php echo $cuenta; ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el numero de cuenta">
    </div>
    <!-- CLABE -->
    <div class="col-md-6 form-group">
        <label for="clave">Clabe Interbancaria:</label>
        <input type="text" id="clave" name="clave" value="<?php echo $clave; ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el numero de clave" maxlength="20">
    </div>
</div>
<?php if( !is_null($requisicion) ): ?>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="">Folio de Requisicion:</label>
            <a href="<?= Route::names('requisiciones.edit', $requisiciones->id); ?>" target="_blank"><span type="text" class="form-control form-control-sm text-uppercase"><?php echo $folio ?> </a></span>
        </div>
    </div>
<?php endif ?>