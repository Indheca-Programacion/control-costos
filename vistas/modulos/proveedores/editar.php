<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Proveedores <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('proveedores.index')?>"> <i class="fas fa-list-alt"></i> Proveedores</a></li>
	            <li class="breadcrumb-item active">Editar proveedor</li>
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
			<div class="col-md-11 col-lg-12">
                <div class="card card-secondary card-outline">
					<div class="card-header">
                        <ul class="nav nav-tabs" id="tabDatosFiscales" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="generales-tab" data-toggle="pill" href="#generales" role="tab" aria-controls="generales" aria-selected="true">General</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="marco-legal-tab" data-toggle="pill" href="#marco-legal" role="tab" aria-controls="marco-legal" aria-selected="false">Marco Legal</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="marco-financiero-tab" data-toggle="pill" href="#marco-financiero" role="tab" aria-controls="marco-financiero" aria-selected="false">Marco Financiero</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="calidad-producto-tab" data-toggle="pill" href="#calidad-producto" role="tab" aria-controls="calidad-producto" aria-selected="false">Calidad del Producto</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="validacion-tab" data-toggle="pill" href="#validacion" role="tab" aria-controls="validacion" aria-selected="false">Validación / Observaciones</a>
                            </li>
                        </ul>
                    </div>
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('proveedores.update', $proveedor->id); ?>" enctype="multipart/form-data">
							<input type="hidden" name="_method" value="PUT">

                        <div class="tab-content" id="tabServicioContent">
                                <div class="tab-pane fade show active" id="generales" role="tabpanel" aria-labelledby="generales-tab">
                                    <?php include "vistas/modulos/proveedores/form-section-generales.php";?>
                                </div>
                                <div class="tab-pane fade" id="marco-legal" role="tabpanel" aria-labelledby="marco-legal-tab">
                                    <?php
                                    include "vistas/modulos/proveedores/form-section-marco-legal.php";
                                    ?>
                                </div>
                                <div class="tab-pane fade" id="marco-financiero" role="tabpanel" aria-labelledby="marco-financiero-tab">
                                    <?php
                                    include "vistas/modulos/proveedores/form-section-marco-financiero.php";
                                    ?>
                                </div>
                                <div class="tab-pane fade" id="calidad-producto" role="tabpanel" aria-labelledby="calidad-producto-tab">
                                    <?php
                                    include "vistas/modulos/proveedores/form-section-calidad-producto.php";
                                    ?>
                                </div>
                                <div class="tab-pane fade" id="validacion" role="tabpanel" aria-labelledby="validacion-tab">
                                    <?php
                                    $ruta = "vistas/modulos/datos-fiscales/form-section-validacion.php";

                                    if (file_exists($ruta)) {
                                        include $ruta;
                                    } else {
                                        echo "<h3>Deshabilitada</h3>";
                                    }
                                    ?>
                                </div>


                        </div> <!-- /.tab-content -->
                        </form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>

                    </div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

</div>

<!-- Modal -->
<div class="modal fade" id="archivoModal" tabindex="-1" role="dialog" aria-labelledby="archivoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archivoModalLabel">Vista Previa del Archivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="archivoIframe" src="" style="width: 100%; height: 500px;" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ESTADO ARCHIVO -->
<div class="modal fade" id="estadoArchivoModal" tabindex="-1" role="dialog" aria-labelledby="estadoArchivoLabel" aria-hidden="true">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">

			<div class="modal-header bg-info text-white">
				<h5 class="modal-title" id="estadoArchivoLabel">Archivo Documento</h5>
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

