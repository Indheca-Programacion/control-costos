<?php 

	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

    <section class="content-header">

        <div class="container-fluid">
            <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Mis datos Fiscales</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                <li class="breadcrumb-item active">Empleados</li>
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
                        <form id="formSend" method="POST" action="<?php echo Route::names('datos-fiscales.update', $proveedor->id); ?>" >
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="proveedorId" value="<?php echo $proveedor->id; ?>">
                        <div class="tab-content" id="tabServicioContent">
                                <div class="tab-pane fade show active" id="generales" role="tabpanel" aria-labelledby="generales-tab">
                                    <?php include "vistas/modulos/datos-fiscales/form-section-generales.php";?>
                                </div>
                                <div class="tab-pane fade" id="marco-legal" role="tabpanel" aria-labelledby="marco-legal-tab">
                                    <?php
                                    include "vistas/modulos/datos-fiscales/form-section-marco-legal.php";
                                    ?>
                                </div>
                                <div class="tab-pane fade" id="marco-financiero" role="tabpanel" aria-labelledby="marco-financiero-tab">
                                    <?php
                                    include "vistas/modulos/datos-fiscales/form-section-marco-financiero.php";
                                    ?>
                                </div>
                                <div class="tab-pane fade" id="calidad-producto" role="tabpanel" aria-labelledby="calidad-producto-tab">
                                    <?php
                                    include "vistas/modulos/datos-fiscales/form-section-calidad-producto.php";
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



                            <div id="msgSend"></div>
                        </div> <!-- /.tab-content -->
                    </form>
                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
            </div> <!-- /.col -->
        </div> <!-- ./row -->
    </div><!-- /.container-fluid -->

    <!-- Modal para ver archivos individuales -->
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

    </section>

</div>

<?php 
array_push($arrayArchivosJS, 'vistas/js/datos-fiscales.js?v=1.00');
?>

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