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
        
        $this->Image($this->logo, 10, 10, 56, 11, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
    
        $this->setCellPaddings(1, 1, 1, 1); // set cell padding
        $this->SetTextColor(0, 0, 0); // Color del texto
        $this->SetFillColor(222, 222, 222); // Set background color to gray
        $this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
        $this->MultiCell(240, 3, "CHECADOR:", 0, 'R', 0, 1, '', '', true);
        $this->MultiCell(240, 3, "BANCO:", 0, 'R', 0, 1, '', '', true);

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


// set document information
$pdf->setTitle("Reporte de Cargas");
// remove default header/footer
// $pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Cambiar la disposición de la página a horizontal (landscape)
$pdf->setPageOrientation('L');

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

$pdf->SetFont('helvetica', '', 7.5); // Fuente, Tipo y Tamaño
$pdf->ln(2); // Salto de Línea

$pdf->SetTextColor(0, 0, 0); // Color del texto
$pdf->SetFillColor(222, 222, 222); // Gris claro
$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(30, 12, "FECHA", 1, 'C', 1, 0, 10, '', true, 0, false, true, '7', 'M',1);
$pdf->MultiCell(30, 12, "No. DE FOLIO", 1, 'C', 1, 0, '', '', true, 0, false, true, '12', 'M');
$pdf->MultiCell(30, 12, "TIPO DE MATERIAL", 1, 'C', 1, 0, '', '', true, 0, false, true, '12', 'M');
$pdf->MultiCell(30, 12, "No. DE CAMION", 1, 'C', 1, 0, '', '', true, 0, false, true, '12', 'M',1);
$pdf->MultiCell(30, 12, "No. DE PLACAS", 1, 'C', 1, 0, '', '', true, 0, false, true, '12', 'M');
$pdf->MultiCell(30, 12, "CANTIDAD M3", 1, 'C', 1, 0, '', '', true, 0, false, true, '12', 'M',1 );
$pdf->MultiCell(40, 12, "BANCO DE SALIDA", 1, 'C', 1, 0, '', '', true, 0, false, true, '12', 'M');
$pdf->MultiCell(40, 12, "OBRA DE LLEGADA", 1, 'C', 1, 1, '', '', true, 0, false, true, '12', 'M',1);

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
$subtotal = 0;
foreach($cargas as $key => $detalle) {

	$pdf->MultiCell(30, 7, fFechaLarga($detalle['fechaHoraCarga']), 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(30, 7, $detalle["folioCarga"], 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(30, 7, mb_strtoupper($detalle["nombreMaterial"]), 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(30, 7, mb_strtoupper($detalle["numeroEconomicoMaquinaria"]), 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(30, 7, mb_strtoupper($detalle["placaMaquinaria"]), 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(30, 7, $detalle["pesoCarga"], 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(40, 7, "BANCO DE SALIDA", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
    $pdf->MultiCell(40, 7, "OBRA DE LLEGADA", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M',1);


}



// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output("reporte_cargas.pdf", 'I');

//============================================================+
// END OF FILE
//============================================================+
