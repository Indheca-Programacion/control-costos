<?php 
    $obraId = isset($old['obraId']) ? $old['obraId'] : '';
?>
<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" id="periodo" name="periodo" value="<?php echo date('W'); ?>">

<!-- Stepper -->
<div class="bs-stepper-header" role="tablist">
    <div class="step">
        <button type="button" class="step-trigger active" id="stepper1trigger1">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label">Obra</span>
        </button>
    </div>
    <div class="bs-stepper-line"></div>
    <div class="step">
        <button type="button" class="step-trigger" id="stepper1trigger2">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label">Detalles</span>
        </button>
    </div>
    <div class="bs-stepper-line"></div>
    <div class="step">
        <button type="button" class="step-trigger" id="stepper1trigger3">
            <span class="bs-stepper-circle">3</span>
            <span class="bs-stepper-label">Finalizar</span>
        </button>
    </div>
</div>

<style>
    .bs-stepper-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    .bs-stepper-line {
        flex: 1 1 0%;
        height: 2px;
        background: #dee2e6;
        margin: 0 8px;
    }
    .step-trigger {
        background: none;
        border: none;
        outline: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6c757d;
        font-weight: 500;
    }
    .step-trigger.active,
    .step-trigger:focus,
    .step-trigger:hover {
        color: #0d6efd;
    }
    .bs-stepper-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        background: #dee2e6;
        color: #6c757d;
        font-weight: bold;
        margin-bottom: 0.25rem;
        font-size: 1.1rem;
        transition: background 0.2s, color 0.2s;
    }
    .step-trigger.active .bs-stepper-circle {
        background: #0d6efd;
        color: #fff;
    }
</style>

