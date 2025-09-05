<?php
	if ( isset($permiso->id) ) {
		$codigo = isset($old["codigo"]) ? $old["codigo"] : $permiso->codigo;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $permiso->nombre;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $permiso->descripcion;
	} else {
		$codigo = isset($old["codigo"]) ? $old["codigo"] : "";
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-header">
              <h3 class="card-title">Datos generales</h3>
            </div>

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="form-group">
					<label for="codigo">Código:</label>
					<input type="text" name="codigo" value="<?php echo fString($codigo); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el código del permiso">
				</div>

				<div class="form-group">
					<label for="nombre">Nombre:</label>
					<?php if ( isset($permiso->id) ) : ?>
						<input type="text" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" disabled>
					<?php else: ?>
						<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm" placeholder="Ingresa el nombre del permiso">
					<?php endif; ?>
				</div>

				<div class="form-group">
					<label for="descripcion">Descripción:</label>
					<input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del permiso">
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-header">
              <h3 class="card-title">Aplicaciones</h3>
            </div>

			<div class="card-body">

				<?php foreach($aplicaciones as $aplicacion) { ?>
				<div class="form-check">
					<!-- <div class="input-group perfiles"> -->
						<input type="checkbox" name="aplicaciones[]" id="<?=fString($aplicacion["nombre"])?>" value="<?php echo fString($aplicacion["nombre"]); ?>" <?php echo in_arrayi($aplicacion["nombre"], $permiso->aplicaciones) ? "checked" : "" ; ?> class="form-check-input">
						<label class="form-check-label text-uppercase" for="<?=fString($aplicacion["nombre"])?>">
							<?php echo fString($aplicacion["descripcion"]); ?>
						</label>
					<!-- </div> -->
				</div>
				<?php } ?>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
