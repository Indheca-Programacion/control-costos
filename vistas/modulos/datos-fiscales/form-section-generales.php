<?php
	if ( isset($proveedor->razonSocial) ) {

		$empresa = isset($old["empresa"]) ? $old["empresa"] : $proveedor->razonSocial;
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $proveedor->nombre;
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : $proveedor->apellidoPaterno;
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : $proveedor->apellidoMaterno;

		$telefono = isset($old["telefono"]) ? $old["telefono"] : $proveedor->telefono;
		$correo = isset($old["correo"]) ? $old["correo"] : $proveedor->correo;

		$condicionContado = isset($old["condicionContado"]) ? $old["condicionContado"] : $proveedor->condicionContado;
		$condicionCredito = isset($old["condicionCredito"]) ? $old["condicionCredito"] : $proveedor->condicionCredito;
		$ubicacion = isset($old["ubicacion"]) ? $old["ubicacion"] : $proveedor->ubicacion;
        $tags = $proveedor->tags;

        $informacionTecnicaTags = $tagProveedores;
	} 
?>

<div class="row">
    <div class="col-md-6">
        <div class="card card-info card-outline">
            <div class="card-body">
                <div class="form-group">
                    <label for="empresa">Empresa:</label>
                    <input type="text" class="form-control form-control-sm" <?php echo $empresa ? 'disabled' :''?>  value="<?php echo $empresa?>"  id="empresa" name="empresa" placeholder="Nombre de la empresa" required>
                </div>
                <div class="form-group">
                    <button type="button" class="btn btn-info btnSubirArchivo" id="1">
                        <i class="fas fa-folder-open"></i> Cargar CV Empresarial
                    </button>

                    <?php foreach($datoFiscalArchivos->cv as $key=>$cv) : ?>
                    <p class="text-info mb-0"><?php echo $cv['archivo']; ?>
                    <i class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo $cv['ruta']?>" style="cursor: pointer;" data-toggle="modal" data-target="#archivoModal"></i>
                    <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cv['id']; ?>" folio="<?php echo $cv['archivo']; ?>"></i>
                    </p>
                    <?php endforeach; ?>

                    <input type="file" class="form-control form-control-sm d-none" id="archivo" multiple>
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control form-control-sm" id="nombre" name="nombre"  value="<?php echo $nombre?>"  placeholder="Nombre del contacto" required>
                </div>

                <div class="form-group">
                    <label for="apellidoPaterno">Apellido Paterno:</label>
                    <input type="text" class="form-control form-control-sm" id="apellidoPaterno" name="apellidoPaterno"  value="<?php echo $apellidoPaterno?>"  placeholder="Apellido Paterno del contacto" required>
                </div>

                <div class="form-group">
                    <label for="apellidoMaterno">Apellido Materno:</label>
                    <input type="text" class="form-control form-control-sm" id="apellidoMaterno" name="apellidoMaterno"  value="<?php echo $apellidoMaterno?>"  placeholder="Apellido Materno del contacto" required>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" class="form-control form-control-sm" id="telefono" name="telefono"  value="<?php echo $telefono?>"  
                        placeholder="Teléfono del contacto" required>
                    </div>
                    
                    <div class="col-md-6 form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" class="form-control form-control-sm" id="correo" name="correo"  value="<?php echo $correo?>" 
                         placeholder="Correo del contacto" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 form-group">
                        <label for="ofertados">Productos O Servicios Ofertados:</label>

                        <select name="tags[]" id="tags" class="custom-select form-controls select2" multiple>
                            <?php if (!isset($proveedor->id)) : ?>
                                <option value="">Selecciona un Tag</option>
                            <?php endif; ?>

                            <?php foreach($informacionTecnicaTags as $informacionTecnicaTag) { ?>
                                <option value="<?php echo $informacionTecnicaTag["id"]; ?>"
                                    <?php echo in_array($informacionTecnicaTag["id"], $tags) ? ' selected' : ''; ?>>
                                    <?php echo mb_strtoupper(fString($informacionTecnicaTag["descripcion"])); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 form-group">
                        <button type="button" class="btn btn-info btnSubirArchivo" id="2">
                            <i class="fas fa-folder-open"></i> Contrato / Factura / OC 1
                        </button>

                        <?php foreach($datoFiscalArchivos->contrato_factura_oc1 as $key=>$cv) : ?>
                        <p class="text-info mb-0"><?php echo $cv['archivo']; ?>
                        <i class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo $cv['ruta']?>" style="cursor: pointer;" data-toggle="modal" data-target="#archivoModal"></i>
                        <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cv['id']; ?>" folio="<?php echo $cv['archivo']; ?>"></i>
                        </p>
                        <?php endforeach; ?>

                    </div>
                    <div class="col-md-4 form-group">
                        <button type="button" class="btn btn-info btnSubirArchivo" id="3">
                            <i class="fas fa-folder-open"></i> Contrato / Factura / OC 2
                        </button>

                        <?php foreach($datoFiscalArchivos->contrato_factura_oc2 as $key=>$cv) : ?>
                        <p class="text-info mb-0"><?php echo $cv['archivo']; ?>
                        <i class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo $cv['ruta']?>" style="cursor: pointer;" data-toggle="modal" data-target="#archivoModal"></i>
                        <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cv['id']; ?>" folio="<?php echo $cv['archivo']; ?>"></i>
                        </p>
                        <?php endforeach; ?>

                    </div>
                    <div class="col-md-4 form-group">
                        <button type="button" class="btn btn-info btnSubirArchivo" id="4">
                            <i class="fas fa-folder-open"></i> Contrato / Factura / OC 3
                        </button>

                        <?php foreach($datoFiscalArchivos->contrato_factura_oc3 as $key=>$cv) : ?>
                        <p class="text-info mb-0"><?php echo $cv['archivo']; ?>
                        <i class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo $cv['ruta']?>" style="cursor: pointer;" data-toggle="modal" data-target="#archivoModal"></i>
                        <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cv['id']; ?>" folio="<?php echo $cv['archivo']; ?>"></i>
                        </p>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-body">
                <div class="form-group">
                    <label for="condiciones">Condiciones Pago de Contado</label>
                    <textarea name="condicionContado" id="condicionContado" rows = "5" class="form-control form-control-sm" 
                    placeholder="Condiciones de pago de contado" required > <?= isset($proveedor->condicionContado) ? $condicionContado : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="condiciones">Condiciones Credito</label>
                    <textarea name="condicionCredito" id="condicionCredito" rows = "5" class="form-control form-control-sm" 
                    placeholder="Condiciones de pago a credito" required><?= isset($proveedor->condicionCredito) ? $condicionCredito : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label for="ubicacion">Ubicacion Operativa/Mostrador de la empresa</label>
                    <textarea name="ubicacion" id="ubicacion" rows = "5" class="form-control form-control-sm" 
                    placeholder="Ubicacion de la empresa" required><?= isset($proveedor->ubicacion) ? $ubicacion : '' ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

<button type="submit" id="btnSend" class="btn btn-outline-primary">
    <i class="fas fa-save"></i> Actualizar
</button>