<?php 
    $old = old();
    use App\Route;
?>

<div class="content-wrapper">

    <!-- Encabezado -->
    <section class="content-header bg-white py-3 mb-4 border-bottom shadow-lg">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h1 class="h3 mb-0">Mis Datos Legales</h1>
                    <small class="text-muted">Actualiza y gestiona tu información</small>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?=Route::routes('inicio')?>">
                                <i class="fas fa-tachometer-alt"></i> Inicio
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Mis Datos Legales</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Contenido principal -->
    <section class="content">

        <?php if (!is_null(flash())) : ?>
            <div class="d-none" id="msgToast"
                 clase="<?=flash()->clase?>"
                 titulo="<?=flash()->titulo?>"
                 subtitulo="<?=flash()->subTitulo?>"
                 mensaje="<?=flash()->mensaje?>">
            </div>
        <?php endif; ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" id="proveedorId" value="<?php echo $proveedor->id; ?>">
                    <!-- Sección datos generales -->
                    <?php include "vistas/modulos/datos-legales/form-section-marco-legal.php";?>
                    <!-- Mensaje dinámico -->
                    <div id="msgSend" class="mt-3"></div>
                </div> <!-- /.col -->
            </div> <!-- /.row -->
        </div> <!-- /.container-fluid -->

    </section>
</div>

<!-- MODAL PARA VER ARCHIVOS -->
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


<?php 
    array_push($arrayArchivosJS, 'vistas/js/datos-legales.js?v=1.00');
?>