<!-- Modal id="modalAgregarDatosBancarios" -->
<div class="modal fade" id="modalAgregarDatosBancarios" data-backdrop="static" data-keyboard="false" aria-labelledby="modalAgregarDatosBancariosLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalAgregarDatosBancariosLabel"><i class="fas fa-plus"></i> Agregar datos bancarios</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div> <!-- <div class="modal-header"> -->
			<div class="modal-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
						<ul class="mb-0">
								<!-- <li></li> -->
						</ul>
				</div> <!-- <div class="alert alert-danger error-validacion mb-2 d-none"> -->
						
				<form >
						<input type="hidden" name="proveedorId" id="proveedorId" value="<?php echo $proveedor->id ?>" >
                        <input type="hidden" id="datoBancarioId" name="datoBancarioId" >
							
						<div class="row">
	
								<div class="col-md-6 form-group">
									<label for="nombreTitular">Nombre Titular:</label>
									<input type="text" id="nombreTitular" name="nombreTitular" class="form-control form-control-sm text-uppercase" value="" >
								</div>
								<div class="col-md-6 form-group">
									<label for="nombreBanco">Nombre del Banco:</label>
									<input type="text" id="nombreBanco" name="nombreBanco" class="form-control form-control-sm text-uppercase" value="" >
								</div>
	
								<div class="col-md-6 form-group">
									<label for="cuenta">Cuenta</label>
									<input type="number" id="cuenta" name="cuenta" class="form-control form-control-sm text-uppercase" value="" >
								</div>

								<div class="col-md-6 form-group">
									<label for="cuentaClave">Cuenta Clabe</label>
									<input type="number" id="cuentaClave" name="cuentaClave" class="form-control form-control-sm text-uppercase" value="" >
								</div>

                                <div class="col-md-6 form-group">
                                    <label for="divisaId">Divisa</label>
                                    <select id="divisaId" name="divisaId" class="form-control form-control-sm">
                                        <option value="">Seleccione una divisa</option>
                                        <?php foreach ($divisas as $divisa): ?>
                                            <option value="<?= $divisa["id"] ?>"><?=$divisa["descripcion"] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
						</div>
				</form>

			</div>
            <!-- Footer del modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>

                <!-- Botón AGREGAR – visible por defecto -->
                <button type="button" class="btn btn-outline-primary btnAgregarDatosBancarios">
                    <i class="fas fa-save"></i> Agregar
                </button>

                <!-- Botón EDITAR – oculto por defecto -->
                <button type="button"
                        class="btn btn-outline-primary btnEditarDatosBancarios d-none">
                    <i class="fas fa-save"></i> Editar
                </button>
            </div>
		</div>
	</div>
</div>

<!-- Modal para visualizar archivos de permisos -->
<div class="modal fade" id="modalVerArchivos" tabindex="-1" role="dialog" aria-labelledby="modalVerArchivosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerArchivosLabel">Vista Previa del Archivo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="accordionArchivos">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal agregar permiso -->
<div class="modal fade" id="modalAgregarPermiso" tabindex="-1" role="dialog" aria-labelledby="modalAgregarPermisoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarPermisoLabel">Agregar Permiso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formAgregarPermiso">
                    <div class="form-group">
                        <label for="tituloPermiso">Título del Permiso</label>
                        <input type="text" class="form-control form-control-sm" id="tituloPermiso" name="tituloPermiso" required>
                    </div>
                    <div class="form-group">
                        <label for="archivoPermiso">Subir Archivo</label>
                        <input type="file" class="form-control-file" id="archivoPermiso" name="archivoPermiso[]" accept=".pdf" multiple required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary btnAgregarPermiso" >Agregar</button>
            </div>
        </div>
    </div>
</div>

		
<?php array_push($arrayArchivosJS, 'vistas/js/proveedores.js?v=1.00');?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Get the last active tab from localStorage
        const lastActiveTab = localStorage.getItem("lastActiveTab");
        if (lastActiveTab) {
            // Remove active class from all tabs and tab panes
            document.querySelectorAll('#tabDatosFiscales .nav-link').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('#tabServicioContent .tab-pane').forEach(pane => pane.classList.remove('show', 'active'));

            // Activate the last active tab and its corresponding pane
            const activeTab = document.querySelector(`#tabDatosFiscales .nav-link[href="${lastActiveTab}"]`);
            const activePane = document.querySelector(lastActiveTab);
            if (activeTab && activePane) {
                activeTab.classList.add('active');
                activePane.classList.add('show', 'active');
            }
        }

        // Save the active tab to localStorage on tab click
        document.querySelectorAll('#tabDatosFiscales .nav-link').forEach(tab => {
            tab.addEventListener('click', function () {
                localStorage.setItem("lastActiveTab", this.getAttribute('href'));
            });
        });
    });
</script>