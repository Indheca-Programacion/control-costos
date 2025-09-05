<?php

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
		],
		[
			"id" => 8,
			"nombre" => "Tierra Blanca"
		],
		[
			"id" => 9,
			"nombre" => "Guadalajara"
		],
		[
			"id" => 10,
			"nombre" => "Puebla"
		],
		[
			"id" => 11,
			"nombre" => "Querétaro"
		],
		[
			"id" => 12,
			"nombre" => "Zacatecas"
		],

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


	if ( isset($proveedor->id) ) {

		$activo = isset($old["rfc"]) ? ( isset($old["activo"]) && $old["activo"] == "on" ? true : false ) : $proveedor->activo;
		$personaFisica = $proveedor->personaFisica;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $proveedor->nombre;
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : $proveedor->apellidoPaterno;
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : $proveedor->apellidoMaterno;
		$razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : $proveedor->razonSocial;
		$nombreComercial = isset($old["nombreComercial"]) ? $old["nombreComercial"] : $proveedor->nombreComercial;
		$rfc = isset($old["rfc"]) ? $old["rfc"] : $proveedor->rfc;
		$correo = isset($old["correo"]) ? $old["correo"] : $proveedor->correo;
		$credito = isset($old["rfc"]) ? ( isset($old["credito"]) && $old["credito"] == "on" ? true : false ) : $proveedor->credito;
		$limiteCredito = isset($old["limiteCredito"]) ? $old["limiteCredito"] : number_format($proveedor->limiteCredito, 2, '.', ',');
        $telefono = isset($old["telefono"]) ? $old["telefono"] : $proveedor->telefono;
		$zona = isset($old["zona"]) ? $old["zona"] : $proveedor->zona;
		$domicilio = isset($old["domicilio"]) ? $old["domicilio"] : $proveedor->domicilio;
		$estrellas = isset($old["estrellas"]) ? $old["estrellas"] : $proveedor->estrellas;
		$categoria = isset($old["idCategoria"]) ? $old["idCategoria"] : $proveedor->idCategoria;

        $condicionContado = isset($old["condicionContado"]) ? $old["condicionContado"] : $proveedor->condicionContado;
		$condicionCredito = isset($old["condicionCredito"]) ? $old["condicionCredito"] : $proveedor->condicionCredito;
		$ubicacion = isset($old["ubicacion"]) ? $old["ubicacion"] : $proveedor->ubicacion;

	} else {

		$activo = isset($old["activo"]) && $old["activo"] == "on" ? true : false;
		$personaFisica = isset($old["personaFisica"]) && $old["personaFisica"] == "on" ? true : false;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : "";
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : "";
		$razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : "";
		$nombreComercial = isset($old["nombreComercial"]) ? $old["nombreComercial"] : "";
		$rfc = isset($old["rfc"]) ? $old["rfc"] : "";
		$correo = isset($old["correo"]) ? $old["correo"] : "";
		$credito = isset($old["credito"]) && $old["credito"] == "on" ? true : false;
		$limiteCredito = isset($old["limiteCredito"]) ? $old["limiteCredito"] : "0.00";
        $telefono = isset($old["telefono"]) ? $old["telefono"] : "";
		$zona = isset($old["zona"]) ? $old["zona"] : "";
		$domicilio = isset($old["domicilio"]) ? $old["domicilio"] : "";
		$estrellas = isset($old["estrellas"]) ? $old["estrellas"] : 0;
		$categoria = isset($old["idCategoria"]) ? $old["idCategoria"] : "";
	}
?>

<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" name="provedorId" id="proveedorId" value="<?php echo $proveedor->id; ?>">

