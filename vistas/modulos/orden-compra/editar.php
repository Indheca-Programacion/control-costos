<?php
	$old = old();

	
	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Ordenes de Compra <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('orden-compra.index')?>"> <i class="fas fa-tools"></i> Ordenes de Compra</a></li>
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
								Editar Orden de Compra
							</h3>
						</div> <!-- <div class="card-header"> -->
						<div class="card-body">
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<form id="formSend" method="POST" action="<?php echo Route::names('orden-compra.update', $ordenCompra->id); ?>" enctype="multipart/form-data">
								<input type="hidden" name="_method" value="PUT">
								<?php include "vistas/modulos/orden-compra/formulario.php"; ?>
								<button type="button" id="btnSend" class="btn btn-outline-primary">
									<i class="fas fa-save"></i> Actualizar
								</button>		
								<!-- Botón para ver todo -->
								<button type="button" id="btnVerTodo" class="btn btn-outline-primary" data-toggle="modal" data-target="#modalVerPDF">
									<i class="fas fa-eye"></i> Ver Todo
								</button>
								<a href="<?php echo Route::names('orden-compra.print', $ordenCompra->id); ?>" target="_blank" class="btn btn-info float-right"><i class="fas fa-print"></i> Imprimir</a>				
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
	array_push($arrayArchivosJS, 'vistas/js/ordenes-compra.js?v=1.41');
?>
