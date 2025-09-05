<?php

namespace App\Controllers;

require_once "app/Models/Proveedor.php";
require_once "app/Policies/ProveedorPolicy.php";
require_once "app/Requests/SaveProveedoresRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/PermisoProveedor.php";
require_once "app/Models/ProveedorArchivos.php";
require_once "app/Models/CategoriaPermiso.php";

use App\Models\Proveedor;
use App\Policies\ProveedorPolicy;
use App\Requests\SaveProveedoresRequest;
use App\Models\PermisoProveedor;
use App\Models\ProveedorArchivos;
use App\Models\CategoriaPermiso;

use App\Route;

class ProveedoresController
{
    public function index()
    {
        Autorizacion::authorize('view', new Proveedor);

        

        $proveedor = New Proveedor;
        $proveedores = $proveedor->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/proveedores/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $proveedor = new Proveedor;
        Autorizacion::authorize('create', $proveedor);
        
        require_once "app/Models/CategoriaProveedor.php";
        $categoriaProveedor = new \App\Models\CategoriaProveedor;
        $categorias = $categoriaProveedor->consultar();

        $contenido = array('modulo' => 'vistas/modulos/proveedores/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Proveedor);

        $request = SaveProveedoresRequest::validated();

        $proveedor = New Proveedor;
        $respuesta = $proveedor->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Proveedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El proveedor fue creado correctamente' );
            header("Location:" . Route::names('proveedores.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('proveedores.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Proveedor);

        $proveedorArchivos = new ProveedorArchivos;

        $proveedorArchivos->consultarArchivoNombre("CV",$id);
        $proveedorArchivos->consultarArchivoNombre("OC1",$id);
        $proveedorArchivos->consultarArchivoNombre("OC2",$id);
        $proveedorArchivos->consultarArchivoNombre("OC3",$id);

        $proveedorArchivos->consultarArchivoNombre("ActaConstitutiva",$id);
        $proveedorArchivos->consultarArchivoNombre("ConstanciaSituacionFiscal",$id);
        $proveedorArchivos->consultarArchivoNombre("CumplimientoSAT",$id);
        $proveedorArchivos->consultarArchivoNombre("CumplimientoIMSS",$id);
        $proveedorArchivos->consultarArchivoNombre("CumplimientoInfonavit",$id);
        $proveedorArchivos->consultarArchivoNombre("AltaRepse",$id);
        $proveedorArchivos->consultarArchivoNombre("UltimaInformativa",$id);

        $proveedorArchivos->consultarArchivoNombre("ConstanciaFiscal",$id);
        $proveedorArchivos->consultarArchivoNombre("OpinionCumplimiento",$id);
        $proveedorArchivos->consultarArchivoNombre("ComprobanteDomicilio",$id);
        $proveedorArchivos->consultarArchivoNombre("DatosBancarios",$id);

        $proveedorArchivos->consultarArchivoNombre("EstadoCuenta",$id);
        $proveedorArchivos->consultarArchivoNombre("EstadoFinanciero",$id);
        $proveedorArchivos->consultarArchivoNombre("UltimaDeclaracionAnual",$id);

        $proveedorArchivos->consultarArchivoNombre("Soporte",$id);
        $proveedorArchivos->consultarArchivoNombre("Listado",$id);
        $proveedorArchivos->consultarArchivoNombre("Certificaciones",$id);

        require_once "app/Models/Divisa.php";
        $divisa = new \App\Models\Divisa;
        $divisas = $divisa->consultar();

        $permisoProveedor = new PermisoProveedor;
        $permisos = $permisoProveedor->consultarPermisos($id);

        require_once "app/Models/CategoriaProveedor.php";
        $categoriaProveedor = new \App\Models\CategoriaProveedor;
        $categorias = $categoriaProveedor->consultar();

        $proveedor = New Proveedor;

        if ( $proveedor->consultar(null , $id) ) {

            $categoriaPermiso = New CategoriaPermiso;
            $permisosAsignados = $categoriaPermiso->consultar($proveedor->idCategoria);

            $contenido = array('modulo' => 'vistas/modulos/proveedores/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Proveedor);

        $request = SaveProveedoresRequest::validated($id);

        $proveedor = New Proveedor;
        $proveedor->id = $id;
        $respuesta = $proveedor->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Proveedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El proveedor fue actualizado correctamente' );
            header("Location:" . Route::names('proveedores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('proveedores.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Proveedor);

        // Sirve para validar el Token
        if ( !SaveProveedoresRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('proveedores.index'));
            die();

        }

        $proveedor = New Proveedor;
        // $proveedor->id = $id;
        $proveedor->consultar(null , $id); // Para tener la ruta de la foto
        $respuesta = $proveedor->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Proveedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El proveedor fue eliminado correctamente' );

            header("Location:" . Route::names('proveedores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Proveedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este proveedor no se podrá eliminar ***' );
            header("Location:" . Route::names('proveedores.index'));

        }
        
        die();

    }
}
