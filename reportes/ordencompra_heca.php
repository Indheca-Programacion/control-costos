<?php
//============================================================+
// File name   : requisicion.php
// Description : Formato de Requisición
//============================================================+

// Image method signature:
// Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)

//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')

// MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)

// Include the main TCPDF library (search for installation path).
require_once "vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
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
        
        $this->Image($this->logo, 0, 10, 60, 20, $extension, '', '', false, 400, '', false, false, 0, 'CM', false, false);
    
        $this->setCellPaddings(1, 1, 1, 1); // set cell padding

        // Title
        $this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
        $this->SetTextColor(0, 0, 0); // Color del texto
        $this->SetFillColor(165, 164, 157); // Color de fondo
        $this->MultiCell(90, 11, "ORDEN DE COMPRA DE MATERIALES, SERVICIOS Y REFACCIONES", '', '', 0, 1, 60, 10, true);

        $this->SetDrawColor(149, 55, 53); // Color del borde derecho
        $this->SetLineWidth(0.8); // Grosor de línea
        $this->MultiCell(35, 11, "FO-GIH-OP-01.02 \nREV. 02", 'R', '', 0, 0, 60, '', true);
        $this->SetLineWidth(0.1); // Restablecer grosor de línea
        $this->SetDrawColor(0, 0, 0); // Restablecer color de borde

        $this->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
        $this->MultiCell(60, 5, "  SISTEMA DE GESTION INTEGRAL", '', '', 0, 1, '', '', true);
        $this->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
        $this->MultiCell(60, 5, " ISO 9001 | 14001 | 45001", '', '', 0, 0, 96, '', true);

		$this->SetDrawColor(149, 55, 53); // Color del borde derecho
		$this->SetLineWidth(0.8); // Grosor de línea
        $this->MultiCell(0, 8, "Página {$this->getPage()}", 'B', 'R', 0, 0, '10', '', true);
		$this->SetLineWidth(0.1); // Restablecer grosor de línea
        $this->SetDrawColor(0, 0, 0); // Restablecer color de borde

        // $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
        $this->SetTextColor(0, 0, 0); // Color del texto
        $this->SetFillColor(222, 222, 222); // Color de fondo
        $this->SetFont('helvetica', '', 10.2); // Fuente, Tipo y Tamaño

        
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
else $pdf->logo = Route::rutaServidor().$empresa->imagen;
$pdf->empresaId = $empresa->id;
$pdf->empresa = mb_strtoupper(fString($empresa->razonSocial, 'UTF-8'));
$pdf->folio = mb_strtoupper(fString($ordenCompra->id));
$pdf->fechaCreacion = $ordenCompra->fechaCreacion;

// set document information
$pdf->setTitle("OC {$ordenCompra->folio}");
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
$pdf->setMargins(10, 30, 10);
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

$pdf->SetFont('helvetica', '', 7.5); // Fuente, Tipo y Tamaño

$fecha = strtotime($ordenCompra->fechaCreacion);

$diaSemana = fNombreDia(date("w", $fecha));
$dia = date("d", $fecha);
$mes = fNombreMes(date("n", $fecha));
$year = date("Y", $fecha);

$almacen = mb_strtoupper(fString($obra->almacen ?? '109'));

$pdf->MultiCell(150, 3, "ORDEN N°", '', 'R', 0, 0, '', '', true);
$pdf->SetFont('helvetica', '', 7.5); // Fuente, Tipo y Tamaño
$pdf->MultiCell(0, 3, "{$ordenCompra->folio}", 'B', 'C', 0, 1, '', '', true);

$fecha = fFechaLarga($ordenCompra->fechaRequerida);
$fechaCreacion = fFechaLarga($ordenCompra->fechaCreacion);
$razon = mb_strtoupper($proveedor->nombreCompleto ?? $proveedor->razonSocial);

$pdf->MultiCell(32, 3, "PROVEEDOR:", 0, '', 0, 0, 10, '', true);
$pdf->MultiCell(0, 3, "{$razon}", '', '', 0, 1, '', '', true, 0, false, true, '5', 'M', 1);

