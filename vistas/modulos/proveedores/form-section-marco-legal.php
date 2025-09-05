<?php

    $documentacion = [
        [
            "id" => 1, 
            "grupo" => "documentacionML", 
            "permiso" => "acta-constitutiva",
            "titulo" => "Acta Constitutiva",
            'data' => $proveedorArchivos->ActaConstitutiva,
            "tipo" => 5
        ],
        [
            "id" => 2, 
            "grupo" => "documentacionML", 
            "permiso" => "constancia-situacion-fiscal",
            "titulo" => "Situación Fiscal",
            'data' => $proveedorArchivos->ConstanciaSituacionFiscal,
            "tipo" => 6
        ],
    ];

    $opinion = [
        [
            "id" => 1, 
            "grupo" => "opinion-cumplimiento", 
            "permiso" => "opinion-cumplimiento-sat",
            "titulo" => "Opinión de Cumplimiento SAT",
            'data' => $proveedorArchivos->CumplimientoSAT,
            "tipo" => 7
        ],
        [
            "id" => 2, 
            "grupo" => "opinion-cumplimiento", 
            "permiso" => "opinion-cumplimiento-imss",
            "titulo" => "Opinión de Cumplimiento IMSS",
            'data' => $proveedorArchivos->CumplimientoIMSS,
            "tipo" => 8
        ],
        [
            "id" => 3, 
            "grupo" => "opinion-cumplimiento", 
            "permiso" => "opinion-cumplimiento-infonavit",
            "titulo" => "Opinión de Cumplimiento Infonavit",
            'data' => $proveedorArchivos->CumplimientoInfonavit,
            "tipo" => 9
        ],
    ];

    $repse = [
            [
            "id" => 1, 
            "grupo" => "repse", 
            "permiso" => "repse",
            "titulo" => "Repse",
            'data' => $proveedorArchivos->AltaRepse,
            "tipo" => 10
        ],
        [
            "id" => 2, 
            "grupo" => "repse", 
            "permiso" => "ultima-informativa",
            "titulo" => "Ultima Informativa",
            'data' => $proveedorArchivos->UltimaInformativa,
            "tipo" => 11
        ],
    ];

?>

