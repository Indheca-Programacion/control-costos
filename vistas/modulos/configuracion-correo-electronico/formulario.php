<?php
	use App\Route;
	if ( isset($configuracionCorreoElectronico->id) ) {
		$servidor = isset($old["servidor"]) ? $old["servidor"] : $configuracionCorreoElectronico->servidor;
		$puerto = isset($old["puerto"]) ? $old["puerto"] : $configuracionCorreoElectronico->puerto;
		$puertoSSL = isset($old["puerto"]) ? ( isset($old["puertoSSL"]) && $old["puertoSSL"] == "on" ? true : false ) : $configuracionCorreoElectronico->puertoSSL;
		$usuario = isset($old["usuario"]) ? $old["usuario"] : $configuracionCorreoElectronico->usuario;
		$contrasena = isset($old["contrasena"]) ? $old["contrasena"] : "";
		$visualizacionCorreo = isset($old["visualizacionCorreo"]) ? $old["visualizacionCorreo"] : $configuracionCorreoElectronico->visualizacionCorreo;
		$visualizacionNombre = isset($old["visualizacionNombre"]) ? $old["visualizacionNombre"] : $configuracionCorreoElectronico->visualizacionNombre;
		$respuestaCorreo = isset($old["respuestaCorreo"]) ? $old["respuestaCorreo"] : $configuracionCorreoElectronico->respuestaCorreo;
		$respuestaNombre = isset($old["respuestaNombre"]) ? $old["respuestaNombre"] : $configuracionCorreoElectronico->respuestaNombre;
		$comprobacionCorreo = isset($old["comprobacionCorreo"]) ? $old["comprobacionCorreo"] : $configuracionCorreoElectronico->comprobacionCorreo;
	} else {
		$servidor = isset($old["servidor"]) ? $old["servidor"] : "";
		$puerto = isset($old["puerto"]) ? $old["puerto"] : "";
		$puertoSSL = isset($old["puertoSSL"]) && $old["puertoSSL"] == "on" ? true : false;
		$usuario = isset($old["usuario"]) ? $old["usuario"] : "";
		$contrasena = isset($old["contrasena"]) ? $old["contrasena"] : "";
		$visualizacionCorreo = isset($old["visualizacionCorreo"]) ? $old["visualizacionCorreo"] : "";
		$visualizacionNombre = isset($old["visualizacionNombre"]) ? $old["visualizacionNombre"] : "";
		$respuestaCorreo = isset($old["respuestaCorreo"]) ? $old["respuestaCorreo"] : "";
		$respuestaNombre = isset($old["respuestaNombre"]) ? $old["respuestaNombre"] : "";
		$comprobacionCorreo = isset($old["comprobacionCorreo"]) ? $old["comprobacionCorreo"] : "";
	}
?>

<div class="card card-primary card-outline card-outline-tabs">
	<div class="card-header p-0 border-bottom-0">
		<ul class="nav nav-tabs" id="tabConfiguracionCorreo" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="generales-tab" data-toggle="pill" href="#generales" role="tab" aria-controls="generales" aria-selected="true">Datos generales</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="requisiciones-tab" data-toggle="pill" href="#requisiciones" role="tab" aria-controls="requisiciones" aria-selected="false">Requisiciones</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" id="mensajes-tab" data-toggle="pill" href="#mensajes" role="tab" aria-controls="mensajes" aria-selected="false">Mensajes enviados</a>
			</li>
		</ul>
	</div>
	<div class="card-body px-2">
		<div class="tab-content" id="tabConfiguracionCorreoContent">
			<div class="tab-pane fade show active" id="generales" role="tabpanel" aria-labelledby="generales-tab">
				<form id="formSend" method="POST" action="<?php echo Route::routes('configuracion-correo-electronico'); ?>">
					<input type="hidden" name="_method" value="PUT">

					<?php include "vistas/modulos/configuracion-correo-electronico/form-section-generales.php"; ?>

					<button type="button" id="btnSend" class="btn btn-outline-primary">
						<i class="fas fa-save"></i> Actualizar
					</button>
					<div id="msgSend"></div>
				</form>
			</div>
			<div class="tab-pane fade" id="requisiciones" role="tabpanel" aria-labelledby="requisiciones-tab">
				<?php include "vistas/modulos/configuracion-correo-electronico/form-section-requisiciones.php"; ?>
			</div>
			<div class="tab-pane fade" id="mensajes" role="tabpanel" aria-labelledby="mensajes-tab">
				<?php include "vistas/modulos/configuracion-correo-electronico/form-section-mensajes.php"; ?>
			</div>
		</div>
	</div> <!-- /.card -->
</div>
