<?php
namespace App\Ajax;
use App\Route;

require_once "MiPDF.php"; // Cargar clase MYPDF solo una vez

class RequisicionPDFGenerator
{
    protected $requisicion;

    public function __construct($requisicion)
    {
        $this->requisicion = $requisicion;
    }

    public function generar()
    {
        $this->construirContenido();
        return $this->generarPDF();
    }

    protected function construirContenido()
    {
        $requisicion = $this->requisicion;

        $requisicion->consultarDetalles();

        require_once "../Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedor->consultar(null, $requisicion->proveedorId);
        $this->proveedor = $proveedor;

        require_once "../Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null, $requisicion->empresaId);
        $this->empresa = $empresa;

        require_once "../Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obra->consultar(null, $requisicion->idObra);
        $this->obra = $obra;

        require_once "../Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $requisicion->usuarioIdCreacion);

        require_once "../Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisa->consultar(null, $requisicion->divisa);
        $this->divisa = $divisa;

        $usuarioNombre = mb_strtoupper($usuario->nombre);
        $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
        if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
        $solicitoFirma = $usuario->firma;
        unset($usuario);
        $this->solicito = $solicito;

        $reviso = '';
        $revisoFirma = null;
        
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $requisicion->usuarioIdAutorizacion);
        $this->usuario = $usuario;

        $reviso = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
        if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
        $revisoFirma = $usuario->firma;
        unset($usuario);

        $almacenResponsable = '';
        $almacenFirma = null;
        $revisoSello = null;


        if ( !is_null($requisicion->usuarioIdAlmacen) ) {
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdAlmacen);

