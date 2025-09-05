<?php

namespace App\Controllers;
use Exception;  

require_once "app/Controllers/Autorizacion.php";

require_once "app/Models/Proveedor.php";
require_once "app/Models/ProveedorArchivos.php";
require_once "app/Models/DatosFiscal.php";
require_once "app/Models/TagProveedor.php";
require_once "app/Models/PermisoProveedor.php";
require_once "app/Requests/SaveDatosFiscalesRequest.php";
require_once "app/Models/CategoriaPermiso.php";
require_once "app/Models/CategoriaProveedor.php";


use App\Route;
use App\Models\Proveedor;
use App\Models\TagProveedor;
use App\Models\PermisoProveedor;
use App\Models\ProveedorArchivos;
use App\Models\DatosFiscal;
use App\Models\CategoriaPermiso;
use App\Models\CategoriaProveedor;

use App\Requests\SaveDatosFiscalesRequest;


class CalidadProductoProveedorController
{
    public function index()
    {

        $proveedorArchivos = new ProveedorArchivos;
        
        $proveedorArchivos->consultarArchivoNombre("Soporte");
        $proveedorArchivos->consultarArchivoNombre("Listado");
        $proveedorArchivos->consultarArchivoNombre("Certificaciones");

        $permisoProveedor = new PermisoProveedor;
        $permisos = $permisoProveedor->consultarPermisos(usuarioAutenticadoProveedor()["id"]);

        $proveedor = New Proveedor;
        $proveedor->consultar("razonSocial", usuarioAutenticadoProveedor()["usuario"]);
 
        $categoriaPermiso = New CategoriaPermiso;
        $permisosAsignados = $categoriaPermiso->consultar($proveedor->idCategoria);

        $contenido = array('modulo' => 'vistas/modulos/calidad-producto/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }

    public function update($id)
    {   
        try{
            $request = SaveDatosFiscalesRequest::validated($id);

            $datoFiscal = new DatosFiscal;
            $datoFiscal->id = $id;
            $respuesta = $datoFiscal->actualizarCalidadProductoProveedor($request);

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
            'titulo' => 'Actualizar Datos',
            'subTitulo' => 'OK',
            'mensaje' => 'Tus datos fueron actualizado correctamente' );
            header("Location:" . Route::names('calidad-producto.index'));

        } catch (Exception $e) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
            'titulo' => 'Actualizar Datos',
            'subTitulo' => 'Error',
            'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('calidad-producto.index', $id));
        }
    }

}