$pdf->MultiCell(10, 3, "RFC:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(50, 3, "$proveedor->rfc", '', 'C', 0, 0, '', '', true);

$pdf->MultiCell(20, 3, "DOMICILIO:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(0, 3, mb_strtoupper("$proveedor->domicilio"), '', 'L', 0, 1, '', '', true);

$pdf->MultiCell(20, 3, "TELEFONO:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(70, 3, "$proveedor->telefono", '', 'C', 0, 0, '', '', true);

$pdf->MultiCell(20, 3, "COLONIA:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(0, 3, "", '', 'L', 0, 1, '', '', true);

$pdf->MultiCell(30, 3, "FECHA SOLICITUD:", 0, '', 0, 0, 10, '', true);
$pdf->MultiCell(60, 3, "{$fechaCreacion}", '', 'L', 0, 0, '', '', true);

$pdf->MultiCell(40, 3, "FECHA QUE SE REQUIERE:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(60, 3, "{$fecha}", '', 'L', 0, 1, '', '', true);

$pdf->MultiCell(40, 3, "CONDICION DE COMPRA:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(50, 3, "{$proveedor->telefono}", '', 'C', 0, 0, '', '', true);

$contado = "";
$credito = "";
switch ($ordenCompra->condicionPagoId) {
	case 1:
		$contado = 'X';
		break;
	case 2:
		$credito = 'X';
		break;
	case 3:
		$credito = 'X';
		break;
	case 4:
		$credito = 'X';	
		break;
	case 5:
		$credito = 'X';
		break;
}

$pdf->MultiCell(16, 3, "CONTADO:", '', '', 0, 0, '', '', true);
$pdf->MultiCell(5, 3, $contado, 1, 'C', 0, 0, '', '', true);

$pdf->MultiCell(20, 3, "CREDITO:", '', 'R', 0, 0, '', '', true);
$pdf->MultiCell(5, 3, $credito, 1, 'C', 0, 1, '', '', true);


$pdf->ln(2); // Salto de Línea

$pdf->MultiCell(45, 5, "MAQUINARIA O EQUIPO", 1, 'C', 0, 0, 10, '', true);
$pdf->MultiCell(30, 5, "MARCA", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(30, 5, "MODELO", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(40, 5, "SERIE", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(45, 5, "NUM. ECONÓMICO", 1, 'C', 0, 1, '', '', true);
$pdf->MultiCell(0, 5, "", 1, 'C', 0, 1, '', '', true);
$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

$pdf->ln(2); // Salto de Línea

$pdf->SetTextColor(0, 0, 0); // Color del texto

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(78, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$subtotal = 0;
foreach($ordenCompra->detalles as $key => $detalle) {
	$cantidad = $detalle['cantidad'];
	$unidad = mb_strtoupper($detalle['unidad']);
	$concepto = mb_strtoupper($detalle['descripcion'].' | '.$detalle['concepto']);
	$numeroParte = mb_strtoupper($detalle['numeroParte']?? 'NA') ;
	$pu = $detalle['importeUnitario'];
	$costo = ($detalle['importeUnitario']*$detalle['cantidad']) ;
	$codigo = mb_strtoupper($detalle['codigo']?? 'NA') ;

	$partida = $key + 1;

	$y_start = $pdf->GetY();

	$pdf->MultiCell(78, 0, "{$concepto}", 1, 'C', 0, 1, 62, '', true, 0);
	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$num_detalle = count($ordenCompra->detalles);
	$pdf->MultiCell(12, $altoFila, "{$partida}", 1, 'C', 0, 0, 10, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "{$cantidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "$ {$pu}", 1, 'C', 0, 0, 140, '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "$ {$costo}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');

}

$y = $pdf->getY();

while ($y <= 140) {
	$pdf->MultiCell(12, 7, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(78, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(30, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

    $y = $pdf->getY();
}

$pdf->Ln(2); // Salto de Línea

$pdf->SetFillColor(217, 217, 217); // Set background color to gray
$pdf->MultiCell(120, 5, "Importe con letra", 0, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFillColor(255, 255, 255); // Set background color to white

$pdf->MultiCell(25, 5, "Subtotal", 0, 'L', 0, 0, '', '', true, 0, false, true, '5', 'M');
$pdf->MultiCell(0, 5, $ordenCompra->subtotal, 0, 'R', 0, 1, '', '', true, 0, false, true, '5', 'M');

$totalEnLetra = numeroALetras($ordenCompra->total, $divisa->descripcion, $divisa->nombreCorto);
$pdf->MultiCell(120, 5, "$totalEnLetra", 0, 'C', 0, 0, '', '', true, 0, false, true, '5', 'M');

$pdf->MultiCell(25, 5, "Descuentos", 0, 'L', 0, 0, '', '', true, 0, false, true, '5', 'M');
$descuento = $ordenCompra->descuento;
$pdf->MultiCell(0, 5, "$descuento", '', 'R', 0, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->MultiCell(25, 5, "I.V.A", 0, 'L', 0, 0, '130', '', true, 0, false, true, '5', 'M');
$iva = $ordenCompra->iva;
$pdf->MultiCell(0, 5, "$iva", '', 'R', 0, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->MultiCell(25, 5, "Retencion I.S.R:", 0, 'L', 0, 0, '130', '', true, 0, false, true, '5', 'M');
$retencionIsr = $ordenCompra->retencionIsr;
$pdf->MultiCell(0, 5, "$retencionIsr", '', 'R', 0, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(120, 5, "NOTA: ES NECESARIO PRESENTAR ESTA ORDEN DE", 0, '', 0, 0, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

$pdf->MultiCell(25, 5, "Retencion I.V.A:", 0, 'L', 0, 0, '130', '', true, 0, false, true, '5', 'M');
$retencionIva = $ordenCompra->retencionIva;
$pdf->MultiCell(0, 5, "$retencionIva", '', 'R', 0, 1, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(120, 5, "COMPRA ANEXADA A LA FACTURACION", 0, '', 0, 0, '', '', true, 0, false, true, '5', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

$pdf->MultiCell(25, 5, "Total:", 0, 'R', 0, 0, '130', '', true, 0, false, true, '5', 'M');
$total = formatMoney($ordenCompra->total);
$pdf->MultiCell(0, 5, "$total", '', 'R', 0, 1, '', '', true, 0, false, true, '5', 'M');

$justificacion = mb_strtoupper($ordenCompra->justificacion ?? '');
$y = $pdf->getY();
$pdf->MultiCell(0, 15, "JUSTIFICACION:", 1, '', 0, 0, '10', '', true, 0, false, true, '10', 'M');
$pdf->MultiCell(160, 5, "{$justificacion}", 'B', '', 0, 1, '35', $y, true, 0, true, true, 0, 'T', false);

$pdf->ln(13); // Salto de Línea

$y = $pdf->getY();
$pdf->MultiCell(0, 15, "OBRA O DESTINO:", 1, '', 0, 0, '10', '', true, 0, false, true, '10', 'M');
$pdf->MultiCell(160, 5, $obra->descripcion, 'B', '', 0, 1, '36', $y+2, true, 0, true, true, '0', 'T', false);

$pdf->ln(5); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(25, 20, "FACTURAR A:", 0, '', 0, 0, '10', '', true, 0, false, true, '5', 'M');
$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(90, 20, "GRUPO INDUSTRIAL HECA DEL SUR \nR.F.C.:  GIH010116FTA \nCalle Perla #18, Col. Elvira Ochoa \nde Hernandez, Coatzacoalcos, Veracruz CP. 96496", 0, '', 0, 0, '', '', true, 0, false, true, '20', 'M');
$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(0, 5, "No de Cta del Proveedor:", 0, '', 0, 1, '', '', true, 0, false, true, '5', 'M');
$pdf->SetFont('helvetica', '', 6.5); // Fuente, Tipo y Tamaño
$nombreBanco = mb_strtoupper(fString($datosBancarios->nombreBanco ?? ''));
$cuenta = mb_strtoupper(fString($datosBancarios->numeroCuenta ?? ''));
$clabe = mb_strtoupper(fString($datosBancarios->cuentaClave ?? ''));
$pdf->MultiCell(0, 15, "{$nombreBanco} \nCuenta {$cuenta} \nClabe {$clabe} ", 0, '', 1, 1, 125, '', true, 0, false, true, '15', 'M');

$y = $pdf->getY();
if( $y > 260 ) {
	$pdf->AddPage(); // Agregar nueva página si se excede el límite
	$y = 10; // Reiniciar la posición Y
}
$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
if ( !is_null($solicitoFirma) ) {
	$extension = mb_strtoupper(substr($solicitoFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($solicitoFirma, 5, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

if ( !is_null($almacenFirma) ) {
	$extension = mb_strtoupper(substr($almacenFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($almacenFirma, 70, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

if ( !is_null($revisoFirma) ) {
	$extension = mb_strtoupper(substr($revisoFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($revisoFirma, 135, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
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

$pdf->MultiCell(55, 5, "{$reviso}", 'B', 'C', 0, 1, 140, '', true, 0, false, true, '5', 'M', 1);

$pdf->SetFont('helvetica', '', 9); // Fuente, Tipo y Tamaño
$pdf->MultiCell(66, 5, "ELABORA", 0, 'C', 0, 0, 5, '', true);

$pdf->MultiCell(66, 5, "APRUEBA", 0, 'C', 0, 0, '', '', true);

$pdf->MultiCell(66, 5, "AUTORIZA", 0, 'C', 0, 1, '', '', true);

// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("OC {$ordenCompra->folio}.pdf", 'I');

//============================================================+
// END OF FILE
//============================================================+
