<?php
    if ( isset($cotizacion->id) ) {
        
    } else {

    }

?>

<input type="hidden" name="_token" value="<?php echo token() ?>">
<input type="hidden" name="requisicionId" id="requisicionId" value="<?php echo isset($cotizacion->requisicionId) ? $cotizacion->requisicionId : ''; ?>">
<div class="row">

    <div class="col-md-6">
        <label for="direccionEntrega">Direccion de Entrega</label>
        <input type="text" class="form-control form-control-sm text-uppercase" id="direccionEntrega" value="<?php echo $requisicion->direccion ?? 'No definida'; ?>" readonly>
    </div>

    <div class="col-md-6">
        <label for="fechaRequerida">Fecha Requerida</label>
        <span type="date" class="form-control form-control-sm" id="fechaRequerida" readonly><?php echo fFechaLarga($requisicion->fechaReq); ?></span>
    </div>

    <!-- <div class="form-group col-12 d-flex justify-content-end">
        <div class="p-3 bg-light border rounded">
            <strong class="text-dark" style="font-size: 1.2em;"> Fecha Límite:</strong> 
            <span class="ml-2" style="font-size: 1.1em; color: <?php echo isset($cotizacion->fechaLimite) ? '#28a745' : '#dc3545'; ?>;">
                <?php echo isset($cotizacion->fechaLimite) ? fFechaLargaHora($cotizacion->fechaLimite) : 'No definida'; ?>
            </span>
        </div>
    </div> -->
        
    <div class="form-group col-4  mb-1 subir-cotizaciones d-flex flex-column mt-1">
        <button type="button" class="btn btn-info" id="btnSubirCotizaciones" <?php echo ($cotizacion->fechaLimite < date('Y-m-d')) ? 'disabled' : ''; ?>>Adjuntar Cotizaciones</button>
        <input type="file" class="form-control d-none" id="cotizacionArchivos" name="cotizacionArchivos[]" accept="application/pdf">

        <?php foreach($requisicion->cotizacionesProveedor as $key=>$cotizacion) : ?>
            <p class="text-info mb-0 text-right"><?php echo $cotizacion['archivo']; ?>
            <i  class="ml-1 fas fa-eye verArchivo" archivoRuta="<?php echo $cotizacion['ruta']?>" style="cursor: pointer;" ></i>

            </p>
        <?php endforeach; ?>
        <span class="lista-archivos float-left">
        </span>
    </div>

    <div  class="form-group col-12">
        <h4 class="text-primary">
            <i class="fas fa-list-alt"></i> Requerimientos
        </h4>
        <table class="table table-hover text-nowrap" id="tablaRequisicionCotizaciones" width="100%">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requerimientos as $key => $value): ?>
                    <tr class="text-uppercase">
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $value["descripcion"].' | '.$value["concepto"]; ?></td>
                        <td><?php echo $value["cantidad"]; ?></td>
                        <td><?php echo $value["unidad"]; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
