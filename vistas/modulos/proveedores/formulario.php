<?php

	$zonas = [
		[
			"id" => 1,
			"nombre" => "Veracruz"
		],
		[
			"id" => 2,
			"nombre" => "Cotazacoalcos"
		],
		[
			"id" => 3,
			"nombre" => "Ciudad del Carmen"
		],
		[
			"id" => 4,
			"nombre" => "Villahermosa"
		],
		[
			"id" => 5,
			"nombre" => "Tampico"
		],
		[
			"id" => 6,
			"nombre" => "Monterrey"
		],
		[
			"id" => 7,
			"nombre" => "Ciudad de México"
		],
		[
			"id" => 8,
			"nombre" => "Tierra Blanca"
		],
		[
			"id" => 9,
			"nombre" => "Guadalajara"
		],
		[
			"id" => 10,
			"nombre" => "Puebla"
		],
		[
			"id" => 11,
			"nombre" => "Querétaro"
		],
		[
			"id" => 12,
			"nombre" => "Zacatecas"
		],

	];

	if ( isset($proveedor->id) ) {

		$activo = isset($old["rfc"]) ? ( isset($old["activo"]) && $old["activo"] == "on" ? true : false ) : $proveedor->activo;
		$personaFisica = $proveedor->personaFisica;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $proveedor->nombre;
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : $proveedor->apellidoPaterno;
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : $proveedor->apellidoMaterno;
		$razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : $proveedor->razonSocial;
		$nombreComercial = isset($old["nombreComercial"]) ? $old["nombreComercial"] : $proveedor->nombreComercial;
		$rfc = isset($old["rfc"]) ? $old["rfc"] : $proveedor->rfc;
		$correo = isset($old["correo"]) ? $old["correo"] : $proveedor->correo;
		$credito = isset($old["rfc"]) ? ( isset($old["credito"]) && $old["credito"] == "on" ? true : false ) : $proveedor->credito;
		$limiteCredito = isset($old["limiteCredito"]) ? $old["limiteCredito"] : number_format($proveedor->limiteCredito, 2, '.', ',');
        $telefono = isset($old["telefono"]) ? $old["telefono"] : $proveedor->telefono;
		$zona = isset($old["zona"]) ? $old["zona"] : $proveedor->zona;
		$domicilio = isset($old["domicilio"]) ? $old["domicilio"] : $proveedor->domicilio;
		$estrellas = isset($old["estrellas"]) ? $old["estrellas"] : $proveedor->estrellas;

	} else {

		$activo = isset($old["activo"]) && $old["activo"] == "on" ? true : false;
		$personaFisica = isset($old["personaFisica"]) && $old["personaFisica"] == "on" ? true : false;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : "";
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : "";
		$razonSocial = isset($old["razonSocial"]) ? $old["razonSocial"] : "";
		$nombreComercial = isset($old["nombreComercial"]) ? $old["nombreComercial"] : "";
		$rfc = isset($old["rfc"]) ? $old["rfc"] : "";
		$correo = isset($old["correo"]) ? $old["correo"] : "";
		$credito = isset($old["credito"]) && $old["credito"] == "on" ? true : false;
		$limiteCredito = isset($old["limiteCredito"]) ? $old["limiteCredito"] : "0.00";
        $telefono = isset($old["telefono"]) ? $old["telefono"] : "";
		$zona = isset($old["zona"]) ? $old["zona"] : "";
		$domicilio = isset($old["domicilio"]) ? $old["domicilio"] : "";
		$estrellas = isset($old["estrellas"]) ? $old["estrellas"] : 0;
		$categoria = isset($old["idCategoria"]) ? $old["idCategoria"] : "";

	}
?>

<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" name="proveedorId" id="proveedorId" value="<?php echo $proveedor->id; ?>">

