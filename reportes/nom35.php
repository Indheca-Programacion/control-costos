<?php
//============================================================+
// File name   : requisicion.php
// Description : Formato de Requisición
//============================================================+
require_once "../../vendor/autoload.php";

use App\Route;

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {

	//Page header
	public function Header() {
	}

	// Page footer
	public function Footer() {
	}
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->setTitle("Reporte");
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

$pdf->setFont('times', 'BI', 12); // Fuente, Tipo y Tamaño

$pdf->setCellPaddings(1, 1, 1, 1); // set cell padding
// $this->setCellMargins(1, 1, 1, 1); // set cell margins

$pdf->AddPage(); // Agregar nueva página

$pdf->SetY(20);
$pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño

$pdf->multicell(0, 5, "REPORTE DE NOM-035", 0, 'C', 0, 1, '', '', true);
$pdf->SetFillColor(236, 246, 255);
$pdf->multicell(0, 5, "RESULTADOS GENERALES", 0, 'C', 1, 1, '', '', true);

$calificacionFinalTexto = '';
$calificacionFinalDescripcion = '';
$resultados["total"] = number_format($resultados["total"] /count($processedData),2);
switch (true) {
    case ($resultados["total"] < 20):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $calificacionFinalDescripcion = "El estrés no representa un problema para la organización.";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["total"] < 45):
        $calificacionFinalTexto = "BAJO";
        $calificacionFinalDescripcion = "Es necesario una mayor difusión de la política de prevención de riesgos psicosociales y programas para la prevención de los factores de riesgo psicosocial, la promoción de un entorno organizacional favorable y la prevención de la violencia laboral.";
        $pdf->SetFillColor(107, 245, 110);
    break;
    case ($resultados["total"] < 70):
        $calificacionFinalTexto = "MEDIO";
        $calificacionFinalDescripcion = "Se requiere revisar la política de prevención de riesgos psicosociales y programas para la prevención de los factores de riesgo psicosocial, la promoción de un entorno organizacional favorable y la prevención de la violencia laboral, así como reforzar su aplicación y difusión, mediante un Programa de intervención.";
        $pdf->SetFillColor(255, 255, 0);
    break;
    case ($resultados["total"] < 90):
        $calificacionFinalTexto = "ALTO";
        $calificacionFinalDescripcion ="Se requiere realizar un análisis de cada categoría y dominio, de manera que se puedan determinar las acciones de intervención apropiadas a través de un Programa de intervención, que podrá incluir una evaluación específica1 y deberá incluir una campaña de sensibilización, revisar la política de prevención de riesgos psicosociales y programas para la prevención de los factores de riesgo psicosocial, la promoción de un entorno organizacional favorable y la prevención de la violencia laboral, así como reforzar su aplicación y difusión.";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["total"] >= 90):
        $calificacionFinalTexto = "MUY ALTO";
        $calificacionFinalDescripcion ="Se requiere realizar el análisis de cada categoría y dominio para establecer las acciones de intervención apropiadas, mediante un Programa de intervención que deberá incluir evaluaciones específicas1, y contemplar campañas de sensibilización, revisar la política de prevención de riesgos psicosociales y programas para la prevención de los factores de riesgo psicosocial, la promoción de un entorno organizacional favorable y la prevención de la violencia laboral, así como reforzar su aplicación y difusión.";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}