<div class="row">

	<!-- DATOS GENERALES -->
	<div class="col-md-12  col-lg-6 ">
		<div class="card card-info card-outline">
			<div class="card-header bg-light">
                <h5 class="card-title mb-0 text-info">
                    <i class="fas fa-building mr-1"></i> Información General
                </h5>
            </div>
			<div class="card-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
					</ul>
				</div>
				
				<!-- ACTIVO -->
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="activo">Activo:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Proveedor Activo" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="activo" id="activo" <?php echo $activo ? "checked" : ""; ?>>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<!-- DATOS GENERALES -->
				<div class="row">
					<!-- PERSONA FISICA -->
					<div class="col-6 form-group">
						<label for="personaFisica">Persona Física:</label>
						<div class="input-group">
							<input type="text" class="form-control form-control-sm" value="Es una Persona Física" readonly>
							<div class="input-group-append">
								<div class="input-group-text">
									<input type="checkbox" name="personaFisica" id="personaFisica" <?php echo $personaFisica ? "checked" : ""; ?> <?php echo isset($proveedor->id) ? ' disabled' : ''; ?>>
								</div>
							</div>
						</div>
					</div>
					<!-- NOMBRE -->
					<div class="col-md-6">
				
						<div class="form-group">
							<label for="nombre">Nombre(s):</label>
							<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase personaFisica" placeholder="Ingresa el nombre(s)">
						</div>
				
					</div>
					<!-- APELLIDO PARTERNO -->
					<div class="col-md-6 form-group">
						<label for="apellidoPaterno">Apellido Paterno:</label>
						<input type="text" name="apellidoPaterno" value="<?php echo fString($apellidoPaterno); ?>" class="form-control form-control-sm text-uppercase personaFisica" placeholder="Ingresa el apellido paterno">
					</div>
					<!-- APRELLIDO MATERNO -->
					<div class="col-md-6 form-group">
						<label for="apellidoMaterno">Apellido Materno:</label>
						<input type="text" name="apellidoMaterno" value="<?php echo fString($apellidoMaterno); ?>" class="form-control form-control-sm text-uppercase personaFisica" placeholder="Ingresa el apellido materno">
					</div>
					<!-- RAZON SOCIAL -->
					<div class="col-6 form-group">
						<label for="razonSocial">Razón Social:</label>
						<input type="text" name="razonSocial" value="<?php echo fString($razonSocial); ?>" class="form-control form-control-sm text-uppercase personaMoral" placeholder="Ingresa la razón social">
					</div>
					<!-- NOMBRE COMERCIAL -->
					<div class="col-6 form-group">
						<label for="nombreComercial">Nombre Comercial:</label>
						<input type="text" name="nombreComercial" value="<?php echo fString($nombreComercial); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre comercial">
					</div>
					<!-- RFC -->
					<div class="col-md-6 form-group">
						<label for="rfc">RFC:</label>
						<input type="text" name="rfc" value="<?php echo fString($rfc); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingrese el RFC del Proveedor">
					</div>
					<!-- TELEFONO -->
					<div class="col-md-6 form-group">
						<label for="telefono">Telefono:</label>
						<input type="number" name="telefono" value="<?php echo fString($telefono); ?>" max="10" class="form-control form-control-sm  " placeholder="Ingresa el teléfono">
					</div>
					<!-- CORREO ELECTRONICO -->
					<div class="col-6 form-group">
						<label for="correo">Correo Electrónico:</label>
						<input type="email" name="correo" value="<?php echo fString($correo); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el correo electrónico">
					</div>
					<!-- ZONA -->
					<div class="col-6 form-group">
						<label for="zona">Zona:</label>
						<select name="zona" class="form-control form-control-sm select2">
							<option value="">Selecciona una zona</option>
							<?php
								foreach ($zonas as $detalle) {
									echo "<option value='{$detalle["id"]}'" . ($detalle["id"] == $zona ? " selected" : "") . ">{$detalle["nombre"]}</option>";
								}
							?>
						</select>
					</div>
					<!-- DOMICILIO -->
					<div class="col-6 form-group">
						<label for="domicili">Domicilio:</label>
						<input type="text" name="domicilio" value="<?php echo fString($domicilio); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el domicilio">
					</div>
					<!-- CATEGORIA -->
					<div class="col-6 form-group">
						<label for="categoria">Categoria</label>
						<select name="idCategoria" class="form-control form-control-sm select2">
							<option name="" value="">Selecciona una categoria</option>
							<?php
								foreach ($categorias as $detalle) {
									echo "<option value='{$detalle["id"]}'" . ($detalle["id"] == $categoria ? " selected" : "") . ">{$detalle["nombre"]}</option>";
								}
							?>
						</select>
					</div>
					<!-- CALIFICACION -->
					<div class="col-12">
						<label for="estrellas">Calificación:</label>
						<p class="clasificacion">
							<input id="radio1" type="radio" name="estrellas" value="5" <?php echo $estrellas == 5 ? "checked" : ""; ?>>
							<label class="ratio" for="radio1">★</label>
							<input id="radio2" type="radio" name="estrellas" value="4" <?php echo $estrellas == 4 ? "checked" : ""; ?>>
							<label class="ratio" for="radio2">★</label>
							<input id="radio3" type="radio" name="estrellas" value="3" <?php echo $estrellas == 3 ? "checked" : ""; ?>>
							<label class="ratio" for="radio3">★</label>
							<input id="radio4" type="radio" name="estrellas" value="2" <?php echo $estrellas == 2 ? "checked" : ""; ?>>
							<label class="ratio" for="radio4">★</label>
							<input id="radio5" type="radio" name="estrellas" value="1" <?php echo $estrellas == 1 ? "checked" : ""; ?>>
							<label class="ratio" for="radio5">★</label>
						</p>
					</div>
					<div class="row d-none">
						<!-- CREDITO -->
						<div class="col-md-6 form-group">
							<label for="credito">Crédito:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Proveedor con Crédito" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="credito" id="credito" <?php echo $credito ? "checked" : ""; ?>>
									</div>
								</div>
							</div>
						</div>
						<!-- LIMITE DE CREDITO -->
						<div class="col-md-6 form-group">
							<label for="limiteCredito">Límite de Crédito:</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text" id="limiteCredito-addon"><i class="fas fa-dollar-sign"></i></span>
								</div>
								<input type="text" name="limiteCredito" id="limiteCredito" value="<?php echo $limiteCredito; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el límite de crédito" aria-label="Límite de Crédito" aria-describedby="limiteCredito-addon">
							</div>
						</div>
					</div>
				</div>
				
				<hr>

				<!-- CONDICIONES -->
				<div class="row">
					<div class="col-12 mb-3">
						<h5 class="card-title mb-0 text-info">
							<i class="fas fa-folder-open mr-1"></i> Condiciones
						</h5>
					</div>

					<div class="col-6 form-group">
                        <label for="condiciones">Condiciones Pago de Contado</label>
                        <textarea name="condicionContado" id="condicionContado" rows = "5" class="form-control form-control-sm" 
                        placeholder="Condiciones de pago de contado" disabled required > <?= isset($proveedor->condicionContado) ? $condicionContado : '' ?></textarea>
                    </div>
                    <div class="col-6 form-group">
                        <label for="condiciones">Condiciones Credito</label>
                        <textarea name="condicionCredito" id="condicionCredito" rows = "5" class="form-control form-control-sm" 
                        placeholder="Condiciones de pago a credito" disabled required><?= isset($proveedor->condicionCredito) ? $condicionCredito : '' ?></textarea>
                    </div>
                    <div class="col-6 form-group">
                        <label for="ubicacion">Ubicacion Operativa/Mostrador de la empresa</label>
                        <textarea name="ubicacion" id="ubicacion" rows = "5" class="form-control form-control-sm" 
                        placeholder="Ubicacion de la empresa" disabled required><?= isset($proveedor->ubicacion) ? $ubicacion : '' ?></textarea>
                    </div>
				</div>
				<hr>

				<button type="submit" id="btnSend" class="btn btn-outline-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
                <div id="msgSend"></div>
			</div>
		</div>
	</div>

	<!-- ARCHIVOS -->
    <div class="row col-lg-6">
		<?php if(isset($proveedor->id)) : ?>
			<!-- ARCHIVOS DEL PROVEEDOR -->
			<div class="col-12">
				
				<!-- DATOS BANCARIOS -->
				<div class="card card-info card-outline">
					<div class="card-header bg-light ">
						<div class="card-tools">
							<button type="button" class="btn btn-outline-primary float-right mb-2" data-toggle="modal" data-target="#modalAgregarDatosBancarios"> Agregar Datos Bancarios </button>			
						</div>
					</div>
					<div class="card-body">
						<div class="table-responsive">
							<table class="table table-sm table-bordered table-striped mb-0" id="tablaDatosBancarios" width="100%">
								<thead>
									<tr>
									</tr>
								</thead>
								<tbody class="text-uppercase">
									

								</tbody>
							</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaSalidas" width="100%"> -->
						</div> <!-- <div class="table-responsive"> -->
					</div> <!-- <div class="card-body"> -->
				</div> <!-- <div class="card card-info card-outline"> -->

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
									<?php if($proveedorArchivos->CV) : ?>
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
									<?php else: ?>
										<div class="card-body py-2 px-3 d-flex justify-content-between align-items-center shadow rounded-lg my-2">
											<div class="text-truncate text-info" style="max-width: 70%;">
												<strong>Sin archivos</strong>
											</div>
										</div>
									<?php endif; ?>
							</div>
						</div>
						<hr>
						<!-- Contratos / Facturas / OC -->
						<div class="col-12 row mt-3">
							<?php for ($i=1; $i<=3; $i++) : ?>
								<div class=" col-md-4">
									<label>OC <?= $i ?></label>
									<?php if($proveedorArchivos->{'OC'.$i}) : ?>
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
									<?php else: ?>
										<div class="card-body py-2 px-3 d-flex justify-content-between align-items-center shadow rounded-lg my-2">
											<div class="text-truncate text-info" style="max-width: 70%;">
												<strong>Sin archivos</strong>
											</div>
										</div>
									<?php endif; ?>
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
														<?php
															$bgColor = match($item['categoriaId']) {
																'ARCHIVO AUTORIZADO' => 'success',
																'ESTADO PENDIENTE' => 'info',
																default => 'danger',
															};
															$colorVer = ($item['categoriaId'] == 'ARCHIVO AUTORIZADO' || $item['categoriaId'] == 'ESTADO PENDIENTE') ? 'white' : 'info';
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
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>

			</div>
        <?php endif ?>
    </div>
</div>
				