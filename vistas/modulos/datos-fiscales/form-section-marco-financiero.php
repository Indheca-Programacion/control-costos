<?php if ($proveedor->idCategoria): ?>

    <?php if (!grupoAsignado($permisosAsignados, "Marco Financiero")): ?>
        <h3>
            No disponible
        </h3>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <input type="file" class="d-none" id="archivoPermiso[]">
            <?php if (tipoAsignado($permisosAsignados, 'documentacionMF')):?>
                <div class="card card-primary  card-outline">
                    <div class="card-header">
                        <h4 class="">Documentación</h4>
                    </div>
                    <div class="card-body">
                        <div class="row ">

                            <?php if (verificarTipoYPermiso($permisosAsignados,'documentacionMF', "estado-de-cuenta")): ?>
                                <div class="card card-info card-outline border-warnign mb-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                        <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center">Estado de cuenta</h5>
                                        <div class="card-tools">
                                            <button type="button" id="12" class="btn-primary btn btn-sm btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class=" fa fa-plus"></i>     
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->estadoCuenta)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->estadoCuenta as $key => $cv): ?>
                                            <div class="mb-3">
                                                    <div class="d-flex align-items-center text-info">
                                                        <span><?php echo $cv['archivo']; ?></span>
                                                        <!-- VER ARCHIVO -->
                                                        <i class="ml-2 fas fa-eye text-info verArchivo"
                                                        title="Ver archivo"
                                                        style="cursor: pointer;"
                                                        data-toggle="modal"
                                                        data-target="#archivoModal"
                                                        archivoRuta="<?php echo $cv['ruta']; ?>">
                                                        </i>
                                                        <!-- ELIMINAR ARCHIVO -->
                                                        <i class="ml-2 fas fa-trash-alt text-danger eliminarArchivo"
                                                        title="Eliminar archivo"
                                                        style="cursor: pointer;"
                                                        archivoId="<?php echo $cv['id']; ?>"
                                                        folio="<?php echo $cv['archivo']; ?>">
                                                        </i>
                                                    </div>
                                                    <small 
                                                    id="estatus-<?php echo $cv['id']; ?>" 
                                                    class="<?php
                                                        echo ($cv['estatus'] === "DOCUMENTO EN REVISION" || $cv['estatus'] === "RECHAZADO POR JURIDICO")
                                                        ? "text-danger" : "text-success";
                                                    ?>">
                                                    <?php echo $cv['estatus']; ?>
                                                    </small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (verificarTipoYPermiso($permisosAsignados,'documentacionMF', "estados-financieros")): ?>
                                <div class="card card-info ml-md-3 card-outline border-warnign mb-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                         <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center">Estados Financieros</h5>
                                        <div class="card-tools">
                                            <button type="button" id="13" class="btn-primary btn btn-sm mt-2 mt-sm-0  btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class=" fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->estadoFinancieros)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->estadoFinancieros as $key => $cv): ?>
                                            <div class="mb-3">
                                                    <div class="d-flex align-items-center text-info">
                                                        <span><?php echo $cv['archivo']; ?></span>
                                                        <!-- VER ARCHIVO -->
                                                        <i class="ml-2 fas fa-eye text-info verArchivo"
                                                        title="Ver archivo"
                                                        style="cursor: pointer;"
                                                        data-toggle="modal"
                                                        data-target="#archivoModal"
                                                        archivoRuta="<?php echo $cv['ruta']; ?>">
                                                        </i>
                                                        <!-- ELIMINAR ARCHIVO -->
                                                        <i class="ml-2 fas fa-trash-alt text-danger eliminarArchivo"
                                                        title="Eliminar archivo"
                                                        style="cursor: pointer;"
                                                        archivoId="<?php echo $cv['id']; ?>"
                                                        folio="<?php echo $cv['archivo']; ?>">
                                                        </i>
                                                    </div>
                                                    <small 
                                                    id="estatus-<?php echo $cv['id']; ?>" 
                                                    class="<?php
                                                        echo ($cv['estatus'] === "DOCUMENTO EN REVISION" || $cv['estatus'] === "RECHAZADO POR JURIDICO")
                                                        ? "text-danger" : "text-success";
                                                    ?>">
                                                    <?php echo $cv['estatus']; ?>
                                                    </small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (verificarTipoYPermiso($permisosAsignados,'documentacionMF', "ultima-aclacaracion-anual")): ?>
                                <div class="card card-info ml-md-3 card-outline border-warnign mb-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                        <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center"> Ultima Declaracion Anual</h5>
                                        <div class="card-tools">
                                            <button type="button" id="14" class="btn-primary btn btn-sm  btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class=" fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->ultimaDeclaracionAnual)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->ultimaDeclaracionAnual as $key => $cv): ?>
                                            <div class="mb-3">
                                                    <div class="d-flex align-items-center text-info">
                                                        <span><?php echo $cv['archivo']; ?></span>
                                                        <!-- VER ARCHIVO -->
                                                        <i class="ml-2 fas fa-eye text-info verArchivo"
                                                        title="Ver archivo"
                                                        style="cursor: pointer;"
                                                        data-toggle="modal"
                                                        data-target="#archivoModal"
                                                        archivoRuta="<?php echo $cv['ruta']; ?>">
                                                        </i>
                                                        <!-- ELIMINAR ARCHIVO -->
                                                        <i class="ml-2 fas fa-trash-alt text-danger eliminarArchivo"
                                                        title="Eliminar archivo"
                                                        style="cursor: pointer;"
                                                        archivoId="<?php echo $cv['id']; ?>"
                                                        folio="<?php echo $cv['archivo']; ?>">
                                                        </i>
                                                    </div>
                                                    <small 
                                                    id="estatus-<?php echo $cv['id']; ?>" 
                                                    class="<?php
                                                        echo ($cv['estatus'] === "DOCUMENTO EN REVISION" || $cv['estatus'] === "RECHAZADO POR JURIDICO")
                                                        ? "text-danger" : "text-success";
                                                    ?>">
                                                    <?php echo $cv['estatus']; ?>
                                                    </small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
        </div>
    </div>


    <?php if (grupoAsignado($permisosAsignados, "Marco Financiero")): ?>
        <button type="submit" id="btnSend" class="btn btn-outline-primary">
            <i class="fas fa-save"></i> Actualizar
        </button>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">No tienes una categoría asignada.</h4>
        <p>Habla con un administrador.</p>
    </div>
<?php endif; ?>