            $almacenResponsable = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $almacenResponsable .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $almacenFirma = $usuario->firma;
            unset($usuario);
        }

        $autorizoAdicional = '';
        $firmaAutorizoAdicional = null;

        if ( !is_null($requisicion->usuarioIdAutorizacionAdd)) {
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdAutorizacionAdd);

            $autorizoAdicional = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $autorizoAdicional .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $firmaAutorizoAdicional = $usuario->firma;
            unset($usuario);
        } 

        $this->almacenResponsable = $almacenResponsable;
        $this->almacenFirma = $almacenFirma;
        $this->reviso = $reviso;
        $this->solicitoFirma = $solicitoFirma;
        $this->revisoSello = $revisoSello;
        $this->revisoFirma = $revisoFirma;
        $this->firmaAutorizoAdicional = $firmaAutorizoAdicional;
        $this->autorizoAdicional = $autorizoAdicional;

    }

    protected function generarPDF()
    {
        $empresa = $this->empresa;
        $requisicion = $this->requisicion;
        $proveedor = $this->proveedor;
        $obra = $this->obra;
        $divisa = $this->divisa;
        $solicito = $this->solicito;
        $almacenResponsable = $this->almacenResponsable;
        $almacenFirma = $this->almacenFirma;
        $reviso = $this->reviso;
        $solicitoFirma = $this->solicitoFirma;
        $revisoSello = $this->revisoSello;
        $firmaAutorizoAdicional = $this->firmaAutorizoAdicional;
        $autorizoAdicional = $this->autorizoAdicional;

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

        $pdf->ln(5); // Salto de Línea

        $pdf->SetFont('helvetica', '', 7.5); // Fuente, Tipo y Tamaño

        $fecha = strtotime($requisicion->fechaCreacion);

        $diaSemana = fNombreDia(date("w", $fecha));
        $dia = date("d", $fecha);
        $mes = fNombreMes(date("n", $fecha));
        $year = date("Y", $fecha);

        $almacen = mb_strtoupper(fString($obra->almacen ?? '109'));
        $razon = mb_strtoupper(fString($requisicion->razonSocial ?? ''));

        $pdf->MultiCell(35, 3, "FOLIO RQ", 0, 'R', 0, 0, 120, '', true);
        $pdf->MultiCell(45, 3, "{$pdf->folio}", 'B', 'C', 0, 1, '', '', true);

        $pdf->MultiCell(32, 3, "FECHA DE SOLICITUD:", 0, '', 0, 0, 10, '', true);
        $pdf->MultiCell(40, 3, "{$diaSemana}, {$dia} de {$mes} de {$year}", 'B', 'C', 0, 0, '', '', true, 0, false, true, '5', 'M', 1);

        $fecha = isset($requisicion->fechaReq) ? fFechaLarga($requisicion->fechaReq) : '';

        // $diaSemana = fNombreDia(date("w", $fecha));
        // $dia = date("d", $fecha);
        // $mes = fNombreMes(date("n", $fecha));
        // $year = date("Y", $fecha);

        $razon = mb_strtoupper(fString($proveedor->nombreCompleto ?? $proveedor->razonSocial ?? ''));
        $rfc = mb_strtoupper(fString($proveedor->rfc ?? ''));
        $telefono = mb_strtoupper(fString($proveedor->telefono ?? ''));
        $email = fString($proveedor->correo ?? '');

        $pdf->MultiCell(38, 3, "FECHA QUE SE REQUIERE:", 0, '', 0, 0, '', '', true);
        $pdf->MultiCell(40, 3, "{$fecha}", 'B', 'C', 0, 0, '', '', true);

        $pdf->MultiCell(24, 3, "NUM. ALMACEN:", 0, '', 0, 0, '', '', true);
        $pdf->MultiCell(16, 3, "{$almacen}", 'B', 'C', 0, 1, '', '', true);

        $pdf->MultiCell(23, 3, "RAZÓN SOCIAL:", 0, '', 0, 0, 10, '', true);
        $pdf->MultiCell(167, 3, "{$razon}", 'B', '', 0, 1, '', '', true);

        $pdf->MultiCell(22, 3, "NUMERO/RFC:", 0, '', 0, 0, 10, '', true);
        $pdf->MultiCell(35, 3, "{$rfc}", 'B', 'C', 0, 0, '', '', true);

        $pdf->MultiCell(8, 3, "TEL:", 0, '', 0, 0, '', '', true);
        $pdf->MultiCell(30, 3, "{$telefono}", 'B', 'C', 0, 0, '', '', true);

        $pdf->MultiCell(12, 3, "E-MAIL:", 0, '', 0, 0, '', '', true);
        $pdf->MultiCell(83, 3, "{$email}", 'B', 'C', 0, 1, '', '', true);

        $pdf->ln(2); // Salto de Línea

        $pdf->SetFont('helvetica', 'B', 7.5); // Fuente, Tipo y Tamaño

        $pdf->MultiCell(45, 5, "MAQUINARIA O EQUIPO", 1, 'C', 0, 0, 10, '', true);
        $pdf->MultiCell(30, 5, "MARCA", 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, "MODELO", 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, "SERIE", 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(45, 5, "NUM. ECONÓMICO", 1, 'C', 0, 1, '', '', true);

        $equipo = mb_strtoupper(fString($pdf->maquinaria['maquinaria_tipos.descripcion'] ?? 'NA'));
        $marca = mb_strtoupper(fString($pdf->maquinaria['marcas.descripcion'] ?? 'NA'));
        $modelo = mb_strtoupper(fString($pdf->maquinaria['modelos.descripcion'] ?? 'NA'));
        $serie = mb_strtoupper(fString($pdf->maquinaria['serie'] ?? 'NA'));
        $numeroEconomico = mb_strtoupper(fString($pdf->maquinaria['numeroEconomico'] ?? 'NA'));

        $pdf->SetFont('helvetica', '', 7); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(45, 5, "{$equipo}", 1, 'C', 0, 0, 10, '', true);
        $pdf->MultiCell(30, 5, "{$marca}", 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(30, 5, "{$modelo}", 1, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(40, 5, "{$serie}", 1, 'C', 0, 0, '', '', true, 0, false, true, '5');
        $pdf->MultiCell(45, 5, "{$numeroEconomico}", 1, 'C', 0, 1, '', '', true, 0, false, true, '4.2', 'm',1);

        $pdf->SetXY(5, 67);
        $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(18, 7, "CODIGO \n PRODUCTO", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
        $pdf->MultiCell(60, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(20, 7, "NÚM PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(20, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
        $pdf->MultiCell(20, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');


	    $subtotal= 0;
	    $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
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
			$pdf->MultiCell(18, 7, "CODIGO \n PRODUCTO", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
			$pdf->MultiCell(60, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 7, "NÚM PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
			$pdf->MultiCell(20, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

			$pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

			$y_start = $pdf->GetY();
		}

            $pdf->MultiCell(60, 0, "{$concepto}", 1, 'C', 0, 1, 80, '', true, 0);
            $y_end = $pdf->GetY();
            $altoFila = $y_end - $y_start;
            $num_detalle = count($requisicion->detalles);
            $pdf->MultiCell(12, $altoFila, "{$partida}", 1, 'C', 0, 0, 10, $y_start, true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(20, $altoFila, "{$cantidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(20, $altoFila, "{$unidad}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(18, $altoFila, "{$codigo}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(20, $altoFila, "{$numeroParte}", 1, 'C', 0, 0, 140, '', true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(20, $altoFila, "$ {$pu}", 1, 'C', 0, 0, '', '', true, 0, false, true, $altoFila, 'M');
            $pdf->MultiCell(20, $altoFila, "$ {$costo}", 1, 'C', 0, 1, '', '', true, 0, false, true, $altoFila, 'M');

            $subtotal += floatval($detalle['costo']);
            if ( $y_end > 230 ) {
                $pdf->AddPage();

                $pdf->SetXY(5, 30);
                $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
                $pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
                $pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(18, 7, "CODIGO \n PRODUCTO", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
                $pdf->MultiCell(60, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(20, 7, "NÚM PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(20, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
                $pdf->MultiCell(20, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

                $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
            }
        }

        $y = $pdf->getY();
        if ( $y > 228 ) {
            $pdf->AddPage();

            $pdf->SetXY(5, 51);
            $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
            $pdf->MultiCell(12, 7, "PARTIDA", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
            $pdf->MultiCell(20, 7, "CANT", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "UNIDAD", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(18, 7, "CODIGO \n PRODUCTO", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
            $pdf->MultiCell(60, 7, "DESCRIPCIÓN", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "NÚM PARTE", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "P.U.", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "COSTO TOTAL", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

            $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
            $y = $pdf->getY();
        }

        while ($y <= 150) {
            $pdf->MultiCell(12, 7, "", 1, 'C', 0, 0, 10, '', true, 0, false, true, '7', 'M',1);
            $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(18, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M',1);
            $pdf->MultiCell(60, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "", 1, 'C', 0, 0, '', '', true, 0, false, true, '7', 'M');
            $pdf->MultiCell(20, 7, "", 1, 'C', 0, 1, '', '', true, 0, false, true, '7', 'M');

            $y = $pdf->getY();
        }

        $pdf->Ln(2); // Salto de Línea
        $pdf->SetTextColor(0, 128, 255); // Set text color to blue
        $pdf->SetFillColor(245, 245, 245); // Set background color to gray

        $pdf->MultiCell(53, 5, "Retención I.V.A.:", 0, 'R', 1, 0, '10', '', true, 0, false, true, '5', 'M');
        $pdf->SetTextColor(0, 0, 0); // Set text color to black
        $retencionIva = ($requisicion->retencionIva ?? 0) * $subtotal / 100;
        $pdf->MultiCell(10, 5, number_format($retencionIva,2), 0, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');

        $pdf->SetTextColor(0, 128, 255); // Set text color to blue
        $pdf->MultiCell(33, 5, "Retención I.S.R.:", 0, 'R', 1, 0, '73', '', true, 0, false, true, '5', 'M');
        $pdf->SetTextColor(0, 0, 0); // Set text color to black
        $pdf->MultiCell(40, 5, ($requisicion->retencionIsr??0), 0, 'L', 1, 0, '', '', true, 0, false, true, '5', 'M');

        $pdf->SetTextColor(0, 128, 255); // Set text color to blue
        $pdf->MultiCell(32, 5, "Subtotal", 0, 'L', 1, 0, '136', '', true, 0, false, true, '5', 'M');
        $subtotaltext = number_format($subtotal,2);
        $pdf->SetTextColor(0, 0, 0); // Set text color to blue
        $pdf->MultiCell(32, 5, "{$subtotaltext}", 0, 'R', 1, 1, '', '', true, 0, false, true, '5', 'M');

        $pdf->SetTextColor(0, 128, 255); // Set text color to blue
        $pdf->MultiCell(143, 5, "Descuentos", 0, 'R', 1, 0, 10, '', true, 0, false, true, '5', 'M');
        $descuentos = floatval($requisicion->descuento ?? 0);
        $pdf->SetTextColor(0, 0, 0); // Set text color to blue
        $pdf->MultiCell(0, 5, "{$descuentos}", 0, 'R', 1, 1, '', '', true, 0, false, true, '5', 'M');
        $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño

        $pdf->SetFillColor(128, 128, 128); // Set background color to gray
        $pdf->SetTextColor(255, 255, 255); // Set text color to blue
        $pdf->MultiCell(120, 5, "Importe con letra", 0, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');

        $pdf->SetFillColor(245, 245, 245); // Set background color to gray
        $pdf->SetTextColor(0, 128, 255); // Set text color to blue
        $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

        $iva = ($subtotal*$requisicion->iva)/100;
        $pdf->MultiCell(15, 5, "I.V.A.", 0, 'R', 1, 0, 130, '', true, 0, false, true, '5', 'M');
        $pdf->SetTextColor(0, 0, 0); // Set text color to blue
        $pdf->MultiCell(0, 5, number_format($iva,2), 0, 'R', 1, 1, '', '', true, 0, false, true, '5', 'M');
        $total = ($subtotal + $iva) - $descuentos - $retencionIva - ($requisicion->retencionIsr ?? 0);


        $totalEnLetra = numeroALetras($total, $divisa->descripcion, $divisa->nombreCorto);
        $pdf->MultiCell(120, 5, "$totalEnLetra", 0, 'C', 1, 0, '', '', true, 0, false, true, '5', 'M');

        $pdf->SetTextColor(0, 128, 255); // Set text color to blue
        $pdf->MultiCell(40, 5, "Total", 0, 'R', 1, 0, 130, '', true, 0, false, true, '5', 'M');
        $pdf->SetTextColor(0, 0, 0); // Set text color to blue
        $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
        $total = formatMoney($total);
        $pdf->MultiCell(30, 5, "$total", 'B', 'R', 1, 1, '', '', true, 1, false, true, '5', 'M');

        $especificaciones = mb_strtoupper(fString($requisicion->especificaciones ?? ''));
        $pdf->MultiCell(48, 5, "ESPECIFICACIONES ADJUNTAS:", 0, '', 0, 0, '10', '', true, 0, false, true, '5', 'M');
        $pdf->MultiCell(142, 5, "{$especificaciones}", 'B', '', 0, 1, '', '', true, 0, false, true, '5', 'M');

        $pdf->MultiCell(48, 5, "DIRECCION DE ENTREGA:", 0, '', 0, 0, '10', '', true, 0, false, true, '5', 'M');
        $direccion = mb_strtoupper(fString($requisicion->direccion ?? ''));
        $pdf->MultiCell(142, 5, "$direccion", 'B', '', 0, 1, '', '', true, 0, false, true, '5', 'M');

        $justificacion = mb_strtoupper(fString($requisicion->justificacion ?? 'NA'));
        $tipoRequisicion = mb_strtoupper(fString($requisicion->tipoRequisicion ?? 0));
        if ( $tipoRequisicion == 0 ) {
            $tipoRequisicion = "Programada para {$fecha}";
        }else{
            $tipoRequisicion = "Urgente";
        }

        $pdf->ln(2); // Salto de Línea
        $pdf->SetLineWidth(0.4); // Set line width
        $y = $pdf->getY();
        $pdf->Rect(10, $y, 90, 21, 'D', array(), array(222,222,222));
        $pdf->Rect(100, $y, 100, 21, 'D', array(), array(222,222,222));
        $pdf->SetLineWidth(0.2); // Reset line width to default

        $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(100, 5, "JUSTIFICACIÓN:", 0, '', 0, 0, '10', '', true, 0, false, true, '5', 'M');
        $pdf->MultiCell(100, 5, "TIPO DE REPARACION / TIPO DE RQ: PROGRAMADA Ó URGENTE:", 0, '', 0, 1, 100, '', true, 0, false, true, '5', 'M');
        $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(90, 16, "{$justificacion}", 0, 'C', 0, 0, '10', '', true, 0, false, true, '12', 'M',1);
        // $pdf->Line(10, 247, 100, 247, false);
        $pdf->MultiCell(100, 12, "{$tipoRequisicion}", 0, 'C', 0, 1, 100, '', true, 0, false, true, '12', 'M');
        // $pdf->Line(110, 247, 200, 247, false);

        $pdf->ln(2); // Salto de Línea
        $pdf->SetLineWidth(0.4); // Set line width
        $y = $pdf->getY()+1.5;
        $pdf->Rect(10, $y, 50, 25, 'D', array(), array(222,222,222));
        $pdf->Rect(60,  $y, 60, 25, 'D', array(), array(222,222,222));
        $pdf->Rect(120,  $y, 80, 25, 'D', array(), array(222,222,222));
        $pdf->SetLineWidth(0.2); // Reset line width to default

        $pdf->SetFont('helvetica', 'B', 8); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(50, 6, "CONTRATO, ORDEN DE TRABAJO,", 0, '', 0, 0, '10', '', true, 0, false, true, '8', 'M');
        $pdf->MultiCell(60, 6, "RECEPCION DEL MATERIAL O SERVICIO:", 0, '', 0, 0, '', '', true, 0, false, true, '8', 'M');
        $pdf->MultiCell(100, 6, "RECEPCION DE COMPROBANTE FISCAL:", 0, '', 0, 1, '', '', true, 0, false, true, '8', 'M');
        $pdf->SetFont('helvetica', '', 6.5); // Fuente, Tipo y Tamaño
        $contrato = mb_strtoupper(fString(!is_null($obra->descripcion) ? $obra->descripcion : '109. mantenimiento correctivo general'));
        $pdf->MultiCell(50, 12, "{$contrato}", 0, 'C', 0, 0, '10', '', true, 0, false, true, '12', 'M');
        // $pdf->Line(10, 247, 100, 247, false);
        $pdf->MultiCell(60, '', " Entregar en forma impresa: \n Factura o remisión            Orden de compra autorizada \n Certificado de origen         Hojas de seguridad \n Certificado de Calidad       Ficha Técnica \n Garantía                            Estimaciones Autorizadas", 0, 'L', 0, 0, '', '', true, 0, false, true);
        $ruta = Route::rutaServidor();
        $pdf->MultiCell(80, '5', "El CFDI debe ingresarse en PDF y XML en el enlace: \n$ruta \n\nAnexar factura o remision firmados de aceptación, órden de compra autorizada, fichas técnicas, certificados, garantías, hojas de seguridad,  manifiestos, estimaciones autorizadas.", 0, 'L', 0, 1, '', '', true, 0, false, true);

        $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño

        $solicito = mb_strtoupper(fString($solicito));
        $almacenResponsable = mb_strtoupper(fString($almacenResponsable));
        $reviso = mb_strtoupper(fString($reviso));

        $y = $pdf->getY();


        if( $y > 260 ) {
            $pdf->AddPage(); // Agregar nueva página si se excede el límite
            $y = 20; // Reiniciar la posición Y
        }

        $pdf->SetFont('helvetica', '', 10); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(66, 5, "SOLICITA", 0, 'C', 0, 0, 5, '', true);
        $pdf->MultiCell(66, 5, "VB. ALMACEN", 0, 'C', 0, 0, '', '', true);
        $pdf->MultiCell(66, 5, "APRUEBA", 0, 'C', 0, 1, '', '', true);
        
        $y = $pdf->getY();
        
        if ( !is_null($solicitoFirma) ) {
            $extension = mb_strtoupper(substr($solicitoFirma, -3, 3));
            if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

            $pdf->Image('../../'.$solicitoFirma, 10, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
        }

        if ( !is_null($almacenFirma) ) {
            $extension = mb_strtoupper(substr($almacenFirma, -3, 3));
            if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

            $pdf->Image('../../'.$almacenFirma, 70, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
        }

        // if ( !is_null($revisoSello) ) {
        //     $pdf->MultiCell(60, 20, "Sello Digital:\n ".$revisoSello, 1, 'C', 0, 0, 140, 240, true, 0, false, true, '5', 'M',1);
        // } else if ( !is_null($revisoFirma) ) {
        //     $extension = mb_strtoupper(substr($revisoFirma, -3, 3));
        //     if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

        //     $pdf->Image('../../'.$revisoFirma, 130, $y, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
        // }

        $pdf->Ln(20); // Salto de Línea
        $pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
        // $pdf->Line(15, 278, 95, 278, false);
        $pdf->MultiCell(60, 5, "{$solicito}", 'T', 'C', 0, 0, 10, '', true, 0, false, true, '5', 'M', 1);

        $pdf->MultiCell(55, 5, "{$almacenResponsable}", 'T', 'C', 0, 0, 75, '', true, 0, false, true, '5', 'M', 1);

        $pdf->MultiCell(55, 5, "{$reviso}", 'T', 'C', 0, 1, 140, '', true, 0, false, true, '5', 'M', 1);

        $pdf->SetFont('helvetica', '', 8); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(60, 3, "VB ADICIONAL", 0, 'C', 0, 1, 140, '', true);
        $pdf->MultiCell(58, 5, "Folios Vale de salida/Vale de resguardo:", 0, '', 0, 0, '', '', true);
        $pdf->MultiCell(55, 3, "", 'B', 'C', 0, 1, '', '', true);
        $y = $pdf->getY();
        if ( !is_null($firmaAutorizoAdicional) ) {
            $extension = mb_strtoupper(substr($firmaAutorizoAdicional, -3, 3));
            if ( $extension == 'JPG')  $pdf->setJPEGQuality(75); // Calidad de imágen

            $pdf->Image('../../'.$firmaAutorizoAdicional, 130, $y-5, 70, 0, $extension, '', '', false, 300, '', false, false, 0, 'CT', false, false);
        }
        $pdf->MultiCell(15, 5, "Consulta:", 0, '', 0, 0, '', '', true);
        $pdf->MultiCell(58, 3, "", 'B', 'C', 0, 1, '', '', true);

        $pdf->SetFont('helvetica', 'B', 9); // Fuente, Tipo y Tamaño
        $pdf->MultiCell(60, 5, "{$autorizoAdicional}", 'T', 'C', 0, 0, '140', '', true, 0, false, true, '5', 'M', 1);

        $carpetaDestino = '/var/www/html/reportes/tmp/';
        $fileName = "requisicion_" . time() . "_" . uniqid() . ".pdf";
        $filePath = $carpetaDestino . $fileName;

        $pdf->Output($filePath, 'F');

        return $filePath;
    }
}
