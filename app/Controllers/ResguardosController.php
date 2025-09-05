<?php

namespace App\Controllers;

require_once "app/Models/Resguardo.php";
require_once "app/Policies/ResguardoPolicy.php";
require_once "app/Requests/SaveResguardosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Resguardo;
use App\Policies\ResguardoPolicy;
use App\Requests\SaveResguardosRequest;
use App\Route;

class ResguardosController
{
    public function index()
    {
        Autorizacion::authorize('view', new Resguardo);

        $resguardo = New Resguardo;

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/resguardos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $resguardo = new Resguardo;
        Autorizacion::authorize('create', $resguardo);
        
        $resguardos = $resguardo->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultar();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultar();

        require_once "app/Models/Unidad.php";
        $unidad = New \App\Models\Unidad;
        $unidades = $unidad->consultar();

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        $contenido = array('modulo' => 'vistas/modulos/resguardos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Resguardo);
        
        $request = SaveResguardosRequest::validated();

        require_once "app/Models/Inventario.php";
        $inventario = New \App\Models\Inventario;
        $inventario->consultar(null,$request["inventario"]);
        $request["cantidad"] = str_replace(',','',$request["cantidad"]);
        $inventario->disponible =  (float)$inventario->disponible - (float)$request["cantidad"];
        $inventario->actualizarDisponible();

        $resguardo = New Resguardo;
        $respuesta = $resguardo->crear($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Resguardo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El resguardo fue creada correctamente' );
            header("Location:" . Route::names('resguardos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Resguardo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('resguardos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Resguardo);

        $resguardo = New Resguardo;

  

        if ( $resguardo->consultar(null , $id) ) {


            $resguardo->consultarArchivos();
            $cantidadArchivos = count($resguardo->archivos);

            $permitirEliminarArchivos = true;

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuarios = $usuario->consultar();

            require_once "app/Models/Unidad.php";
            $unidad = New \App\Models\Unidad;
            $unidades = $unidad->consultar();

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            $contenido = array('modulo' => 'vistas/modulos/resguardos/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {

            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";

        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Resguardo);

        $request = SaveResguardosRequest::validated($id);
    
        $resguardo = New Resguardo;
        $resguardo->id = $id;
        $respuesta = $resguardo->actualizar($request);
        
        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Resguardo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El resguardo fue actualizada correctamente' );
            header("Location:" . Route::names('resguardos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Resguardo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('resguardos.edit', $id));

        }
        
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Resguardo);

        // Sirve para validar el Token
        if ( !SaveResguardosRequest::validatingToken($error) ) {


            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Sucursal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('resguardos.index'));
            die();

        }

        $resguardo = New Resguardo;
        $resguardo->id = $id;
        $respuesta = $resguardo->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La sucursal fue eliminada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Resguardo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El resguardo fue eliminada correctamente' );
            header("Location:" . Route::names('resguardos.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Resguardo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este resguardo no se podrá eliminar ***' );
            header("Location:" . Route::names('resguardos.index'));

        }
        
        die();

    }

    public function download($id)
    {
        Autorizacion::authorize('view', New Resguardo);

        $resguardo = New Resguardo;

        $respuesta = array();

        if ( $resguardo->consultar(null , $id) ) {

            $resguardo->consultarArchivos();

            $respuesta = array( 'codigo' => ( count($resguardo->archivos) > 0 ) ? 200 : 204,
                                'error' => false,
                                'cantidad' => count($resguardo->archivos),
                                'archivos' => $resguardo->archivos );

        } else {
            $respuesta = array( 'codigo' => 500,
                                'error' => true,
                                'errorMessage' => 'No se logró consultar el resguardo' );
        }

        echo json_encode($respuesta);
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Resguardo);

        $resguardo = New Resguardo;

        if ( $resguardo->consultar(null,$id) ) {

            $resguardoPartida = New Resguardo;

            $resguardoPartida->id = $id;
            $detalles = $resguardoPartida->partidasResguardo();

            require_once "app/Models/Almacen.php";
            $almacen = New \App\Models\Almacen;
            $almacen->consultar(null, $resguardo->almacen);

	    require_once "app/Models/Inventario.php";
            $inventario = New \App\Models\Inventario;
            $inventario->consultar(null, $resguardo->inventario);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $resguardo->usuarioEntrego);

            $nombreEntrego = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno . ' '. $usuario->apellidoMaterno);
            $firmaEntrego = $usuario->firma;


            $nombreRecibio = mb_strtoupper($resguardo->usuarioRecibio);
            $firmaRecibio = $resguardo->firma;

            // if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);

            $tiposContratoInventario = [
                [
                    'id' => 1,
                    'nombreCorto' => '640853819'
                ],
                [
                    'id' => 2,
                    'nombreCorto' => '640852802'
                ],
                        [
                    'id' => 3,
                    'nombreCorto' => '6509854810'
                        ],
                [
                    'id' => 4,
                    'nombreCorto' => 'GENERAL'
                ]
            ];
    
            $numeroContrato = $inventario->numeroContrato;
            $contratoEncontrado = null;

            foreach ($tiposContratoInventario as $contrato) {
                if ($contrato['id'] == $numeroContrato) {
                    $contratoEncontrado = $contrato;
                    break; // salir del ciclo al encontrar coincidencia
                }
            }
        
            include "reportes/vale-resguardo.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
