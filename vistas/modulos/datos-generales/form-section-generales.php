<?php
    if (isset($proveedor->razonSocial)) {
        $razonSocial = $old["razonSocial"] ?? $proveedor->razonSocial;
        $nombreComercial = $old["nombreComercial"] ?? $proveedor->nombreComercial;

        $nombre = $old["nombre"] ?? $proveedor->nombre;
        $apellidoPaterno = $old["apellidoPaterno"] ?? $proveedor->apellidoPaterno;
        $apellidoMaterno = $old["apellidoMaterno"] ?? $proveedor->apellidoMaterno;
        $telefono = $old["telefono"] ?? $proveedor->telefono;
        $correo = $old["correo"] ?? $proveedor->correo;
        $condicionContado = $old["condicionContado"] ?? $proveedor->condicionContado;
        $condicionCredito = $old["condicionCredito"] ?? $proveedor->condicionCredito;
		$domicilio = isset($old["domicilio"]) ? $old["domicilio"] : $proveedor->domicilio;
		$categoria = isset($old["idCategoria"]) ? $old["idCategoria"] : $proveedor->idCategoria;
		$zona = isset($old["zona"]) ? $old["zona"] : $proveedor->zona;

        $ubicacion = $old["ubicacion"] ?? $proveedor->ubicacion;
        $tags = $proveedor->tags;
        $informacionTecnicaTags = $tagProveedores;
    }

	$zonas = [
		[
			"id" => 1,
			"nombre" => "Veracruz"
		],
		[
			"id" => 2,
			"nombre" => "Cotazacoalcos"
		],
		[
			"id" => 3,
			"nombre" => "Ciudad del Carmen"
		],
		[
			"id" => 4,
			"nombre" => "Villahermosa"
		],
		[
			"id" => 5,
			"nombre" => "Tampico"
		],
		[
			"id" => 6,
			"nombre" => "Monterrey"
		],
		[
			"id" => 7,
			"nombre" => "Ciudad de México"
		]
	];

    $documentosIniciales = [
        [
            'titulo' => 'Constancia Fiscal',
            'data' => $proveedorArchivos->ConstanciaFiscal,
            'tipo' => 18
        ],
        [
            'titulo' => 'Opinión de cumplimiento',
            'data' => $proveedorArchivos->OpinionCumplimiento,
            'tipo' => 19
        ],
        [
            'titulo' => 'Comprobante de Domicilio',
            'data' => $proveedorArchivos->ComprobanteDomicilio,
            'tipo' => 20
        ],
	    [
            'titulo' => 'Datos Bancarios',
            'data' => $proveedorArchivos->DatosBancarios,
            'tipo' => 21
		],
    ];
?>

