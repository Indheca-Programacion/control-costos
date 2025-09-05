<?php

namespace App\Controllers;

require_once "app/Models/Sgi.php";
require_once "app/Policies/ResguardoPolicy.php";
require_once "app/Requests/SaveResguardosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Sgi;
use App\Policies\ResguardoPolicy;
use App\Requests\SaveResguardosRequest;
use App\Route;

class SgiController
{
    public function index()
    {
        Autorizacion::authorize('view', new Sgi);
        $documentos = [
            "001 Manual de Gestión Integral" => [
                "Contexto" => [
                    ["titulo"=>"CO-IGC-01 Contexto de la organización Rev 07 Mar-24","ruta"=>"https://drive.google.com/file/d/1QkoSfVAwS7Wrbco6RhewV6V1Dg5XTgr6/preview?usp=sharing"],
                    ["titulo"=>"FO-IGC-P2-02.01 APR Empresa Gral - Sistema Cuest Ext Jun-24 DE","ruta"=>"https://drive.google.com/file/d/19FkKWIofXZaPIQ5Xr0l7QTJJRVrq1onp/preview?usp=sharing"],
                    ["titulo"=>"FO-IGC-P2-02.01 APR Empresa Gral - Sistema Cuest Int Mar-24 DE","ruta"=>"https://drive.google.com/file/d/1x5NdEIRL1mzpehSUFcITUYFa8wNBb4R0/preview?usp=sharing"],
                    ["titulo"=>"FO-IGC-P2-02.01 APR Empresa Gral - Sistema Part Int Mar-24 DE","ruta"=>"https://drive.google.com/file/d/1j9A337C81pyUzaD_Q8EP7lyFz0ChCC4f/preview?usp=sharing"],
                    ["titulo"=>"FO-IGC-P2-02.02 Tratamiento de oportunidades Ene-24 ACT","ruta"=>"https://drive.google.com/file/d/1oba2-Jze4FniIVgqGHvUfOUiKvFLaaAH/preview?usp=sharing"],
                    ["titulo"=>"FO-IGC-P2-02.02 Tratamiento de oportunidades Ene-24 CE","ruta"=>"https://drive.google.com/file/d/1Z22RnuF_kurWW5lQol9edgnKI1Q_oP9m/preview?usp=sharing"],
                    ["titulo"=>"FO-IGC-P2-02.02 Tratamiento de oportunidades Ene-24 CI","ruta"=>"https://drive.google.com/file/d/1r-x2zEHAQbKzSBsqcdHB0yElONX1FVeP/preview?usp=drive_link"],
                ],
                ["titulo"=>"MGI-IGC-01 Manual de Gestion Integral Rev 09 Ene-22 DE", "ruta"=>"https://drive.google.com/file/d/1NrdBJgGcGa0s-32dstavqh6Mme38WVDF/preview?usp=sharing"],
                ["titulo"=>"MP-IGC-01 Mapeo de procesos Rev 03 Mar-24","ruta"=>"https://drive.google.com/file/d/1GnRQLuwXrfN3MHKXoFXd3-T70e7XY7mk/preview?usp=drive_link"],
                ["titulo"=>"OBJ-IGC-01 Objetivos del SGI 2024","ruta"=>"https://drive.google.com/file/d/1M573XqUUBKEiw5QYfns8aEX-6W63j_gg/preview?usp=drive_link"],
                ["titulo"=>"PI-IGC-01 Politica Integral Rev 06 Jun-23 DE","ruta"=>"https://drive.google.com/file/d/1Nk02We3sDmPlY4wbcZP8GP5cofqExVj2/preview?usp=drive_link"],
                
            ],
            "002 Organigrama" => [
                ["titulo"=>"ORG-IGC-01 Organigrama Rev 09 Oct-23 DE","ruta"=>"https://drive.google.com/file/d/1oouEXRYxNZu702yorGQyyBMh9B7lD71l/preview?usp=sharing"],
            ],
            "003 Descripciones de procesos" => [
                ["titulo"=>"DP-IGC-P2.1 Calidad, seguridad y medio amb Rev 02 May-23","ruta"=>"https://drive.google.com/file/d/10CLJFE5ds48VMV1LjTK58EcwvUEe_Z3B/preview?usp=sharing"],
                ["titulo"=>"DP-IGC-P3.1 Recursos Humanos Rev 03 Mar-24 DE","ruta"=>"https://drive.google.com/file/d/1on4FZ7fPfeH0uNAj-d3jeuoEaAA3XXdL/preview?usp=sharing"],
                ["titulo"=>"DP-IGC-P3.2 Mantto prev y corr Rev 04 Jun-24 DE","ruta"=>"https://drive.google.com/file/d/1WJm5_ovkg1TK3A4CVdOmhznIotyoUpXP/preview?usp=sharing"],
                ["titulo"=>"DP-IGC-P4.1 Licitaciones y presupuesto Rev 02 May-23 DE","ruta"=>"https://drive.google.com/file/d/1L1ACF8TO8Myn-p0IZeL9uX8kL1uM9M3Y/preview?usp=sharing"],
                ["titulo"=>"DP-IGC-P4.3 Construcción Rev 02 May-23 DE","ruta"=>"https://drive.google.com/file/d/1dDDUXo1Q9M0D9b_Kw1U-BHHGxGKgbgRW/preview?usp=drive_link"],
                ["titulo"=>"DP-IGC-P4.5 Insumos y materiales Rev 02 Jun-23 DE","ruta"=>"https://drive.google.com/file/d/1SrKkl9IFNgwCcp8vVtByrVZJDFzmuz7U/preview?usp=sharing"],
                ["titulo"=>"DP-IGC-PR7 Administracion de Arrendamiento Rev 01 Feb-21 DE","ruta"=>"https://drive.google.com/file/d/1jrDM7bbrC_tW4nzNov98DDwxgVTWxshO/preview?usp=sharing"],
            ]
        ] ;
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/sgi/index.php');

        include "vistas/modulos/plantilla.php";
    }
}