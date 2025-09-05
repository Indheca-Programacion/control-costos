<?php
	if ( isset($informacionTecnica->id) ) {
		$titulo = isset($old["titulo"]) ? $old["titulo"] : $informacionTecnica->titulo;
		// $archivo = isset($old["archivo"]) ? $old["archivo"] : $informacionTecnica->archivo;
		$archivo = $informacionTecnica->archivo;
		// $formato = isset($old["formato"]) ? $old["formato"] : $informacionTecnica->formato;
		$formato = $informacionTecnica->formato;
		$perfiles = $informacionTecnica->perfiles;
		if ( isset($old) ) $tags = isset($old["tags"]) ? $old["tags"] : array();
		else $tags = $informacionTecnica->tags;
	} else {
		// var_dump($old);
		$titulo = isset($old["titulo"]) ? $old["titulo"] : "";
		// $archivo = isset($old["archivo"]) ? $old["archivo"] : "";
		$archivo = "";
		// $formato = isset($old["formato"]) ? $old["formato"] : "";
		$formato = "";
		$tags = isset($old["tags"]) ? $old["tags"] : array();
		$perfiles = isset($old["perfiles"]) ? $old["perfiles"] : array();
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="titulo">Título:</label>
	<input type="text" id="titulo" name="titulo" value="<?php echo fString($titulo); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa título de la información técnica">
</div>

<div class="form-group">
	<label for="archivo">Archivo:</label>
	<div class="input-group">

		<input type="text" id="archivoActual" value="<?php echo fString($archivo); ?>" class="form-control form-control-sm text-uppercase" placeholder="Selecciona el arhivo" disabled>

		<?php if ( !isset($informacionTecnica->id) ) : ?>
		<div class="input-group-append">
			<button type="button" id="btnSubirArchivo" class="btn btn-sm btn-flat btn-info">
				<i class="fas fa-folder-open"></i> Subir
			</button>
		</div>
		<?php endif; ?>
	</div>
	<?php if ( !isset($informacionTecnica->id) ) : ?>
	<input type="file" class="form-control form-control-sm d-none" id="archivo" name="archivo">
	<span class="text-muted">Archivos permitidos Word, Excel, PDF e Imágenes (con capacidad máxima de 4MB)</span>
	<?php endif; ?>
</div>

<div class="form-group">
	<label for="formato">Formato:</label>
	<input type="text" id="formato" value="<?php echo fString($formato); ?>" class="form-control form-control-sm text-uppercase" disabled>
</div>

<div class="form-group">

	<label for="tags">Tags:</label>
	<select name="tags[]" id="tags" class="custom-select form-controls select2" multiple>
	<?php if ( isset($maquinaria->id) ) : ?>
	<!-- <select id="tags" class="custom-select form-controls select2" style="width: 100%" disabled> -->
	<?php else: ?>
		<!-- <option value="">Selecciona un Tag</option> -->
	<?php endif; ?>
	$informacionTecnicaTags
		<?php foreach($informacionTecnicaTags as $informacionTecnicaTag) { ?>
		<option value="<?php echo $informacionTecnicaTag["id"]; ?>"
			<?php echo in_array($informacionTecnicaTag["id"], $tags) ? ' selected' : ''; ?>
			><?php echo mb_strtoupper(fString($informacionTecnicaTag["descripcion"])); ?>
		</option>
		<?php } ?>
	</select>

</div>

<div class="form-group">

	<label for="perfiles">Perfiles pueden visualizar:</label>
	<select name="perfiles[]" id="perfiles" class="custom-select form-controls select2" multiple>
		<?php foreach($perfilesVisualizar as $perfil) { ?>
		<option value="<?php echo $perfil["id"]; ?>"
			<?php echo in_array($perfil["id"], $perfiles) ? ' selected' : ''; ?>
			><?php echo mb_strtoupper(fString($perfil["nombre"])); ?>
		</option>
		<?php } ?>
	</select>

</div>