<div class="row">

	<div class="col-md-12 col-lg-6 ">
		
		<div class="card card-warning card-outline">

			<div class="card-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
						<!-- <li></li> -->
					</ul>
				</div>
				<div class="row">
					
					<div class="col-md-6">
				
						<div class="form-group">
							<label for="activo">Activo:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Proveedor Activo" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="activo" id="activo" <?php echo $activo ? "checked" : ""; ?>>
									</div>
								</div>
							</div>
						</div>
					</div>
				
				</div>
				
				<div class="row">
				
					<div class="col-md-6">
				
						<div class="form-group">
							<label for="personaFisica">Persona Física:</label>
							<div class="input-group">
								<input type="text" class="form-control form-control-sm" value="Es una Persona Física" readonly>
								<div class="input-group-append">
									<div class="input-group-text">
										<input type="checkbox" name="personaFisica" id="personaFisica" <?php echo $personaFisica ? "checked" : ""; ?> <?php echo isset($proveedor->id) ? ' disabled' : ''; ?>>
									</div>
								</div>
							</div>
						</div>
				
					</div>
				
					<div class="col-md-6">
				
						<div class="form-group">
							<label for="nombre">Nombre(s):</label>
							<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase personaFisica" placeholder="Ingresa el nombre(s)">
						</div>
				
					</div>
				
				</div>
				
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="apellidoPaterno">Apellido Paterno:</label>
						<input type="text" name="apellidoPaterno" value="<?php echo fString($apellidoPaterno); ?>" class="form-control form-control-sm text-uppercase personaFisica" placeholder="Ingresa el apellido paterno">
					</div>
					<div class="col-md-6 form-group">
						<label for="apellidoMaterno">Apellido Materno:</label>
						<input type="text" name="apellidoMaterno" value="<?php echo fString($apellidoMaterno); ?>" class="form-control form-control-sm text-uppercase personaFisica" placeholder="Ingresa el apellido materno">
					</div>
				
				</div>
				
				<div class="form-group">
					<label for="razonSocial">Razón Social:</label>
					<input type="text" name="razonSocial" value="<?php echo fString($razonSocial); ?>" class="form-control form-control-sm text-uppercase personaMoral" placeholder="Ingresa la razón social">
				</div>
				
				<div class="form-group">
					<label for="nombreComercial">Nombre Comercial:</label>
					<input type="text" name="nombreComercial" value="<?php echo fString($nombreComercial); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre comercial">
				</div>
				
				<div class="row">
					<div class="col-md-6 form-group">
						<label for="rfc">RFC:</label>
						<input type="text" name="rfc" value="<?php echo fString($rfc); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingrese el RFC del Proveedor">
					</div>
					<div class="col-md-6 form-group">
						<label for="telefono">Telefono:</label>
						<input type="number" name="telefono" value="<?php echo fString($telefono); ?>" max="10" class="form-control form-control-sm  " placeholder="Ingresa el teléfono">
					</div>
				</div>
				
				<div class="form-group">
					<label for="correo">Correo Electrónico:</label>
					<input type="email" name="correo" value="<?php echo fString($correo); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el correo electrónico">
				</div>
				
				<div class="form-group">
					<label for="zona">Zona:</label>
					<select name="zona" class="form-control form-control-sm select2">
						<option value="">Selecciona una zona</option>
						<?php
							foreach ($zonas as $detalle) {
								echo "<option value='{$detalle["id"]}'" . ($detalle["id"] == $zona ? " selected" : "") . ">{$detalle["nombre"]}</option>";
							}
						?>
					</select>
				</div>
				
				<div class="form-group">
					<label for="domicili">Domicilio:</label>
					<input type="text" name="domicilio" value="<?php echo fString($domicilio); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el domicilio">
				</div>

				<div class="form-group">
					<label for="categoria">Categoria</label>
					<select name="idCategoria" class="form-control form-control-sm select2">
						<option name="" value="">Selecciona una categoria</option>
						<?php
							foreach ($categorias as $detalle) {
								echo "<option value='{$detalle["id"]}'" . ($detalle["id"] == $categoria ? " selected" : "") . ">{$detalle["nombre"]}</option>";
							}
						?>
					</select>
				</div>
				
				<div class="row">
					<div class="col">
						<label for="estrellas">Calificación:</label>
						<p class="clasificacion">
							<input id="radio1" type="radio" name="estrellas" value="5" <?php echo $estrellas == 5 ? "checked" : ""; ?>>
							<label class="ratio" for="radio1">★</label>
							<input id="radio2" type="radio" name="estrellas" value="4" <?php echo $estrellas == 4 ? "checked" : ""; ?>>
							<label class="ratio" for="radio2">★</label>
							<input id="radio3" type="radio" name="estrellas" value="3" <?php echo $estrellas == 3 ? "checked" : ""; ?>>
							<label class="ratio" for="radio3">★</label>
							<input id="radio4" type="radio" name="estrellas" value="2" <?php echo $estrellas == 2 ? "checked" : ""; ?>>
							<label class="ratio" for="radio4">★</label>
							<input id="radio5" type="radio" name="estrellas" value="1" <?php echo $estrellas == 1 ? "checked" : ""; ?>>
							<label class="ratio" for="radio5">★</label>
						</p>
					</div>
				</div>
				
				<div class="row d-none">
				
					<div class="col-md-6 form-group">
						<label for="credito">Crédito:</label>
						<div class="input-group">
							<input type="text" class="form-control form-control-sm" value="Proveedor con Crédito" readonly>
							<div class="input-group-append">
								<div class="input-group-text">
									<input type="checkbox" name="credito" id="credito" <?php echo $credito ? "checked" : ""; ?>>
								</div>
							</div>
						</div>
					</div>
				
					<div class="col-md-6 form-group">
						<label for="limiteCredito">Límite de Crédito:</label>
						<div class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text" id="limiteCredito-addon"><i class="fas fa-dollar-sign"></i></span>
							</div>
							<input type="text" name="limiteCredito" id="limiteCredito" value="<?php echo $limiteCredito; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el límite de crédito" aria-label="Límite de Crédito" aria-describedby="limiteCredito-addon">
						</div>
					</div>
				
				</div>
			</div>
		</div>
	</div>

	<?php if(isset($proveedor->id)) : ?>

	<div class="col-md-12 col-lg-6">
		<div class="card card-info card-outline">
			<div class="card-body">
				<button type="button" class="btn btn-outline-primary float-right mb-2" data-toggle="modal" data-target="#modalAgregarDatosBancarios"> Agregar Datos Bancarios </button>			
				<div class="table-responsive">
		
					<table class="table table-sm table-bordered table-striped mb-0" id="tablaDatosBancarios" width="100%">
						<thead>
							<tr>
								<th class="text-right" >#</th>									
								<th>Nombre Titular</th>
								<th >Nombre Banco</th>
								<th >Cuenta</th>
								<th>Cuenta Clave</th>

								<th style="width: 10px;">Acciones</th>
							</tr>
						</thead>
						<tbody class="text-uppercase">

						</tbody>
					</table> <!-- <table class="table table-sm table-bordered table-striped mb-0" id="tablaSalidas" width="100%"> -->
				</div> <!-- <div class="table-responsive"> -->
			</div> <!-- <div class="card-body"> -->
		</div> <!-- <div class="card card-info card-outline"> -->
	</div><!-- <div class="col-md-6"> -->
	<?php endif ?>

</div>
				