<div id="creacionRequisicion">

    <div class="row" id="formulario-step-1">
        <div class="col-md-6">
            <div class="form-group">
                <label for="obra" class="text-capitalize">Hola. <?php echo $usuarioAutenticado->prefijo_usuario.' '. $usuarioAutenticado->nombre; ?> ¿A qué obra se hará el cargo?</label>
                <select name="fk_IdObra" id="obraId" class="form-control select2" required>
                    <option value="">Seleccione una obra</option>
                    <?php foreach ($obras as $obra): ?>
                        <option value="<?= $obra["id"] ?>"><?= $obra["descripcion"] ?></option>
                    <?php endforeach; ?>
                </select>
            </div> <!-- /.form-group -->
        </div> <!-- /.col-md-6 -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="tipoGasto">¿Es un gasto <u>directo</u> o <u>indirecto</u>?</label>
                <select id="tipoGasto" class="form-control select2">
                    <option value="1">Directo</option>
                    <option value="2">Indirecto</option>
                </select>
            </div> <!-- /.form-group -->
        </div> <!-- /.col-md-6 -->
    </div> <!-- /.row -->

    <div class="row d-none" id="formulario-step-2">
        <div class="col-md-6">

            <div class="input-group">
                <input type="text" class="form-control" placeholder="Busque un material" disabled>
                <div class="input-group-append">
                    <button type="button" id="btnBuscarInsumo" class="btn btn-sm btn-outline-primary" title="Buscar Insumo">
                        <i class="fas fa-search"></i>
                    </button>
                    <button type="button" id="btnBuscarIndirecto" class="btn btn-sm btn-outline-primary d-none" title="Buscar Indirecto">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="col-12 form-group mt-2">
            <form id="formCrearRequiSend">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered table-striped mb-0" id="tablaRequiDetalles" width="100%">
                        <thead>
                            <tr>
                                <th class="text-right" style="min-width: 80px;">Partida</th>
                                <th style="min-width: 192px;">Descripción</th>
                                <th style="min-width: 64px;">Cant.</th>
                                <th style="min-width: 64px;">Costo</th>
                                <th style="min-width: 112px;">Unidad</th>
                                <th style="min-width: 320px;">Concepto</th>
                                <th style="display: none;">elementId</th>
                            </tr>
                        </thead>
                        <tbody class="text-uppercase">
                        </tbody>
                    </table>
                </div>
            </form>
        </div>

    </div> <!-- /.row -->

    <div class="row d-none" id="formulario-step-3">

        <div class="col-sm-6 form-group">
            <label for="folio">Folio</label>
            <input type="text" class="form-control form-control-sm text-uppercase" id="folio" name="folio" placeholder="Ingresa el folio ">
        </div>

        <div class="col-md-6 form-group">
            <label for="divisa">Divisa:</label>
            <select class="custom-select select2" id="divisa" name="divisa">
                <?php foreach($divisas as $divisa) { ?>
                    <option value="<?php echo $divisa["id"]; ?>">
                    <?php echo mb_strtoupper(fString($divisa["nombreCorto"])); ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-6 form-group">
            <label for="tipoRequisicion">Tipo de Requisicion:</label>
            <select class="custom-select select2" id="tipoRequisicion" name="tipoRequisicion">
                <option value="0">Programada</option>
                <option value="1">Urgente</option>
            </select>
        </div>

        <div class="col-md-6 form-group">
            <label for="presupuesto">Presupuesto:</label>
            <select class="custom-select select2" id="presupuesto" name="presupuesto">
                <option value="0" selected>General</option>
            </select>
        </div>

        <div class="col-md-6 form-group">
            <label for="fechaRequerida">Fecha Requerida:</label>						
            <div class="input-group date" data-target-input="nearest">
                <input type="text" name="fechaRequerida" id="fechaRequerida" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#modalCrearRequiInsumoIndirecto_fechaRequerida">
                <div class="input-group-append" data-target="#fechaRequerida" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
                </div>
            </div>
        </div>

        <div class="col-md-6 form-group">
            <label for="direccion">Direccion:</label>
            <input type="text" id="direccion" name="direccion" value="" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la direccion">
        </div>

        <div class="col-md-6 form-group">
            <label for="especificaciones">Especificaciones:</label>
            <textarea id="especificaciones" name="especificaciones" class="form-control form-control-sm text-uppercase" rows="2" placeholder="Ingresa las especificaciones"></textarea>
        </div>

        <div class="col-md-6 form-group">
            <label for="categoriaId">Categoría :</label>
            <select name="categoriaId" id="categoriaId" class="form-control select2 select2-hidden-accessible" style="width: 100%" tabindex="-1" aria-hidden="true">
                <option value="">Selecciona una Categoría</option>
                <?php foreach($categoriasOrdenCompra as $categoria) : ?>
                    <option value="<?php echo $categoria['id']; ?>">
                        <?php echo $categoria['nombre']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-6 form-group">
            <label for="justificacion">Justificacion:</label>
            <textarea id="justificacion" name="justificacion" rows="4" class="form-control form-control-sm"></textarea>
        </div> <!-- <div class="col-md-12 form-group"> -->

        <div class="col-md-6 form-group">
            <label for="proveedor">Proveedor (Opcional): </label>
            <button type="button" class="btn btn-info mb-2" data-toggle="modal" data-target="#modalSeleccionarProveedor">
                <i class="fas fa-truck-loading"></i> Seleccionar Proveedor
            </button>
            <input type="hidden" name="proveedorId" id="proveedorId">
            <input type="text" disabled  id="proveedor" class="form-control form-control-sm">
        </div>

        <div class="col-md-6 form-group subir-cotizaciones d-flex flex-column align-items-end mt-1">
            <label for="">¿La requisicion ya está cotizada?</label>
            <button type="button" class="btn btn-info float-right" id="btnSubirCotizaciones">
                <i class="fas fa-folder-open"></i> Cargar Cotizaciones
            </button>
            <span class="lista-archivos">
            </span>
            <input type="file" class="form-control form-control-sm d-none" id="cotizacionArchivos" multiple>

            <div class="text-muted mt-1 text-right">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>
        </div>

    </div>

    <button type="button" class="btn btn-secondary d-none" id="btnAnterior">
        <i class="fas fa-arrow-left"></i> Anterior
    </button>

    <button type="button" class="btn btn-primary" id="btnSiguiente">
        <i class="fas fa-arrow-right"></i> Siguiente
    </button>

    <button type="button" id="btnCrearRequisicion" class="btn btn-outline-primary d-none" >
        <i class="fas fa-plus"></i> Crear requisición
    </button>

</div>

<div id="terminacionRequisicion" class="d-none">
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">¡Requisición creada exitosamente!</h4>
        <p>La requisición ha sido creada y se encuentra en proceso de aprobación.</p>
        <hr>
        <p class="mb-0">Puede revisar el estado de la requisición <a id="requisicionLink" href="#"> Aquí</a>.</p>
    </div>

</div>