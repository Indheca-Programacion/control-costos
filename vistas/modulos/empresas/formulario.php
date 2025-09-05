<?php
	if ( isset($empresa->id) ) {
		$razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : $empresa->razonSocial;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $empresa->nombreCorto;
		$rfc = isset($old["rfc"]) ? $old["rfc"] : $empresa->rfc;
		$calle = isset($old["calle"]) ? $old["calle"] : $empresa->calle;
		$noExterior = isset($old["noExterior"]) ? $old["noExterior"] : $empresa->noExterior;
		$noInterior = isset($old["noInterior"]) ? $old["noInterior"] : $empresa->noInterior;		
		$colonia = isset($old["colonia"]) ? $old["colonia"] : $empresa->colonia;
		$localidad = isset($old["localidad"]) ? $old["localidad"] : $empresa->localidad;
		$referencia = isset($old["referencia"]) ? $old["referencia"] : $empresa->referencia;
		$municipio = isset($old["municipio"]) ? $old["municipio"] : $empresa->municipio;
		$estado = isset($old["estado"]) ? $old["estado"] : $empresa->estado;
		$pais = isset($old["pais"]) ? $old["pais"] : $empresa->pais;
		$codigoPostal = isset($old["codigoPostal"]) ? $old["codigoPostal"] : $empresa->codigoPostal;
		$nomenclaturaOT = isset($old["nomenclaturaOT"]) ? $old["nomenclaturaOT"] : $empresa->nomenclaturaOT;
		$logoAnterior = $empresa->logo;
		$logo = $empresa->logo;
		$imagenAnterior = $empresa->imagen;
		$imagen = $empresa->imagen;
	} else {
		$razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$rfc = isset($old["rfc"]) ? $old["rfc"] : "";
		$calle = isset($old["calle"]) ? $old["calle"] : "";
		$noExterior = isset($old["noExterior"]) ? $old["noExterior"] : "";
		$noInterior = isset($old["noInterior"]) ? $old["noInterior"] : "";
		$colonia = isset($old["colonia"]) ? $old["colonia"] : "";
		$localidad = isset($old["localidad"]) ? $old["localidad"] : "";
		$referencia = isset($old["referencia"]) ? $old["referencia"] : "";
		$municipio = isset($old["municipio"]) ? $old["municipio"] : "";
		$estado = isset($old["estado"]) ? $old["estado"] : "";
		$pais = isset($old["pais"]) ? $old["pais"] : "";
		$codigoPostal = isset($old["codigoPostal"]) ? $old["codigoPostal"] : "";
		$nomenclaturaOT = isset($old["nomenclaturaOT"]) ? $old["nomenclaturaOT"] : "";
		$logo = null;
		$imagen = null;
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="form-group">
					<label for="razonSocial">Razón Social:</label>
					<?php if ( isset($empresa->id) ) : ?>
						<!-- <input type="text" value="<?php echo fString($razonSocial); ?>" class="form-control form-control-sm text-uppercase" disabled> -->
					<?php else: ?>
					<?php endif; ?>
						<input type="text" name="razonSocial" value="<?php echo fString($razonSocial); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la razón social de la Empresa">
				</div>
				
				<div class="row">

					<div class="col-md-7 form-group">
						<label for="rfc">Nombre Corto:</label>
						<input type="text" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el Nombre Corto">
					</div>

					<div class="col-md-5 form-group">
						<label for="rfc">RFC:</label>
						<input type="text" name="rfc" value="<?php echo fString($rfc); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el RFC">
					</div>

				</div>

				<div class="form-group">
					<label for="calle">Calle:</label>
					<input type="text" name="calle" value="<?php echo fString($calle); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la calle">
				</div>

				<div class="row">
					<div class="col-md-7 form-group">
						<label for="noExterior">No. Exterior:</label>
						<input type="text" name="noExterior" value="<?php echo fString($noExterior); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número exterior">
					</div>
					<div class="col-md-5 form-group">
						<label for="noInterior">No. Interior:</label>
						<input type="text" name="noInterior" value="<?php echo fString($noInterior); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número interior">
					</div>
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-body">

				<div class="row">
					<div class="col-md-4 form-group">
						<label for="codigoPostal">Código Postal:</label>
						<input type="text" name="codigoPostal" value="<?php echo fString($codigoPostal); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el código postal">
					</div>
					<div class="col-md-8 form-group">
						<label for="colonia">Colonia:</label>
						<input type="text" name="colonia" value="<?php echo fString($colonia); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la colonia">
					</div>
				</div>

				<div class="form-group">
					<label for="municipio">Localidad:</label>
					<input type="text" name="localidad" value="<?php echo fString($localidad); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la localidad">
				</div>

				<div class="form-group">
					<label for="municipio">Referencia:</label>
					<input type="text" name="referencia" value="<?php echo fString($referencia); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la referencia">
				</div>

				<div class="form-group">
					<label for="municipio">Municipio:</label>
					<input type="text" name="municipio" value="<?php echo fString($municipio); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el municipio">
				</div>

				<div class="row">
					<div class="col-md-6 form-group">
						<label for="estado">Estado:</label>
						<input type="text" name="estado" value="<?php echo fString($estado); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el estado">
					</div>
					<div class="col-md-6 form-group">
						<label for="pais">País:</label>
						<input type="text" name="pais" value="<?php echo fString($pais); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el país">
					</div>
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-primary card-outline">

			<div class="card-body">

				<div class="form-group">
					<label for="nomenclaturaOT">Nomenclatura OT:</label>
					<input type="text" id="nomenclaturaOT" name="nomenclaturaOT" value="<?php echo fString($nomenclaturaOT); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la nomenclatura para la OT">
				</div>

			</div>
			
		</div> <!-- <div class="card card-primary card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-success card-outline">

			<div class="card-body">

				<?php
					if ( is_null($logo) ) {
						$previsualLogo = App\Route::rutaServidor()."vistas/img/empresas/default/logo.jpg";
					} else {
						$previsualLogo = App\Route::rutaServidor().$logo;
					}
				?>
				<div class="col-12 col-sm-12 form-group">
					<label for="logo">Logo:</label>
					<div style="width: 50%;">
						<picture>
							<img src="<?php echo $previsualLogo; ?>" id="imgLogo" class="img-fluid img-thumbnail previsualizar" style="width: 100%">
						</picture>
					</div>
					<span class="text-muted">Presione sobre la imágen si desea cambiarla (Resolución recomendada 500 x 500 pixeles)</span>
					<?php if ( isset($empresa->id) ) : ?>
						<input type="hidden" name="logoAnterior" value="<?php echo $logoAnterior; ?>">
					<?php endif; ?>
					<input type="file" class="form-control form-control-sm d-none" id="logo" name="logo">
				</div>

				<?php
					if ( is_null($imagen) ) {
						$previsualImagen = App\Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
					} else {
						$previsualImagen = App\Route::rutaServidor().$imagen;
					}
				?>
				<div class="col-12 col-md-12 form-group">
					<label for="imagen">Imágen:</label>
					<picture>
						<img src="<?php echo $previsualImagen; ?>" id="imgImagen" class="img-fluid img-thumbnail previsualizar" style="width: 100%">
					</picture>
					<span class="text-muted">Presione sobre la imágen si desea cambiarla (Resolución recomendada 600 x 200 pixeles)</span>
					<?php if ( isset($empresa->id) ) : ?>
						<input type="hidden" name="imagenAnterior" value="<?php echo $imagenAnterior; ?>">
					<?php endif; ?>
					<input type="file" class="form-control form-control-sm d-none" id="imagen" name="imagen">
				</div>

			</div>

		</div> <!-- <div class="card card-success card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
