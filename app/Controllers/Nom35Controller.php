<?php

namespace App\Controllers;

require_once "app/Models/Nom35.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Nom35;
use App\Route;

class Nom35Controller
{
    public function index()
    {
        Autorizacion::authorize('view', New Nom35);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/nom35/index.php');

        include "vistas/modulos/plantilla.php";
    }
}