<?php if ($proveedor->idCategoria): ?>

    <input type="file" class="d-none" id="archivoPermiso[]">
    <input type="file" id="archivo" name="archivo[]" multiple accept="application/pdf" style="display: none;">   

    <div class="accordion col-12 col-xl-10" id="accordionMarcoLegal">

        <!-- ===================== Documentación ===================== -->
        <?php if (tipoAsignado($permisosAsignados, 'documentacionML')):?>
            <div class="card shadow-sm mb-3">
                <div class="card-header p-2" id="headingDocs">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left text-primary font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseDocs">
                            <i class="fas fa-folder-open mr-2"></i> Documentación
                        </button>
                    </h2>
                </div>
                <div id="collapseDocs" class="collapse " data-parent="#accordionMarcoLegal">
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($documentacion as $doc): ?>
                                <?php if (verificarTipoYPermiso($permisosAsignados, $doc["grupo"], $doc["permiso"])): ?>
                                    <div class="col-lg-6 col-xl-4 mb-4">
                                        <div class="card shadow border-0 h-100">
                                            <div class="card-header bg-info text-white">
                                                <h3 class="card-title"> <i class="fas fa-file-alt mr-2"></i><?= $doc["titulo"] ?></h3>
                                                <div class="card-tools">
                                                    <button type="button" 
                                                        id="<?= $doc["tipo"] ?>" 
                                                        class="btn btn-sm btn-light btnSubirArchivo" 
                                                        title="Subir archivo" 
                                                        data-toggle="tooltip">
                                                        <i class="fas fa-upload text-info"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body no-scroll" style="max-height: 120px; overflow-y: auto;">
                                                <?php if (!empty($doc['data'])): ?>
                                                    <?php foreach($doc['data'] as $item): ?>
														<?php
															$bgColor = match($item['categoriaId']) {
																'ARCHIVO AUTORIZADO' => 'success',
																'ESTADO PENDIENTE' => 'info',
																default => 'danger',
															};
															$colorVer = ($item['categoriaId'] == 'ARCHIVO AUTORIZADO' || $item['categoriaId'] == 'ESTADO PENDIENTE') ? 'white' : 'white';
															$colorBorrar = ($item['categoriaId'] == 'ARCHIVO AUTORIZADO' || $item['categoriaId'] == 'ESTADO PENDIENTE') ? 'danger' : 'white';
														?>
														<div class="
														    bg-<?= $bgColor ?>
														    shadow rounded-lg pt-2 pb-1 px-3  my-1">
															<div class="d-flex justify-content-between align-items-star ">
																<div class="text-truncate" style="max-width: 70%;">
																	<i class="fas fa-file-pdf 
																	text-<?= $colorBorrar?>
																	mr-2"></i>
																	<strong><?= $item['titulo']; ?></strong>
																</div>
																							
																<div class="d-flex justify-content-around align-items-center">
																	<!-- BOTÓN VER -->
																	<i class=" fas fa-eye text-<?= $colorVer ?> verArchivo mr-2"
																	style="cursor: pointer; font-size: 1rem;"
																	title="Ver archivo"
																	data-toggle="modal" data-target="#archivoModal"
																	archivoRuta="<?= $item['ruta']; ?>">
																	</i>

																	<!-- BOTÓN BORRAR -->
																	<i class="fas fa-trash-alt text-<?= $colorBorrar ?> eliminarArchivo"
																	style="cursor: pointer; font-size: 1rem;"
																	title="Eliminar archivo"
																	archivoId="<?= $item['id']; ?>" 
																	folio="<?= $item['titulo']; ?>">
																	</i>

																	<?php if ($item['categoriaId'] == 'ESTADO PENDIENTE' ): ?>
																		<!-- BOTÓN APROBAR -->
																		<i class="fas fa-check text-gray-dark estadoArchivo mx-2"
																		style="cursor: pointer; font-size: 1rem;"
																		title="Aprobar archivo"
																		estadoArchivo="AUTORIZADO"
																		archivoId="<?= $item['id']; ?>" 
																		folio="<?= $item['titulo']; ?>">
																		</i>

																		<i class="fa fa-close  text-gray-dark  estadoArchivo"
																		style="cursor: pointer; font-size: 1.8rem;"
																		title="Aprobar archivo"
																		estadoArchivo="RECHAZADO"
																		archivoId="<?= $item['id']; ?>" 
																		folio="<?= $item['titulo']; ?>">
																		  <span aria-hidden="true">&times;</span>
																		</i>
																	<?php endif; ?>


																</div>
															</div>
															<div>
																<p class="mt-1 text-white small font-italic">
																	Observación: <?= trim($item['observacion']) !== '' ? htmlspecialchars($item['observacion']) : 'Sin observación' ?>
																</p>
															</div>
														</div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="card text-center ">
                                                        <div class="card-body">
                                                            <h5 class="card-title text-muted">Sin archivos disponibles</h5>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
                <!-- ===================== Opinión Cumplimiento ===================== -->
        <?php if (tipoAsignado($permisosAsignados, 'opinion-cumplimiento')):?>
            <div class="card shadow-sm mb-3">
                <div class="card-header p-2" id="headingDocs">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left text-primary font-weight-bold" type="button" data-toggle="collapse" data-target="#collapseOpinion">
                        <i class="fas fa-folder-open mr-2"></i> Opinión de Cumplimiento
                        </button>
                    </h2>
                </div>
                <div id="collapseOpinion" class="collapse " data-parent="#accordionMarcoLegal">
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($opinion as $doc): ?>
                                <?php if (verificarTipoYPermiso($permisosAsignados, $doc["grupo"], $doc["permiso"])): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card shadow border-0 h-100">
                                              <div class="card-header bg-info text-white">
                                                <h3 class="card-title"> <i class="fas fa-file-alt mr-2"></i><?= $doc["titulo"] ?></h3>
                                                <div class="card-tools">
                                                    <button type="button" 
                                                        id="<?= $doc["tipo"] ?>" 
                                                        class="btn btn-sm btn-light btnSubirArchivo" 
                                                        title="Subir archivo" 
                                                        data-toggle="tooltip">
                                                        <i class="fas fa-upload text-info"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body no-scroll" style="max-height: 120px; overflow-y: auto;">
                                                <?php if (!empty($doc['data'])): ?>
                                                    <?php foreach($doc['data'] as $item): ?>
                                                      														<?php
															$bgColor = match($item['categoriaId']) {
																'ARCHIVO AUTORIZADO' => 'success',
																'ESTADO PENDIENTE' => 'info',
																default => 'danger',
															};
															$colorVer = ($item['categoriaId'] == 'ARCHIVO AUTORIZADO' || $item['categoriaId'] == 'ESTADO PENDIENTE') ? 'white' : 'white';
															$colorBorrar = ($item['categoriaId'] == 'ARCHIVO AUTORIZADO' || $item['categoriaId'] == 'ESTADO PENDIENTE') ? 'danger' : 'white';
														?>
														<div class="
														    bg-<?= $bgColor ?>
														    shadow rounded-lg pt-2 pb-1 px-3  my-1">
															<div class="d-flex justify-content-between align-items-star ">
																<div class="text-truncate" style="max-width: 70%;">
																	<i class="fas fa-file-pdf 
																	text-<?= $colorBorrar?>
																	mr-2"></i>
																	<strong><?= $item['titulo']; ?></strong>
																</div>
																							
																<div class="d-flex justify-content-around align-items-center">
																	<!-- BOTÓN VER -->
																	<i class=" fas fa-eye text-<?= $colorVer ?> verArchivo mr-2"
																	style="cursor: pointer; font-size: 1rem;"
																	title="Ver archivo"
																	data-toggle="modal" data-target="#archivoModal"
																	archivoRuta="<?= $item['ruta']; ?>">
																	</i>

																	<!-- BOTÓN BORRAR -->
																	<i class="fas fa-trash-alt text-<?= $colorBorrar ?> eliminarArchivo"
																	style="cursor: pointer; font-size: 1rem;"
																	title="Eliminar archivo"
																	archivoId="<?= $item['id']; ?>" 
																	folio="<?= $item['titulo']; ?>">
																	</i>

																	<?php if ($item['categoriaId'] == 'ESTADO PENDIENTE' ): ?>
																		<!-- BOTÓN APROBAR -->
																		<i class="fas fa-check text-gray-dark estadoArchivo mx-2"
																		style="cursor: pointer; font-size: 1rem;"
																		title="Aprobar archivo"
																		estadoArchivo="AUTORIZADO"
																		archivoId="<?= $item['id']; ?>" 
																		folio="<?= $item['titulo']; ?>">
																		</i>

																		<i class="fa fa-close  text-gray-dark  estadoArchivo"
																		style="cursor: pointer; font-size: 1.8rem;"
																		title="Aprobar archivo"
																		estadoArchivo="RECHAZADO"
																		archivoId="<?= $item['id']; ?>" 
																		folio="<?= $item['titulo']; ?>">
																		  <span aria-hidden="true">&times;</span>
																		</i>
																	<?php endif; ?>

																</div>
															</div>
															<div>
																<p class="mt-1 text-white small font-italic">
																	Observación: <?= trim($item['observacion']) !== '' ? htmlspecialchars($item['observacion']) : 'Sin observación' ?>
																</p>
															</div>
														</div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="card text-center ">
                                                        <div class="card-body">
                                                            <h5 class="card-title text-muted">Sin archivos disponibles</h5>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- ===================== Repse ===================== -->
        <?php if (tipoAsignado($permisosAsignados, 'repse')):?>
            <div class="card shadow-sm mb-3">
                <div class="card-header p-2">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left text-primary font-weight-bold" 
                                type="button" data-toggle="collapse" data-target="#collapseRepse">
                            <i class="fas fa-folder-open mr-2"></i> Repse
                        </button>
                    </h2>
                </div>
                <div id="collapseRepse" class="collapse" data-parent="#accordionMarcoLegal">
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($repse as $doc): ?>
                                <?php if (verificarTipoYPermiso($permisosAsignados, $doc["grupo"], $doc["permiso"])): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card shadow border-0 h-100">
                                              <div class="card-header bg-info text-white">
                                                <h3 class="card-title"> <i class="fas fa-file-alt mr-2"></i><?= $doc["titulo"] ?></h3>
                                                <div class="card-tools">
                                                    <button type="button" 
                                                        id="<?= $doc["tipo"] ?>" 
                                                        class="btn btn-sm btn-light btnSubirArchivo" 
                                                        title="Subir archivo" 
                                                        data-toggle="tooltip">
                                                        <i class="fas fa-upload text-info"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="card-body no-scroll" style="max-height: 120px; overflow-y: auto;">
                                                <?php if (!empty($doc['data'])): ?>
                                                    <?php foreach($doc['data'] as $item): ?>
                                                        														<?php
															$bgColor = match($item['categoriaId']) {
																'ARCHIVO AUTORIZADO' => 'success',
																'ESTADO PENDIENTE' => 'info',
																default => 'danger',
															};
															$colorVer = ($item['categoriaId'] == 'ARCHIVO AUTORIZADO' || $item['categoriaId'] == 'ESTADO PENDIENTE') ? 'white' : 'white';
															$colorBorrar = ($item['categoriaId'] == 'ARCHIVO AUTORIZADO' || $item['categoriaId'] == 'ESTADO PENDIENTE') ? 'danger' : 'white';
														?>
														<div class="
														    bg-<?= $bgColor ?>
														    shadow rounded-lg pt-2 pb-1 px-3  my-1">
															<div class="d-flex justify-content-between align-items-star ">
																<div class="text-truncate" style="max-width: 70%;">
																	<i class="fas fa-file-pdf 
																	text-<?= $colorBorrar?>
																	mr-2"></i>
																	<strong><?= $item['titulo']; ?></strong>
																</div>
																							
																<div class="d-flex justify-content-around align-items-center">
																	<!-- BOTÓN VER -->
																	<i class=" fas fa-eye text-<?= $colorVer ?> verArchivo mr-2"
																	style="cursor: pointer; font-size: 1rem;"
																	title="Ver archivo"
																	data-toggle="modal" data-target="#archivoModal"
																	archivoRuta="<?= $item['ruta']; ?>">
																	</i>

																	<!-- BOTÓN BORRAR -->
																	<i class="fas fa-trash-alt text-<?= $colorBorrar ?> eliminarArchivo"
																	style="cursor: pointer; font-size: 1rem;"
																	title="Eliminar archivo"
																	archivoId="<?= $item['id']; ?>" 
																	folio="<?= $item['titulo']; ?>">
																	</i>

																	<?php if ($item['categoriaId'] == 'ESTADO PENDIENTE' ): ?>
																		<!-- BOTÓN APROBAR -->
																		<i class="fas fa-check text-gray-dark estadoArchivo mx-2"
																		style="cursor: pointer; font-size: 1rem;"
																		title="Aprobar archivo"
																		estadoArchivo="AUTORIZADO"
																		archivoId="<?= $item['id']; ?>" 
																		folio="<?= $item['titulo']; ?>">
																		</i>

																		<i class="fa fa-close  text-gray-dark  estadoArchivo"
																		style="cursor: pointer; font-size: 1.8rem;"
																		title="Aprobar archivo"
																		estadoArchivo="RECHAZADO"
																		archivoId="<?= $item['id']; ?>" 
																		folio="<?= $item['titulo']; ?>">
																		  <span aria-hidden="true">&times;</span>
																		</i>
																	<?php endif; ?>


																</div>
															</div>
															<div>
																<p class="mt-1 text-white small font-italic">
																	Observación: <?= trim($item['observacion']) !== '' ? htmlspecialchars($item['observacion']) : 'Sin observación' ?>
																</p>
															</div>
														</div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="card text-center ">
                                                        <div class="card-body">
                                                            <h5 class="card-title text-muted">Sin archivos disponibles</h5>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- ===================== Permiso Y Licencia ===================== -->
        <?php if (tipoAsignado($permisosAsignados, 'permiso-licencia')): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header p-2" id="headingPermisos">
                    <h2 class="mb-0">
                        <button class="btn btn-link btn-block text-left text-primary font-weight-bold" 
                                type="button" data-toggle="collapse" data-target="#collapsePermiso">
                            <i class="fas fa-folder-open mr-2"></i> Permisos y Licencias
                        </button>
                    </h2>
                </div>

                <div id="collapsePermiso" class="collapse" data-parent="#accordionMarcoLegal">
                    <div class="card-body">
                        <?php if (permisoAsignado($permisosAsignados, "permisos-licencias")): ?>
                            
                            <!-- Botón agregar permiso -->
                            <!-- <div class="text-right mb-3">
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalAgregarPermiso">
                                    <i class="fas fa-plus-circle"></i> Agregar Permiso
                                </button>
                            </div> -->

                            <!-- Tabla de permisos -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm mb-0" id="tblPermisosLicencias" style="width:100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Permiso</th>
                                            <th style="width:200px">Archivos</th>
                                            <th style="width:400px">Validación</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($permisos as $key => $permiso): ?>
                                            <tr>
                                                <td class="text-uppercase font-weight-bold"><?php echo $permiso['titulo']; ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-info btn-xs btnSubirArchivoPermiso" 
                                                            data-permiso-id="<?php echo $permiso['id']; ?>" title="Subir Archivos">
                                                        <i class="fas fa-folder-open"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-success btn-xs verArchivos" 
                                                            data-toggle="modal" data-target="#modalVerArchivos" 
                                                            data-permiso-id="<?php echo $permiso['id']; ?>" title="Ver Archivos">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-xs btnEliminarPermiso" 
                                                            data-permiso-id="<?php echo $permiso['id']; ?>" title="Eliminar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                                <td class="text-uppercase text-center">
                                                    <?php echo $permiso['estatus']; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                        <?php else: ?>
                            <p class="text-danger mb-0">No hay permisos asignados.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
  <div class="alert alert-warning" role="alert">
    <h4 class="alert-heading">No tienes una categoría asignada.</h4>
    <p>Habla con un administrador.</p>
  </div>
<?php endif; ?>

