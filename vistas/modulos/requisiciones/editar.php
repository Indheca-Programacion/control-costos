<?php
	$old = old();

	$cantidadComprobantes = count($requisicion->comprobantesPago);
	$cantidadOrdenes = count($requisicion->ordenesCompra);
	$cantidadFacturas = count($requisicion->facturas);
	$cantidadCotizaciones = count($requisicion->cotizaciones);
	$cantidadVales = count($requisicion->valesAlmacen);
	$cantidadResguardos = count($requisicion->resguardos);
	$cantidadDocs = $cantidadComprobantes+$cantidadOrdenes+$cantidadFacturas+$cantidadCotizaciones+$cantidadVales;


	use App\Route;
?>

<div class="content-wrapper relative">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Requisiciones <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('requisiciones.index')?>"> <i class="fas fa-tools"></i> Requisiciones</a></li>
	            <li class="breadcrumb-item active">Editar requisición</li>
	          </ol>
	        </div>
	      </div>
	    </div><!-- /.container-fluid -->

	</section>

	<section class="content requisiciones">

		<?php if ( !is_null(flash()) ) : ?>
		<div class="d-none" id="msgToast" clase="<?=flash()->clase?>" titulo="<?=flash()->titulo?>" subtitulo="<?=flash()->subTitulo?>" mensaje="<?=flash()->mensaje?>"></div>
		<?php endif; ?>

		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="card card-primary card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-edit"></i>
								Editar requisición
							</h3>
						</div> <!-- <div class="card-header"> -->
						<div class="card-body">
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<form id="formSend" method="POST" action="<?php echo Route::names('requisiciones.update', $requisicion->id); ?>" enctype="multipart/form-data">
								<input type="hidden" name="_method" value="PUT">
								<?php include "vistas/modulos/requisiciones/formulario.php"; ?>
							</form>
							<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->

		<!-- Modal Seleccionar Proveedor -->
		<div id="modalSeleccionarProveedor" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalSeleccionarProveedorLabel">
			<div class="modal-dialog modal-lg  modal-dialog-centered">
				<div class="modal-content" >
					<div class="modal-header">
						<h5 class="modal-title" id="modalSeleccionarProveedorLabel">Seleccionar proveedor</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
						<a target="_blank" href="<?=Route::names('proveedores.create')?>" class="btn btn-primary ml-3">
							Agregar Proveedor
						</a>
					</div>
					<div class="modal-body" id="modalDetallesContent">
						<table class="table table-sm table-bordered" id="tablaProveedores" >

							<thead>
								<tr>
									<th style="width:10px">#</th>
									<th>Proveedor</th>
									<th>Telefono</th>
									<th>Direccion</th>
									<th>Email</th>
									<th>Calificacion</th>
								</tr>
							</thead>

						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal Comprobar Existencias -->
		<div id="modalComprobarExistencias" class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalComprobarExistenciasLabel">
			<div class="modal-dialog modal-xl  modal-dialog-centered">
				<div class="modal-content" >
					<div class="modal-header">
						<h5 class="modal-title" id="modalComprobarExistenciasLabel">Comprobar existencias</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body" id="modalDetallesContent">
						<table class="table table-sm table-bordered table-striped" id="tablaExistencias" >

							<thead>
								<tr>
									<th style="width:10px" class="text-center"></th>
									<th>Producto</th>
									<th>Cantidad</th>
									<th>Existencias</th>
									<th>Unidad</th>
									<th>Almacen</th>
								</tr>
							</thead>

							<tbody class="text-uppercase">
							</tbody>

						</table>
					</div> <!-- /.modal-body -->
					<div class="modal-footer">
						<button type="button" id="btnCrearEntrada" class="btn btn-info">
							Generar Salida
						</button>
				</div> 
			</div> 
		</div> 
	</section>

	<!-- CHAT REQUISICIONES -->
	<div class="position-fixed w-100 d-flex justify-content-end" style="bottom: 0; right:0;">
		<div class="card card-primary direct-chat direct-chat-primary mx-2 collapsed-card">
			<div class="card-header">
				<h3 class="card-title">Chat Directo</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-tool" data-card-widget="collapse">
						<i class="fas fa-plus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<div id="error-message-container"></div>
				<div 
				class="direct-chat-messages"
				style="height: 300px;width: 370px;"
				id="direct-chat-messages"
				>
				</div>
			</div>
			<div class="card-footer">
				<div class="input-group">
					<input type="hidden" name="idRequisicion" id="idRequisicion" value=<?php echo $requisicion->id; ?> >
					<input type="text" name="mensaje" id="mensaje" placeholder="Escribe un mensaje ..." class="form-control">
					<span class="input-group-append">
						<button type="button" class="btn btn-primary" id="btnCrearMensaje">Enviar</button>
					</span>
				</div>
				<div id="mensaje-peticion"></div>
			</div>
		</div>
	</div>

