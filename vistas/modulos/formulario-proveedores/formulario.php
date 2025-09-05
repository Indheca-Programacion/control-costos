<?php
$documentos = [
    [
        "id" => "constanciaFiscal",
        "label" => "1. Constancia de identificación fiscal actualizada o TAX ID"
    ],
    [
        "id" => "opinionCumplimiento",
        "label" => "2. Opinión de cumplimiento de obligaciones fiscales no mayor a 1 mes"
    ],
    [
        "id" => "comprobanteDomicilio",
        "label" => "3. Comprobante de domicilio no mayor a 3 meses"
    ],
    [
        "id" => "datosBancarios",
        "label" => "4. Carta con datos bancarios, incluyendo clabe, moneda firmada y sellada por su institución bancaria o carátula de estado de cuenta bancaria"
    ]
];

?>

<input type="hidden" name="_token" id="_token" value="<?php echo createToken(); ?>">

<div class="row">
    <!-- RAZÓN SOCIAL -->
	<div class="col-lg-4 form-group d-flex flex-column justify-content-end">
		<label for="">Razón social del Proveedor:</label>
		<input type="text" name="razonSocial" id="razonSocial" class="form-control form-control-sm text-uppercase" >
	</div>
    
    <!-- RFC -->
    <div class="col-lg-4 form-group d-flex flex-column justify-content-end">
		<label for="">RFC:</label>
		<input type="text" name="rfc" id="rfc" class="form-control form-control-sm text-uppercase" >
	</div>

    <!-- CORREO ELECTRONICO DEL VENDEDOR -->
    <div class="col-lg-4 form-group d-flex flex-column justify-content-end">
		<label for="">Correo electronico del vendedor:</label>
		<input type="email" name="correoElectronico" id="correoElectronico" class="form-control form-control-sm " required>
	</div>

    <!-- NOMBRE Y APELLIDOS DEL VENDEDOR -->
    <div class="col-lg-7 form-group d-flex flex-column justify-content-end">
		<label for="">Nombre y apellidos del vendor:</label>    
		<input type="text" name="nombreApellido" id="nombreApellido" class="form-control form-control-sm text-uppercase">
	</div>

    <!-- TELEFONO FIJO Y/O MÓVIL DEL VENDEDOR -->
    <div class="col-lg-5 form-group d-flex flex-column justify-content-end">
		<label for="">Telefono fijo y/o del vendedor, incluir LADA:</label>
		<input type="number" name="telefono" id="telefono" class="form-control form-control-sm text-uppercase">
	</div>

    <!-- ORIGEN DEL PROVEEDOR NACIONAL/INTERNACIONAL -->
    <div class="col-lg-5 form-group d-flex flex-column justify-content-end">
        <label for="origenProveedor">Origen del proveedor Nacional/Internacional:</label>
        <select name="origenProveedor" id="origenProveedor" class="form-control form-control-sm text-uppercase">
            <option value="">Seleccione una opción</option>
            <option value="NACIONAL">Nacional</option>
            <option value="INTERNACIONAL">Internacional</option>
        </select>
    </div>
</div>

<hr>

