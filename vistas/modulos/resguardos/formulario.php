<?php
    if (isset($resguardo->id)) {
        $usuarioEntrego = isset($old["usuarioEntrego"]) ? $old["usuarioEntrego"] : $resguardo->usuarioEntrego;

        $usuarioRecibioId = isset($old["usuarioRecibioId"]) ? $old["usuarioRecibioId"] : $resguardo->usuarioRecibioId;

        $obraId = isset($old["obra"]) ? $old["obra"] : $resguardo->obra;
        $fechaAsignacion = isset($old["fechaAsignacion"]) ? $old["fechaAsignacion"] : $resguardo->fechaAsignacion;
        $inventario = isset($old["inventario"]) ? $old["inventario"] : $resguardo->inventario;
        $descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $resguardo->descripcion;
        $observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $resguardo->observaciones;

    } else {
        $usuarioEntrego = isset($old["usuarioEntrego"]) ? $old["usuarioEntrego"] : "";

        $usuarioRecibioId = isset($old["usuarioRecibioId"]) ? $old["usuarioRecibioId"] : "";
        $obraId = isset($old["obra"]) ? $old["obra"] : "";
        $fechaAsignacion = isset($old["fechaAsignacion"]) ? $old["fechaAsignacion"] : "";
        $inventario = isset($old["inventario"]) ? $old["inventario"] : "";
        $descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
        $observaciones = isset($old["observaciones"]) ? $old["observaciones"] : "";
    }

    // $status = [
    //     ["id" => 1, "descripcion" => "Inactivo"],
    //     ["id" => 2, "descripcion" => "Nuevo"],
    //     ["id" => 3, "descripcion" => "Seminuevo"],
    //     ["id" => 4, "descripcion" => "Dañado"],
    // ];

?>
<input type="hidden" name="_token" value="<?php echo token(); ?>">
<input type="hidden" id="inventarioId" value="<?php echo $inventario ?>" name="inventario" >
<input type="hidden" id="resguardoId" value="<?php echo $resguardo->id ?>" name="resguardoId" >

<div class="row">
    <!-- FORMULARIO DE RESGUAROS -->
    <div class="col-md-5">
        <div class="card card-warning card-outline">
            <div class="card-body">
                <div class="alert alert-danger error-validacion mb-2 d-none">
                    <ul class="mb-0">
                        <!-- <li></li> -->
                    </ul>
                </div>
                
                <div class="row">
                    <!-- Usuario que recibe -->
                    <div class="col-md-6 form-group">

                        <label for="usuarioRecibio">Usuario que recibe:</label>
                        <select <?php if(isset($resguardo->id)) echo 'disabled' ?> name="usuarioRecibio" id="usuarioRecibio" class="custom-select form-controls form-control-sms select2" style="width: 100%">
                            <option value="">Selecciona una empleado</option>
                            <?php foreach($usuarios as $usuario) { ?>
                            <option value="<?php echo $usuario["id"]; ?>"
                                <?php echo $usuarioRecibioId == $usuario["id"] ? ' selected' : ''; ?>
                                ><?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
                            </option>
                            <?php } ?>
                        </select>

                    </div>

                    <!-- Obra -->
                    <div class="col-md-6 form-group">

                    <label for="obra">Obra:</label>
                    <select <?php if(isset($resguardo->id)) echo 'disabled' ?>  name="obra" id="obra" class="custom-select form-controls form-control-sms select2" style="width: 100%">

                        <?php if (isset($obraId) && trim($obraId) === "") { ?>

                            <option value="">Selecciona una obra</option>
                            <?php foreach($obras as $obra) { ?>
                                <option value="<?php echo $obra["id"]; ?>"
                                    <?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>>
                                    <?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
                                </option>
                            <?php } ?>

                        <?php } else { ?>
                            <option value="no_asignada">Sin obra asignada</option>  
                        <?php } ?>
                    </select>
                    </div>

                    <!-- Fecha -->
                    <div class="col-md-6 form-group">
                        <label for="fechaAsignacion">Fecha de Entregado:</label>

                        <input class="form-control form-control-sm" 
                                type="datetime-local" 
                                id="fecha" 
                                <?php if(isset($resguardo->id) || $id==null ) echo 'disabled' ?>
                                name="fecha" 
                                value="<?php echo $fechaAsignacion; ?>"
                                placeholder="Ingresa la fecha" 
                                required>
                    </div>


                    <!-- Observaciones -->
                    <div class="col-12 form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea 
                            name="observaciones" 
                            id="observaciones" 
                            class="form-control form-control-sm text-uppercase"
                            <?php echo (isset($resguardo->id) || $id == null) ? 'disabled' : ''; ?>
                        ><?php echo $observaciones ?? "Sin observaciones"; ?>
                        </textarea>

                    </div>
                </div>			
            </div><!-- <div class="card-body"> -->
        </div><!-- <div class="card card-warning card-outline"> -->
    </div>

        <!-- TABLA PARTIDAS RESGUARDOS -->
	<div class="col-md-7">
        <div class="card card-info card-outline">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-sm table-bordered table-striped mb-0 " id="tablaResguardoPartida" width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th >Concepto</th>
                                <th >Cantidad</th>
                                <th >Unidad</th>
                                <th >Numero Parte</th>
                                <th >Partida</th>
                            </tr>
                        </thead>

                        <tbody class="text-uppercase">
                        </tbody>

                    </table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaSalidas" width="100%"> -->

                </div> <!-- <div class="table-responsive"> -->

            </div> <!-- <div class="card-body"> -->

        </div> <!-- <div class="card card-info card-outline"> -->

    </div><!-- <div class="col-md-6"> -->

</div>