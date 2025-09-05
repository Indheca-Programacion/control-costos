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
			$this->Rect(5, 5, 65, 22, 'DF', array(), array(222,222,222));
			$this->Image($this->logo, 6, 5, 63, 22, $extension, '', '', false, 300, '', false, false, 0, 'CM', false, false);
		
			$this->setCellPaddings(1, 1, 1, 1); // set cell padding

			// Title
			// $this->Rect(70, 5, 95, 11, 'D', array(), array(222,222,222));
			$this->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(165, 164, 157); // Color de fondo
			$this->MultiCell(95, 11, "REQUISICIÓN DE PERSONAL", 1, 'C', 1, 0, 70, 5, true);

			// $this->Rect(165, 5, 40, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(255, 255, 255); // Color del texto
			$this->SetFillColor(126, 126, 126); // Color de fondo
			$this->MultiCell(40, 11, "FO-IGC-AD-01.01 \n REV 06", 1, 'C', 1, 1, '', '', true);

			// $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
			$this->SetTextColor(0, 0, 0); // Color del texto
			$this->SetFillColor(222, 222, 222); // Color de fondo
			$this->MultiCell(95, 11, "SISTEMA DE GESTIÓN INTEGRAL \n ISO 9001:2015, ISO 14001:2015, ISO 45001:2018", 1, 'C', 1, 0, 70, 16, true);

			$this->MultiCell(40, 11, "PÁGINA {$this->getPage()} DE {$this->getNumPages()}", 1, 'C', 1, 1, '', '', true, 0, false, true, '11', 'M');

			// $this->Rect(165, 16, 40, 11, 'D', array(), array(222,222,222));

		$this->Ln(2); // Salto de Línea
		// $fechaCreacion = fFechaLarga($this->fechaCreacion);
		$fecha = strtotime($this->fechaCreacion);

		$diaSemana = fNombreDia(date("w", $fecha));
		$dia = date("d", $fecha);
		$mes = fNombreMes(date("n", $fecha));
		$year = date("Y", $fecha);
		
		$fechaReq ="";
		if($this->fechaRequerida !== null && $this->fechaRequerida !== ""){
			$fechaReq = strtotime($this->fechaRequerida);
			$fechaReq =  fNombreDia(date("w", $fechaReq)).", ".date("d", $fechaReq)." de ".fNombreMes(date("n", $fechaReq)) ." de ".date("Y", $fechaReq);
		}
		$folio = $this->folio;
		
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->fechaRequerida = $requisicion->fecha_requisicion;

$pdf->logo = Route::rutaServidor()."vistas/img/empresas/62302058.png";

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

$pdf->SetXY(5, 40);
$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño

$pdf->MultiCell(20, 5, "FOLIO ", 0, 'R', 0, 0, 130, 30, true);
$pdf->MultiCell(70, 5, "$requisicion->folio", 0, 'C', 0, 1, '', '', true);
$pdf->Line(165, 35, 205, 35, false);

$pdf->Ln(2); // Salto de Línea

$pdf->MultiCell(50, 7, "FECHA DE REQUISICIÓN:", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(22, 7, "$requisicion->fecha_requisicion", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->MultiCell(50, 5, "NUMERO DE CONTRATO:", 0, '', 0, 0, 110, 40, true);
$pdf->MultiCell(40, 5, "$requisicion->contrato", 0, 'C', 0, 0, '', '', true);
$pdf->Line(155, 45, 200, 45, false);

$pdf->Ln(4); // Salto de Línea

$pdf->MultiCell(50, 5, "ORDEN DE TRABAJO:", 0, '', 0, 0, 110, '', true);
$pdf->MultiCell(40, 5, "$requisicion->orden_trabajo", 0, 'C', 0, 0, '', '', true);
$pdf->Line(155, 49, 200, 49, false);

$pdf->Ln(10); // Salto de Línea
$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "JEFE INMEDIATO (SOLICITANTE)", 0, 'C', 0, 0, '', '', true);
$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "$requisicion->jefe_inmediato", 0, 'C', 0, 0, '', 50, true);
$pdf->Line(30, 55, 100, 55, false);

$pdf->Ln(4); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "CARGO QUE DESEMPEÑA", 0, 'C', 0, 0, 120, '', true);
$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "$requisicion->cargo", 0, 'C', 0, 0, 120, 50, true);
$pdf->Line(120, 55, 190, 55, false);

