<?php
	if ( isset($sucursal->id) ) {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $sucursal->empresaId;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $sucursal->descripcion;
		$domicilioFiscal = isset($old["descripcion"]) ? ( isset($old["domicilioFiscal"]) && $old["domicilioFiscal"] == "on" ? true : false ) : $sucursal->domicilioFiscal;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $sucursal->nombreCorto;
		$calle = isset($old["calle"]) ? $old["calle"] : $sucursal->calle;
		$noExterior = isset($old["noExterior"]) ? $old["noExterior"] : $sucursal->noExterior;
		$noInterior = isset($old["noInterior"]) ? $old["noInterior"] : $sucursal->noInterior;		
		$colonia = isset($old["colonia"]) ? $old["colonia"] : $sucursal->colonia;
		$localidad = isset($old["localidad"]) ? $old["localidad"] : $sucursal->localidad;
		$referencia = isset($old["referencia"]) ? $old["referencia"] : $sucursal->referencia;
		$municipio = isset($old["municipio"]) ? $old["municipio"] : $sucursal->municipio;
		$estado = isset($old["estado"]) ? $old["estado"] : $sucursal->estado;
		$pais = isset($old["pais"]) ? $old["pais"] : $sucursal->pais;
		$codigoPostal = isset($old["codigoPostal"]) ? $old["codigoPostal"] : $sucursal->codigoPostal;
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$domicilioFiscal = isset($old["domicilioFiscal"]) && $old["domicilioFiscal"] == "on" ? true : false;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
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
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="form-group">
					<label for="empresaId">Empresa:</label>
					<?php if ( isset($sucursal->id) ) : ?>
					<select id="empresaId" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>
					<?php else: ?>
					<select name="empresaId" id="empresaId" class="custom-select form-controls form-control-sms select2" style="width: 100%">
						<option value="">Selecciona una Empresa</option>
					<?php endif; ?>
						<?php foreach($empresas as $empresa) { ?>
						<option value="<?php echo $empresa["id"]; ?>"
							<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
						</option>
						<?php } ?>
					</select>
				</div>

				<div class="form-group">
					<label for="descripcion">Descripción:</label>
					<input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción de la Sucursal">
				</div>
				
				<div class="row">

					<div class="col-md-7 form-group">
						<label for="rfc">Nombre Corto:</label>
						<input type="text" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el Nombre Corto">
					</div>

					<div class="col-md-5 form-group">

						<label for="domicilioFiscal">Domicilio:</label>
						<div class="input-group">
							<input type="text" class="form-control form-control-sm" value="Fiscal" readonly>
							<div class="input-group-append">
								<div class="input-group-text">
									<input type="checkbox" name="domicilioFiscal" id="domicilioFiscal" <?php echo $domicilioFiscal ? "checked" : ""; ?>>
								</div>
							</div>
						</div>
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

</div> <!-- <div class="row"> -->
