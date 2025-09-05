<?php
if (isset($solicitudProveedor->id)) {
    $rfc = isset($old["rfc"]) ? $old["rfc"] : $solicitudProveedor->rfc;
    $razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : $solicitudProveedor->razonSocial;
    $correoElectronico = isset($old["correoElectronico"]) ? $old["correoElectronico"] : $solicitudProveedor->correoElectronico;
    $nombreApellido = isset($old["nombreApellido"]) ? $old["nombreApellido"] : $solicitudProveedor->nombreApellido;
    $telefono = isset($old["telefono"]) ? $old["telefono"] : $solicitudProveedor->telefono;
    $origenProveedor = isset($old["origenProveedor"]) ? $old["origenProveedor"] : $solicitudProveedor->origenProveedor;
    $tipoProveedor = isset($old["tipoProveedor"]) ? $old["tipoProveedor"] : $solicitudProveedor->tipoProveedor;
    $claveProveedor = isset($old["claveProveedor"]) ? $old["claveProveedor"] : $solicitudProveedor->claveProveedor;
    $entregaMaterial = isset($old["entregaMaterial"]) ? $old["entregaMaterial"] : $solicitudProveedor->entregaMaterial;
    $diasCredito = isset($old["diasCredito"]) ? $old["diasCredito"] : $solicitudProveedor->diasCredito;
    $estatusSolicitudProveedor = isset($old["estatusSolicitudProveedor"]) ? $old["estatusSolicitudProveedor"] : $solicitudProveedor->estatusSolicitudProveedor;
} else {
    $rfc = isset($old["rfc"]) ? $old["rfc"] : "";
    $razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : "";
    $correoElectronico = isset($old["correoElectronico"]) ? $old["correoElectronico"] : "";
    $nombreApellido = isset($old["nombreApellido"]) ? $old["nombreApellido"] : "";
    $telefono = isset($old["telefono"]) ? $old["telefono"] : "";
    $origenProveedor = isset($old["origenProveedor"]) ? $old["origenProveedor"] : "";
    $tipoProveedor = isset($old["tipoProveedor"]) ? $old["tipoProveedor"] : "";
    $claveProveedor = isset($old["claveProveedor"]) ? $old["claveProveedor"] : "";
    $entregaMaterial = isset($old["entregaMaterial"]) ? $old["entregaMaterial"] : "";
    $diasCredito = isset($old["diasCredito"]) ? $old["diasCredito"] : "";
    $estatusSolicitudProveedor = isset($old["estatusSolicitudProveedor"]) ? $old["estatusSolicitudProveedor"] : "";

}

$documentos = [
	    [
        'titulo' => 'Datos Bancarios',
        'data' => $solicitudProveedor->datos_bancarios
		],
		[
			'titulo' => 'Comprobante de Domicilio',
			'data' => $solicitudProveedor->comprobante_domicilio
		],
		[
			'titulo' => 'Constancia Fiscal',
			'data' => $solicitudProveedor->constancia_fiscal
		],
		[
			'titulo' => 'Opinión de cumplimiento',
			'data' => $solicitudProveedor->opinion_cumplimiento
		]
];

?>

