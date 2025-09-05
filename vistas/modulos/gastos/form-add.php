<form action="" method="post" id="formAddPartida" enctype="multipart/form-data">
    <div class="row">
        <!-- Fecha de facturacion -->
        <div class="col-md-6 form-group">
            <label for="fecha">Fecha <?php echo $tipoGasto == 1 ? ' de Facturacion:' : '';?></label>
            <input type="text" class="form-control form-control-sm datetimepicker-input" id="datetimepicker3" name="fecha" data-toggle="datetimepicker" data-target="#datetimepicker3"/>
        </div>
        <!-- Tipo de Gasto -->
        <div class="col-md-6 form-group">
            <label for="tipo">Tipo de Gasto:</label>
            <select class="custom-select select2" id="tipo" name="tipoGasto">
                <option value="" selected>Selecciona un tipo de gasto</option>
                <?php foreach($gastosTipo as $value) { ?>
                <option value="<?php echo $value["id"]; ?>">
                <?php echo mb_strtoupper(fString($value["descripcion"])); ?>
                </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="row">
        <!-- OBRA -->
        <div class="col-md-6 form-group">
            <label for="obra-destino">Obra:</label>
            <select class="custom-select select2" id="obra-destino" name="obra">
                <option value="" selected>Selecciona una Obra</option>
                <?php foreach($obras as $obra) { ?>
                <option value="<?php echo $obra["id"]; ?>">
                <?php echo '[ ' . mb_strtoupper(fString($obra["empresas.nombreCorto"])) . ' ] ' . mb_strtoupper(fString($obra["descripcion"])); ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <!-- SOLICITO -->
        <div class="col-md-6 form-group">
            <label for="solicito">Solicitó:</label>
            <input type="text" class="form-control form-control-sm text-uppercase" name="solicito" id="solicito">
        </div>
        <!-- Descripcion -->
        <div class="col-md-6 form-group">
            <label for="descripcion">Descripcion</label>
            <select class="custom-select select2" id="descripcion" name="obraDetalle">
                <option value="" selected>Selecciona una descripcion</option>
                <?php foreach($obra_detalles as $detalle) { ?>
                    <?php if(mb_strtolower($detalle["unidad"]) <> "nómina") { ?>
                        <option value="<?php echo $detalle["id"]; ?>">
                        <?php echo mb_strtoupper(fString($detalle["descripcion"])); ?>
                    <?php } ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <!-- No. Economico -->
        <div class="col-md-6 form-group">
            <label for="economico">No. Economico:</label>
            <input type="text" class="form-control form-control-sm" name="economico" id="economico"> 
        </div>
        <!-- COSTO -->
        <div class="col-md-6 form-group">
            <label for="costo">Costo Total</label>
            <input type="text" id="costo" value="0" name="costo" class="form-control form-control-sm campoConDecimal">
        </div>
        <!-- CANTIDAD -->
        <div class="col-md-6 form-group">
            <label for="cantidad">Cantidad</label>
            <input type="text" id="cantidad" value="0" name="cantidad" class="form-control form-control-sm campoSinDecimal">
        </div>
        <!-- PROVEEDOR -->
        <div class="col-md-6 form-group">
            <label for="proveedor">Proveedor: </label>
            <input type="text" id="proveedor" name="proveedor" class="form-control form-control-sm">
        </div>
        <?php if($tipoGasto == 1) :?>
        <!-- FACTURA -->
        <div class="col-md-6 form-group">
            <label for="factura">Factura</label>
            <input type="text" id="factura" name="factura" class="form-control form-control-sm">
        </div>
        <?php endif ?>
    </div>
    <div class="row">
        <!-- OBSERVACIONES -->
        <div class="col-12 form-group">
            <label for="observaciones">Observaciones:</label>
            <textarea name="observaciones" id="observaciones" class="form-control form-control-sm" rows="4"></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col subir-archivos">
            <button type="button" class="btn btn-info float-left" id="btnSubirArchivos">
                <i class="fas fa-folder-open"></i> Cargar Facturas
            </button>
            <span class="lista-archivos">
            </span>
		    <input type="file" class="form-control form-control-sm d-none" id="archivo" multiple>
        </div>
        <div class="col d-flex justify-content-end">
            <button type="button" id="btnAddPartida" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> Añadir Partida
            </button>
        </div>
    </div>
</form>