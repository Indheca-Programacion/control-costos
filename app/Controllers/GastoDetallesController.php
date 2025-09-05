<?php

namespace App\Controllers;

require_once "app/Models/GastoDetalles.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\GastoDetalles;
use App\Route;

class GastoDetallesController
{

    public function destroy($id)
    {

        $gastos = New GastoDetalles;
        $gastos->id = $id;
        $respuesta = $gastos->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Detalle',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Detalle fue eliminado correctamente' );

            header("Location:" . Route::names('gastos.edit',$_POST["gastoId"]));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Detalle',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. Si hay archivos debe de borrarlos antes.' );
            header("Location:" . Route::names('gastos.edit',$_POST["gastoId"]));

        }
        
        die();
    }

}
