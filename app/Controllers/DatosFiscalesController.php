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

use App\Route;
use App\Models\Proveedor;
use App\Models\TagProveedor;
use App\Models\PermisoProveedor;
use App\Models\ProveedorArchivos;
use App\Models\DatosFiscal;
use App\Models\CategoriaPermiso;

use App\Requests\SaveDatosFiscalesRequest;


class DatosFiscalesController
{
    public function index()
    {
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/datos-fiscales/index.php');
        $proveedorArchivos = new ProveedorArchivos;
        $proveedorArchivos->consultarCV();
        $proveedorArchivos->consultarContratoFacturaOC1();
        $proveedorArchivos->consultarContratoFacturaOC2();
        $proveedorArchivos->consultarContratoFacturaOC3();

        $proveedorArchivos->consultarActaConstitutiva();
        $proveedorArchivos->consultarConstanciaSituacionFiscal();
        $proveedorArchivos->consultarCumplimientoSAT();
        $proveedorArchivos->consultarCumplimientoIMSS();
        $proveedorArchivos->consultarCumplimientoInfonavit();
        $proveedorArchivos->consultarAltaRepse();
        $proveedorArchivos->consultarUltimaInformativa();

        $proveedorArchivos->consultarEstadoCuenta();
        $proveedorArchivos->consultarEstadoFinancieros();
        $proveedorArchivos->consultarUltimaDeclaracionAnual();

        $proveedorArchivos->consultarSoporte();
        $proveedorArchivos->consultarListado();
        $proveedorArchivos->consultarCertificaciones();

        $permisoProveedor = new PermisoProveedor;
        $permisos = $permisoProveedor->consultarPermisos(usuarioAutenticadoProveedor()["id"]);

        $tagProveedor = new TagProveedor;
        $tagProveedores = $tagProveedor->consultar();

        $proveedor = New Proveedor;
        $proveedor->consultar("razonSocial", usuarioAutenticadoProveedor()["usuario"]);
 
        $categoriaPermiso = New CategoriaPermiso;
        $permisosAsignados = $categoriaPermiso->consultar($proveedor->idCategoria);

        include "vistas/modulos/plantilla_proveedores.php";
    }

    public function update($id)
    {   
        try{
            $request = SaveDatosFiscalesRequest::validated($id);

            $datoFiscal = new DatosFiscal;
            $datoFiscal->id = $id;
            $respuesta = $datoFiscal->actualizar($request);

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
            'titulo' => 'Actualizar Empleado',
            'subTitulo' => 'OK',
            'mensaje' => 'El empleado fue actualizado correctamente' );
            header("Location:" . Route::names('datos-fiscales.index'));

        } catch (Exception $e) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
            'titulo' => 'Actualizar Empleado',
            'subTitulo' => 'Error',
            'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('datos-fiscales.index', $id));

        }
    }

}
