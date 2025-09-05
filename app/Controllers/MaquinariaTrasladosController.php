<?php

namespace App\Controllers;

require_once "app/Models/MaquinariaTraslado.php";
require_once "app/Requests/SaveMaquinariaTrasladosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\MaquinariaTraslado;

use App\Requests\SaveMaquinariaTrasladosRequest;
use App\Route;

class MaquinariaTrasladosController
{
    public function index()
    {
        Autorizacion::authorize('view', New MaquinariaTraslado);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/movimientos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
    }

    public function store()
    {

        $MaquinariaTraslados = New MaquinariaTraslado;
        Autorizacion::authorize('create', $MaquinariaTraslados);
        
        $request = SaveMaquinariaTrasladosRequest::validated();

        $respuesta = $MaquinariaTraslados->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Asignacion de QR',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La asignacion de QR fue creado correctamente' );
            header("Location:" . Route::names('qr-cargas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Asignacion de QR',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('qr-cargas.edit', $_POST["nId01Qr"]));

        }
        
        die();

    }

    public function edit($id)
    {
     
    }

    public function update($id)
    {

    }

    public function destroy($id)
    {
    }
}
