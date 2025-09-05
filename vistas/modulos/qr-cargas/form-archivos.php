

    <input type="hidden" name="_token" value="<?php echo createToken(); ?>">
    <input type="hidden" name="nId01Qr" id="idQrCarga" value="<?php echo $qrCargas->id ?>">
    <input type="hidden" name="idMaquinaria" id="idMaquinaria" value="<?php echo $idMaquinaria?>">

    <div class="form-group col-md-6 col-xl-12  d-flex flex-column subir-evidencias">
					
        <label for="evidenciaCarga">Evidencia de la Carga:</label>
                    
        <button type="button" class="btn btn-info" id="btnSubirEvidencia"
            style="width:fit-content;">
            <i class="fas fa-folder-open"></i> Cargar Archivos
        </button>
    
        <?php foreach($qrCargas->evidencias as $key=>$evidencia) : ?>
        <p class="text-info mb-0"><?php echo $evidencia['archivo']; ?>
        <i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $evidencia['ruta']?>" style="cursor: pointer;" ></i>
            <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $evidencia['id']; ?>" folio="<?php echo $evidencia['archivo']; ?>"></i>
        </p>
        <?php endforeach; ?>
    
        <span class="lista-archivos">
        </span>
    
        <input type="file" class="form-control form-control-sm d-none" id="evidenciaArchivos" multiple accept="image/*">

            
        <?php if(empty($qrCargas->evidencias)) :     ?>
        <div class="text-muted text-red mt-1">*La evidencia es obligatoria</div>
        <?php endif; ?>
        
        <div class="text-muted ">Archivos permitidos PNG, JPG, JPEG (con capacidad máxima de 4MB)</div>
                        
    </div>                  


    <div class="form-group col-md-6 col-xl-12 d-flex flex-column subir-verificacion">
					
        <label for="evidenciaCarga">Verificación:</label>
                    
        <button type="button" class="btn btn-info" id="btnSubirVerificacion"
            style="width:fit-content;">
            <i class="fas fa-folder-open"></i> Cargar Archivos
        </button>
    
        <?php foreach($qrCargas->verificaciones as $key=>$verificacion) : ?>
        <p class="text-info mb-0"><?php echo $verificacion['archivo']; ?>
        <i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $verificacion['ruta']?>" style="cursor: pointer;" ></i>
            <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $verificacion['id']; ?>" folio="<?php echo $verificacion['archivo']; ?>"></i>
        </p>
        <?php endforeach; ?>
    
        <span class="lista-archivos">
        </span>

        <input type="file" class="form-control form-control-sm d-none" id="verificacionArchivos" multiple accept="application/pdf,image/*">
    
        <div class="text-muted mt-1">Archivos permitidos PNG, JPG, JPEG (con capacidad máxima de 4MB)</div>
                        
    </div>           
    
    <div class="form-group col-md-6 col-xl-12 d-flex flex-column subir-tarjeta-circulacion">
                        
        <label for="tarjetaCirculacion">Tarjeta de Circulación:</label>
                    
        <button type="button" class="btn btn-info" id="btnSubirTarjetaCirculacion"
            style="width:fit-content;">
            <i class="fas fa-folder-open"></i> Cargar Archivos
        </button>
    
        <?php foreach($qrCargas->tarjetasCirculacion as $key=>$tarjetaCirculacion) : ?>
        <p class="text-info mb-0"><?php echo $tarjetaCirculacion['archivo']; ?>
        <i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $tarjetaCirculacion['ruta']?>" style="cursor: pointer;" ></i>
            <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $tarjetaCirculacion['id']; ?>" folio="<?php echo $tarjetaCirculacion['archivo']; ?>"></i>
        </p>
        <?php endforeach; ?>
    
        <span class="lista-archivos">
        </span>
    
        <input type="file" class="form-control form-control-sm d-none" id="tarjetaCirculacionArchivos" multiple accept="application/pdf,image/*">
    
        <div class="text-muted mt-1">Archivos permitidos PDF, PNG, JPEG Y JPG (con capacidad máxima de 4MB)</div>
                        
    </div>

    <div class="form-group col-md-6 col-xl-12 d-flex flex-column subir-acuerdo">
					
        <label for="evidenciaCarga">Acuerdos:</label>
                    
        <button type="button" class="btn btn-info" id="btnSubirAcuerdo"
            style="width:fit-content;">
            <i class="fas fa-folder-open"></i> Cargar Archivos
        </button>
    
        <?php foreach($qrCargas->acuerdos as $key=>$acuerdo) : ?>
        <p class="text-info mb-0"><?php echo $acuerdo['archivo']; ?>
        <i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $acuerdo['ruta']?>" style="cursor: pointer;" ></i>
            <i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $acuerdo['id']; ?>" folio="<?php echo $acuerdo['archivo']; ?>"></i>
        </p>
        <?php endforeach; ?>
    
        <span class="lista-archivos">
        </span>
    
        <input type="file" class="form-control form-control-sm d-none" id="acuerdoArchivos" multiple accept="application/pdf">
    
        <div class="text-muted mt-1">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>
                        
    </div>

