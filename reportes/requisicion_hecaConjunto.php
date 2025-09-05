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
require_once "../../vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
	public $logo;
	public $empresaId;
	public $empresa;
	public $folio;
	public $fechaCreacion;
	public $maquinaria;
	public $mantenimientoTipo;

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
        $this->MultiCell(65, 11, "REQUISICIÓN DE COMPRA", '', '', 0, 1, 60, 10, true);

        $this->SetDrawColor(149, 55, 53); // Color del borde derecho
        $this->SetLineWidth(0.8); // Grosor de línea
        $this->MultiCell(35, 11, "FO-GIH-OP-01.01 \nREV. 02", 'R', '', 0, 0, 60, '', true);
        $this->SetLineWidth(0.1); // Restablecer grosor de línea
        $this->SetDrawColor(0, 0, 0); // Restablecer color de borde

        $this->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
        $this->MultiCell(60, 5, "  SISTEMA DE GESTION INTEGRAL", '', '', 0, 1, '', '', true);
        $this->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
        $this->MultiCell(60, 5, " ISO 9001 | 14001 | 45001", '', '', 0, 0, 96, '', true);

        $this->MultiCell(0, 5, "Página {$this->getPage()}", 0, 'R', 0, 0, '', '', true);

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
// Carpeta donde se guardarán los PDFs
$carpetaDestino = '/var/www/html/reportes/tmp/';
// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

if ( is_null($empresa->imagen) ) $pdf->logo = Route::rutaServidor()."vistas/img/empresas/default/imagen.jpg";
else $pdf->logo = Route::rutaServidor().$empresa->imagen;
$pdf->empresaId = $empresa->id;
$pdf->empresa = mb_strtoupper(fString($empresa->razonSocial, 'UTF-8'));
$pdf->folio = mb_strtoupper(fString($requisicion->folio));
$pdf->fechaCreacion = $requisicion->fechaCreacion;
$pdf->maquinaria = isset($requisicion->maquinaria) ? $requisicion->maquinaria : 'NA' ;
$pdf->mantenimientoTipo = isset($requisicion->mantenimientoTipo) ? $requisicion->mantenimientoTipo : 'NA' ; 

// set document information
$pdf->setTitle("Requisición {$pdf->folio}");
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

$pdf->ln(7); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 7.5); // Fuente, Tipo y Tamaño

$fecha = strtotime($requisicion->fechaCreacion);

$diaSemana = fNombreDia(date("w", $fecha));
$dia = date("d", $fecha);
$mes = fNombreMes(date("n", $fecha));
$year = date("Y", $fecha);

$almacen = mb_strtoupper(fString($obra->almacen ?? '109'));

$pdf->SetDrawColor(149, 55, 53); // Color del borde derecho
$pdf->SetLineWidth(0.8); // Grosor de línea

$pdf->MultiCell(150, 3, "FOLIO N°", 'T', 'R', 0, 0, '', '', true);
$pdf->SetFont('helvetica', '', 7.5); // Fuente, Tipo y Tamaño
$pdf->MultiCell(0, 3, "{$pdf->folio}", 'T', 'C', 0, 1, '', '', true);

$pdf->SetLineWidth(0.1); // Restablecer grosor de línea
$pdf->SetDrawColor(0, 0, 0); // Restablecer color de borde

$razon = mb_strtoupper(fString($proveedor->nombreCompleto ?? $proveedor->razonSocial ?? ''));

$pdf->MultiCell(32, 3, "PROVEEDOR:", 0, '', 0, 0, 10, '', true);
$pdf->MultiCell(0, 3, "{$razon}", 'B', '', 0, 1, '', '', true, 0, false, true, '5', 'M', 1);


$pdf->MultiCell(32, 3, "DIRECCION:", 0, '', 0, 0, 10, '', true);
$domicilio = mb_strtoupper(fString($proveedor->direccion ?? ''));
$pdf->MultiCell(0, 3, "{$domicilio}", 'B', '', 0, 1, '', '', true, 0, false, true, '5', 'M', 1);