$pdf->Ln(10); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "ÁREA", 0, 'C', 0, 0, '', 65, true);
$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "$requisicion->area", 0, 'C', 0, 0, '', 60, true);
$pdf->Line(30, 65, 100, 65, false);

$pdf->Ln(4); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "DEPARTAMENTO", 0, 'C', 0, 0, 120, 65, true);
$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "$requisicion->departamento", 0, 'C', 0, 0, 120, 60, true);
$pdf->Line(120, 65, 190, 65, false);

$pdf->Ln(12); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "I.	INFORMACIÓN SOBRE LA VACANTE", 0, 'C', 0, 0, '', '', true);

$pdf->Ln(8); // Salto de Línea
$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño
$razon = ($requisicion->razon == 1) ? "X" : "";
$pdf->MultiCell(7, 5, "$razon", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "CREACIÓN DE CARGO", 0, '', 0, 0, '', '', true);
$razon = ($requisicion->razon == 2) ? "X" : "";
$pdf->MultiCell(7, 5, "$razon", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "REEMPLAZO TEMPORAL", 0, '', 0, 0, '', '', true);
$razon = ($requisicion->razon == 3) ? "X" : "";
$pdf->MultiCell(7, 5, "$razon", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "REEMPLAZO DEFINITIVO", 0, '', 0, 0, '', '', true);

$pdf->Ln(8); // Salto de Línea

$pdf->MultiCell(70, 5, "LA VANCANTE SE PRODUJÓ POR:", 0, '', 0, 0, '', '', true);

$pdf->Ln(8); // Salto de Línea
$origen = ($requisicion->origen == 1) ? "X" : "";
$pdf->MultiCell(7, 5, "$origen", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "RENUNCIA DEL TITULAR", 0, '', 0, 0, '', '', true);

$origen = ($requisicion->origen == 2) ? "X" : "";
$pdf->MultiCell(7, 5, "$origen", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "CANCELACIÓN DEL CONTRATO", 0, '', 0, 0, '', '', true);

$origen = ($requisicion->origen == 3) ? "X" : "";
$pdf->MultiCell(7, 5, "$origen", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "INCREMENTO DE ACTIVIDADES", 0, '', 0, 0, '', '', true);

$pdf->Ln(12); // Salto de Línea

$origen = ($requisicion->origen == 4) ? "X" : "";
$pdf->MultiCell(7, 5, "$origen", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "INCAPACIDAD", 0, '', 0, 0, '', '', true);

$origen = ($requisicion->origen == 5) ? "X" : "";
$pdf->MultiCell(7, 5, "$origen", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "VACACIONES", 0, '', 0, 0, '', '', true);