<div class="row">

    <!-- TIPO DE PROVEEDOR -->
    <div class="col-lg-12 form-group d-flex flex-column justify-content-end">
        <input type="hidden" name="tipoProveedor" value="0">
        <label for="">Tipo de proveedor:</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input tipo-proveedor" type="checkbox" name="tipoProveedor" id="materiales" value="MATERIALES"
                    <?= in_array('MATERIALES', $tipoProveedor ?? []) ? 'checked' : '' ?>>
                <label class="form-check-label" for="materiales">Materiales</label>
            </div>
            <div class="form-check mx-3">
                <input class="form-check-input tipo-proveedor" type="checkbox" name="tipoProveedor" id="servicio" value="SERVICIO"
                    <?= in_array('SERVICIO', $tipoProveedor ?? []) ? 'checked' : '' ?>>
                <label class="form-check-label" for="servicio">Servicio</label>
            </div>
            <div class="form-check">
                <input class="form-check-input tipo-proveedor" type="checkbox" name="tipoProveedor" id="ambos" value="AMBOS"
                    <?= in_array('AMBOS', $tipoProveedor ?? []) ? 'checked' : '' ?>>
                <label class="form-check-label" for="ambos">Ambos</label>
            </div>
        </div>
    </div>

    <!-- CLAVE DEL PROVEEDOR -->
    <div class="col-lg-12 form-group d-flex flex-column justify-content-end">
        <input type="hidden" name="claveProveedor" value="0">
        <label for="">Clabe del proveedor:</label>
        <div class="d-flex gap-3">
            <div class="form-check">
                <input class="form-check-input clave-proveedor" type="checkbox" name="claveProveedor" id="mayorista" value="MAYORISTA"
                    <?= in_array('MAYORISTA', $claveProveedor ?? []) ? 'checked' : '' ?>>
                <label class="form-check-label" for="mayorista">Mayorista</label>
            </div>
            <div class="form-check mx-3">
                <input class="form-check-input  clave-proveedor" type="checkbox" name="claveProveedor" id="distribuidor" value="DISTRIBUIDOR"
                    <?= in_array('DISTRIBUIDOR', $claveProveedor ?? []) ? 'checked' : '' ?>>
                <label class="form-check-label" for="distribuidor">Distribuidor</label>
            </div>
            <div class="form-check">
                <input class="form-check-input  clave-proveedor" type="checkbox" name="claveProveedor" id="fabricante" value="FABRICANTE"
                    <?= in_array('FABRICANTE', $claveProveedor ?? []) ? 'checked' : '' ?>>
                <label class="form-check-label" for="fabricante">Fabricante</label>
            </div>
        </div>
    </div>

    <!-- LUGAR EN EL QUE SE ENTREGA EL MATERIAL O SE PRESTA EL SERVICIO -->
	<div class="col-lg-9 form-group d-flex flex-column justify-content-end">
		<label for="">Lugar en el que se entrega el material o se presta el servicio:</label>
		<input type="text" name="entregaMaterial" id="entregaMaterial" class="form-control form-control-sm text-uppercase">
	</div>

    <!-- DIAS DE CREDITO-->
	<div class="col-lg-3 form-group d-flex flex-column justify-content-end">
		<label for="">Dias de credito:</label>
		<input type="number" name="diasCredito" id="diasCredito" class="form-control form-control-sm text-uppercase" >
	</div>
</div>

<hr>

<div class="row">

	<div class="col-lg-12 form-group d-flex flex-column justify-content-end">
        <h5>
            <b>
                Se solicita agregar la siguiente información
            </b>
        </h5>
	</div>

    <?php foreach ($documentos as $doc): ?>
        <div class="col-12">
            <div class="form-group">
                <label for="<?= $doc['id'] ?>"><?= $doc['label'] ?>:</label>
                <div class="custom-file col-lg-8 ">
                    <input
                        type="file"
                        class="custom-file-input"
                        id="<?= $doc['id'] ?>"
                        name="<?= $doc['id'] ?>"
                        accept="application/pdf"
                    >
                    <?php if (
                        isset($_SESSION['archivos_subidos']) &&
                        isset($_SESSION['archivos_subidos'][$doc['id']])
                    ): ?>
                        <label class="custom-file-label"> 
                            <?= $_SESSION['archivos_subidos'][$doc['id']]["nombre_archivo_original"] ?>
                        </label>
                    <?php else: ?>
                        <label class="custom-file-label" for="<?= $doc['id'] ?>">Seleccionar archivo</label>
                    <?php endif; ?>

                    <?php if (!empty($errores[$doc['id']])): ?>
                        <small class="text-danger"><?= $errores[$doc['id']] ?></small>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- AVISO DE TERMINOS -->
    <!-- Valor por defecto si NO se marca -->
    <div class="col-lg-12 form-group mt-3">
        <input 
            type="hidden"
            id="terminosCondiciones" 
            name="terminosCondiciones" 
            value="0"
        />
        <div class="form-check d-flex align-items-start">
        <input 
            class="mt-1 form-check-input" 
            type="checkbox" 
            id="terminosCondiciones" 
            name="terminosCondiciones" 
            required 
        >
            <label class="form-check-label ml-1" for="terminosCondiciones">
                Acepto y comprendo que todos los datos solicitados en este formulario son de cáracter obligatorito y que por ser información sensible y confidencial se protegerá la privacidad de la información.
            </label>
        </div>
    </div>
</div>

