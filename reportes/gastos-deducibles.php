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
	public $folio;
	public $fechaCreacion;
	public $telefono;

	//Page header
	public function Header() {
		
		$extension = mb_strtoupper(substr($this->logo, -3, 3));
		if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen

			// Logo
			$this->SetLineStyle(array('width' => 0, 'color' => array(255, 255, 255)));
			$this->SetFillColor(242, 242, 242); // Color de fondo
			$this->RoundedRect(6, 5, 70, 22, 3.5, '1111', 'DF');
			$this->Image($this->logo, 8, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
		
			$this->setCellPaddings(1, 1, 1, 1); // set cell padding

			// Title
			$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(170, 171, 175); // Color de fondo
            $this->RoundedRect(75, 5, 170, 11, 3.5, '0001', 'DF');
			$this->SetFillColor(0, 0, 0); // Color de fondo
			$this->MultiCell(170, 11, "GASTOS DEDUCIBLES", 0, 'C', 0, 0, 75, 5, true);

			// $this->Rect(165, 5, 40, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(255, 255, 255); // Color del texto
			$this->SetFillColor(139, 143, 146); // Color de fondo
			$this->RoundedRect(240, 5, 50, 11, 3.5, '1000', 'DF');
			$this->SetFillColor(0, 0, 0); // Color de fondo

			$this->MultiCell(40, 11, "FO-IGC-AD-20 \n REV 02", 0, 'C', 0, 1, '', '', true);

			// $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(242, 242, 242); // Color de fondo
			$this->MultiCell(165, 11, "SISTEMA DE GESTIÓN INTEGRAL \n ISO 9001:2015, ISO 14001:2015, ISO 45001:2018", 1, 'C', 1, 0, 75, 16, true);

			$this->MultiCell(50, 11, "PÁGINA {$this->getPage()} DE {$this->getNumPages()}", 1, 'C', 1, 1, '', '', true, 0, false, true, '11', 'M');

			// $this->Rect(165, 16, 40, 11, 'D', array(), array(222,222,222));

		$this->Ln(2); // Salto de Línea

	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

// create new PDF document
$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

$pdf->logo = Route::rutaServidor()."vistas/img/empresas/95274811.png";

$pdf->setPrintFooter(false);

// set default header data
$pdf->setHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->setDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->setMargins(30, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
// $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->setFont('times', 'BI', 12); // Fuente, Tipo y Tamaño

$pdf->setCellPaddings(1, 1, 1, 1); // set cell padding

$pdf->AddPage(); // Agregar nueva página

$pdf->SetX(15);
$pdf->SetFont('helvetica', 'N', 9); // Fuente, Tipo y Tamaño
$nombreCompleto = mb_strtoupper($usuario->nombreCompleto);
$pdf->MultiCell(60, 7, "Nombre del encargado:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(80, 6, "{$nombreCompleto}", 'B', 'C', 0, 1, '', '', true);
$pdf->SetX(15);

$obra = mb_strtoupper($obra->descripcion);
$pdf->MultiCell(60, 7, "Obra o Destino:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(80, 6, "{$obra}", 'B', 'C', 0, 0, '', '', true, 0, false, true, '7', 'M', true);

$almacen = ''; //TODO: cambiar para que sea un almacen seleccionado

$pdf->MultiCell(18, 7, "Almacen:", 0, 'L', 0, 0, 170, '', true);
$pdf->MultiCell(60, 6, "$almacen", 'B', 'C', 0, 1, '', '', true);

$pdf->SetX(15);
$fecha_inicio = fFechaSQL($gastos->fecha_inicio);
$fecha_fin = $gastos->fecha_fin ? fFechaSQL($gastos->fecha_fin) : null;
$periodo = formatFecha($fecha_inicio,$fecha_fin);

$pdf->MultiCell(60, 7, "Periodo Semana:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(80, 6, "$periodo", 'B', 'C', 0, 1, '', '', true);
$pdf->SetX(15);

$pdf->MultiCell(60, 7, "Fecha Inicio:", 0, 'L', 0, 0, '', '', true);
$pdf->MultiCell(80, 6, "{$gastos->fecha_envio}", 'B', 'C', 0, 1, '', '', true);

$pdf->SetX(250);
$pdf->MultiCell(40, 7, "$: \n Importe de la caja", 'B', 'B', 0, 1, '', '', true);
//Tabla

$pdf->SetFont('helvetica', 'B', 7); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(230, 230, 230); // Color de fondo
$pdf->MultiCell(15, 9, "Partida", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(25, 9, "Fecha de Facturación", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 9, "Tipo de Gasto y/ó Concepto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 9, "Proveedor", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "No. De Factura", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Total c/iva", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 9, "Solicitó", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 9, "Destino u Obra", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Equipo No. Economico", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(45, 9, "Observaciones ó Justificación", 1, 'C',1, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('dejavusans', '', 6); // Fuente, Tipo y Tamaño
$totalGeneral = 0;

foreach($detallesGastos as $key => $detalle) {
	$partida = $key + 1;

	$y_start = $pdf->GetY();
	$fecha = fFechaLarga($detalle["fecha"]);
	// $pdf->MultiCell(80, 0, "", 1, 'C', 0, 1, 65, '', true, 0);
	$tipoGasto  = mb_strtoupper($detalle["tipoGasto"]);
	$obra = mb_strtoupper(fString($detalle["obra"]));
	$proveedor = mb_strtoupper(fString($detalle["proveedor"]));
	$observaciones = mb_strtoupper(fString($detalle["observaciones"]));
	$solicito = mb_strtoupper(fString($detalle["solicito"]));

	$pdf->MultiCell(40, 0, "{$proveedor}", 1, 'C', 0, 1, 75, '', true, true);
	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$pdf->MultiCell(15, $altoFila, "$partida", 1, 'C', 0, 0, 5, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(25, $altoFila, "{$fecha}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "{$tipoGasto}", 1, 'C', 0, 0, '', '', true, 0,false, true, $altoFila, 'M',true);
	$pdf->MultiCell(20, $altoFila, "{$detalle["factura"]}", 1, 'C', 0, 0, 115, '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "$ {$detalle["costo"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(40, $altoFila, "{$solicito}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M',true);
	$pdf->MultiCell(30, $altoFila, "{$obra}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(20, $altoFila, "{$detalle["economico"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(45, $altoFila, "{$observaciones}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M',true);

	if ( $y_end > 150 && $partida < count($detallesGastos) ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 51);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(15, 9, "Partida", 1, 'C', 1, 0, 5, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(25, 9, "Fecha de Facturación", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 9, "Tipo de Gasto y/ó Concepto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(40, 9, "Proveedor", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "No. De Factura", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Total c/iva", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(40, 9, "Solicitó", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 9, "Destino u Obra", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Equipo No. Economico", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(45, 9, "Observaciones ó Justificación", 1, 'C',1, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	}
    $totalGeneral += $detalle["costo"];

}
$y = $pdf->getY();

while ($y <= 134) {
	$pdf->MultiCell(15, 7, "", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(40, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(40, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(45, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
	$y = $pdf->getY();
}

$pdf->MultiCell(20, 7, "Total General", 1, 'C', 0, 0, 115, '', true, 0, false, true, '7', 'M');

$pdf->MultiCell(20, 7, "$ {$totalGeneral}", 1, 'C', 0, 1, 135, '', true, 0, false, true, '7', 'M');

$pdf->Ln(10);
if ( !is_null($encargadoFirma) ) {
	$extension = mb_strtoupper(substr($encargadoFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($encargadoFirma, 55, $y+10, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}
$y = $pdf->getY()+5;
if ( !is_null($autorizacionFirma) ) {
	$extension = mb_strtoupper(substr($autorizacionFirma, -3, 3));
	if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($autorizacionFirma, 55, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}
$pdf->MultiCell(40, 7, "Firma del Encargado:", 0, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(60, 6, "$usuarioEncargado", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "Banco:", 0, 'R', 0, 0, 190, '', true, 1, false, true, '7', 'M');
$pdf->MultiCell(70, 6, "{$gastos->banco}", 'B', 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 7, "Autorizado por:", 0, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(60, 6, "$usuarioAutorizacion", 'B', '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "Cuenta:", 0, 'R', 0, 0, 190, '', true, 1, false, true, '7', 'M');
$pdf->MultiCell(70, 6, "{$gastos->cuenta}", 'B', 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 7, "Clabe interbancaria:", 0, 'R', 0, 0, 190, '', true, 1, false, true, '7', 'M');
$pdf->MultiCell(70, 6, "{$gastos->clave}", 'B', 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->Output("Gastos Deducibles.pdf", 'I');

function formatFecha($fechaInicio, $fechaFin) {
	$fechaInicioObj = DateTime::createFromFormat('Y-m-d', $fechaInicio);
	$fechaFinObj = $fechaFin ? DateTime::createFromFormat('Y-m-d', $fechaFin) : null;

	if ($fechaFinObj) {
		$numeroMes = mb_strtoupper(fNombreMes(date('n', strtotime($fechaFin))));
		$anio = date('Y', strtotime($fechaFin));
		$fechaInicioStr = $fechaInicioObj->format('j');
		$fechaFinStr = $fechaFinObj->format('j');

		return "DEL $fechaInicioStr AL $fechaFinStr DE $numeroMes DEL $anio";
	} else {
		return "";
	}
}