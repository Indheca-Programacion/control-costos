<?php
	$old = old();
	$idPuesto = isset($old["idPuesto"]) ? $old["idPuesto"] : "";
	$idObra = isset($old["idObra"]) ? $old["idObra"] : "";


	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Usuarios <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('usuarios.index')?>"> <i class="fas fa-user"></i> Usuarios</a></li>
	            <li class="breadcrumb-item active">Editar usuario</li>
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
							Editar usuario
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('usuarios.update', $usuario->id); ?>" enctype="multipart/form-data">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/usuarios/formulario.php"; ?>
							<!-- <input type="button" id="btnSend" class="btn btn-primary" value="Actualizar usuario"> -->
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
						<div class="row">
							<!-- TABLA PUESTOS DEL USUARIO -->
							<div class="col-6">
								<?php if ( usuarioAutenticado() ): ?>
									<?php if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("puestos") ): ?>
										<div class="card card-green card-outline mt-4" >
											<div class="card-header">
											<h3 class="card-title">Puestos</h3>
											<div class="card-tools">

											<button type="button" class="btn btn-outline-primary " data-toggle="modal" data-target="#modalAsignarPuesto">Asignar Puesto</button>
				
											</div>
											</div>
											<div class="card-body">
											<table class="table table-bordered table-striped" id="tablaPuestos" width="100%">
												<thead>
												<tr>
													<th style="width:10px">#</th>
													<th>Nombre</th>
													<th>Zona</th>
													<th>Acciones</th>
												</tr> 
												</thead>
												<tbody class="text-uppercase">
												</tbody>	
											</table>
											</div>
										</div> 
									<?php endif ?>
								<?php endif ?>
							</div>
						</div>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->
	</section>
</div>

<!-- modalAsignarPuesto -->
<div class="modal fade" id="modalAsignarPuesto" role="dialog" aria-labelledby="modalAsignarPuestoLabel" aria-hidden="true">
	<div class="modal-dialog modal-md modal-dialog-centered" role="document">
    	<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalAgregarIncidenciaLongTitle">Asignar Puesto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form id="formSendAsignarPuesto" method="POST" enctype="multipart/form-data">
								
			<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">

			<div class="modal-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
						<!-- <li></li> -->
					</ul>
				</div>
				<div class="row ">
					<!-- Puesto-->
					<div class="form-group col-12">
						<label for="puestoId">Puesto:</label>
						<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
						<?php if ( $usuarioAutenticado->checkAdmin() ) : ?>
							<select name="idPuesto" id="idPuesto" class="custom-select form-controls form-control-sms select2" style="width: 100%">
								<option value="">Selecciona un puesto</option>
						<?php else: ?>
							<select id="idPuesto" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>
						<?php endif; ?>
						<?php foreach($puestos as $puesto) { ?>
								<option value="<?php echo $puesto["id"]; ?>"
								<?php echo $idPuesto == $puesto["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($puesto["nombreCorto"])); ?>
							</option>
						<?php } ?>
							</select>	
					</div>
					<!-- ZONA -->
					<div class="form-group col-12">
						<label for="idObra">Obra:</label>
						<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
						<?php if ( $usuarioAutenticado->checkAdmin() ) : ?>
							<select name="idObra" id="idObra"  class="custom-select form-controls form-control-sms select2" style="width: 100%">
								<option value="">Selecciona una obra</option>
							<?php else: ?>
									<select id="idObra" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>
								<?php endif; ?>
								<?php foreach($obras as $zona) { ?>
								<option value="<?php echo $zona["id"]; ?>"
									<?php echo $idObra == $zona["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($zona["descripcion"])); ?>
								</option>			
						<?php } ?>
							</select>	
					</div>
				</div>
			</div>

			</form>	
			<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			<button type="button" class="btn btn-primary btnAsignarPuesto">Asignar</button>
			</div>
    	</div>
	</div>
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/usuarios.js?v=1.1');
?>
