<?php

namespace App\Controllers;

require_once "app/Models/CategoriaProveedor.php";
require_once "app/Models/CategoriaPermiso.php";
require_once "app/Models/PermisoCategoriaProveedor.php";

require_once "app/Policies/CategoriaPermisoPolicy.php";
require_once "app/Requests/SaveCategoriaPermisoRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\CategoriaProveedor;
use App\Models\CategoriaPermiso;
use App\Models\PermisoCategoriaProveedor;
use App\Policies\CategoriaPermisoPolicy;
use App\Requests\SaveCategoriaPermisoRequest;
use App\Route;

class CategoriasPermisosController
{
    public function index()
    {

        $categoria = new CategoriaProveedor;
        $categorias = $categoria->consultar();

        $permiso = new PermisoCategoriaProveedor;
        $permisos = $permiso->consultar();

        $categoriaPermiso = New CategoriaPermiso;
        $permisosAsignados = $categoriaPermiso->consultar(null);

        $permisosMarcados = [];
        foreach ($permisosAsignados as $row) {
            $catId = $row['idCategoria'];
            $grpId = $row['idGrupo'];
            $permId = $row['idPermiso'];
            
            $permisosMarcados[$catId][$grpId][$permId] = true;
        }

        $contenido = array('modulo' => 'vistas/modulos/categoria-permisos/index.php');
        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        
        Autorizacion::authorize('create', New CategoriaPermiso);

        $request = SaveCategoriaPermisoRequest::validated();

        $categoriaPermiso = New CategoriaPermiso;
        $respuesta = $categoriaPermiso->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Categoria Proveedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La categoria del proveedor fue creada correctamente' );
            header("Location:" . Route::names('categoria-permiso-proveedor.index'));

        } else {

            // $array = array();
            // array_push($array, "Hubo un error al procesar, de favor intente de nuevo.");
            // $_SESSION[CONST_SESSION_APP]["errors"] = $array;
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Categoria Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('categoria-permiso-proveedor.index'));

        }
        
        die();

    }

}