$pdf->multicell(0, 5, "{$resultados["total"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->multicell(0, 5, "$calificacionFinalDescripcion", 0, 'C', 1, 1, '', '', true);
$pdf->SetTextColor(0, 0, 0); // White text

$pdf->Ln(5); // Salto de línea

$pdf->multicell(0, 5, "RESULTADOS POR CATEGORÍA", 0, 'C', 0, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$resultados["Ambiente de trabajo"] = number_format($resultados["Ambiente de trabajo"] /count($processedData),2);
switch (true) {
    case ($resultados["Ambiente de trabajo"] < 3):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Ambiente de trabajo"] <= 3 && $resultados["Ambiente de trabajo"] < 5):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
    break;
    case ($resultados["Ambiente de trabajo"] <= 5 && $resultados["Ambiente de trabajo"] < 7):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
    break;
    case ($resultados["Ambiente de trabajo"] <= 7 && $resultados["Ambiente de trabajo"] < 9):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Ambiente de trabajo"] >= 9):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "AMBIENTE DE TRABAJO: {$resultados["Ambiente de trabajo"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Factores propios de la actividad"] = number_format($resultados["Factores propios de la actividad"] /count($processedData),2);
switch (true) {
    case ($resultados["Factores propios de la actividad"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Factores propios de la actividad"] <= 10 && $resultados["Factores propios de la actividad"] < 20):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
    break;
    case ($resultados["Factores propios de la actividad"] <= 20 && $resultados["Factores propios de la actividad"] < 30):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
    break;
    case ($resultados["Factores propios de la actividad"] <= 30 && $resultados["Factores propios de la actividad"] < 40):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Factores propios de la actividad"] >= 40):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "FACTORES PROPIOS DE LA ACTIVIDAD: {$resultados["Factores propios de la actividad"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Organización del tiempo de trabajo"] = number_format($resultados["Organización del tiempo de trabajo"] /count($processedData),2);
switch (true) {
    case ($resultados["Organización del tiempo de trabajo"] < 4):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Organización del tiempo de trabajo"] <= 4 && $resultados["Organización del tiempo de trabajo"] < 6):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
    break;
    case ($resultados["Organización del tiempo de trabajo"] <= 6 && $resultados["Organización del tiempo de trabajo"] < 9):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
    break;
    case ($resultados["Organización del tiempo de trabajo"] <= 9 && $resultados["Organización del tiempo de trabajo"] < 12):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Organización del tiempo de trabajo"] >= 12):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Organización del tiempo de trabajo: {$resultados["Organización del tiempo de trabajo"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Liderazgo y relaciones en el trabajo"] = number_format($resultados["Liderazgo y relaciones en el trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Liderazgo y relaciones en el trabajo"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Liderazgo y relaciones en el trabajo"] <= 10 && $resultados["Liderazgo y relaciones en el trabajo"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Liderazgo y relaciones en el trabajo"] <= 18 && $resultados["Liderazgo y relaciones en el trabajo"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Liderazgo y relaciones en el trabajo"] <= 28 && $resultados["Liderazgo y relaciones en el trabajo"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Liderazgo y relaciones en el trabajo"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "LIDERAZGO Y RELACIONES EN EL TRABAJO: {$resultados["Liderazgo y relaciones en el trabajo"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);

$pdf->Ln(5); // Agregar una ligera separación

$pdf->SetFillColor(236, 246, 255);
$pdf->multicell(0, 5, "RESULTADOS POR DOMINIO Y DIMENSIONES", 0, 'C', 1, 1, '', '', true);
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Condiciones en el ambiente de trabajo"] = number_format($resultados["Condiciones en el ambiente de trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Condiciones en el ambiente de trabajo"] < 3):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Condiciones en el ambiente de trabajo"] <= 3 && $resultados["Condiciones en el ambiente de trabajo"] < 5):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Condiciones en el ambiente de trabajo"] <= 5 && $resultados["Condiciones en el ambiente de trabajo"] < 7):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Condiciones en el ambiente de trabajo"] <= 7 && $resultados["Condiciones en el ambiente de trabajo"] < 9):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Condiciones en el ambiente de trabajo"] >= 9):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Condiciones en el ambiente de trabajo: {$resultados["Condiciones en el ambiente de trabajo"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Carga de trabajo"] = number_format($resultados["Carga de trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Carga de trabajo"] < 12):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Carga de trabajo"] <= 12 && $resultados["Carga de trabajo"] < 16):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Carga de trabajo"] <= 16 && $resultados["Carga de trabajo"] < 20):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Carga de trabajo"] <= 20 && $resultados["Carga de trabajo"] < 24):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Carga de trabajo"] >= 24):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Carga de trabajo: {$resultados["Carga de trabajo"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Falta de control sobre el trabajo"] = number_format($resultados["Falta de control sobre el trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Falta de control sobre el trabajo"] < 5):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Falta de control sobre el trabajo"] <= 5 && $resultados["Falta de control sobre el trabajo"] < 8):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Falta de control sobre el trabajo"] <= 8 && $resultados["Falta de control sobre el trabajo"] < 11):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Falta de control sobre el trabajo"] <= 11 && $resultados["Falta de control sobre el trabajo"] < 14):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Falta de control sobre el trabajo"] >= 14):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Falta de control sobre el trabajo: {$resultados["Falta de control sobre el trabajo"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Jornada de trabajo"] = number_format($resultados["Jornada de trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Jornada de trabajo"] < 1):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Jornada de trabajo"] <= 1 && $resultados["Jornada de trabajo"] < 2):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Jornada de trabajo"] <= 2 && $resultados["Jornada de trabajo"] < 4):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Jornada de trabajo"] <= 4 && $resultados["Jornada de trabajo"] < 6):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Jornada de trabajo"] >= 6):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Jornada de trabajo: {$resultados["Jornada de trabajo"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Interferencia en la relación trabajo-familia"] = number_format($resultados["Interferencia en la relación trabajo-familia"] /count($processedData), 2);
switch (true) {
    case ($resultados["Interferencia en la relación trabajo-familia"] < 1):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Interferencia en la relación trabajo-familia"] <= 1 && $resultados["Interferencia en la relación trabajo-familia"] < 2):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Interferencia en la relación trabajo-familia"] <= 2 && $resultados["Interferencia en la relación trabajo-familia"] < 4):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Interferencia en la relación trabajo-familia"] <= 4 && $resultados["Interferencia en la relación trabajo-familia"] < 6):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Interferencia en la relación trabajo-familia"] >= 6):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Interferencia en la relación trabajo-familia: {$resultados["Interferencia en la relación trabajo-familia"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Liderazgo"] = number_format($resultados["Liderazgo"] /count($processedData),2);
switch (true) {
    case ($resultados["Liderazgo"] < 3):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Liderazgo"] <= 3 && $resultados["Liderazgo"] < 5):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Liderazgo"] <= 5 && $resultados["Liderazgo"] < 8):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Liderazgo"] <= 8 && $resultados["Liderazgo"] < 11):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Liderazgo"] >= 11):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Liderazgo: {$resultados["Liderazgo"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Relaciones en el trabajo"] = number_format($resultados["Relaciones en el trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Relaciones en el trabajo"] < 5):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Relaciones en el trabajo"] <= 5 && $resultados["Relaciones en el trabajo"] < 8):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Relaciones en el trabajo"] <= 8 && $resultados["Relaciones en el trabajo"] < 11):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Relaciones en el trabajo"] <= 11 && $resultados["Relaciones en el trabajo"] < 14):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Relaciones en el trabajo"] >= 14):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Relaciones en el trabajo: {$resultados["Relaciones en el trabajo"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Violencia"] = number_format($resultados["Violencia"] /count($processedData), 2);
