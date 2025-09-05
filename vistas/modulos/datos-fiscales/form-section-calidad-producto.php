<?php
	if ( isset($proveedor->razonSocial) ) {

		$tiempoEntrega = isset($old["tiempoEntrega"]) ? $old["tiempoEntrega"] : $proveedor->tiempoEntrega;
		$modalidadEntrega = isset($old["modalidadEntrega"]) ? $old["modalidadEntrega"] : $proveedor->modalidadEntrega;
		$distribuidorAutorizado = isset($old["distribuidorAutorizado"]) ? $old["distribuidorAutorizado"] : $proveedor->distribuidorAutorizado;
		$recursos = isset($old["recursos"]) ? $old["recursos"] : $proveedor->recursos;

	} 
?>

<?php if ($proveedor->idCategoria): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-body">
                    <label for="">Capacidad de suministro</label>
                    <div class="form-group">
                        <label for="tiempoEntrega">Tiempo de entrega:</label>
                        <textarea name="tiempoEntrega" id="tiempoEntrega" rows = "5" class="form-control form-control-sm" 
                        placeholder="***Para productos en stock, garantizamos un tiempo de entrega de 3 a 5 días hábiles. En el caso de productos personalizados o bajo pedido, el tiempo de entrega es de 15 a 20 días hábiles, dependiendo de la complejidad y disponibilidad." 
                        required><?= isset($tiempoEntrega) ? $tiempoEntrega : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="modalidadEntrega">Modalidad en entrega de material</label>
                        <textarea name="modalidadEntrega" id="modalidadEntrega" rows = "5" class="form-control form-control-sm" 
                        placeholder="**Realizamos entregas en nuestras sucursales ubicadas en Cádenas, Veracruz y Coatzacoalcos para mayor comodidad. Además, ofrecemos servicios de entrega a domicilio en zonas urbanas dentro de un radio de 30km desde nuestras sucursales perincipales." 
                        required><?= isset($modalidadEntrega) ? $modalidadEntrega : '' ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="distribuidorAutorizado">¿Es distribuidor autorizado de alguna marca?</label>
                        <textarea name="distribuidorAutorizado" id="distribuidorAutorizado" rows = "5" class="form-control form-control-sm" placeholder="**Nos enorgullece ser distribuidores autorizados de reconocidas marcas entre las cuales están [nombre de marcas], asegurando a nuestros clientes acceso directo a lo mejor del mercado." required><?= isset($distribuidorAutorizado) ? $distribuidorAutorizado : '' ?></textarea>
                    </div>


                    <div class="d-flex align-items-end flex-column">

                        <button type="button" class="btn btn-info btnSubirArchivo" id="15" style="width:fit-content;">
                            <i class="fas fa-folder-open"></i> Cargar Soporte
                        </button>

                        <?php foreach($proveedorArchivos->Soporte as $key=>$cv) : ?>
                            <p class="text-info mb-0"><?php echo $cv['archivo']; ?>
                                <i class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo $cv['ruta']?>" style="cursor: pointer;" data-toggle="modal" data-target="#archivoModal"></i>
                                <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cv['id']; ?>" folio="<?php echo $cv['archivo']; ?>"></i>
                            </p>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-warning card-outline">
                <div class="card-body"> 
                    <div class="form-group">
                        <label for="recursos">Recuersos con los que cuenta:</label>
                        <textarea name="recursos" id="recursos" rows = "5" class="form-control form-control-sm" 
                        placeholder="**Disponemos de maquinaria y equipos especializados para la fabricación y distribuición de nuestros productos, incluyendo sistemas de ensamblaje automatizados, lineas de producción eficientes, equipos de almacenamiento y transporte, así como tecnología de embalaje de última generación. Esto nos permite garantizar la calidad y puntuabilidad en cada entrega." required><?= isset($recursos) ? $recursos: '' ?></textarea>

                        <div class="d-flex align-items-end flex-column">
                            <button type="button" class="btn btn-info mt-2 btnSubirArchivo" id="16">
                                <i class="fas fa-folder-open"></i> Cargar Listado
                            </button>

                            <?php foreach($proveedorArchivos->Listado as $key=>$cv) : ?>
                                <p class="text-info mb-0"><?php echo $cv['archivo']; ?>
                                <i class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo $cv['ruta']?>" style="cursor: pointer;" data-toggle="modal" data-target="#archivoModal"></i>
                                <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cv['id']; ?>" folio="<?php echo $cv['archivo']; ?>"></i>
                                </p>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <hr>
                    <?php if (permisoAsignado($permisosAsignados,"certificaciones")): ?>

                        <div class="d-flex align-items-end flex-column">
                            <button type="button" class="btn btn-info btnSubirArchivo" id="17">
                                <i class="fas fa-folder-open"></i> Cargar Certificaciones
                            </button>
        
                            <?php foreach($proveedorArchivos->certificaciones as $key=>$cv) : ?>
                                <p class="text-info mb-0"><?php echo $cv['archivo']; ?>
                                <i class="ml-1 fas fa-eye text-info verArchivo" archivoRuta="<?php echo $cv['ruta']?>" style="cursor: pointer;" data-toggle="modal" data-target="#archivoModal"></i>
                                <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cv['id']; ?>" folio="<?php echo $cv['archivo']; ?>"></i>
                                </p>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <button type="submit" id="btnSend" class="btn btn-outline-primary">
        <i class="fas fa-save"></i> Actualizar
    </button>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">No tienes una categoría asignada.</h4>
        <p>Habla con un administrador.</p>
    </div>
<?php endif; ?>