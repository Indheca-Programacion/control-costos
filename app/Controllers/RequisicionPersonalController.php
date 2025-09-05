<?php 

namespace App\Controllers;

require_once "app/Models/Usuario.php";
require_once "app/Models/RequisicionPersonal.php";
require_once "app/Policies/RequisicionPersonalPolicy.php";
require_once "app/Requests/SaveRequisicionPersonalRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\RequisicionPersonal;
use App\Policies\RequisicionPersonalPolicy;
use App\Requests\SaveRequisicionPersonalRequest;
use App\Models\Usuario;
use App\Requests\Request;

use App\Route;

class RequisicionPersonalController
{
    public function index()
    {

        // Validar Autorizacion
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisicion-personal", "ver") ) {

            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();

        }

        $contenido = array('modulo' => 'vistas/modulos/requisicion-personal/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
    }

    public function store()
    {    
    }

    public function edit($id)
    {
        
        // Autorizacion::authorize('update', New RequisicionPersonal);

        $requisicion = New RequisicionPersonal;

        if ( $requisicion->consultar(null , $id) ) {

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

            $usuario->consultarPerfiles();

            $perfil_rh = false;
            //TODO: Agregar perfil de jefe de area
            if ((in_array('jefe de RH',$usuario->perfiles))) {
                $perfil_rh = true;
            }

            $permiso_authorizar = false;
            if(Autorizacion::permiso($usuario, "autorizar-req", "crear") && (is_null($requisicion->usuarioIdAuthRH) && $perfil_rh ) || ( is_null($requisicion->usuarioIdAutorizacion) && !$perfil_rh ) ){
                $permiso_authorizar =true;
            }

            require_once "app/Models/Empleado.php";
            $empleado = New \App\Models\Empleado;
            $empleados = $empleado->consultarActivos();
            
            $descripcion = $requisicion->consultarPuesto();

            $contenido = array('modulo' => 'vistas/modulos/requisicion-personal/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
        
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New RequisicionPersonal);

        $request = SaveRequisicionPersonalRequest::validated($id);

        $mensaje = 'La requisición fue actualizada correctamente';

        $requisicion = New RequisicionPersonal;
        $requisicion->id = $id;
        $respuesta = $requisicion->actualizar($request);
        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'OK',
                                                           // 'mensaje' => 'La requisición fue actualizada correctamente' );
                                                           'mensaje' => $mensaje );
            header("Location:" . Route::names('requisicion-personal.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('requisicion-personal .edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New RequisicionPersonal);

        $requisicion = New RequisicionPersonal;

        if ( $requisicion->consultar(null , $id) ) {

                $usuario = New Usuario;
                
                $array=json_decode($requisicion->trabajadores);
                $empleadosId = implode(', ', $array);
                
                require_once "app/Models/Empleado.php";
                $empleado = New \App\Models\Empleado;
                $usuario->consultar(null, $requisicion->usuarioIdAutorizacion);
                
                $autorizoFirma = null;
                $usuarioNombre = $usuario->nombre;  
                $solicito = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
                if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . $usuario->apellidoMaterno;
                $autorizoFirma = $usuario->firma;
                
                
                $usuario->consultar(null, $requisicion->usuarioIdAuthRH);
                $AutorizoRHFirma = null;
                if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . $usuario->apellidoMaterno;
                $AutorizoRHFirma = $usuario->firma;
                
                
                $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
                $empleados = $empleado->consultarEmpleados($empleadosId);
                $descripcion = $requisicion->consultarPuesto();

            include "reportes/requisicion-personal.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
