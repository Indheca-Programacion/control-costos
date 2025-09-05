<?php
// var_dump($perfil->permisos);
	if ( isset($perfil->id) ) {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $perfil->nombre;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $perfil->descripcion;
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
					<label for="name">Nombre:</label>
					<?php if ( isset($perfil->id) ) : ?>
						<input type="text" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" disabled>
					<?php else: ?>
						<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre del perfil">
					<?php endif; ?>
				</div>
				<div class="form-group">
					<label for="display_name">Descripción:</label>
					<input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del perfil">
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

		<button type="button" id="btnSend" class="btn btn-outline-primary">
			<i class="fas fa-save"></i> Guardar
		</button>
		<div id="msgSend"></div>

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-header">
              <h3 class="card-title">Permisos</h3>
            </div>

			<div class="card-body">

				<table class="table table-bordered table-striped" width="100%">
         
				<thead>	         
					<tr>
						<th>Descripción</th>
						<th class="text-center" style="width:55px;">Ver</th>
						<th class="text-center" style="width:55px;">Crear</th>
						<th class="text-center" style="width:55px;">Editar</th>
						<th class="text-center" style="width:55px;">Borrar</th>
					</tr> 
				</thead>

				<tbody>

				<?php foreach($permisos as $permiso) { ?>
				<tr>
				<td class="text-capitalize" style="padding: 4px 8px;"><?php echo fString($permiso["descripcion"]); ?></td>
				<td class="text-center" style="padding: 2px;">
					<input type="checkbox" name="permisos[<?php echo $permiso["nombre"]; ?>][]" value="ver" <?php echo $perfil->checkPermiso($permiso["nombre"], "ver") ? "checked" : ""; ?>>
				</td>
				<td class="text-center" style="padding: 2px;">
					<input type="checkbox" name="permisos[<?php echo $permiso["nombre"]; ?>][]" value="crear" <?php echo $perfil->checkPermiso($permiso["nombre"], "crear") ? "checked" : ""; ?>>
				</td>
				<td class="text-center" style="padding: 2px;">
					<input type="checkbox" name="permisos[<?php echo $permiso["nombre"]; ?>][]" value="actualizar" <?php echo $perfil->checkPermiso($permiso["nombre"], "actualizar") ? "checked" : ""; ?>>
				</td>
				<td class="text-center" style="padding: 2px;">
					<input type="checkbox" name="permisos[<?php echo $permiso["nombre"]; ?>][]" value="eliminar" <?php echo $perfil->checkPermiso($permiso["nombre"], "eliminar") ? "checked" : ""; ?>>
				</td>
				</tr>
				<?php } ?>

				</tbody>

       			</table>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
