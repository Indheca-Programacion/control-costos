<?php

$documentacion = [
    [
        "id" => 1, 
        "grupo" => "documentacionMF", 
        "permiso" => "estado-de-cuenta",
        "titulo" => "Estado de cuenta",
        "data" => $proveedorArchivos->EstadoCuenta,
        "tipo" => 12

    ],
    [
        "id" => 2, 
        "grupo" => "documentacionMF", 
        "permiso" => "estados-financieros",
        "titulo" => "Estados Financieros",
        "data" => $proveedorArchivos->EstadoFinanciero,
        "tipo" => 13

    ],
    [
        "id" => 3, 
        "grupo" => "documentacionMF", 
        "permiso" => "ultima-aclacaracion-anual",
        "titulo" => "Ultima Declaracion Anual",
        "data" => $proveedorArchivos->UltimaDeclaracionAnual,
        "tipo" => 14
    ],
];

?>
 
<?php if ($proveedor->idCategoria): ?>
    
    <input type="file" id="archivo" name="archivo[]" multiple accept="application/pdf" style="display: none;">   

    <?php if (!grupoAsignado($permisosAsignados, "Marco Financiero")): ?>
        <h3>
            No disponible
        </h3>
    <?php endif; ?>

    <div class="accordion" id="accordionMarcoLegal">
        <!-- ===================== Documentación ===================== -->
        <?php if (tipoAsignado($permisosAsignados, 'documentacionMF')):?>
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
    </div>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">No tienes una categoría asignada.</h4>
        <p>Habla con un administrador.</p>
    </div>
<?php endif; ?>