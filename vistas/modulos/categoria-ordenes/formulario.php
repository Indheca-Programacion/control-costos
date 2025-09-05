<?php
	if ( isset($categoriaOrdenes->id) ) {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $categoriaOrdenes->nombre;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $categoriaOrdenes->descripcion;
	} else {
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
					<label for="nombre">Nombre:</label>
					<?php if ( isset($categoriaProveedor->id) ) : ?>
						<input type="text" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" disabled>
					<?php else: ?>
						<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre de la categoria">
					<?php endif; ?>
				</div>

				<div class="form-group">
					<label for="descripcion">Descripción:</label>
					<input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción de la categoria">
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