<div class="row">

	<div class="col-lg-6 ">

		<div class="card card-info card-outline shadow-sm">
			<div class="card-header bg-info text-white">
				<h3 class="card-title mb-0">
					<i class="fas fa-user-check mr-2"></i>Datos Solicitud del Proveedor
				</h3>
			</div>

			<div class="card-body">
				<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">
				<input 
					type="hidden" 
					id="idSolicitudProveedor" 
					name="idSolicitudProveedor" 
					value="<?php echo $solicitudProveedor->id?>">

				<div class="row">

					<!-- Primer grupo -->
					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="rfc">RFC:</label>
							<input type="text" id="rfc" name="rfc" value="<?php echo fString($rfc ?? ''); ?>" disabled class="form-control form-control-sm text-uppercase" placeholder="Ingresa el RFC">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="razonSocial">Razón Social:</label>
							<input type="text" id="razonSocial" name="razonSocial" value="<?php echo fString($razonSocial ?? ''); ?>" disabled class="form-control form-control-sm text-uppercase" placeholder="Ingresa la razón social">
						</div>
					</div>

					<!-- Segundo grupo -->
					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="correoElectronico">Correo Electrónico:</label>
							<input type="email" id="correoElectronico" name="correoElectronico" value="<?php echo fString($correoElectronico ?? ''); ?>" disabled class="form-control form-control-sm" placeholder="Ingresa el correo electrónico">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="nombreApellido">Nombre y Apellido:</label>
							<input type="text" id="nombreApellido" name="nombreApellido" value="<?php echo fString($nombreApellido ?? ''); ?>" disabled class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre y apellido">
						</div>
					</div>

					<!-- Tercer grupo -->
					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="telefono">Teléfono:</label>
							<input type="text" id="telefono" name="telefono" value="<?php echo fString($telefono ?? ''); ?>" disabled class="form-control form-control-sm" placeholder="Ingresa el teléfono">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="origenProveedor">Origen del Proveedor:</label>
							<input type="text" id="origenProveedor" name="origenProveedor" value="<?php echo fString($origenProveedor ?? ''); ?>" disabled class="form-control form-control-sm text-uppercase" placeholder="Ingresa el origen del proveedor">
						</div>
					</div>

					<!-- Cuarto grupo -->
					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="tipoProveedor">Tipo de Proveedor:</label>
							<input type="text" id="tipoProveedor" name="tipoProveedor" value="<?php echo fString($tipoProveedor ?? ''); ?>" disabled class="form-control form-control-sm text-uppercase" placeholder="Ingresa el tipo de proveedor">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="claveProveedor">Clave del Proveedor:</label>
							<input type="text" id="claveProveedor" name="claveProveedor" value="<?php echo fString($claveProveedor ?? ''); ?>" disabled class="form-control form-control-sm text-uppercase" placeholder="Ingresa la clave del proveedor">
						</div>
					</div>

					<!-- Quinto grupo -->
					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="entregaMaterial">Entrega de Material:</label>
							<input type="text" id="entregaMaterial" name="entregaMaterial" value="<?php echo fString($entregaMaterial ?? ''); ?>"  disabled class="form-control form-control-sm text-uppercase" placeholder="Detalle sobre entrega de material">
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group border-bottom pb-2">
							<label for="diasCredito">Días de Crédito:</label>
							<input type="number" id="diasCredito" name="diasCredito" value="<?php echo fString($diasCredito ?? ''); ?>" disabled class="form-control form-control-sm" placeholder="Número de días de crédito">
						</div>
					</div>

					<!-- Estatus final -->
					<div class="col-md-12">
						<div class="form-group">
							<label for="estatusSolicitudProveedor">Estatus Solicitud:</label>
							<input type="text" id="estatusSolicitudProveedor" name="estatusSolicitudProveedor" value="<?php echo fString($estatusSolicitudProveedor ?? ''); ?>" disabled class="form-control form-control-sm">
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>


	<div class="col-lg-6" >

		<div class="card card-info card-outline shadow">
			<div class="card-header bg-info text-white">
				<h3 class="card-title">
					<i class="fas fa-file-alt mr-2"></i>Archivos del Proveedor
				</h3>
			</div>

			<div class="card-body">
				<div class="container"> <!-- o .container-fluid -->
					<div class="row">
						<?php foreach($documentos as $doc): ?>
							<?php foreach($doc['data'] as $item): ?>
								<div class="col-6">
									<div class="card mb-4 shadow-sm border 	">
										<div class="card-header bg-light d-flex justify-content-between align-items-center">
											<span class="text-dark font-weight-bold">
												<i class="fas fa-folder-open mr-1 text-secondary"></i>
												<?php echo $doc['titulo']; ?>
											</span>
											<small class="text-muted">
												ID: <?php echo $item['id']; ?>
											</small>
										</div>

										<div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
											<div class="text-truncate" style="max-width: 70%;">
												<p class="mb-1 text-muted small">Nombre del archivo:</p>
                                        		<i class="fas fa-file-pdf text-danger mr-2"></i>
                                        		<strong class="text-info"><?= $item['titulo']; ?></strong>
                                    		</div>

											<div class="btn-group btn-group-sm">
												<div class="btn btn-outline-info verArchivo"
														archivoRuta="<?php echo $item['ruta']; ?>"
														title="Ver archivo">
													<i class="fas fa-eye"></i>
												</div>

												<?php if(!$item['categoriaId']): ?>
													<div class="btn btn-outline-success estadoArchivo"
															estadoArchivo="AUTORIZADO"
															archivoId="<?php echo $item['id']; ?>"
															title="Autorizar">
														<i class="fa fa-check"></i>
													</div>

													<div class="btn btn-outline-danger estadoArchivo"
															estadoArchivo="RECHAZADO"
															archivoId="<?php echo $item['id']; ?>"
															title="Rechazar">
														<i class="fa fa-times"></i>
													</div>
												<?php else: ?>
													<span class="badge badge-<?php echo ($item['categoriaId'] == 'ARCHIVO AUTORIZADO') ? 'success' : 'danger'; ?> ml-2 align-self-center">
														<?php echo ucfirst(strtolower($item['categoriaId'] ?$item['categoriaId'] : "No Modificado" )); ?>
													</span>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

	</div>
</div> <!-- <div class="row"> -->
