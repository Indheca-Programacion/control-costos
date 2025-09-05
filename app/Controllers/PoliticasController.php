<?php

namespace App\Controllers;

use App\Route;

class PoliticasController
{
    public function index()
    {

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/politicas/index.php');

        include "vistas/modulos/plantilla.php";
    }


}