<div class="row">

    <!-- INPUT MULTIPLES ARCHIVOS -->
    <input type="file" id="archivo" name="archivo[]" multiple accept="application/pdf" style="display: none;">   

    <!-- Información General -->
    <div class="col-md-6 mb-3">
        <div class="card shadow-lg border-left-info">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 text-info">
                    <i class="fas fa-building mr-1"></i> Información General
                </h5>
            </div>
            <div class="card-body">

                <!-- RAZON SOCIAL -->
                <div class="form-group">
                    <label for="razonSocial">Razón Social:</label>
                    <input type="text" class="form-control form-control-sm"
                           <?= $razonSocial ? 'disabled' : '' ?> value="<?= $razonSocial ?>" 
                           id="razonSocial" name="razonSocial" placeholder="Nombre de la Razon Social" required>
                </div>

                <!-- NOMBRE COMERCIAL -->
                <div class="form-group">
                    <label for="nombreComercial">Nombre Comercial::</label>
                    <input type="text" class="form-control form-control-sm"
                           <?= $nombreComercial ? 'disabled' : '' ?> value="<?= $nombreComercial ?>" 
                           id="nombreComercial" name="nombreComercial" placeholder="Nombre comercial" required>
                </div>

                <!-- NOMBRE CONTACTO -->
                <div class="form-group">
                    <label for="nombre">Nombre de Contacto:</label>
                    <input type="text" class="form-control form-control-sm" id="nombre" name="nombre"
                           value="<?= $nombre ?>" placeholder="Nombre del contacto" required>
                </div>

                <div class="form-row">
                    <!-- APELLIDO PATERNO -->
                    <div class="form-group col-md-6">
                        <label for="apellidoPaterno">Apellido Paterno:</label>
                        <input type="text" class="form-control form-control-sm" id="apellidoPaterno"
                               name="apellidoPaterno" value="<?= $apellidoPaterno ?>" placeholder="Apellido paterno" required>
                    </div>
                    <!-- APELLIDO MATERNO -->
                    <div class="form-group col-md-6">
                        <label for="apellidoMaterno">Apellido Materno:</label>
                        <input type="text" class="form-control form-control-sm" id="apellidoMaterno"
                               name="apellidoMaterno" value="<?= $apellidoMaterno ?>" placeholder="Apellido materno" required>
                    </div>
                </div>

                <div class="form-row">
                    <!-- TELEFONO -->
                    <div class="form-group col-md-6">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" class="form-control form-control-sm" id="telefono" name="telefono"
                               value="<?= $telefono ?>" placeholder="Teléfono de contacto" required>
                    </div>
                    <!-- CORREO -->
                    <div class="form-group col-md-6">
                        <label for="correo">Correo:</label>
                        <input type="email" class="form-control form-control-sm" id="correo" name="correo"
                               value="<?= $correo ?>" placeholder="Correo de contacto" required>
                    </div>

                    <div class="form-group col-md-12">
                        <!-- Productos o servicios -->
                        <label for="tags">Productos o Servicios Ofertados:</label>
                        <select name="tags[]" id="tags" class="custom-select select2" multiple>
                            <?php if (!isset($proveedor->id)) : ?>
                                <option value="">Selecciona un Tag</option>
                            <?php endif; ?>
                            <?php foreach($informacionTecnicaTags as $informacionTecnicaTag) { ?>
                                <option value="<?= $informacionTecnicaTag["id"]; ?>"
                                    <?= in_array($informacionTecnicaTag["id"], $tags) ? 'selected' : ''; ?>>
                                    <?= mb_strtoupper(fString($informacionTecnicaTag["descripcion"])); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
    
                </div>

                <div class="form-row">
                    <!-- ZONA -->
                	<div class="form-group col-6">
                        <label for="zona">Zona:*</label>
                        <select name="zona" class="form-control form-control-sm select2">
                            <option value="">Selecciona una zona</option>
                            <?php
                                foreach ($zonas as $detalle) {
                                    echo "<option value='{$detalle["id"]}'" . ($detalle["id"] == $zona ? " selected" : "") . ">{$detalle["nombre"]}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <!-- CATEGORIA -->
                    <div class="form-group col-6">
                        <label for="categoria">Categoria</label>
                        <select name="idCategoria" class="form-control form-control-sm select2" 
                            <?php echo isset($proveedor->id) ? 'disabled' : ''; ?>>
                            <option value="">Sin categoria asignada</option>
                            <?php
                                foreach ($categorias as $detalle) {
                                    echo "<option value='{$detalle["id"]}'" . ($detalle["id"] == $categoria ? " selected" : "") . ">{$detalle["nombre"]}</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <!-- DOMICILIO -->
                    <div class="form-group col-12">
                        <label for="domicilio">Domicilio:*</label>
                        <input type="text" name="domicilio" value="<?php echo fString($domicilio); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el domicilio">
                    </div>

                    <hr class="col-12">

                    <div class="col-12 mb-3">
                        <h5 class="card-title mb-0 text-info">
                            <i class="fas fa-folder-open mr-1"></i> Condiciones
                        </h5>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="condiciones">Condiciones Pago de Contado</label>
                            <textarea name="condicionContado" id="condicionContado" rows = "5" class="form-control form-control-sm" 
                            placeholder="Condiciones de pago de contado" required > <?= isset($proveedor->condicionContado) ? $condicionContado : '' ?></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="condiciones">Condiciones Credito</label>
                            <textarea name="condicionCredito" id="condicionCredito" rows = "5" class="form-control form-control-sm" 
                            placeholder="Condiciones de pago a credito" required><?= isset($proveedor->condicionCredito) ? $condicionCredito : '' ?></textarea>
                        </div>
                        <div class="form-group col-md-6 ">
                            <label for="ubicacion">Ubicación Operativa/Mostrador de la empresa</label>
                            <textarea name="ubicacion" id="ubicacion" rows = "5" class="form-control form-control-sm" 
                            placeholder="Ubicacion de la empresa" required><?= isset($proveedor->ubicacion) ? $ubicacion : '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer ">
                <div id="msgSend"></div>
                <button type="submit" id="btnSend" class="btn btn-success btn-md text-right">
                    <i class="fas fa-save mr-1"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>

    <!-- ARCHIVOS DEL PROVEEDOR -->
    <div class="col-12 col-lg-6">
                
        <!-- ARCHIVOS -->
        <div class="card shadow-sm border-left-secondary">

            <div class="card-header bg-light">
                <h5 class="card-title mb-0 text-secondary">
                    <i class="fas fa-folder-open mr-1"></i> Archivos Adjuntos
                </h5>
            </div>
            <div class="card-body ">

                <!-- CV empresarial -->
                <div class=" col-md-6 col-lg-5">
                        <div class="form-group col-md-12">
                        <label>CV Empresarial:</label>
                            <button type="button" class="btn btn-outline-info btn-block btn-sm btnSubirArchivo" id="1">
                                <i class="fas fa-upload"></i> Cargar CV Empresarial
                            </button>
                            <?php foreach($proveedorArchivos->CV as $item) : ?>
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
                <hr>
                <!-- Contratos / Facturas / OC -->
                <div class="col-12 row mt-3">
                    <?php for ($i=1; $i<=3; $i++) : ?>
                        <div class=" col-md-4">
                            <label>OC <?= $i ?></label>
                            <button type="button" class="btn btn-outline-info btn-block btn-sm btnSubirArchivo" id="<?= $i+1 ?>">
                                <i class="fas fa-upload"></i> Subir archivo
                            </button>
                            <?php foreach($proveedorArchivos->{'OC'.$i} as $item) : ?>
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
                                            archivoId="<?= $item['id']; ?>" folio="<?= $item['titulo']; ?>"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endfor; ?>
                </div>

            </div>

        </div>

        <!-- ARCHIVOS INICIALES-->
        <div class="card shadow-sm border-left-secondary">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0 text-secondary">
                    <i class="fas fa-folder-open mr-1"></i> Archivos Iniciales
                </h5>
            </div>
			<div class="card-body">
				<div class="container"> <!-- o .container-fluid -->
					<div class="row">
						<?php foreach($documentosIniciales as $key => $doc): ?>
							<div class="col-12 col-xl-6">
								<div class="card mb-4 shadow-sm border 	">
									<div class="card-header bg-light ">
                                        <span class="card-title text-dark font-weight-bold">
                                            <i class="fas fa-folder-open mr-1 text-secondary"></i>
                                            <?php echo $doc['titulo']; ?>
                                        </span>
                                        <div class="card-tools">
                                            <button type="button" 
                                                id="<?php echo $doc['tipo'];?>" 
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
                                                <div class="
                                                bg-<?= 
                                                    $item['categoriaId'] == 'ARCHIVO AUTORIZADO' ? 'success' : 
                                                    ($item['categoriaId'] == 'ESTADO PENDIENTE' ? 'info' : 'danger') 
                                                ?>
                                                shadow rounded-lg pt-2 pb-1 px-3  my-1">
                                                    <div class="d-flex justify-content-between align-items-star ">
                                                        <div class="text-truncate" style="max-width: 70%;">
                                                            <i class="fas fa-file-pdf 
                                                            text-<?= 
                                                                $item['categoriaId'] == 'ARCHIVO AUTORIZADO' ? 'danger' : 
                                                                ($item['categoriaId'] == 'ESTADO PENDIENTE' ? 'danger' : 'white') 
                                                            ?>
                                                            mr-2"></i>
                                                            <strong><?= $item['titulo']; ?></strong>
                                                        </div>
                                                                                    
                                                        <div class="ml-auto d-flex align-items-star ">
                                                            <i class="fas fa-eye 
                                                                text-white
                                                                mr-3 verArchivo" style="cursor: pointer;"
                                                                title="Ver archivo"
                                                                data-toggle="modal" data-target="#archivoModal"
                                                                archivoRuta="<?= $item['ruta']; ?>"></i>

                                                            <i class="fas fa-trash-alt 
                                                                text-<?= 
                                                                    $item['categoriaId'] == 'ARCHIVO AUTORIZADO' ? 'danger' : 
                                                                    ($item['categoriaId'] == 'ESTADO PENDIENTE' ? 'danger' : 'white') 
                                                                ?> eliminarArchivo"
                                                                style="cursor: pointer;"
                                                                title="Eliminar archivo"
                                                                archivoId="<?= $item['id']; ?>" folio="<?= $item['titulo']; ?>"></i>
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
						<?php endforeach; ?>
					</div>
				</div>
			</div>
        </div>

    </div>
</div>


