<?php
	if ( isset($permisoCategoriaProveedor->id) ) {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $permisoCategoriaProveedor->nombre;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $permisoCategoriaProveedor->descripcion;
		$grupo = isset($old["grupo"]) ? $old["grupo"] : $permisoCategoriaProveedor->grupo;
		$tipo = isset($old["tipo"]) ? $old["tipo"] : $permisoCategoriaProveedor->tipo;

	} else {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$grupo = isset($old["grupo"]) ? $old["grupo"] : "";
		$tipo = isset($old["tipo"]) ? $old["tipo"] : "";
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
					<?php if ( isset($permisoCategoriaProveedor->id) ) : ?>
						<input type="text" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" disabled>
					<?php else: ?>
						<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre del permiso">
					<?php endif; ?>
				</div>

				<div class="form-group">
					<label for="descripcion">Descripción:</label>
					<input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del permiso">
				</div>

				<div class="form-group">
					<label for="grupo">Grupo:</label>
					<select name="grupo" class="form-control form-control-sm text-uppercase">
						<option value="">-- Selecciona una opción --</option>
						<option value="Marco Financiero" <?php echo ($grupo == 'Marco Financiero') ? 'selected' : ''; ?>>Marco Financiero</option>
						<option value="Calidad de Producto" <?php echo ($grupo == 'Calidad de Producto') ? 'selected' : ''; ?>>Calidad de Producto</option>
						<option value="Marco Legal" <?php echo ($grupo == 'Marco Legal') ? 'selected' : ''; ?>>Marco Legal</option>
					</select>
				</div>

				<div class="form-group">
					<label for="tipo">Tipo:</label>
					<select name="tipo" class="form-control form-control-sm text-uppercase">
						<option value="">-- Selecciona un tipo --</option>
						<option value="documentacionML" <?php echo ($tipo == 'documentacionML') ? 'selected' : ''; ?>>documentacionML</option>
						<option value="opinion-cumplimiento" <?php echo ($tipo == 'opinion-cumplimiento') ? 'selected' : ''; ?>>opinion cumplimiento</option>
						<option value="permiso-licencia" <?php echo ($tipo == 'permiso-licencia') ? 'selected' : ''; ?>>permiso licencia</option>
						<option value="repse" <?php echo ($tipo == 'repse') ? 'selected' : ''; ?>>repse</option>
						<option value="documentacionMF" <?php echo ($tipo == 'documentacionMF') ? 'selected' : ''; ?>>documentacionMF</option>
						<option value="certificaciones" <?php echo ($tipo == 'certificaciones') ? 'selected' : ''; ?>>certificaciones</option>
					</select>
				</div>



				
			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