</div>

	<!-- Modal -->
	<div class="modal fade" id="crearCotizacionModal" role="dialog" aria-labelledby="crearCotizacionModalLabel" >
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="crearCotizacionModalLabel">Crear Cotización</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<!-- Formulario o contenido del modal -->
					<form action="<?=Route::routes('requisiciones.crear-cotizacion', $requisicion->id)?>" method="POST">
					<input type="hidden" name="_token" value="<?=token()?>">
					<div class="form-group">
						<label for="proveedorId">Nombre del Proveedor</label>
						<select class="form-control select2" id="proveedorId" name="proveedorId[]" multiple="multiple" required>
							<?php foreach ($proveedores as $proveedor): ?>
								<option value="<?= $proveedor["id"] ?>"><?= $proveedor["proveedor"] ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="form-group">
						<label for="fechaLimite">Fecha Límite</label>
						<div class="input-group date2" id="fechaLimite" data-target-input="nearest">
							<input type="text" name="fechaLimite" id="fechaLimite" value="" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaLimite">
							<div class="input-group-append" data-target="#fechaLimite" data-toggle="datetimepicker">
								<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
							</div>
						</div>
					</div>
					<button type="submit" class="btn btn-primary">Guardar</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal solicitud de cambio -->
	<div class="modal fade" id="modalSolicitudCambio" tabindex="-1" role="dialog" aria-labelledby="modalSolicitudCambioLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalSolicitudCambioLabel">Nota Informativa</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="formSolicitudCambio" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="lugar">Lugar:</label>
								<input type="text" class="form-control form-control-sm" id="lugar" name="lugar">
							</div>
							<div class="col-md-6 fomr-group">
								<label for="fecha">Fecha:</label>
								<div class="input-group date" id="fecha" data-target-input="nearest">
									<input type="text" name="fecha" id="fecha" value="" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fecha">
									<div class="input-group-append" data-target="#fecha" data-toggle="datetimepicker">
										<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
									</div>
								</div>
							</div>
							<div class="col-12 fomr-group">
								<label for="descripcion">Descripcion:</label>
								<textarea class="form-control form-control-sm" id="descripcion" placeholder="Descripcion del cambio" name="descripcion" rows="3"></textarea>
							</div>
							<div class="col-12 form-group">
								<label for="fotos">Evidencia fotografica:</label>
								<input type="file" class="form-control-file" id="fotos" name="fotos[]" multiple accept="image/*">
								<small class="form-text text-muted">Puedes seleccionar varias imágenes.</small>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="btnEnviarNotaInformativa">Solicitar Cambio</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal solicitud de cambio de obra-->
	<div class="modal fade" id="modalSolicitudCambioObra" tabindex="-1" role="dialog" aria-labelledby="modalSolicitudCambioObraLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalSolicitudCambioObraLabel">Cambio de Obra</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="formSolicitudCambio" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-12 form-group">
								<label for="obraId">Obras:</label>
								<select name="obra" id="obraId" class="custom-select form-controls select2">
									<option value="">Selecciona una Obra</option>
									<?php foreach($listadoObras as $obra) { ?>
									<option value="<?php echo $obra["id"]; ?>"
										<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>
										><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
									</option>
									<?php } ?>
								</select>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="btnCambiarObra">Cambiar Obra</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Modificar Partidas -->
	<div class="modal fade" id="modalModificarPartidas" role="dialog" aria-labelledby="modalModificarPartidasLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalModificarPartidasLabel">Modificar Partidas de la Requisición</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="formModificarPartidas" method="POST" action="<?= Route::names('partidas.update', $requisicion->id) ?>">
					<input type="hidden" name="_token" value="<?= token() ?>">
					<input type="hidden" name="_method" value="PUT">
					<input type="hidden" name="requisicionId" value="<?= $requisicion->id ?>">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="descripcion">Descripcion</label>
								<select id="modal_descripcion" class="form-control form-control-sm select2" name="obraDetalleId">
									<?php foreach ($descripciones as $partida): ?>
										<option value="<?= $partida['id'] ?>"><?= $partida['descripcion'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="modal_unidad">Unidad</label>
								<select class="form-control form-control-sm select2" id="modal_unidad" name="unidadId">
									<option value="">Selecciona una unidad</option>
									<?php foreach ($unidades as $unidad): ?>
										<option value="<?= $unidad['id'] ?>"><?= $unidad['descripcion'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="modal_cantidad">Cantidad</label>
								<input type="number" class="form-control form-control-sm" id="modal_cantidad" name="cantidad" min="1" step="0.0001">
							</div>
							<div class="col-md-6 form-group">
								<label for="modal_costo_unitario">Costo Unitario</label>
								<input type="number" class="form-control form-control-sm" id="modal_costo_unitario" name="costo_unitario" step="0.0001">
							</div>
							<div class="col-md-6 form-group">
								<label for="modal_costo">Costo </label>
								<input type="number" class="form-control form-control-sm" id="modal_costo" name="costo" step="0.0001">
							</div>
							<div class="col-12 form-group">
								<label for="modal_concepto">Concepto</label>
								<input type="text" class="form-control form-control-sm text-uppercase" id="modal_concepto" name="concepto">
							</div>

						</div>
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="submit" class="btn btn-primary">Guardar Cambios</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Modal Agregar Partidas -->
	<div class="modal fade" id="modalAgregarPartidas" role="dialog" aria-labelledby="modalAgregarPartidasLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalAgregarPartidasLabel">Agregar Partidas a la Requisición</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form id="formAgregarPartidas" method="POST" action="<?= Route::names('partidas.store', $requisicion->id) ?>">
					<input type="hidden" name="_token" value="<?= token() ?>">
					<input type="hidden" name="requisicionId" value="<?= $requisicion->id ?>">
					<input type="hidden" name="periodo" value="<?= $requisicion->periodo ?>">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6 form-group">
								<label for="modalAgregarPartida_descripcion">Descripcion</label>
								<select id="modalAgregarPartida_descripcion" class="form-control form-control-sm select2" name="obraDetalleId">
									<option value="">Selecciona una descripción</option>
									<?php foreach ($descripciones as $partida): ?>
										<option data-unidad="<?= $partida['unidad'] ?>" value="<?= $partida['id'] ?>"><?= $partida['descripcion'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="modalAgregarPartida_unidad">Unidad</label>
								<select class="form-control form-control-sm select2" id="modalAgregarPartida_unidad" name="unidadId">
									<option value="">Selecciona una unidad</option>
									<?php foreach ($unidades as $unidad): ?>
										<option value="<?= $unidad['id'] ?>"><?= $unidad['descripcion'] ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-6 form-group">
								<label for="modalAgregarPartida_cantidad">Cantidad</label>
								<input type="number" class="form-control form-control-sm" id="modalAgregarPartida_cantidad" name="cantidad" min="1" step="0.0001">
							</div>
							<div class="col-md-6 form-group">
								<label for="modalAgregarPartida_costo_unitario">Costo Unitario</label>
								<input type="number" class="form-control form-control-sm" id="modalAgregarPartida_costo_unitario" name="costo_unitario" step="0.0001">
							</div>
							<div class="col-md-6 form-group">
								<label for="modalAgregarPartida_costo">Costo </label>
								<input type="number" class="form-control form-control-sm" id="modalAgregarPartida_costo" name="costo" step="0.0001">
							</div>
							<div class="col-12 form-group">
								<label for="modalAgregarPartida_concepto">Concepto</label>
								<input type="text" class="form-control form-control-sm text-uppercase" id="modalAgregarPartida_concepto" name="concepto">
							</div>

						</div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="submit" class="btn btn-primary">Guardar Cambios</button>
					</div>
				</form>
			</div>
		</div>
	</div>

<!-- Modal -->
<div class="modal fade" id="modalVerImagenes" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerImagenesLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalVerImagenesLabel">Fotos de la Partida <span></span></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row row-cols-1 row-cols-lg-2 imagenes">
				</div>
				<div class="alert alert-danger error-validacion d-none">
					<ul class="mb-0">
						<li></li>
					</ul>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="modalAddCostosTotales" class="modal fade" data-keyboard="false" tabindex="-1" aria-labelledby="modalAddCostosTotales">
	<div class="modal-dialog  modal-dialog-centered" role="document">
		<div class="modal-content" >
			<div class="modal-header">
				<h5 class="modal-title" id="ModalDetallesLabel">Actualizar Costos Finales</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="modalDetallesContent">
				<?php foreach ($requisicion->detalles as $key => $value) {					
					echo '<input type="hidden" id="partidaId'.$key.'" value="'.$value["id"].'">';
					echo '<label for="descripcion">' . $value["descripcion"] . '</label>';;
					echo '<input type="text" class="col-6 form-control form-control-sm campoConDecimal" id="costo'.$key.'" value="'.$value["costo_total"].'">';
				} ?>
			</div>	
			<div class="modal-footer">
				<button type="button" id="btnUpdate" class="btn btn-info">
					Actualizar Datos
				</button>
			</div>
		</div>
	</div>
</div>

<!-- MODAL DOCUMENTOS -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfModalLabel">Visualizador de PDF</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
				<iframe id="pdfViewer" src="" width="100%" height="600px"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar PDF -->
<div class="modal fade" id="modalVerPDF" tabindex="-1" role="dialog" aria-labelledby="modalVerPDFLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title" id="modalVerPDFLabel">Visualizar PDF</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body" style="height:80vh;">
		<iframe id="iframePDF" src="" frameborder="0" style="width:100%;height:100%;"></iframe>
	  </div>
	</div>
  </div>
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/requisiciones.js?v=2.8');
	array_push($arrayArchivosJS, 'vistas/js/mensaje-requisicion.js?v=2.4');
?>