$telefono = mb_strtoupper(fString($proveedor->telefono ?? ''));
$pdf->MultiCell(32, 3, "TELÉFONO:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(30, 3, "{$telefono}", 'B', 'C', 0, 0, '', '', true);


$pdf->MultiCell(12, 3, "FAX:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(30, 3, "", 'B', 'C', 0, 0, '', '', true);


$email = fString($proveedor->correo ?? '');
$pdf->MultiCell(12, 3, "e-mail:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(0, 3, "{$email}", 'B', 'C', 0, 1, '', '', true);


$fecha = isset($requisicion->fechaReq) ? fFechaLarga($requisicion->fechaReq) : '';

$pdf->MultiCell(38, 3, "FECHA SOLICITUD:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(45, 3, fFechaLarga($requisicion->fechaCreacion), 'B', 'C', 0, 0, '', '', true);

$pdf->MultiCell(38, 3, "FECHA QUE SE REQUIERE:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(0, 3, "{$fecha}", 'B', 'C', 0, 1, '', '', true);


$pdf->ln(2); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 7.5); // Fuente, Tipo y Tamaño

$pdf->MultiCell(45, 5, "MAQUINARIA O EQUIPO", 1, 'C', 0, 0, 10, '', true);
$pdf->MultiCell(25, 5, "MARCA", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(30, 5, "MODELO", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(40, 5, "SERIE", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(20, 5, "ODO / HOR", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(0, 5, "NUM. ECONÓMICO", 1, 'C', 0, 1, '', '', true);

$equipo = mb_strtoupper(fString($pdf->maquinaria['maquinaria_tipos.descripcion'] ?? 'NA'));
$marca = mb_strtoupper(fString($pdf->maquinaria['marcas.descripcion'] ?? 'NA'));
$modelo = mb_strtoupper(fString($pdf->maquinaria['modelos.descripcion'] ?? 'NA'));
$serie = mb_strtoupper(fString($pdf->maquinaria['serie'] ?? 'NA'));
$numeroEconomico = mb_strtoupper(fString($pdf->maquinaria['numeroEconomico'] ?? 'NA'));

$pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño
$pdf->MultiCell(45, 5, "{$equipo}", 1, 'C', 0, 0, 10, '', true);
$pdf->MultiCell(25, 5, "{$marca}", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(30, 5, "{$modelo}", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(40, 5, "{$serie}", 1, 'C', 0, 0, '', '', true, 0, false, true, '5');
$pdf->MultiCell(20, 5, isset($pdf->maquinaria['odo_hor']) ? $pdf->maquinaria['odo_hor'] : 'NA', 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(0, 5, "{$numeroEconomico}", 1, 'C', 0, 1, '', '', true, 0, false, true, '4.2', 'm',1);

$pdf->ln(2); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(78, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$subtotal = 0;
foreach($requisicion->detalles as $key => $detalle) {
	$cantidad = number_format($detalle['cantidad'], 2);
	$unidad = mb_strtoupper($detalle['unidad']);
	$concepto = mb_strtoupper($detalle['descripcion'].' | '.$detalle['concepto']);
	$numeroParte = mb_strtoupper($detalle['numeroParte']?? 'NA') ;
	$pu = number_format($detalle['costo_unitario'],2);
	$costo = number_format($detalle['costo'],2) ;
	$codigo = mb_strtoupper($detalle['codigo']?? 'NA') ;

	$partida = $key + 1;

	$y_start = $pdf->GetY();
	if ( $y_start > 160 && $partida == count($requisicion->detalles) ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 30);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(78, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(30, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(30, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

		$y_start = $pdf->GetY();
	}

	$pdf->MultiCell(78, 0, "{$concepto}", 1, 'C', 0, 1, 62, '', true, 0);
	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$num_detalle = count($requisicion->detalles);
	$pdf->MultiCell(12, $altoFila, "{$partida}", 1, 'C', 0, 0, 10, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "{$cantidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "$ {$pu}", 1, 'C', 0, 0, 140, '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "$ {$costo}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');
	$subtotal += $detalle['costo'];
	if ( $y_end > 220 ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 30);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(78, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(30, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(30, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	}
}

$y = $pdf->getY();
if ( $y > 220 ) {
	$pdf->AddPage();

	$pdf->SetXY(5, 51);
	$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(78, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(30, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(30, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

	$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	$y = $pdf->getY();
}

while ($y <= 190) {
	$pdf->MultiCell(12, 7, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(78, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(30, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

    $y = $pdf->getY();
}

$pdf->MultiCell(12, 7, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(78, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "SUBTOTAL", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, formatMoney($subtotal), 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$iva = round($requisicion->iva, 2);
$pdf->MultiCell(12, 7, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(78, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "IVA", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, formatMoney($iva), 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$descuentos = floatval($requisicion->descuento ?? 0);
$retencionIva = $requisicion->retencionIva ?? 0;
$total = ($subtotal - $descuentos + $iva) - $retencionIva - ($requisicion->retencionIsr ?? 0);

$pdf->MultiCell(12, 7, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(78, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "TOTAL", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, formatMoney($total), 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->ln(1); // Salto de Línea

$justificacion = mb_strtoupper($requisicion->justificacion ?? '');
$y = $pdf->getY();
$pdf->MultiCell(0, 15, "JUSTIFICACION:", 1, '', 0, 0, '10', '', true, 0, false, true, '10', 'M');
$pdf->MultiCell(160, 5, "{$justificacion}", 'B', '', 0, 1, '35', $y, true, 0, true, true, 0, 'T', false);

$pdf->ln(11); // Salto de Línea

$y = $pdf->getY();
$pdf->MultiCell(0, 15, "OBRA O DESTINO:", 1, '', 0, 0, '10', '', true, 0, false, true, '10', 'M');
$pdf->MultiCell(160, 5, $obra->descripcion, 'B', '', 0, 1, '36', $y+2, true, 0, true, true, '0', 'T', false);

$pdf->ln(10); // Salto de Línea

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

$solicito = mb_strtoupper(fString($solicito));
$almacenResponsable = mb_strtoupper(fString($almacenResponsable));
$reviso = mb_strtoupper(fString($reviso));

$y = $pdf->getY();

if( $y > 260 ) {
	$pdf->AddPage(); // Agregar nueva página si se excede el límite
	$y = 10; // Reiniciar la posición Y
}

$pdf->SetFont('helvetica', '', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(66, 5, "SOLICITA", 0, 'C', 0, 0, 5, '', true);
$pdf->MultiCell(66, 5, "REVISÓ", 0, 'C', 0, 0, '', '', true);
$pdf->MultiCell(66, 5, "APRUEBA", 0, 'C', 0, 1, '', '', true);


$y = $pdf->getY();

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

if ( !is_null($revisoSello) ) {
	$pdf->MultiCell(60, 20, "Sello Digital:\n ".$revisoSello, 1, 'C', 0, 0, 140, 240, true, 0, false, true, '5', 'M',1);
} else if ( !is_null($revisoFirma) ) {
	$extension = mb_strtoupper(substr($revisoFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image('../../'.$revisoFirma, 130, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

$pdf->Ln(20); // Salto de Línea
$pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
// $pdf->Line(15, 278, 95, 278, false);
$pdf->MultiCell(60, 5, "{$solicito}", 'T', 'C', 0, 0, 10, '', true, 0, false, true, '5', 'M', 1);

$pdf->MultiCell(55, 5, "{$almacenResponsable}", 'T', 'C', 0, 0, 75, '', true, 0, false, true, '5', 'M', 1);

$pdf->MultiCell(55, 5, "{$reviso}", 'T', 'C', 0, 1, 140, '', true, 0, false, true, '5', 'M', 1);

// ---------------------------------------------------------
//Close and output PDF document
$fileName = "requisicion.pdf";
$filePath = $carpetaDestino . $fileName;
$pdf->Output($filePath, 'F'); // Guarda en el servidor

$rutasArchivos = $filePath; // Guardar la ruta en el array


//============================================================+
// END OF FILE
//============================================================+