switch (true) {
    case ($resultados["Violencia"] < 7):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Violencia"] <= 7 && $resultados["Violencia"] < 10):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Violencia"] <= 10 && $resultados["Violencia"] < 13):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Violencia"] <= 13 && $resultados["Violencia"] < 16):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Violencia"] >= 16):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Violencia: {$resultados["Violencia"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Condiciones peligrosas e inseguras"] = number_format($resultados["Condiciones peligrosas e inseguras"] /count($processedData), 2);
switch (true) {
    case ($resultados["Condiciones peligrosas e inseguras"] < 3):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Condiciones peligrosas e inseguras"] <= 3 && $resultados["Condiciones peligrosas e inseguras"] < 5):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Condiciones peligrosas e inseguras"] <= 5 && $resultados["Condiciones peligrosas e inseguras"] < 7):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Condiciones peligrosas e inseguras"] <= 7 && $resultados["Condiciones peligrosas e inseguras"] < 9):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Condiciones peligrosas e inseguras"] >= 9):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Condiciones peligrosas e inseguras: {$resultados["Condiciones peligrosas e inseguras"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Condiciones deficientes e insalubres"] = number_format($resultados["Condiciones deficientes e insalubres"] /count($processedData), 2);
switch (true) {
    case ($resultados["Condiciones deficientes e insalubres"] < 3):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Condiciones deficientes e insalubres"] <= 3 && $resultados["Condiciones deficientes e insalubres"] < 5):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Condiciones deficientes e insalubres"] <= 5 && $resultados["Condiciones deficientes e insalubres"] < 7):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Condiciones deficientes e insalubres"] <= 7 && $resultados["Condiciones deficientes e insalubres"] < 9):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Condiciones deficientes e insalubres"] >= 9):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Condiciones deficientes e insalubres: {$resultados["Condiciones deficientes e insalubres"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Trabajos peligrosos"] = number_format($resultados["Trabajos peligrosos"] /count($processedData), 2);
switch (true) {
    case ($resultados["Trabajos peligrosos"] < 3):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Trabajos peligrosos"] <= 3 && $resultados["Trabajos peligrosos"] < 5):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Trabajos peligrosos"] <= 5 && $resultados["Trabajos peligrosos"] < 7):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Trabajos peligrosos"] <= 7 && $resultados["Trabajos peligrosos"] < 9):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Trabajos peligrosos"] >= 9):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Trabajos peligrosos: {$resultados["Trabajos peligrosos"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Cargas cuantitativas"] = number_format($resultados["Cargas cuantitativas"] /count($processedData), 2);
switch (true) {
    case ($resultados["Cargas cuantitativas"] < 12):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Cargas cuantitativas"] <= 12 && $resultados["Cargas cuantitativas"] < 16):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Cargas cuantitativas"] <= 16 && $resultados["Cargas cuantitativas"] < 20):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Cargas cuantitativas"] <= 20 && $resultados["Cargas cuantitativas"] < 24):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Cargas cuantitativas"] >= 24):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Cargas cuantitativas: {$resultados["Cargas cuantitativas"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Ritmos de trabajo acelerado"] = number_format($resultados["Ritmos de trabajo acelerado"] /count($processedData), 2);
switch (true) {
    case ($resultados["Ritmos de trabajo acelerado"] < 12):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Ritmos de trabajo acelerado"] <= 12 && $resultados["Ritmos de trabajo acelerado"] < 16):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Ritmos de trabajo acelerado"] <= 16 && $resultados["Ritmos de trabajo acelerado"] < 20):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Ritmos de trabajo acelerado"] <= 20 && $resultados["Ritmos de trabajo acelerado"] < 24):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Ritmos de trabajo acelerado"] >= 24):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Ritmos de trabajo acelerado: {$resultados["Ritmos de trabajo acelerado"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Carga mental"] = number_format($resultados["Carga mental"] /count($processedData), 2);
switch (true) {
    case ($resultados["Carga mental"] < 12):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Carga mental"] <= 12 && $resultados["Carga mental"] < 16):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Carga mental"] <= 16 && $resultados["Carga mental"] < 20):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Carga mental"] <= 20 && $resultados["Carga mental"] < 24):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Carga mental"] >= 24):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Carga mental: {$resultados["Carga mental"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Cargas de alta responsabilidad"] = number_format($resultados["Cargas de alta responsabilidad"] /count($processedData), 2);
switch (true) {
    case ($resultados["Cargas de alta responsabilidad"] < 12):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Cargas de alta responsabilidad"] <= 12 && $resultados["Cargas de alta responsabilidad"] < 16):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Cargas de alta responsabilidad"] <= 16 && $resultados["Cargas de alta responsabilidad"] < 20):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Cargas de alta responsabilidad"] <= 20 && $resultados["Cargas de alta responsabilidad"] < 24):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Cargas de alta responsabilidad"] >= 24):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Cargas de alta responsabilidad: {$resultados["Cargas de alta responsabilidad"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Cargas contradictorias o inconsistentes"] = number_format($resultados["Cargas contradictorias o inconsistentes"] /count($processedData), 2);
switch (true) {
    case ($resultados["Cargas contradictorias o inconsistentes"] < 12):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Cargas contradictorias o inconsistentes"] <= 12 && $resultados["Cargas contradictorias o inconsistentes"] < 16):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Cargas contradictorias o inconsistentes"] <= 16 && $resultados["Cargas contradictorias o inconsistentes"] < 20):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Cargas contradictorias o inconsistentes"] <= 20 && $resultados["Cargas contradictorias o inconsistentes"] < 24):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Cargas contradictorias o inconsistentes"] >= 24):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Cargas contradictorias o inconsistentes: {$resultados["Cargas contradictorias o inconsistentes"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Jornadas de trabajo extensas"] = number_format($resultados["Jornadas de trabajo extensas"] /count($processedData), 2);
switch (true) {
    case ($resultados["Jornadas de trabajo extensas"] < 1):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Jornadas de trabajo extensas"] <= 1 && $resultados["Jornadas de trabajo extensas"] < 2):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Jornadas de trabajo extensas"] <= 2 && $resultados["Jornadas de trabajo extensas"] < 4):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Jornadas de trabajo extensas"] <= 4 && $resultados["Jornadas de trabajo extensas"] < 6):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Jornadas de trabajo extensas"] >= 6):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Jornadas de trabajo extensas: {$resultados["Jornadas de trabajo extensas"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Influencia del trabajo fuera del centro laboral"] = number_format($resultados["Influencia del trabajo fuera del centro laboral"] /count($processedData), 2);
switch (true) {
    case ($resultados["Influencia del trabajo fuera del centro laboral"] < 1):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Influencia del trabajo fuera del centro laboral"] <= 1 && $resultados["Influencia del trabajo fuera del centro laboral"] < 2):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Influencia del trabajo fuera del centro laboral"] <= 2 && $resultados["Influencia del trabajo fuera del centro laboral"] < 4):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Influencia del trabajo fuera del centro laboral"] <= 4 && $resultados["Influencia del trabajo fuera del centro laboral"] < 6):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Influencia del trabajo fuera del centro laboral"] >= 6):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Influencia del trabajo fuera del centro laboral: {$resultados["Influencia del trabajo fuera del centro laboral"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Influencia de las responsabilidades familiares"] = number_format($resultados["Influencia de las responsabilidades familiares"] /count($processedData), 2);
switch (true) {
    case ($resultados["Influencia de las responsabilidades familiares"] < 1):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Influencia de las responsabilidades familiares"] <= 1 && $resultados["Influencia de las responsabilidades familiares"] < 2):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Influencia de las responsabilidades familiares"] <= 2 && $resultados["Influencia de las responsabilidades familiares"] < 4):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Influencia de las responsabilidades familiares"] <= 4 && $resultados["Influencia de las responsabilidades familiares"] < 6):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Influencia de las responsabilidades familiares"] >= 6):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, strtoupper("Influencia de las responsabilidades familiares: {$resultados["Influencia de las responsabilidades familiares"]} - {$calificacionFinalTexto}"), 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Limitada o nula posibilidad de desarrollo"] = number_format($resultados["Limitada o nula posibilidad de desarrollo"] /count($processedData),2);
