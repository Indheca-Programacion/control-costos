<?php

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF2 extends TCPDF {
	public $logo;
	public $empresaId;
	public $empresa;
	public $folio;
	public $fechaCreacion;

	//Page header
	public function Header() {
		
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen

        // Logo
        
        $this->Image($this->logo, 10, 10, 56, 11, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
    
        $this->setCellPaddings(1, 1, 1, 1); // set cell padding

        // Title
        // $this->Rect(70, 5, 95, 11, 'D', array(), array(222,222,222));
        $this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
        $this->SetTextColor(0, 0, 0); // Color del texto
        $this->SetFillColor(165, 164, 157); // Color de fondo
        $this->MultiCell(65, 11, "SISTEMA DE GESTION INTEGRAL", 'LR', 'C', 0, 1, 80, 10, true);

        $this->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
        $this->SetTextColor(36, 64, 96); // Color del texto
        $this->MultiCell(60, 11, "ISO 9001 | ISO 14001 | ISO 45001", 0, 'C', 0, 0, 80, 15, true);

        $this->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
        $this->MultiCell(35, 11, "FO-IGC-P4-08.03 \n REV 09", 0, 'C', 0, 1, 165, 10, true);

        
	}


	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

function generarPDFOrdenes($datos) {
    // Carpeta donde se guardarán los PDFs
    $carpetaDestino = '/tmp/';

    // Crear la carpeta si no existe
    if (!is_dir($carpetaDestino)) {
        mkdir($carpetaDestino, 0777, true);
    }

    $rutasArchivos = []; // Almacenar rutas de los archivos generados

    foreach ($datos as $orden) {

        if ( file_exists ( "app/Models/Requisicion.php" ) ) {
            require_once "app/Models/Requisicion.php";
            require_once "app/Models/Empresa.php";
            require_once "app/Models/Divisa.php";
            require_once "app/Models/Proveedor.php";
            require_once "app/Models/Obra.php";
            require_once "app/Models/Usuario.php";
            require_once "app/Models/DatosBancarios.php";

        } else {
            require_once "../Models/Requisicion.php";
            require_once "../Models/Empresa.php";
            require_once "../Models/Divisa.php";
            require_once "../Models/Proveedor.php";
            require_once "../Models/Obra.php";
            require_once "../Models/Usuario.php";
            require_once "../Models/DatosBancarios.php";
        }

     
        $datosBancarios = New \App\Models\DatosBancarios;
        $datosBancarios->consultar(null, $orden["datoBancarioId"]);

        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null, $orden["EMPRESA_ID"]);

        $divisa = New \App\Models\Divisa;
        $divisa->consultar(null, $orden["monedaId"]);

        $proveedor = New \App\Models\Proveedor;
        $proveedor->consultar(null, $orden["proveedorId"]);

        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $orden["usuarioIdCreacion"]);

        $usuarioNombre = mb_strtoupper($usuario->nombre);
        $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
        if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
        $solicitoFirma = $usuario->firma;
        unset($usuario);

        $reviso = '';
        $revisoFirma = null;
        
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $orden["usuarioIdAutorizacion"]);

        $reviso = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
        if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
        $revisoFirma = $usuario->firma;
        unset($usuario);

        $almacenResponsable = '';
        $almacenFirma = null;

        if ( !is_null($orden["usuarioIdAprobacion"]) ) {
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $orden["usuarioIdAprobacion"]);

            $almacenResponsable = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $almacenResponsable .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $almacenFirma = $usuario->firma;
            unset($usuario);
        }


    // create new PDF document
    $pdf = new MYPDF2(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
    else $pdf->logo = Route::rutaServidor().$empresa->imagen;

    $pdf->empresaId = $empresa->id;
    $pdf->empresa = mb_strtoupper(fString($empresa->razonSocial, 'UTF-8'));
    $pdf->folio = mb_strtoupper(fString($orden["id"]));
    $pdf->fechaCreacion = $orden["fechaCreacion"];

    // set document information
    $pdf->setTitle("OC ".$orden["folio"]);
    // remove default header/footer
    // $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default header data
    $pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

    // set default monospaced font
    $pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->setMargins(10, PDF_MARGIN_TOP, 10);
    $pdf->setHeaderMargin(PDF_MARGIN_HEADER);
    // $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    // $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setAutoPageBreak(TRUE, 5);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // ---------------------------------------------------------

    $pdf->setCellPaddings(1, 1, 1, 1); // set cell padding
    // $this->setCellMargins(1, 1, 1, 1); // set cell margins

    $pdf->AddPage(); // Agregar nueva página

    $pdf->ln(5); // Salto de Línea

    $pdf->SetTextColor(0, 0, 0); // Color del texto
    $pdf->SetFillColor(222, 222, 222); // Set background color to gray
    $pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(190, 3, "ORDEN DE COMPRA", 0, 'C', 1, 1, 10, 22, true);

    $pdf->SetFont('helvetica', '', 7.5); // Fuente, Tipo y Tamaño

    $fecha = strtotime($orden["fechaCreacion"]);

    $diaSemana = fNombreDia(date("w", $fecha));
    $dia = date("d", $fecha);
    $mes = fNombreMes(date("n", $fecha));
    $year = date("Y", $fecha);

    $almacen = mb_strtoupper(fString($obra->almacen ?? '109'));

    $folioOC = $orden["reposicion_gastos"] == 1 ? "RG".$orden["folio"] : $orden["folio"];
    $pdf->MultiCell(35, 3, "FOLIO OC", 0, 'R', 0, 0, 5, '', true);
    $pdf->MultiCell(45, 3, $orden["folio"], 'B', 'C', 0, 0, '', '', true);

    $pdf->MultiCell(35, 3, "FOLIO RQ", 0, 'R', 0, 0, 120, '', true);
    $pdf->MultiCell(45, 3, "{$orden["REQUISICIONES_ID"]}", 'B', 'C', 0, 1, '', '', true);

    $pdf->MultiCell(32, 3, "FECHA DE ELABORACION:", 0, '', 0, 0, 10, '', true, 0, false, true, '5', 'M', 1);
    $pdf->MultiCell(40, 3, "{$diaSemana}, {$dia} de {$mes} de {$year}", 'B', 'C', 0, 0, '', '', true, 0, false, true, '5', 'M', 1);

    $fecha = fFechaLarga($orden["fechaRequerida"]);
    $razon = mb_strtoupper(fString($proveedor->nombreCompleto ?? $proveedor->razonSocial));

    $pdf->MultiCell(38, 3, "FECHA QUE SE REQUIERE:", 0, '', 0, 0, '', '', true);
    $pdf->MultiCell(40, 3, "{$fecha}", 'B', 'C', 0, 0, '', '', true);

    $pdf->MultiCell(24, 3, "NUM. ALMACEN:", 0, '', 0, 0, '', '', true);
    $pdf->MultiCell(16, 3, "{$almacen}", 'B', 'C', 0, 1, '', '', true);

    $pdf->MultiCell(23, 3, "RAZÓN SOCIAL:", 0, '', 0, 0, 10, '', true);
    $pdf->MultiCell(167, 3, "{$razon}", 'B', 'L', 0, 1, '', '', true);

    $pdf->MultiCell(22, 3, "NUMERO/RFC:", 0, '', 0, 0, 10, '', true);
    $pdf->MultiCell(35, 3, "$proveedor->rfc", 'B', 'C', 0, 0, '', '', true);

    $pdf->MultiCell(8, 3, "TEL:", 0, '', 0, 0, 90, '', true);
    $pdf->MultiCell(30, 3, "{$proveedor->telefono}", 'B', 'C', 0, 0, '', '', true);

    $pdf->MultiCell(12, 3, "E-MAIL:", 0, '', 0, 0, 140, '', true);
    $pdf->MultiCell(48, 3, "{$proveedor->correo}", 'B', 'C', 0, 1, '', '', true, 0, false, true, '5', 'M', 1);

    $pdf->MultiCell(23, 3, "DOMICILIO:", 0, '', 0, 0, 10, '', true);
    $pdf->MultiCell(167, 3, mb_strtoupper("$proveedor->domicilio"), 'B', 'L', 0, 1, '', '', true);

    $pdf->ln(2); // Salto de Línea

    $pdf->SetTextColor(255, 255, 255); // Color del texto
    $pdf->SetFillColor(128, 128, 128); // Set background color to gray
    $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(0, 3, "DATOS DEL EQUIPO", 0, 'C', 1, 1, '', '', true);
    $equipo = "";
    $pdf->MultiCell(0, 5, "$equipo", 0, 'C', 0, 1, '', '', true);

    $pdf->ln(2); // Salto de Línea

    $pdf->SetTextColor(0, 0, 0); // Color del texto

    $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(12, 7, "PTDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(10, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(18, 7, "CODIGO \n PRODU C", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(70, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(20, 7, "NUM. P \n ARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1 );
    $pdf->MultiCell(20, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(20, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M',1);

    $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
    $subtotal = 0;
    foreach($orden["partidas"] as $key => $partida) {
        $cantidad = number_format($partida['cantidad'], 2);
        $unidad = mb_strtoupper($partida['unidad']);
        $concepto = mb_strtoupper($partida['descripcion'].' | '.$partida['concepto']);
        $numeroParte = mb_strtoupper($partida['numeroParte']?? 'NA') ;
        $pu = number_format($partida['importeUnitario'],2);
        $costo = number_format(($partida['importeUnitario']*$partida['cantidad']),2) ;
        $codigo = mb_strtoupper($partida['codigo']?? 'NA') ;

        $partidaId = $key + 1;

        $y_start = $pdf->GetY();

        $pdf->MultiCell(70, 0, "{$concepto}", 1, 'C', 0, 1, 70, '', true, 0);
        $y_end = $pdf->GetY();
        $altoFila = $y_end - $y_start;
        $num_detalle = count($orden["partidas"]);
        $pdf->MultiCell(12, $altoFila, "{$partidaId}", 1, 'C', 0, 0, 10, $y_start, true, 0, false, true, $altoFila, 'M');
        $pdf->MultiCell(10, $altoFila, "{$cantidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
        $pdf->MultiCell(20, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
        $pdf->MultiCell(18, $altoFila, "{$codigo}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
        $pdf->MultiCell(20, $altoFila, "{$numeroParte}", 1, 'C', 0, 0, 140, '', true, 0, false, true, $altoFila, 'M');
        $pdf->MultiCell(20, $altoFila, "{$pu}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
        $pdf->MultiCell(20, $altoFila, "$ {$costo}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');

        if ( $y_end > 270 ) {
            $pdf->AddPage();

            $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
            $pdf->MultiCell(12, 7, "PTDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
            $pdf->MultiCell(10, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(18, 7, "CODIGO \n PRODU C", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
            $pdf->MultiCell(70, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "NUM. P \n ARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1 );
            $pdf->MultiCell(20, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M',1);

            $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
        }

    }

    $pdf->Ln(2); // Salto de Línea

    $pdf->SetTextColor(0, 128, 255); // Set text color to blue
    $pdf->SetFillColor(245, 245, 245); // Set background color to gray

    $pdf->MultiCell(53, 5, "Retención I.V.A.:", 0, 'R', 1, 0, '10', '', true, 0, false, true, '5', 'M');
    $pdf->SetTextColor(0, 0, 0); // Set text color to black
    $pdf->MultiCell(10, 5, $orden["retencionIva"], 0, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');

    $pdf->SetTextColor(0, 128, 255); // Set text color to blue
    $pdf->MultiCell(33, 5, "Retención I.S.R.:", 0, 'R', 1, 0, '73', '', true, 0, false, true, '5', 'M');
    $pdf->SetTextColor(0, 0, 0); // Set text color to black
    $pdf->MultiCell(40, 5, $orden["retencionIsr"], 0, 'L', 1, 0, '', '', true, 0, false, true, '5', 'M');

    $pdf->SetTextColor(0, 128, 255); // Set text color to blue
    $pdf->MultiCell(32, 5, "Subtotal", 0, 'L', 1, 0, '136', '', true, 0, false, true, '5', 'M');
    $pdf->SetTextColor(0, 0, 0); // Set text color to blue
    $pdf->MultiCell(32, 5, $orden["subtotal"], 0, 'R', 1, 1, '', '', true, 0, false, true, '5', 'M');
    $pdf->SetTextColor(0, 128, 255); // Set text color to blue
    $pdf->MultiCell(143, 5, "Descuentos", 0, 'R', 1, 0, '', '', true, 0, false, true, '5', 'M');
    $descuentos = $orden["descuento"];
    $pdf->SetTextColor(0, 0, 0); // Set text color to blue
    $pdf->MultiCell(0, 5, "{$descuentos}", 0, 'R', 1, 1, '', '', true, 0, false, true, '5', 'M');
    $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño

    $pdf->SetFillColor(128, 128, 128); // Set background color to gray
    $pdf->SetTextColor(255, 255, 255); // Set text color to blue
    $pdf->MultiCell(120, 5, "Importe con letra", 0, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');

    $pdf->SetFillColor(245, 245, 245); // Set background color to gray
    $pdf->SetTextColor(0, 128, 255); // Set text color to blue
    $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

    $pdf->MultiCell(15, 5, "I.V.A.", 0, 'R', 1, 0, 130, '', true, 0, false, true, '5', 'M');
    $pdf->SetTextColor(0, 0, 0); // Set text color to blue
    $pdf->MultiCell(0, 5, $orden["iva"], 0, 'R', 1, 1, '', '', true, 0, false, true, '5', 'M');

    $totalEnLetra = numeroALetras($orden["total"], $divisa->descripcion, $divisa->nombreCorto);
    $pdf->MultiCell(120, 5, "$totalEnLetra", 0, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');

    $pdf->SetTextColor(0, 128, 255); // Set text color to blue
    $pdf->MultiCell(40, 5, "Total", 0, 'R', 1, 0, 130, '', true, 0, false, true, '5', 'M');
    $pdf->SetTextColor(0, 0, 0); // Set text color to blue
    $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
    $total = formatMoney($orden["total"]);
    $pdf->MultiCell(30, 5, "$total", 'B', 'R', 1, 1, '', '', true, 0, false, true, '5', 'M');

    $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
    $pdf->Ln(); // Salto de Línea

    $pdf->SetTextColor(0, 0, 0); // Set text color to blue


    $pdf->SetLineWidth(0.4); // Set line width for thicker borders

    $especificaciones = mb_strtoupper(fString($orden["especificaciones"] ?? ''));
    $pdf->MultiCell(48, 5, "ESPECIFICACIONES ADJUNTAS:", '', '', 1, 0, '10', '', true, 0, false, true, '5', 'M');
    $pdf->MultiCell(0, 5, "$especificaciones", 'B', '', 1, 1, '', '', true, 0, false, true, '5', 'M');

    $pdf->MultiCell(40, 5, "DIRECCION DE ENTREGA:", 0, '', 1, 0, '10', '', true, 0, false, true, '5', 'M');
    $direccionEntrega = mb_strtoupper(fString($orden["direccion"] ?? ''));
    $pdf->MultiCell(0, 5, "$direccionEntrega", 'B', '', 1, 1, '', '', true, 0, false, true, '5', 'M',1);

    $pdf->MultiCell(40, 5, "CONDICIONES DE PAGO:", 0, '', 1, 0, '10', '', true, 0, false, true, '5', 'M');
    switch ($orden["condicionPagoId"]) {
        case 1:
            $condicionPago = 'CONTADO';
            break;
        case 2:
            $condicionPago = '30 DIAS';
            break;
        case 3:
            $condicionPago = 'CREDITO';
            break;
        case 4:
            $condicionPago = 'CREDITO 15 DIAS';
            break;
        case 5:
            $condicionPago = 'OTRO - ANTICIPO A CONTRA ENTREGA';
            break;
        default:
            $condicionPago = '';
            break;
    }
    $pdf->SetLineWidth(0.2); // Reset line width to default
    $pdf->MultiCell(0, 5, "$condicionPago", 'B', '', 1, 1, '', '', true, 0, false, true, '5', 'M');

    $pdf->SetLineWidth(0.2); // Reset line width to default

    $justificacion = mb_strtoupper(fString($requisicion->justificacion ?? 'NA'));

    $tipoRequisicion = mb_strtoupper(fString($requisicion->tipoRequisicion ?? 0));
    if ( $tipoRequisicion == 0 ) {
        $tipoRequisicion = "Programada para {$fecha}";
    }else{
        $tipoRequisicion = "Urgente";
    }

    $pdf->ln(2); // Salto de Línea
    $pdf->SetLineWidth(0.4); // Set line width
    $yRect = $pdf->getY();
    $pdf->Rect(10, $yRect, 90, 15, 'D', array(), array(222,222,222));
    $pdf->Rect(100,  $yRect, 100, 15, 'D', array(), array(222,222,222));
    $pdf->SetLineWidth(0.2); // Reset line width to default

    $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(100, 5, "JUSTIFICACIÓN:", 0, '', 0, 0, '10', '', true, 0, false, true, '5', 'M');
    $pdf->MultiCell(100, 5, "TIPO DE REPARACION / TIPO DE RQ: PROGRAMADA Ó URGENTE:", 0, '', 0, 1, 100, '', true, 0, false, true, '5', 'M');
    $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(90, 11, "{$justificacion}", 0, 'C', 0, 0, '', '', true, 0, false, true, '11', 'M', 1);

    $pdf->MultiCell(100, 12, "$tipoRequisicion \n ".$orden["tiempoEntrega"], 0, 'C', 0, 1, 100, '', true, 0, false, true, '12', 'M');

    $pdf->SetLineWidth(0.4); // Set line width
    $yRect = $pdf->getY();
    $pdf->Rect(10, $yRect, 50, 23, 'D', array(), array(222,222,222));
    $pdf->Rect(60, $yRect, 60, 23, 'D', array(), array(222,222,222));
    $pdf->Rect(120, $yRect, 80, 23, 'D', array(), array(222,222,222));
    $pdf->SetLineWidth(0.2); // Reset line width to default

    $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(50, 5, "CONTRATO, ORDEN DE TRABAJO,", 0, '', 0, 0, '10', '', true, 0, false, true, '5', 'M');
    $pdf->MultiCell(60, 5, "RECEPCION DEL MATERIAL O SERVICIO:", 0, '', 0, 0, '', '', true, 0, false, true, '5', 'M');
    $pdf->MultiCell(100, 5, "RECEPCION DE COMPROBANTE FISCAL:", 0, '', 0, 1, '', '', true, 0, false, true, '5', 'M');
    $pdf->SetFont('helvetica', '', 6.5); // Fuente, Tipo y Tamaño
    $contrato = mb_strtoupper(fString(!is_null($orden["OBRA_DESCRIPCION"]) ? $orden["OBRA_DESCRIPCION"] : '109. mantenimiento correctivo general'));
    $yRect = $pdf->getY();

    $pdf->MultiCell(50, 12, "{$contrato}", 0, 'C', 0, 0, '10', $yRect, true, 0, false, true, '12', 'M');
    // $pdf->Line(10, 247, 100, 247, false);
    $pdf->MultiCell(60, '', " Entregar en forma impresa: \n Factura o remisión            Orden de compra autorizada \n Certificado de origen         Hojas de seguridad \n Certificado de Calidad       Ficha Técnica \n Garantía                            Estimaciones Autorizadas", 0, 'L', 0, 0, '', '', true, 0, false, true);
    $ruta = Route::rutaServidor();
    $pdf->MultiCell(80, '5', "El CFDI debe ingresarse en PDF y XML en el enlace: \n$ruta \nAnexar factura o remision firmados de aceptación, órden de compra autorizada, fichas técnicas, certificados, garantías, hojas de seguridad,  manifiestos, estimaciones autorizadas.", 0, 'L', 0, 1, '', '', true, 0, false, true);

    $y = $pdf->getY()+4;
    $pdf->SetFillColor(239, 248, 255); // Set background color to gray
    $pdf->MultiCell(20, 20, "FACTURAR A:", 0, '', 1, 0, '10', $y, true, 0, false, true, '5', 'M');
    $pdf->MultiCell(60, 20, "INDHECA GRUPO CONSTRUCTOR \nR.F.C.: IGC050407LA9 \nCalle Jazmín #35, Col. Benito \nJuárez, Xalapa, Veracruz CP. 91056", 0, '', 1, 0, '', '', true, 0, false, true, '20', 'M');
    $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(0, 5, "No de Cta del Proveedor:", 0, '', 1, 1, '', '', true, 0, false, true, '5', 'M');
    $pdf->SetFont('helvetica', '', 6.5); // Fuente, Tipo y Tamaño
    $nombreBanco = mb_strtoupper(fString($datosBancarios->nombreBanco ?? ''));
    $cuenta = mb_strtoupper(fString($datosBancarios->numeroCuenta ?? ''));
    $clabe = mb_strtoupper(fString($datosBancarios->cuentaClave ?? ''));
    $pdf->MultiCell(0, 15, "{$nombreBanco} \nCuenta {$cuenta} \nClabe {$clabe} ", 0, '', 1, 1, 90, '', true, 0, false, true, '15', 'M');

    $y = $pdf->getY();

    if( $y > 260 ) {
        $pdf->AddPage(); // Agregar nueva página si se excede el límite
        $y = 10; // Reiniciar la posición Y
    }
    $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
    if ( !is_null($solicitoFirma) ) {
        $extension = mb_strtoupper(substr($solicitoFirma, -3, 3));
        if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

        $pdf->Image('../../'.$solicitoFirma, 5, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
    }

    if ( !is_null($almacenFirma) ) {
        $extension = mb_strtoupper(substr($almacenFirma, -3, 3));
        if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

        $pdf->Image('../../'.$almacenFirma, 70, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
    }

    if ( !is_null($revisoFirma) ) {
        $extension = mb_strtoupper(substr($revisoFirma, -3, 3));
        if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

        $pdf->Image('../../'.$revisoFirma, 130, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
    }

    $solicito = mb_strtoupper(fString($solicito));
    $almacenResponsable = mb_strtoupper(fString($almacenResponsable));
    $reviso = mb_strtoupper(fString($reviso));


    $pdf->SetFont('helvetica', '', 10); // Fuente, Tipo y Tamaño
    $pdf->Ln(5); // Salto de Línea

    $pdf->Ln(12); // Salto de Línea
    $pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
    // $pdf->Line(15, 278, 95, 278, false);
    $pdf->MultiCell(50, 5, "{$solicito}", 'B', 'C', 0, 0, 10, '', true, 0, false, true, '5', 'M', 1);

    $pdf->MultiCell(55, 5, "{$almacenResponsable}", 'B', 'C', 0, 0, 75, '', true, 0, false, true, '5', 'M', 1);

    $pdf->MultiCell(55, 5, "{$reviso}", 'B', 'C', 0, 1, 140, '', true , 0, false, true, '5', 'M', 1);

    $pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
    $pdf->MultiCell(66, 5, "ELABORA", 0, 'C', 0, 0, 5, '', true);

    $pdf->MultiCell(66, 5, "APRUEBA", 0, 'C', 0, 0, '', '', true);

    $pdf->MultiCell(66, 5, "AUTORIZA", 0, 'C', 0, 1, '', '', true);

        // Definir y guardar el archivo PDF
        $fileName = "orden_compra_{$orden['id']}_" . time() . ".pdf";
        $filePath = $carpetaDestino . $fileName;
        $pdf->Output($filePath, 'F'); // Guarda en el servidor

        $rutasArchivos[] = $filePath; // Guardar la ruta en el array
    }

    return $rutasArchivos;
}

