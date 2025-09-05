<?php if ($proveedor->idCategoria): ?>

    <?php if (!grupoAsignado($permisosAsignados, "Marco Legal")): ?>
        <h3>
            No disponible
        </h3>

    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <input type="file" class="d-none" id="archivoPermiso[]">

            <?php if (tipoAsignado($permisosAsignados, 'documentacionML')):?>
                <div class="card card-primary  card-outline">
                    <div class="card-header">
                        <h4 class="">Documentación</h4>
                    </div>
                    <div class="card-body">
                        <div class="row ">

                            <?php if (verificarTipoYPermiso($permisosAsignados,'documentacionML', "acta-constitutiva")): ?>
                                <div class="card card-info mr-sm-3 card-outline border-warnign mb-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                          <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center"> Acta Constitutiva</h5>
                                        <div class="card-tools">
                                            <button type="button" id="5" class=" btn-primary btn btn-sm  btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class=" fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->acta_constitutiva)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->acta_constitutiva as $key => $cv): ?>
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

                            <?php if (permisoAsignado($permisosAsignados, "constancia-situacion-fiscal")): ?>
                                
                                <div class="card card-info card-outline border-warnign mb-3 col-md-5  ml-lg-3 col-lg-3">
                                    <div class="card-header">
                                       <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center">Constancia de Situación Fiscal</h5>
                                        <div class="card-tools">
                                            <button type="button" id="6" class="btn-primary btn btn-sm  btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class="fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->constancia_situacion_fiscal)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach($datoFiscalArchivos->constancia_situacion_fiscal as $key=>$cv) : ?>
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

            <?php if (tipoAsignado($permisosAsignados, 'opinion-cumplimiento')):?>
                <div class="card card-primary  card-outline">
                    <div class="card-header">
                        <h4 class="">Opinion de cumplimiento:</h4>
                    </div>
                    <div class="card-body">
                        <div class="row ">
                            <?php if (verificarTipoYPermiso($permisosAsignados,'opinion-cumplimiento', "opinion-cumplimiento-sat")): ?>
                                <div class="card card-info card-outline border-warnign mb-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                         <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center"> Opinión de Cumplimiento SAT</h5>
                                        <div class="card-tools">
                                            <button type="button" id="7" class="btn-primary btn btn-sm   btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class="fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->cumplimientoSAT)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->cumplimientoSAT as $key => $cv): ?>
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

                            <?php if (verificarTipoYPermiso($permisosAsignados,'opinion-cumplimiento', "opinion-cumplimiento-imss")): ?>
                                <div class="card card-info card-outline border-warnign mb-3 ml-md-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                           <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center"> Opinión de Cumplimiento IMSS</h5>
                                        <div class="card-tools">
                                            <button type="button" id="8" class="btn-primary btn btn-sm  btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class="fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->cumplimientoIMSS)): ?>  
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->cumplimientoIMSS as $key => $cv): ?>
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

                            <?php if (verificarTipoYPermiso($permisosAsignados,'opinion-cumplimiento', "opinion-cumplimiento-infonavit")): ?>
                                <div class="card card-info card-outline border-warnign mb-3 ml-lg-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                       <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center"> Opinión de Cumplimiento Infonavit</h5>
                                        <div class="card-tools">
                                            <button type="button" id="9" class="btn-primary btn btn-sm  btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class=" fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->cumplimientoInfonavit)): ?>  
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->cumplimientoInfonavit as $key => $cv): ?>
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

            <?php if (tipoAsignado($permisosAsignados, 'permiso-licencia')):?>
                <div class="card card-primary  card-outline">
                    <div class="card-header">
                        <h4 class="">Permisos y Licencias:</h4>
                    </div>
                    <div class="card-body">
                        <?php if (permisoAsignado($permisosAsignados, "permisos-licencias")): ?>

                            <button type="button" class="btn btn-info btn-sm form-group float-right" data-toggle="modal" data-target="#modalAgregarPermiso">Agregar Permiso</button>

                            <table class="table table-bordered table-striped table-sm" id="tblPermisosLicencias" style="width:100%">
                                <thead>
                                    <tr>
                                        <th >Permiso</th>
                                        <th style="width:200px">Archivos</th>
                                        <th style="width:400px">Validacion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($permisos as $key=>$permiso) : ?>
                                    <tr>
                                        <td class="text-uppercasae"><?php echo $permiso['titulo']; ?></td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-xs btnSubirArchivoPermiso" data-permiso-id="<?php echo $permiso['id']; ?>">
                                                <i class="fas fa-folder-open"></i>
                                            </button>
                                            <button type="button" class="btn btn-success btn-xs verArchivos" data-toggle="modal" data-target="#modalVerArchivos" data-permiso-id="<?php echo $permiso['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-xs btnEliminarPermiso" data-permiso-id="<?php echo $permiso['id']; ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                        <td class="text-uppercase"><?php echo $permiso['estatus']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (tipoAsignado($permisosAsignados, 'repse')):?> 
                <div class="card card-primary  card-outline">
                    <div class="card-header">
                        <h4 class="">Repse:</h4>
                    </div>
                    <div class="card-body">
                        <div class="row ">
                            <?php if (verificarTipoYPermiso($permisosAsignados,'repse', "repse")): ?>
                                <div class="card card-info card-outline border-warnign mb-3 col-md-5 col-lg-3">
                                    <div class="card-header">
                                       <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center"> Repse</h5>
                                        <div class="card-tools">
                                            <button type="button" id="10" class="btn-primary btn btn-sm    btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class="fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->alta_repse)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->alta_repse as $key => $cv): ?>
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

                            <?php if (verificarTipoYPermiso($permisosAsignados,'repse', "ultima-informativa")): ?>
                                <div class="card card-info card-outline border-warnign mb-3  ml-md-3  col-md-5 col-lg-3">
                                    <div class="card-header">
                                       <h5 class="card-title h-100  mb-0 font-weight-bold d-flex align-items-center"> Ultima Informativa</h5>
                                        <div class="card-tools">
                                            <button type="button" id="11" class="btn-primary btn btn-sm   btnSubirArchivo flex align-items-center " title="Subir nuevo archivo">
                                                <i class=" fa fa-plus"></i> 
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($datoFiscalArchivos->ultima_informativa)): ?>
                                            <p class="text-danger">Sin archivos</p>
                                        <?php endif; ?>
                                        <?php foreach ($datoFiscalArchivos->ultima_informativa as $key => $cv): ?>
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

    <?php if (grupoAsignado($permisosAsignados, "Marco Legal")): ?>
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