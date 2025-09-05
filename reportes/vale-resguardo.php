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
		

			// Logo
			$this->Image($this->logo, 6, 5, 63, 22, '', '', '', false, 300, '', false, false, 0, 'CM', false, false);
			$this->setCellPaddings(1, 1, 1, 1); // set cell padding

			// Title
			$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
			$this->MultiCell(95, 11, "RESGUARDO DE HERRAMIENTA, MAQUINARIA Y EQUIPO DE TRANSPORTE", 0, 'C', 0, 0, 70, 5, true);

		$this->Ln(2); // Salto de Línea
		
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->folio = $resguardo->id;

$pdf->logo = Route::rutaServidor()."vistas/img/empresas/logo-indheca.jpeg";

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
// $this->setCellMargins(1, 1, 1, 1); // set cell margins

$pdf->AddPage(); // Agregar nueva página

$almacen = mb_strtoupper(fString($almacen->nombre));

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño

// Fecha
$pdf->MultiCell(145, 5, "FOLIO:", 0, 'R', 0, 0, '', 14, true);
$pdf->MultiCell(30, 5, "{$resguardo->id}", 'B', '', 0, 1, '','', true);

// Fecha
$pdf->MultiCell(145, 5, "FECHA:", 0, 'R', 0, 0, '', 22, true);
$pdf->MultiCell(30, 5, "{$resguardo->fechaAsignacion}", 'B', '', 0, 1, '','', true);

// NOMBRE QUIEN ENTREGA
$pdf->MultiCell(40, 5, "NOMBRE QUIEN ENTREGA:", 0, 'L', 0, 0, 5, 30, true);
$pdf->MultiCell(70, 5, "{$nombreEntrego}", 'B', '', 0, 0, '', '', true);

$pdf->MultiCell(22, 5, "ALMACEN:", 0, 'R', 0, 0, 0, 38, true);
$pdf->MultiCell(60, 5, "{$almacen}", 'B', '', 0, 0, '', '', true);

//$pdf->MultiCell(40, 5, "NUMERO DE CONTRATO:", 0, 'C', 0, 0, '', '', true);
//$pdf->MultiCell(35, 5, "{$contratoEncontrado['nombreCorto']}", 'B', '', 0, 0, '', '', true);

$pdf->Ln(6); 

// ("ANCHO","ALTO","TEXTO","MARGEN","JUSTIFICACION","FONDO","SALTO DE LINEA","POSICIONES X","POSICIONES Y","","7 TAMAÑO DE ESPACIO")

$pdf->MultiCell(20, 9, "ID", 1, 'C', 0, 0, 7, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "CANTIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(25, 9, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(25, 9, "N° DE PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(104, 9, "DESCRIPCION", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

foreach ($detalles as $key => $detalle) {

    $y_start = $pdf->GetY();
    $descripcion = $detalle["descripcion"];
    $unidad = mb_strtoupper($detalle["unidad"]);

    // Agregar ID en cada fila
    $pdf->MultiCell(20, 9, "{$detalle["id"]}", 1, 'C', 0, 0, 7, '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(20, 9, "{$detalle["cantidad"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(25, 9, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(25, 9, "{$detalle["numeroParte"]}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
    $pdf->MultiCell(104, 9, "{$descripcion}", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

    $y_end = $pdf->GetY();

}

// INICIA FUNCION PARA AGREGAR COLUMNAS
$y = $pdf->getY();

while ($y <= 200) {
	$pdf->MultiCell(20, 9, "", 1, 'C', 0, 0, 7, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(25, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(104, 9, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

    $y = $pdf->getY();
}


// TERMINA FUNCION AGREGAR COLUMNAS

$pdf->Ln(20); // Salto de Línea

// OBTENER EXTENCION DE LA IMAGEN
$extension =  mb_strtoupper(pathinfo($firmaEntrego, PATHINFO_EXTENSION));
if ( $extension == 'PNG')  $pdf->setJPEGQuality(75); // Calidad de imágen
// FIRMA ENTREGO
$pdf->Image($firmaEntrego, 10, 210, 50, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);


// OBTENER EXTENCION DE LA IMAGEN
$extension =  mb_strtoupper(pathinfo($firmaRecibio, PATHINFO_EXTENSION));
if ( $extension == 'PNG')  $pdf->setJPEGQuality(75); // Calidad de imágen
// FIRMA RECIBIO
$pdf->Image($firmaRecibio, 88, 206, 35, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);


$pdf->SetX(10);
$pdf->MultiCell(50, 7, "{$nombreEntrego}", 0, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 7, "{$nombreRecibio}", 0, 'C', 0, 0, 80, '', true);
$pdf->MultiCell(50, 7, "", 0, 'C', 0, 1, 145, '', true);

$pdf->SetX(7);

$pdf->MultiCell(54, 7, "ENTREGÓ", 'T', 'C', 0, 0, '', '', true);

$pdf->MultiCell(54, 7, "RECIBIÓ", 'T', 'C', 0, 0, 77, '', true);

$pdf->MultiCell(54, 7, "AUTORIZÓ", 'T', 'C', 0, 0, 145, '', true);


$pdf->Ln(17); // Salto de Línea


$pdf->SetX(7);
$observaciones = mb_strtoupper($resguardo->observaciones);

$pdf->MultiCell(194, 20, "OBSERVACIONES:", 1, '', 0,0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(109, 7, "{$observaciones}", 'B', '', 0, 0, 40, '', true, 0, false, true, '7', 'M');

$pdf->Ln(8); // Salto de Línea

$pdf->SetX(8);

$pdf->MultiCell(194, 5, "NOTA: Este resguardo ampara la herramienta o equipo que aquí se describe; acepto cuidarla y devolverla en buen estado; en caso de mal uso o perdida, autorizo sea descontada de mi salario; conforme al Art.110 fracc. I de la LEY FEDERAL DEL TRABAJO", 0, 'L', 0, 0, '', '', true);


$pdf->Output("Vale-Entrada {$pdf->folio}.pdf", 'I');
