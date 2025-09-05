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
	public $fechaInicio;

	//Page header
	public function Header() {
		// $extension = mb_strtoupper(substr($this->logo, -3, 3));
		// if ( $extension == 'JPG') $this->setJPEGQuality(75); // Calidad de imágen

		// 	// Logo
		// 	$this->SetLineStyle(array('width' => 0, 'color' => array(255, 255, 255)));
		// 	$this->Image($this->logo, 31, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
		
		// 	$this->setCellPaddings(1, 1, 1, 1); // set cell padding

		// 	// Title
		// 	$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
		// 	$this->SetTextColor(0, 0, 0); // Color del texto
		// 	$this->SetFillColor(0, 0, 0); // Color de fondo
		// 	$this->MultiCell(60, 11, "Fecha de Inicio: {$this->fechaCreacion}", 0, 'L', 0, 1, 120, '', true);
		// 	$this->MultiCell(60, 11, "Fecha de Fin:", 0, 'L', 0, 0, 120, '', true);

		// $this->Ln(2); // Salto de Línea

		}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}
// create new PDF document
$pdf = new MYPDF('L', 'mm', 'A4', true, 'UTF-8', false);

$pdf->logo = Route::rutaServidor()."vistas/img/empresas/63098003.png";


$pdf->fechaCreacion = fFechaLarga($obra->fechaInicio);
// set document information
$pdf->setTitle("Proforma");
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
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
// $pdf->setFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
// $pdf->setAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setAutoPageBreak(TRUE, 5);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// ---------------------------------------------------------

$pdf->setCellPaddings(1, 1, 1, 1); // set cell padding

$pdf->AddPage(); // Agregar nueva página

