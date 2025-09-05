<?php

namespace App\Controllers;

require_once "app/Models/Usuario.php";
require_once "app/Policies/UsuarioPolicy.php";
require_once "app/Requests/SaveUsuariosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Usuario;
use App\Policies\UsuarioPolicy;
use App\Requests\SaveUsuariosRequest;
use App\Route;

class UsuariosController
{
    public function index()
    {
        if ( !usuarioAutenticado() ) {
            include "vistas/modulos/plantilla.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }

    	$usuario = New Usuario;
        $usuario -> id = usuarioAutenticado()["id"];
        Autorizacion::authorize('view', $usuario);

		if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "usuarios", "ver") ) {

        	$id = $usuario -> id;

        	$usuarios = array();
        	array_push( $usuarios, $usuario->consultar(null , $id) );

        } else {

        	$usuarios = $usuario->consultar();

        }

        // include "vistas/modulos/usuarios/index.php";
        $contenido = array('modulo' => 'vistas/modulos/usuarios/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $usuario = new Usuario;
        Autorizacion::authorize('create', $usuario);

        require_once "app/Models/Perfil.php";
        $perfil = New \App\Models\Perfil;
        $perfiles = $perfil->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();
        
        require_once "app/Models/Ubicacion.php";
        $ubicacion = New \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();
        
        // include "vistas/modulos/usuarios/crear.php";
        $contenido = array('modulo' => 'vistas/modulos/usuarios/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', new Usuario);

        $request = SaveUsuariosRequest::validated();

        $usuario = New Usuario;
        $respuesta = $usuario->crear($request);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El usuario fue creado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Usuario',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El usuario fue creado correctamente' );
            header("Location:" . Route::names('usuarios.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Usuario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('usuarios.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        $usuario = New Usuario;
        $usuario -> id = $id;
        Autorizacion::authorize('update', $usuario);

        // $usuario->consultar(null , $id);

        if ( $usuario->consultar(null , $id) ) {

            $usuario->consultarPerfiles();
            $usuario->consultarPermisos();

            require_once "app/Models/Perfil.php";
            $perfil = New \App\Models\Perfil;
            $perfiles = $perfil->consultar();

            require_once "app/Models/Puesto.php";
            $puesto = New \App\Models\Puesto;
            $puestos = $puesto->consultar();


            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            // require_once "app/Models/Permiso.php";
            // $permiso = New \App\Models\Permiso;
            // $permisos = $permiso->consultar();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            // include "vistas/modulos/usuarios/editar.php";
            $contenido = array('modulo' => 'vistas/modulos/usuarios/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
    	$usuario = New Usuario;
        $usuario -> id = $id;
        Autorizacion::authorize('update', $usuario);
        
        $request = SaveUsuariosRequest::validated();

        //Si el usuario es 'Administrador' o tiene el permiso 'usuarios,actualizar' puede actualizar perfiles y permisos
        $usuarioAutenticado = New Usuario;
        $usuarioAutenticado->consultar("usuario", usuarioAutenticado()["usuario"]);
        if ( Autorizacion::perfil($usuarioAutenticado, CONST_ADMIN) || Autorizacion::permiso($usuarioAutenticado, "usuarios", "actualizar") ) {
        
            $respuesta = $usuario->actualizar($request);

        } else {

            $respuesta = $usuario->actualizarPerfil($request);

        }

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El usuario fue actualizado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Usuario',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El usuario fue actualizado correctamente' );
            header("Location:" . Route::names('usuarios.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Usuario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('usuarios.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', new Usuario);

        // Sirve para validar el Token
        if ( !SaveUsuariosRequest::validatingToken($error) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = $error;
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Usuario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('usuarios.index'));
            die();

        }

        // Verifica que el usuario no sea Administrador
        $usuario = New Usuario;
        if ( $usuario->consultar(null , $id) ) {
            
            if ( mb_strtoupper($usuario->usuario) == mb_strtoupper(CONST_ADMIN) ) {

                // $_SESSION[CONST_SESSION_APP]["flash"] = "El usuario 'Administrador' no puede ser eliminado";
                // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

                $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Usuario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => "El usuario 'Administrador' no puede ser eliminado" );
                header("Location:" . Route::names('usuarios.index'));

                die();
            }

        }
        
        // $usuario = New Usuario;
        // $usuario->id = $id;
        $respuesta = $usuario->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "El usuario fue eliminado correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Usuario',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El usuario fue eliminado correctamente' );

            header("Location:" . Route::names('usuarios.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este Usuario no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Usuario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este Usuario no se podrá eliminar ***' );
            header("Location:" . Route::names('usuarios.index'));

        }
        
        die();
    }

    public function editPerfil()
    {
        if ( !usuarioAutenticado() ) {
            include "vistas/modulos/plantilla.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }

        $usuario = New Usuario;
        $usuario -> id = usuarioAutenticado()["id"];
        Autorizacion::authorize('update', $usuario);

        $id = $usuario -> id;

        if ( $usuario->consultar(null , $id) ) {

            $usuario->consultarPerfiles();
            $usuario->consultarPermisos();

            // var_dump($usuario);
            // die();

            require_once "app/Models/Perfil.php";
            $perfil = New \App\Models\Perfil;
            $perfiles = $perfil->consultar();

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/Puesto.php";
            $puesto = New \App\Models\Puesto;
            $puestos = $puesto->consultar();


            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            // include "vistas/modulos/usuarios/editar.php";
            $contenido = array('modulo' => 'vistas/modulos/usuarios/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
