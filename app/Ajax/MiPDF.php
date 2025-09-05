<?php
namespace App\Ajax;
use App\Route;

require_once '../../vendor/autoload.php';

if (!class_exists('App\Ajax\MYPDF')) {
    class MYPDF extends \TCPDF {
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
        $this->MultiCell(35, 11, "FO-IGC-P4-03.01 \n REV 05", 0, 'C', 0, 1, 165, 10, true);

        // $this->Rect(70, 16, 95, 11, 'D', array(), array(222,222,222));
        $this->SetTextColor(0, 0, 0); // Color del texto
        $this->SetFillColor(222, 222, 222); // Color de fondo
        $this->SetFont('helvetica', '', 10.2); // Fuente, Tipo y Tamaño
        $this->MultiCell(190, 3, "REQUISICION DE COMPRA", 0, 'C', 1, 1, 10, 22, true);
	}

	// Page footer
	public function Footer() {
		// $this->setY(-25); // Position at 25 mm from bottom
	}
    }
}