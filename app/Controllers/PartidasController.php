<?php

namespace App\Controllers;

require_once "app/Models/Partida.php";
require_once "app/Requests/SavePartidasRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Partida;
use App\Requests\SavePartidasRequest;
use App\Route;

class PartidasController
{
    public function index()
    {
    }

    public function create()
    {
    }

    // public function store(SaveRoleRequest $request)
    public function store()
    {

        $request = SavePartidasRequest::validated();

        $partida = New Partida;
        $respuesta = $partida->crear($request,[]);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Partida',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El partida fue creado correctamente' );
            header("Location:" . Route::names('requisiciones.edit', $request["requisicionId"]));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Partida',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('requisiciones.edit', $request["requisicionId"]));

        }
        
        die();
    }

    // public function edit(Role $role)
    public function edit($id)
    {        
    }

    // public function update(SaveRoleRequest $request, Role $role)
    public function update($id)
    {        

        $request = SavePartidasRequest::validated();
        
        $partida = New Partida;
        $partida->id = $id;
        $respuesta = $partida->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Partida',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El partida fue actualizado correctamente' );
            header("Location:" . Route::names('requisiciones.edit', $request["requisicionId"]));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Partida',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('requisiciones.edit', $request["requisicionId"]));

        }
        
        die();
    }

    // public function destroy(Role $role)
    public function destroy($id)
    {        
        Autorizacion::authorize('delete', new Perfil);

        // Sirve para validar el Token
        if ( !SavePerfilesRequest::validatingToken($error) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = $error;
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('perfiles.index'));
            die();

        }

        // throw new \Illuminate\Auth\Access\AuthorizationException('No se puede eliminar este
        // Verifica que el usuario no sea Administrador
        $perfil = New Perfil;        
        if ( $perfil->consultar(null , $id) ) {
            
            if ( mb_strtoupper($perfil->nombre) == mb_strtoupper(CONST_ADMIN) ) {

                // $_SESSION[CONST_SESSION_APP]["flash"] = "El perfil 'Administrador' no puede ser eliminado";
                // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => "El perfil 'Administrador' no puede ser eliminado" );
                header("Location:" . Route::names('perfiles.index'));

                die();
            }

        }
        
        $perfil = New Perfil;
        $perfil->id = $id;
        $respuesta = $perfil->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El Perfil fue eliminado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Perfil fue eliminado correctamente' );

            header("Location:" . Route::names('perfiles.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este perfil no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Perfil',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este perfil no se podrá eliminar ***' );
            header("Location:" . Route::names('perfiles.index'));

        }
        
        die();
    }
}
// End of file: app/Controllers/PerfilesController.php