switch (true) {
    case ($resultados["Limitada o nula posibilidad de desarrollo"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Limitada o nula posibilidad de desarrollo"] <= 10 && $resultados["Limitada o nula posibilidad de desarrollo"] < 20):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
    break;
    case ($resultados["Limitada o nula posibilidad de desarrollo"] <= 20 && $resultados["Limitada o nula posibilidad de desarrollo"] < 30):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
    break;
    case ($resultados["Limitada o nula posibilidad de desarrollo"] <= 30 && $resultados["Limitada o nula posibilidad de desarrollo"] < 40):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Limitada o nula posibilidad de desarrollo"] >= 40):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "LIMITADA O NULA POSIBILIDAD DE DESARROLLO: {$resultados["Limitada o nula posibilidad de desarrollo"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Falta de control y autonomía sobre el trabajo"] = number_format($resultados["Falta de control y autonomía sobre el trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Falta de control y autonomía sobre el trabajo"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Falta de control y autonomía sobre el trabajo"] <= 10 && $resultados["Falta de control y autonomía sobre el trabajo"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Falta de control y autonomía sobre el trabajo"] <= 18 && $resultados["Falta de control y autonomía sobre el trabajo"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Falta de control y autonomía sobre el trabajo"] <= 28 && $resultados["Falta de control y autonomía sobre el trabajo"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Falta de control y autonomía sobre el trabajo"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "FALTA DE CONTROL Y AUTONOMÍA SOBRE EL TRABAJO: {$resultados["Falta de control y autonomía sobre el trabajo"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Escasa claridad de funciones"] = number_format($resultados["Escasa claridad de funciones"] /count($processedData), 2);
switch (true) {
    case ($resultados["Escasa claridad de funciones"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Escasa claridad de funciones"] <= 10 && $resultados["Escasa claridad de funciones"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Escasa claridad de funciones"] <= 18 && $resultados["Escasa claridad de funciones"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Escasa claridad de funciones"] <= 28 && $resultados["Escasa claridad de funciones"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Escasa claridad de funciones"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "ESCASA CLARIDAD DE FUNCIONES: {$resultados["Escasa claridad de funciones"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Limitada o inexistente capacitación"] = number_format($resultados["Limitada o inexistente capacitación"] /count($processedData),2);
switch (true) {
    case ($resultados["Limitada o inexistente capacitación"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Limitada o inexistente capacitación"] <= 10 && $resultados["Limitada o inexistente capacitación"] < 20):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
    break;
    case ($resultados["Limitada o inexistente capacitación"] <= 20 && $resultados["Limitada o inexistente capacitación"] < 30):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
    break;
    case ($resultados["Limitada o inexistente capacitación"] <= 30 && $resultados["Limitada o inexistente capacitación"] < 40):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Limitada o inexistente capacitación"] >= 40):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "LIMITADA O INEXISTENTE CAPACITACIÓN: {$resultados["Limitada o inexistente capacitación"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Escasa claridad de funciones"] = number_format($resultados["Escasa claridad de funciones"] /count($processedData), 2);
switch (true) {
    case ($resultados["Escasa claridad de funciones"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Escasa claridad de funciones"] <= 10 && $resultados["Escasa claridad de funciones"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Escasa claridad de funciones"] <= 18 && $resultados["Escasa claridad de funciones"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Escasa claridad de funciones"] <= 28 && $resultados["Escasa claridad de funciones"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Escasa claridad de funciones"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "ESCASA CLARIDAD DE FUNCIONES: {$resultados["Escasa claridad de funciones"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Características del liderazgo"] = number_format($resultados["Características del liderazgo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Características del liderazgo"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Características del liderazgo"] <= 10 && $resultados["Características del liderazgo"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Características del liderazgo"] <= 18 && $resultados["Características del liderazgo"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Características del liderazgo"] <= 28 && $resultados["Características del liderazgo"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Características del liderazgo"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "CARACTERÍSTICAS DEL LIDERAZGO: {$resultados["Características del liderazgo"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Relaciones sociales en el trabajo"] = number_format($resultados["Relaciones sociales en el trabajo"] /count($processedData), 2);
switch (true) {
    case ($resultados["Relaciones sociales en el trabajo"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Relaciones sociales en el trabajo"] <= 10 && $resultados["Relaciones sociales en el trabajo"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Relaciones sociales en el trabajo"] <= 18 && $resultados["Relaciones sociales en el trabajo"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Relaciones sociales en el trabajo"] <= 28 && $resultados["Relaciones sociales en el trabajo"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Relaciones sociales en el trabajo"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "RELACIONES SOCIALES EN EL TRABAJO: {$resultados["Relaciones sociales en el trabajo"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Violencia laboral"] = number_format($resultados["Violencia laboral"] /count($processedData), 2);
switch (true) {
    case ($resultados["Violencia laboral"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Violencia laboral"] <= 10 && $resultados["Violencia laboral"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Violencia laboral"] <= 18 && $resultados["Violencia laboral"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Violencia laboral"] <= 28 && $resultados["Violencia laboral"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Violencia laboral"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "VIOLENCIA LABORAL: {$resultados["Violencia laboral"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Cargas psicológicas emocionales"] = number_format($resultados["Cargas psicológicas emocionales"] /count($processedData),2);
switch (true) {
    case ($resultados["Cargas psicológicas emocionales"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Cargas psicológicas emocionales"] <= 10 && $resultados["Cargas psicológicas emocionales"] < 20):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
    break;
    case ($resultados["Cargas psicológicas emocionales"] <= 20 && $resultados["Cargas psicológicas emocionales"] < 30):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
    break;
    case ($resultados["Cargas psicológicas emocionales"] <= 30 && $resultados["Cargas psicológicas emocionales"] < 40):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Cargas psicológicas emocionales"] >= 40):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "CARGAS PSICOLÓGICAS EMOCIONALES: {$resultados["Cargas psicológicas emocionales"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(0.2); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text

$resultados["Deficiente relación con los colaboradores que supervisa"] = number_format($resultados["Deficiente relación con los colaboradores que supervisa"] /count($processedData), 2);
switch (true) {
    case ($resultados["Deficiente relación con los colaboradores que supervisa"] < 10):
        $calificacionFinalTexto = "NULO O DESPRECIABLE";
        $pdf->SetFillColor(155, 229, 247);
        break;
    case ($resultados["Deficiente relación con los colaboradores que supervisa"] <= 10 && $resultados["Deficiente relación con los colaboradores que supervisa"] < 18):
        $calificacionFinalTexto = "BAJO";
        $pdf->SetFillColor(107, 245, 110);
        break;
    case ($resultados["Deficiente relación con los colaboradores que supervisa"] <= 18 && $resultados["Deficiente relación con los colaboradores que supervisa"] < 28):
        $calificacionFinalTexto = "MEDIO";
        $pdf->SetFillColor(255, 255, 0);
        break;
    case ($resultados["Deficiente relación con los colaboradores que supervisa"] <= 28 && $resultados["Deficiente relación con los colaboradores que supervisa"] < 38):
        $calificacionFinalTexto = "ALTO";
        $pdf->SetFillColor(255, 165, 0); // Orange color
        break;
    case ($resultados["Deficiente relación con los colaboradores que supervisa"] >= 38):
        $calificacionFinalTexto = "MUY ALTO";
        $pdf->SetFillColor(255, 0, 0); // Red background
        $pdf->SetTextColor(255, 255, 255); // White text
        break;
}
$pdf->multicell(0, 5, "DEFICIENTE RELACIÓN CON LOS COLABORADORES QUE SUPERVISA: {$resultados["Deficiente relación con los colaboradores que supervisa"]} - {$calificacionFinalTexto}", 0, 'C', 1, 1, '', '', true);
$pdf->Ln(5); // Agregar una ligera separación
$pdf->SetTextColor(0, 0, 0); // White text


$pdf->addPage();
$pdf->multicell(0, 5, "CANTIDAD DE RESPUESTAS POR EMPLEADOS:", 0, 'C', 0, 1, '', '', true);
$pdf->multicell(0, 5, "Empleados que contestaron: ".count($processedData), 0, 'C', 0, 1, '', '', true);

$pdf->SetFillColor(236, 246, 255);
$pdf->multicell(0, 5, "CONDICIONES EN EL AMBIENTE DE TRABAJO", 0, 'C', 1, 1, '', '', true);

$pdf->SetFillColor(211, 211, 211);
$currentCategory = '';
$currentDomain = '';

foreach ($preguntas as $key => $pregunta) {

    if ( $pdf->getY() > 280 ) {
        $pdf->AddPage(); // Agregar nueva página
    }

    if ($pregunta['categoria'] !== $currentCategory) {
        $currentCategory = $pregunta['categoria'];
        $pdf->SetFillColor(236, 246, 255);
        $pdf->multicell(0, 5, strtoupper("{$currentCategory}"), 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M', 1);
    }

    if ($pregunta['dominio'] !== $currentDomain) {
        $currentDomain = $pregunta['dominio'];
        $pdf->SetFillColor(211, 211, 211);
        $pdf->multicell(85, 5, strtoupper("{$currentDomain}"), 1, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M', 1);
        $pdf->multicell(19, 5, "Siempre", 1, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M', 1);
        $pdf->multicell(19, 5, "Casi siempre", 1, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M', 1);
        $pdf->multicell(19, 5, "Algunas veces", 1, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M', 1);
        $pdf->multicell(19, 5, "Casi nunca", 1, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M', 1);
        $pdf->multicell(19, 5, "Nunca", 1, 'C', 1, 1, '', '', true, 0, false, true, '5', 'M', 1);
    }

    $pdf->SetFillColor(255, 255, 255);
    $pdf->multicell(85, 5, "{$key}", 1, 'L', 1, 0, '', '', true, 0, false, true, '5', 'M', 1);
    $pdf->multicell(19, 5, $pregunta['respuestas']['Siempre'], 1, 'C', 1, 0, '', '', true,true, 0, false, true, '5', 'M', 1);
    $pdf->multicell(19, 5, $pregunta['respuestas']['Casi siempre'], 1, 'C', 1, 0, '', '', true,true, 0, false, true, '5', 'M', 1);
    $pdf->multicell(19, 5, $pregunta['respuestas']['Algunas veces'], 1, 'C', 1, 0, '', '', true, true, 0, false, true, '5', 'M', 1);
    $pdf->multicell(19, 5, $pregunta['respuestas']['Casi nunca'], 1, 'C', 1, 0, '', '', true, true, 0, false, true, '5', 'M', 1);
    $pdf->multicell(19, 5, $pregunta['respuestas']['Nunca'], 1, 'C', 1, 1, '', '', true, true, 0, false, true, '5', 'M', 1);

}

// ---------------------------------------------------------
//Close and output PDF document
$pdf->Output(__DIR__ ."/tmp/reporteNom35.pdf", 'F');

//============================================================+
// END OF FILE
//============================================================+
