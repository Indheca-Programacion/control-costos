<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Detalles Solicitud Proveedor <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('solicitud-proveedor.index')?>"> <i class="fas fa-dolly"></i> Solicitud Proveedor</a></li>
	            <li class="breadcrumb-item active">Detalles Solicitud Proveedor</li>
	          </ol>
	        </div>
	      </div>
	    </div><!-- /.container-fluid -->

	</section>

	<section class="content">

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
							Detalles Solicitud Proveedor
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('solicitud-proveedor.update', $solicitudProveedor->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/solicitud-proveedor/formulario.php"; ?>


							<?php if($solicitudProveedor->estatusSolicitudProveedor === "REVISION POR PARTE DE COMPRAS"):?>
								<div>
									<button type="button" id="btnRechazarProveedorModal" class="btn btn-outline-danger">
										<i class="fas fa-save"></i> Rechazar
									</button>

									<button type="button" id="btnAutorizarProveedorModal" class="btn btn-outline-success">
										<i class="fas fa-save"></i> Autorizar
									</button>

								</div>
							<?php endif?>


							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

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

<!-- MODAL AUTORIZAR -->
<div class="modal fade" id="autorizarProveedorModal" tabindex="-1" role="dialog" aria-labelledby="autorizarProveedorLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">

			<!-- Header -->
			<div class="modal-header bg-success text-white">
				<h5 class="modal-title" id="autorizarProveedorLabel">Autorizar Proveedor</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<!-- Body -->
			<div class="modal-body">

				<div class="mb-3">
					<h5 class="font-weight-bold text-secondary">
						¿Desea autorizar el <b class="text-dark">proveedor</b>?
					</h5>
					<p class="text-muted mb-0">
						<i>Puedes agregar una observación.</i>
					</p>
				</div>

				<hr>

				<!-- OBSERVACIONES -->
				<div class="form-group">
					<label for="observacionSolicitudProveedor" class="font-weight-bold">Observación</label>
					<textarea 
						name="observacionSolicitudProveedor" 
						id="observacionSolicitudProveedor" 
						class="form-control form-control-sm text-uppercase" 
						placeholder="Escribe una observación (opcional)"
						rows="5"
					></textarea>
				</div>

			</div>

			<!-- Footer -->
			<div class="modal-footer d-flex justify-content-between align-items-center">

				<div id="mensajeCargando" class="text-success font-weight-bold d-none">
					<i class="fas fa-spinner fa-spin"></i> Enviando solicitud...
				</div>

				<div id="botonesModal">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-outline-success btnAutorizarSolicitudProveedor">
						<i class="fas fa-save"></i> Aceptar
					</button>
				</div>

			</div>

		</div>
	</div>
</div>


<!-- MODAL RECHAZAR -->
<div class="modal fade" id="rechazarProveedorModal" tabindex="-1" role="dialog" aria-labelledby="rechazarProveedorLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">

			<!-- Header -->
			<div class="modal-header bg-danger text-white">
				<h5 class="modal-title" id="rechazarProveedorLabel">Rechazar Proveedor</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<!-- Body -->
			<div class="modal-body">

				<div class="mb-3">
					<h5 class="font-weight-bold text-secondary">
						¿Desea rechazar el <b class="text-dark">proveedor</b>?
					</h5>
					<p class="text-muted mb-0">
						<i>Agrega el motivo del rechazo.</i>
					</p>
				</div>

				<hr>

				<!-- OBSERVACIONES -->
				<div class="form-group">
					<label for="observacionRechazo" class="font-weight-bold">Observación</label>
					<textarea 
						name="observacionRechazo" 
						id="observacionRechazo" 
						class="form-control form-control-sm text-uppercase" 
						placeholder="Motivo del rechazo." 
						rows="5"
					></textarea>
				</div>

			</div>

			<!-- Footer -->
			<div class="modal-footer d-flex justify-content-between align-items-center">

				<div id="mensajeCargandoRechazar" class="text-success font-weight-bold d-none">
					<i class="fas fa-spinner fa-spin"></i> Enviando solicitud...
				</div>

				<div id="botonesModalRechazar">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						Cerrar
					</button>
					<button type="button" class="btn btn-outline-danger btnRechazarSolicitudProveedor">
						<i class="fas fa-save"></i> Aceptar
					</button>
				</div>

			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="estadoArchivoModal" tabindex="-1" role="dialog" aria-labelledby="estadoArchivoLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">

			<div class="modal-header bg-info text-white">
				<h5 class="modal-title" id="estadoArchivoLabel">Rechazar Documento</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">

				<div class="mb-3">
					<h5 class="font-weight-bold text-secondary mb-2">
						¿Desea <span id="tituloEstado" class="font-weight-bold "></span> el <b>documento</b>?
					</h5>
					<p class="text-muted mb-0">
						<i class="subtituloEstado"></i>
					</p>
				</div>

				<hr>

				<!-- Inputs ocultos -->
				<input type="hidden" id="archivoId" name="archivoId" value="">
				<input type="hidden" id="estadoArchivo" name="estadoArchivo" value="">

				<!-- Observaciones -->
				<div class="form-group">
					<label for="observacionEstadoArchivo" class="font-weight-bold">Observación</label>
					<textarea 
						name="observacionEstadoArchivo" 
						id="observacionEstadoArchivo"
						class="form-control form-control-sm text-uppercase" 
						placeholder=""
						rows="5"
					></textarea>
				</div>

			</div>

			<div class="modal-footer d-flex justify-content-between align-items-center">
				<div id="mensajeCargandoBotonesArchivos" class="text-success font-weight-bold d-none">
					<i class="fas fa-spinner fa-spin"></i> Enviando solicitud...
				</div>

				<div id="botonesModalEstadoArchivo">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">
						Cerrar
					</button>
					<button type="button" class="btn btn-outline-success btnEstadoArchivo">
						<i class="fas fa-save"></i> Aceptar
					</button>
				</div>
			</div>

		</div>
	</div>
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/solicitud-proveedor.js');
?>
