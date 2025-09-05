<?php
	
	$lugar = isset($old["lugar"]) ? $old["lugar"] : $NotaInformativa->lugar;
	$fecha = isset($old["fecha"]) ? $old["fecha"] : $NotaInformativa->fecha;
	$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $NotaInformativa->descripcion;

	
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">


<div class="row">
	<div class="col-md-6 form-group">
		<label for="lugar">Lugar:</label>
		<input type="text" class="form-control form-control-sm" id="lugar" name="lugar" value="<?php echo htmlspecialchars($lugar); ?>" placeholder="Ingresa el lugar" required>
		<small class="form-text text-muted">Lugar donde se realizó la nota informativa.</small>
	</div>
	<div class="col-md-6 fomr-group">
		<label for="fecha">Fecha:</label>
		<div class="input-group date" id="fecha" data-target-input="nearest">
			<input type="text" name="fecha" id="fecha" value="<?= $fecha ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fecha">
			<div class="input-group-append" data-target="#fecha" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
			</div>
		</div>
	</div>
	<div class="col-12 fomr-group">
		<label for="descripcion">Descripcion:</label>
		<textarea class="form-control form-control-sm" id="descripcion" placeholder="Descripcion del cambio" name="descripcion" rows="3"><?php echo $descripcion ?></textarea>
	</div>
	<div class="col-12 form-group">
		<label for="fotos">Evidencia fotografica:</label>
		
			<div class="mt-2">
				<label>Imágenes actuales:</label>
				<div class="d-flex flex-wrap gap-2">
					<?php foreach ($NotaInformativa->imagenes as $imagen): ?>
						<div style="width: 120px; margin: 5px;">
								<img src="<?php echo htmlspecialchars($imagen["ruta"]); ?>" alt="Imagen" class="img-thumbnail" style="width: 100%; height: auto;">
						</div>
					<?php endforeach; ?>
				</div>
			</div>
	</div>
</div>