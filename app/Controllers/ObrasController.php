<?php

namespace App\Controllers;

require_once "app/Models/Obra.php";
// require_once "app/Policies/ObraPolicy.php";
require_once "app/Requests/SaveObrasRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Obra;
// use App\Policies\ObraPolicy;
use App\Requests\SaveObrasRequest;
use App\Route;

class ObrasController
{
    public function index()
    {
        Autorizacion::authorize('view', New Obra);

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, \usuarioAutenticado()["id"]);

        $obra = New Obra;
        if ( $usuario->empresaId == 4 ) $obras = [["descripcion" => "371.Proyecto Viviendas -Edificación", "id" => 109]];
        else $obras = $obra->consultar();
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/obras/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $obra = New Obra;
        Autorizacion::authorize('create', $obra);

        require_once "app/Models/Plantilla.php";
        $Plantilla = New \App\Models\Plantilla;
        $plantillas = $Plantilla->consultarPorUsuario(usuarioAutenticado()["id"]);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $status = $estatus->consultar();

        require_once "app/Models/Ubicacion.php";
        $ubicacion = New \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultarCompradores();

        $formularioEditable = true;
        $contenido = array('modulo' => 'vistas/modulos/obras/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New Obra);

        $request = SaveObrasRequest::validated();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario = $usuario->consultar(null,usuarioAutenticado()["id"]);

        $request["ubicacionId"] = $usuario["ubicacionId"];

        $obra = New Obra;
        $respuesta = $obra->crear($request);

        if ($_POST["plantilla"] !== 0) {
            require_once "app/Models/Plantilla.php";
            $Plantilla = New \App\Models\Plantilla;
            $plantillas = $Plantilla->consultarDetalles($_POST["plantilla"]);

            require_once "app/Models/ObraDetalles.php";
            $obraDetalles = New \App\Models\ObraDetalles;

            $arrayDetalles = array();

            foreach ($plantillas as $key => $value) {
                if ($value["tipo"] == 'Directo') {
                    $directo = $value["directoId"];
                    $indirecto = null;
                }else{
                    $indirecto = $value["indirectoId"];
                    $directo = null;
                }
                array_push($arrayDetalles,[
                    "obraId" => $obra->id,
                    "presupuesto" => $value["presupuesto"],
                    "cantidad" => $value["cantidad"],
                    "insumoId" => $directo,
                    "indirectoId" => $indirecto,
                ]);
            }            

            // Inserta los detalles de obra
            foreach ($arrayDetalles as $key => $value) {
                $respuesta = $obraDetalles->agregar($value);
            }

        }

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Obra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La obra fue creada correctamente' );
            header("Location:" . Route::names('obras.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Obra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('obras.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Obra);

        $obra = New Obra;

        if ( $obra->consultar(null , $id) ) {

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/Estatus.php";
            $estatus = New \App\Models\Estatus;
            $status = $estatus->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();    

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuarios = $usuario->consultarCompradores();
            
            $formularioEditable = false;
            if ( $obra->estatus["obraAbierta"] ) $formularioEditable = true;

            $contenido = array('modulo' => 'vistas/modulos/obras/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Obra);

        $request = SaveObrasRequest::validated($id);

        // Validar que la fechaFinalizacion no sea menor a fechaInicio
        if ( $request['fechaFinalizacion'] != ''  ) {

            $fechaInicio = \DateTime::createFromFormat('Y-m-d', fFechaSQL($request['fechaInicio']));
            $fechaFinalizacion = \DateTime::createFromFormat('Y-m-d', fFechaSQL($request['fechaFinalizacion']));
            
            if ( $fechaFinalizacion < $fechaInicio ) {

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                               'titulo' => 'Actualizar Obra',
                                                               'subTitulo' => 'Error',
                                                               'mensaje' => 'La fecha de finalización no puede ser menor a la fecha de inicio' );
                header("Location:" . Route::names('obras.edit', $id));

                die();

            }

        }

        $obra = New Obra;
        $obra->id = $id;
        $respuesta = $obra->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Obra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La obra fue actualizada correctamente' );
            header("Location:" . Route::names('obras.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Obra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('obras.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Obra);

        // Sirve para validar el Token
        if ( !SaveObrasRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Obra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('obras.index'));
            die();

        }

        $obra = New Obra;
        $obra->id = $id;
        $respuesta = $obra->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Obra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La obra fue eliminada correctamente' );

            header("Location:" . Route::names('obras.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Obra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta obra no se podrá eliminar ***' );
            header("Location:" . Route::names('obras.index'));

        }
        
        die();
    }
}
