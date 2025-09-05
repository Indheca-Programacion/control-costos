<?php use App\Route; ?>
<div class="content m-4">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <!-- <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
                    <li class="breadcrumb-item active">Politicas</li> -->
                </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <section class="content">
        
            <div class="row justify-content-center">
                <div class="col-md-8">
                <div class="card card-secondary card-outline text-center">

                    <div class="card-body container-fluid ">

                        <h1 class="text-center"></h1>

                        <p class="text-center">Ha recibido un pago departe de <strong class="text-uppercase"><?= $empresa->razonSocial ?></strong> correspondiente a la OC. <strong> <?= $correoProveedor->ordenCompra ?></strong> </p> <p> Favor de adjuntar la siguiente documentacion</p>

                        <form method="post" id="formSend" enctype="multipart/form-data">
                            <input type="hidden" name="requisicion" value="<?= $correoProveedor->requisicionId ?>">
                            <input type="hidden" name="correoProveedor" value="<?= $id ?>">
                            <div class="row text-center "> 
                                <div class="col-md-6 form-group subir-facturas-pdf">
                                    <button type="button" id="btnSubirFacturasPDF" class="btn btn-primary">
                                        <i class="fas fa-file-pdf"></i> Factura (PDF)
                                    </button>
                                    <span class="lista-archivos">
                                    </span>
                                    
                                    <input type="file" class="form-control form-control-sm d-none" id="facturaArchivosPDF">
                                </div>
                                <div class="col-md-6 form-group subir-facturas-xml">
                                    <button type="button" id="btnSubirFacturasXML" class="btn btn-primary">
                                        <i class="fas fa-file-pdf"></i> Factura (XML)
                                    </button>
                                    <span class="lista-archivos">
                                    </span>

                                    <input type="file" class="form-control form-control-sm d-none" id="facturaArchivosXML">
                                </div>
                                <!-- <div class="col-md-12 form-group">
                                    <button type="button" class="btn btn-primary">
                                        <i class="fas fa-file-pdf"></i> Doct. Adicional (de ser el caso)
                                    </button>
                                </div> -->
                                <div class="col-12 form-group">
                                    <button type="button" class="btn btn-info" id="btnSubirFacturas"> <i class="fas fa-upload"></i> Subir Facturas</button>
                                </div>
                            </div>
                        </form>

                    </div> <!-- /.card-body -->
                </div> <!-- /.card -->
                </div> <!-- /.col -->
            </div> <!-- ./row -->
        
    </section>
</div>

<?php
  array_push($arrayArchivosJS, 'vistas/js/proveedores.js?v=1.1');
?>