$origen = ($requisicion->origen == 6) ? "X" : "";
$pdf->MultiCell(7, 5, "$origen", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(50, 5, "PROMOCIÓN O TRASLADO", 0, '', 0, 0, '', '', true);

$pdf->Ln(12); // Salto de Línea
$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(100, 7, "FECHA EN QUE DEBE ESTAR CUBIERTA LA VACANTE:", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(60, 7, "$requisicion->fecha_cubrir", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->Ln(12); // Salto de Línea

$pdf->MultiCell(40, 7, "EDAD SUGERIDA:", 0, '', 0, 0, '', '', true, 0, false, true);
$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(20, 7, "Entre:", 0, 'C', 0, 0, '', '', true, 0, false, true);
$pdf->MultiCell(7, 5, "$requisicion->edad_init", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(10, 7, "Y:", 0, 'C', 0, 0, '', '', true, 0, false, true);
$pdf->MultiCell(7, 5, "$requisicion->edad_end", 1, 'C', 0, 0, '', '', true);
$pdf->MultiCell(30, 7, "AÑOS DE EDAD:", 0, 'C', 0, 0, '', '', true, 0, false, true);

$pdf->Ln(12); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "II. PERFIL EDUCACIÓN.", 0, '', 0, 0, '', '', true);

$pdf->Ln(12); // Salto de Línea

$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(40, 7, "ESPECIALIDAD:", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(120, 7, "$requisicion->especialidad", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->Ln(7);

$pdf->MultiCell(40, 7, "POST-GRADO:", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(120, 7, "$requisicion->postgrado", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->Ln(7);

$pdf->MultiCell(40, 7, "LICENCIATURA:", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(120, 7, "$requisicion->licenciatura", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->Ln(7);

$pdf->MultiCell(40, 7, "CARRERA TECNICA:", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(120, 7, "$requisicion->carrera", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->Ln(7);

$pdf->MultiCell(40, 7, "OTROS:", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(120, 7, "$requisicion->otros_estudios", 1, '', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->Ln(12); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 10); // Fuente, Tipo y Tamaño
$pdf->MultiCell(70, 5, "III.	INFORMACIÓN SOBRE EL CARGO.", 0, '', 0, 0, '', '', true);

$pdf->Ln(10); // Salto de Línea

$pdf->MultiCell(45, 5, "NOMBRE DEL PUESTO:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(100, 5, "$descripcion", 0, 'C', 0, 0, '', '', true);
$pdf->Line(72, 212, 190, 212, false);

$pdf->SetFont('helvetica', 'N', 10); // Fuente, Tipo y Tamaño

$pdf->Ln(10); // Salto de Línea

$pdf->MultiCell(35, 5, "DEDICACION:", 0, '', 0, 0, '', '', true);

$dedicacion  = ($requisicion->dedicacion == 1) ? "X" : "";
$pdf->MultiCell(10, 5, "T.C.", 0, 'R', 0, 0, '', '', true);
$pdf->MultiCell(7, 5, "$dedicacion", 1, 'C', 0, 0, '', '', true);

$dedicacion  = ($requisicion->dedicacion == 2) ? "X" : "";
$pdf->MultiCell(15, 5, "M.T.", 0, 'R', 0, 0, '', '', true);
$pdf->MultiCell(7, 5, "$dedicacion", 1, 'C', 0, 0, '', '', true);

$pdf->MultiCell(25, 5, "HORARIO:", 0, 'R', 0, 0, '', '', true);

$horario  = ($requisicion->horario == 1) ? "X" : "";
$pdf->MultiCell(20, 5, "DIURNO", 0, 'R', 0, 0, '', '', true);
$pdf->MultiCell(7, 5, "$horario", 1, 'C', 0, 0, '', '', true);

$horario  = ($requisicion->horario == 2) ? "X" : "";
$pdf->MultiCell(25, 5, "NOCTURNO", 0, 'R', 0, 0, '', '', true);
$pdf->MultiCell(7, 5, "$horario", 1, 'C', 0, 0, '', '', true);

$pdf->Ln(10); // Salto de Línea

$pdf->MultiCell(160, 7, "FUNCIONES A REALIZAR EN EL CARGO. (DESCRIPCIÓN BREVE Y GENERAL):", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->Ln(7);
$pdf->MultiCell(160, 20, "$requisicion->funciones", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');

$pdf->Ln(35); // Salto de Línea
if ( !is_null($autorizoFirma) ) {
	$extension = mb_strtoupper(substr($autorizoFirma, -3, 3));
	if ( $extension == 'PNG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($autorizoFirma, 35, 270, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

if ( !is_null($AutorizoRHFirma) ) {
	$extension = mb_strtoupper(substr($AutorizoRHFirma, -3, 3));
	if ( $extension == 'PNG')  $pdf->setJPEGQuality(75); // Calidad de imágen

	$pdf->Image($AutorizoRHFirma, 120, 270, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
}

$pdf->MultiCell(80, 5, "Jefe de Área", 0, 'C', 0, 0, '', '', true);
$pdf->MultiCell(80, 5, "Jefe de Recursos Humanos", 0, 'C', 0, 0, '', '', true);

$pdf->Line(120, 290, 180, 290, false);
$pdf->Line(40, 290, 100, 290, false);

$pdf->AddPage(); // Agregar nueva página

$pdf->MultiCell(45, 5, "NOMBRE DE LA OBRA:   ", 0, '', 0, 0, '', '', true);
$pdf->Line(70, 32, 120, 32, false);
$pdf->Ln(8); // Salto de Línea
$pdf->MultiCell(46, 5, "NÚMERO DE CONTRATO:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(46, 5, "$requisicion->contrato", 0, 'C', 0, 0, '', '', true);
$pdf->Line(75, 40, 120, 40, false);
$pdf->Ln(8); // Salto de Línea
$pdf->MultiCell(45, 5, "ORDEN DE TRABAJO:", 0, '', 0, 0, '', '', true);
$pdf->MultiCell(45, 5, "$requisicion->orden_trabajo", 0, 'C', 0, 0, '', '', true);
$pdf->Line(68, 48, 120, 48, false);
$pdf->Ln(8); // Salto de Línea
$pdf->MultiCell(45, 5, "RESPONSABLE:", 0, '', 0, 0, '', '', true);
$pdf->Line(60, 56, 120, 56, false);
$pdf->Ln(8); // Salto de Línea

$categoria  = ($requisicion->categoria == 1) ? "X" : "";

$pdf->MultiCell(40, 7, "OBRA ( $categoria )", 0, 'C', 0, 0, '', '', true);

$categoria  = ($requisicion->categoria == 2) ? "X" : "";

$pdf->MultiCell(40, 7, "ADMINISTRACIÓN ( $categoria )", 0, 'C', 0, 0, '', '', true);

$categoria  = ($requisicion->categoria == 3) ? "X" : "";
$pdf->MultiCell(40, 7, "TALLER ( $categoria )", 0, 'C', 0, 0, '', '', true);

$categoria  = ($requisicion->categoria == 4) ? "X" : "";
$pdf->MultiCell(40, 7, "ARRENDAMIENTO ( $categoria )", 0, 'C', 0, 0, '', '', true);

$pdf->Ln(8); // Salto de Línea

$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
$pdf->MultiCell(50, 9, "NOMBRE DEL TRABAJADOR", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(28, 9, "CATEGORIA", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(28, 9, "FECHA REQUERIDA", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 9, "FECHA DE TERMINO", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
$pdf->MultiCell(40, 9, "SALARIO NETO", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
foreach($empleados as $key => $empleado) {
	$nombre = mb_strtoupper($empleado['nombreCompleto']);

	$partida = $key + 1;

	$y_start = $pdf->GetY();
	if ( $y_start > 223 && $partida == count($empleados) ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 51);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(50, 9, "NOMBRE DEL TRABAJADOR", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(28, 9, "CATEGORIA", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(28, 9, "FECHA REQUERIDA", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(40, 9, "FECHA DE TERMINO", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(40, 9, "SALARIO NETO", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

		$y_start = $pdf->GetY();
	}

	$pdf->MultiCell(50, 0, "{$nombre}", 1, 'C', 0, 1, 12, '', true, 0);

	$y_end = $pdf->GetY();
	$altoFila = $y_end - $y_start;
	$pdf->MultiCell(28, $altoFila, "{$descripcion}", 1, 'C', 0, 0, 62, $y_start, true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(28, $altoFila, "{$requisicion->fecha_requisicion}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(40, $altoFila, "{$requisicion->fecha_inicio}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
	$pdf->MultiCell(40, $altoFila, "{$requisicion->salario_semanal}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');

	if ( $y_end > 270 ) {
		$pdf->AddPage();

		$pdf->SetXY(5, 51);
		$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
		$pdf->MultiCell(20, 7, "PARTIDA", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(80, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
		$pdf->MultiCell(60, 7, "COSTO", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

		$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	}
}
$y = $pdf->getY();
if ( $y > 228 ) {
	$pdf->AddPage();

	$pdf->SetXY(5, 51);
	$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
	$pdf->MultiCell(20, 7, "PARTIDA", 1, 'C', 0, 0, 5, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(80, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(60, 7, "COSTO", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

	$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
	$y = $pdf->getY();
}

while ($y <= 200) {
	$pdf->MultiCell(50, 9, "", 1, 'C', 0, 0, 12, '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(28, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(28, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(40, 9, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
	$pdf->MultiCell(40, 9, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

    $y = $pdf->getY();
}

$pdf->Ln(8); // Salto de Línea

$pdf->MultiCell(40, 9, "OBSERVACIONES:", 0, '', 0, 1, '', '', true, 0, false, true, '7', 'M');

$pdf->MultiCell(142, 9, "$requisicion->observacion", 0, '', 0, 1, '', '', true);

$pdf->Line(31, 224, 172, 224, false);
$pdf->Line(31, 228, 172, 228, false);
$pdf->Line(31, 232, 172, 232, false);
$pdf->Line(31, 236, 172, 236, false);


$pdf->Ln(40); // Salto de Línea

$pdf->Line(35, 268, 73, 268, false);
$pdf->MultiCell(47, 7, "SOLICITÓ", 0, 'C', 0, 0, '', '', true);
$pdf->Line(78, 268, 120, 268, false);
$pdf->MultiCell(47, 7, "REVISÓ", 0, 'C', 0, 0, '', '', true);
$pdf->Line(125, 268, 170, 268, false);
$pdf->MultiCell(47, 7, "AUTORIZÓ", 0, 'C', 0, 0, '', '', true);


$pdf->Output("Requisición {$pdf->folio}.pdf", 'I');