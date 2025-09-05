<?php
	if ( isset($proveedor->razonSocial) ) {

		$tiempoEntrega = isset($old["tiempoEntrega"]) ? $old["tiempoEntrega"] : $proveedor->tiempoEntrega;
		$modalidadEntrega = isset($old["modalidadEntrega"]) ? $old["modalidadEntrega"] : $proveedor->modalidadEntrega;
		$distribuidorAutorizado = isset($old["distribuidorAutorizado"]) ? $old["distribuidorAutorizado"] : $proveedor->distribuidorAutorizado;
		$recursos = isset($old["recursos"]) ? $old["recursos"] : $proveedor->recursos;

	} 
?>

<!-- INPUT MULTIPLES ARCHIVOS -->
<input type="file" id="archivo" name="archivo[]" multiple accept="application/pdf" style="display: none;">   

<?php if ($proveedor->idCategoria): ?>
    <div class="row">
        <!-- Primera columna: Información de entrega -->
        <div class="col-md-6">
            <div class="card shadow-lg border-left-info">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 text-info">
                       <i class="fas fa-shipping-fast mr-2"></i>Información de Entrega
                    </h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="tiempoEntrega">Tiempo de entrega:</label>
                        <textarea name="tiempoEntrega" id="tiempoEntrega" rows="4" class="form-control form-control-sm" required><?= isset($tiempoEntrega) ? $tiempoEntrega : '' ?></textarea>
                        <small class="form-text text-muted">
                            Para productos en stock, el tiempo de entrega es de 3 a 5 días hábiles. Para productos personalizados, de 15 a 20 días hábiles, dependiendo de la complejidad y disponibilidad.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="modalidadEntrega">Modalidad en entrega de material:</label>
                        <textarea name="modalidadEntrega" id="modalidadEntrega" rows="4" class="form-control form-control-sm" required><?= isset($modalidadEntrega) ? $modalidadEntrega : '' ?></textarea>
                        <small class="form-text text-muted">
                            Entregas disponibles en sucursales de Cárdenas, Veracruz y Coatzacoalcos. También ofrecemos entrega a domicilio en zonas urbanas dentro de un radio de 30km.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="distribuidorAutorizado">¿Es distribuidor autorizado de alguna marca?</label>
                        <textarea name="distribuidorAutorizado" id="distribuidorAutorizado" rows="4" class="form-control form-control-sm" required><?= isset($distribuidorAutorizado) ? $distribuidorAutorizado : '' ?></textarea>
                        <small class="form-text text-muted">
                            Indique si es distribuidor autorizado. Ejemplo: "Distribuidor autorizado de marcas como [nombre de marcas]".
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="recursos">Recursos con los que cuenta:</label>
                        <textarea name="recursos" id="recursos" rows="5" class="form-control form-control-sm" required><?= isset($recursos) ? $recursos : '' ?></textarea>
                        <small class="form-text text-muted">
                            Ejemplo: Maquinaria especializada, líneas de producción, transporte propio, tecnología de embalaje, etc.
                        </small>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" id="btnSend" class="btn btn-success btn-md ">
                        <i class="fas fa-save mr-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

        <!-- Segunda columna: Documentos -->
        <div class="col-md-4">
            <div class="card shadow-sm border-left-secondary">

                <div class="card-header bg-light">
                    <h5 class="card-title mb-0 text-secondary">
                        <i class="fas fa-folder-open mr-1"></i> Archivos Adjuntos
                    </h5>
                </div>
                <div class="card-body">

                    <!-- Listado -->
                    <div class="mb-3">
                        <label class="font-weight-bold">Listado de recursos</label>
                        <div class="">
                            <button type="button" class="btn btn-outline-info btn-block btn-sm btnSubirArchivo" id="16">
                                <i class="fas fa-upload"></i>  Cargar Listado
                            </button>

                            <?php foreach ($proveedorArchivos->Listado as $key => $item) : ?>
                                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center shadow rounded-lg my-2">
                                    <div class="text-truncate" style="max-width: 70%;">
                                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                                        <strong><?= $item['titulo']; ?></strong>
                                    </div>
                                                                
                                    <div class="ml-auto d-flex align-items-center">
                                        <i class="fas fa-eye text-info mr-3 verArchivo" style="cursor: pointer;"
                                            title="Ver archivo"
                                            data-toggle="modal" data-target="#archivoModal"
                                            archivoRuta="<?= $item['ruta']; ?>"></i>

                                        <i class="fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;"
                                            title="Eliminar archivo"
                                            archivoId="<?= $item['id']; ?>" folio="<?= $item['archivo']; ?>"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Certificaciones -->
                    <?php if (permisoAsignado($permisosAsignados, "certificaciones")) : ?>
                        <hr>
                        <div class="mb-3">
                            <label class="font-weight-bold">Certificaciones</label>
                            <button type="button" class="btn btn-outline-info btn-block btn-sm btnSubirArchivo" id="17">
                                <i class="fas fa-upload"></i> Cargar Certificaciones
                            </button>

                            <?php foreach ($proveedorArchivos->Certificaciones as $key => $item) : ?>
                                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center shadow rounded-lg my-2">
                                    <div class="text-truncate" style="max-width: 70%;">
                                        <i class="fas fa-file-pdf text-danger mr-2"></i>
                                        <strong><?= $item['titulo']; ?></strong>
                                    </div>
                                                                    
                                    <div class="ml-auto d-flex align-items-center">
                                        <i class="fas fa-eye text-info mr-3 verArchivo" style="cursor: pointer;"
                                            title="Ver archivo"
                                            data-toggle="modal" data-target="#archivoModal"
                                            archivoRuta="<?= $item['ruta']; ?>"></i>
                                        <i class="fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;"
                                            title="Eliminar archivo"
                                            archivoId="<?= $item['id']; ?>" folio="<?= $item['archivo']; ?>"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Soporte -->
                    <hr>

                    <div>
                        <label class="font-weight-bold">Soporte</label>
                        <button type="button" class="btn btn-outline-info btn-block btn-sm btnSubirArchivo" id="15">
                            <i class="fas fa-upload"></i> Cargar Soporte
                        </button>
                        <?php foreach ($proveedorArchivos->Soporte as $key => $item) : ?>
                            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center shadow rounded-lg my-2">
                                <div class="text-truncate" style="max-width: 70%;">
                                    <i class="fas fa-file-pdf text-danger mr-2"></i>
                                    <strong><?= $item['titulo']; ?></strong>
                                </div>
                                <div class="ml-auto d-flex align-items-center">
                                    <i class="fas fa-eye text-info mr-3 verArchivo" style="cursor: pointer;"
                                        title="Ver archivo"
                                        data-toggle="modal" data-target="#archivoModal"
                                        archivoRuta="<?= $item['ruta']; ?>"></i>
                                    <i class="fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;"
                                    title="Eliminar archivo"
                                    archivoId="<?= $item['id']; ?>" folio="<?= $item['archivo']; ?>"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">No tienes una categoría asignada.</h4>
        <p>Habla con un administrador.</p>
    </div>
<?php endif; ?>