$comp = 220/($obra->periodos+$obra->semanaExtra);
$pdf->setFont('helvetica', 'B', 12); // Fuente, Tipo y Tamaño
$obraNombre = mb_strtoupper(fString($obra->descripcion));
$pdf->MultiCell(200, 14, "RESUMEN DE INGRESOS VS EGRESOS \n {$obraNombre}", 0, '', 0, 1, '', '', true, 0, false, true, '14', 'M');

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(230, 230, 230); // Color de fondo
$pdf->MultiCell(35, 9, "Descripcion", 1, 'C', 1, 0, 15, '', true, 0, false, true, '7', 'M');
if (count($arrayMeses)>7) {
	$meses = count($arrayMeses)/3;
	for ($i=0; $i < $meses; $i++) { 
		$trimestre = $i+1;
		$pdf->MultiCell(25, 9, "Q{$trimestre}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	}
}else{
	foreach ($arrayMeses as $value) {
		$pdf->MultiCell(25, 9, "{$value}", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
	}
}
$pdf->MultiCell(30, 9, "Presupuesto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 9, "Total", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
foreach($resumenCostos as $key => $detalle) {
	
	$pdf->MultiCell(35, 7, "{$detalle["descripcion"]}", 1, 'C', 0, 0, 15, '', true, 0, false, true, '7', 'M',1);
	
	if (count($arrayMeses)>7) {
		$suma = 0;
		$contador = 0;
		foreach ($arrayMeses as $mes) {
			$contador++;
			$suma += $detalle[$mes];
			
			// Si es el tercer elemento o si es el último elemento y quedan menos de 3 elementos
			if ($contador % 3 == 0 || ($contador == count($arrayMeses) && $contador % 3 != 0)) {
				// Imprimir la celda con el total
				$suma = number_format($suma,2);
				$pdf->MultiCell(25, 7, "$ {$suma}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
				$suma = 0; // Reiniciar el acumulador
			}

		}
    }
	else{
		foreach ($arrayMeses as $mes) {
			$costoMes = number_format($detalle[$mes],2);
			$pdf->MultiCell(25, 7, "$ {$costoMes}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		}
	}
	$presupuesto = number_format($detalle['presupuesto'],2);
	$remanente = number_format($detalle['remanente'],2);
	$pdf->MultiCell(30, 7, "$ {$presupuesto}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(30, 7, "$ {$remanente}", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
}

$pdf->SetFont('helvetica', 'B', 12); // Fuente, Tipo y Tamaño

$pdf->MultiCell(200, 14, "Directos", 0, '', 0, 1, '', '', true, 0, false, true, '14', 'M');

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(230, 230, 230); // Color de fondo
$pdf->MultiCell(80, 9, "Descripcion", 1, 'C', 1, 0, 15, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Código", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 9, "Tipo", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Unidad.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Cantidad", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 9, "Presupuesto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(35, 9, "Remanente", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(35, 9, "Remanente Cantidad", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(256, 256, 256); // Color de fondo
$totalPresupuesto=0;
$totalRemanente=0;
foreach($directos as $key => $detalle) {
	$partida = $key + 1;

	$totalPresupuesto += floatval($detalle["presupuesto"]);
	$totalRemanente += floatval($detalle["remanente"]);
	
	$codigo = mb_strtoupper(fString($detalle["codigo"]));
	$tipo = mb_strtoupper(fString($detalle["tipo"]));
	$unidad = mb_strtoupper(fString($detalle["unidad"]));
	$cantidad = number_format($detalle["cantidad"],2);
	$presupuesto = number_format($detalle["presupuesto"],2);
	$remanente = number_format($detalle["remanente"],2);
	$remanente_cantidad = number_format($detalle["remanente_cantidad"],2);

	$y_start = $pdf->GetY();
	$descripcion = mb_strtoupper(($detalle["descripcion"]));;
	$pdf->MultiCell(80, 8, "{$descripcion}", 1, 'C', 0, 1, 15, '', true, 0);
	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$pdf->MultiCell(20, $altoFila, "{$codigo}", 1, 'C', 0, 0, 95, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "{$tipo}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M',true);
	$pdf->MultiCell(20, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M',1);
	$pdf->MultiCell(20, $altoFila, "{$cantidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "$ {$presupuesto}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	if($detalle["remanente"]<0) $pdf->SetFillColor(236, 83, 83); // Color de fondo
	$pdf->MultiCell(35, $altoFila, "$ {$remanente} ", 1, 'C', 1, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->SetFillColor(256, 256, 256); // Color de fondo
	$pdf->MultiCell(35, $altoFila, "{$remanente_cantidad}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');

	$color = selectorColor($totalPresupuesto, $totalRemanente);
	if ( !isset($directos[$key+1]["tipo"]) || $detalle["tipo"] !== $directos[$key+1]["tipo"]  ) {
		$totalPresupuesto = number_format($totalPresupuesto,2);
		$totalRemanente = number_format($totalRemanente,2);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño

		$pdf->MultiCell(80, 7, "Total {$tipo}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 7, "$ {$totalPresupuesto}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFillColor($color[0],$color[1],$color[2]); // Color de fondo
		$pdf->MultiCell(35, 7, "$ {$totalRemanente} ", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->SetFillColor(256, 256, 256); // Color de fondo
		$pdf->MultiCell(35, '7', "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
		$pdf->AddPage(); // Agregar nueva página

		if ($key < count($directos)-1) {
			
			$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(230, 230, 230); // Color de fondo
			$pdf->MultiCell(80, 9, "Descripcion", 1, 'C', 1, 0, 15, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 9, "Código", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(30, 9, "Tipo", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 9, "Unidad.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 9, "Cantidad", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(30, 9, "Presupuesto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(35, 9, "Remanente", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(35, 9, "Remanente Cantidad", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');
	
			$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(256, 256, 256); // Color de fondo
		}

		$totalPresupuesto=0;
		$totalRemanente=0;
	}

	if ( $y_end > 180 && $partida < count($directos) ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 30);
		$pdf->SetFillColor(230, 230, 230); // Color de fondo
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(80, 9, "Descripcion", 1, 'C', 1, 0, 15, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Código", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 9, "Tipo", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Unidad.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Cantidad", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 9, "Presupuesto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(35, 9, "Remanente", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(35, 9, "Remanente Cantidad", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
		$pdf->SetFillColor(256, 256, 256); // Color de fondo
	}

}

$pdf->setFont('times', 'B', 12); // Fuente, Tipo y Tamaño
$pdf->MultiCell(200, 14, "Indirectos", 0, '', 0, 1, '', '', true, 0, false, true, '14', 'M');

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(230, 230, 230); // Color de fondo
$pdf->MultiCell(80, 9, "Descripcion", 1, 'C', 1, 0, 15, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Código", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 9, "Tipo", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Unidad.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(20, 9, "Cantidad", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(30, 9, "Presupuesto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(35, 9, "Remanente", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(35, 9, "Remanente Cantidad", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
$pdf->SetFillColor(256, 256, 256); // Color de fondo

$totalPresupuesto=0;
$totalRemanente=0;
foreach($indirectos as $key => $detalle) {
	$partida = $key + 1;

	$totalPresupuesto += floatval($detalle["presupuesto"]);
	$totalRemanente += floatval($detalle["remanente"]);

	$codigo = mb_strtoupper(fString($detalle["codigo"]));
	$tipo = mb_strtoupper(fString($detalle["tipo"]));
	$unidad = mb_strtoupper(fString($detalle["unidad"]));
	$cantidad = number_format($detalle["cantidad"],2);
	$presupuesto = number_format($detalle["presupuesto"],2);
	$remanente = number_format($detalle["remanente"],2);
	$remanente_cantidad = number_format($detalle["remanente_cantidad"],2);

	$y_start = $pdf->GetY();
	$descripcion = mb_strtoupper(($detalle["descripcion"]));;
	$pdf->MultiCell(80, 8, "{$descripcion}", 1, 'C', 0, 1, 15, '', true, 0);
	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$pdf->MultiCell(20, $altoFila, "{$codigo}", 1, 'C', 0, 0, 95, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "{$tipo}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M',true);
	$pdf->MultiCell(20, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M',1);
	$pdf->MultiCell(20, $altoFila, "{$cantidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(30, $altoFila, "$ {$presupuesto}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	if($detalle["remanente"]<0) $pdf->SetFillColor(236, 83, 83); // Color de fondo
	$pdf->MultiCell(35, $altoFila, "$ {$remanente} ", 1, 'C', 1, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->SetFillColor(256, 256, 256); // Color de fondo
	$pdf->MultiCell(35, $altoFila, "{$remanente_cantidad}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');
	
	if ( !isset($indirectos[$key+1]["tipo"]) || $detalle["tipo"] !== $indirectos[$key+1]["tipo"] ){
		$color = selectorColor($totalPresupuesto, $totalRemanente);
		$totalPresupuesto = number_format($totalPresupuesto,2);
		$totalRemanente = number_format($totalRemanente,2);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(80, 7, "TOTAL {$tipo}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
		$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 7, "$ {$totalPresupuesto}", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->SetFillColor($color[0],$color[1],$color[2]); // Color de fondo
		$pdf->MultiCell(35, 7, "$ {$totalRemanente} ", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->SetFillColor(256, 256, 256); // Color de fondo
		$pdf->MultiCell(35, '7', "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');
		$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
		if ($key < count($indirectos)-1) {
			$pdf->AddPage(); // Agregar nueva página
			$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(230, 230, 230); // Color de fondo
			$pdf->MultiCell(80, 9, "Descripcion", 1, 'C', 1, 0, 15, '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 9, "Código", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(30, 9, "Tipo", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 9, "Unidad.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 9, "Cantidad", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(30, 9, "Presupuesto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(35, 9, "Remanente", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(35, 9, "Remanente Cantidad", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');

			$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
			$pdf->SetFillColor(256, 256, 256); // Color de fondo
		}

		$totalPresupuesto=0;
		$totalRemanente=0;
	}

	if ( $y_end > 180 && $partida < count($indirectos) ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 30);
		$pdf->SetFillColor(230, 230, 230); // Color de fondo
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(80, 9, "Descripcion", 1, 'C', 1, 0, 15, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Código", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 9, "Tipo", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Unidad.", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 9, "Cantidad", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(30, 9, "Presupuesto", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(35, 9, "Remanente", 1, 'C', 1, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(35, 9, "Remanente Cantidad", 1, 'C', 1, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 6); // Fuente, Tipo y Tamaño
		$pdf->SetFillColor(256, 256, 256); // Color de fondo

	}

}
// Crear una tabla que ocupe toda la altura
function selectorColor($presupuesto, $remanente)
{	
	if ($presupuesto <= 0 || $remanente <= 0) {
		return array(236, 83, 83);
	}
	// Calcular el porcentaje de remanente
	$porcentajeRemanente = ($remanente / $presupuesto) * 100;

    // Definimos una escala de colores según el porcentaje
    if ($porcentajeRemanente == 100) {
        // Rojo intenso (mucho remanente)
        $color = array(256, 256, 256);
    } elseif ($porcentajeRemanente > 30) {
        // Verde (remanente moderado)
        $color = array(71, 159, 118);
    } else {
        // Verde (poco remanente)
        $color = array(253, 126, 20);
    }

    return $color;
}

$pdf->Output("Proforma de {$obra->descripcion}.pdf", 'I');