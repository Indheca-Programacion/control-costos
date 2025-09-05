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


class DatosLegalesProveedorController
{
    public function index()
    {

        $proveedorArchivos = new ProveedorArchivos;

        $proveedorArchivos->consultarArchivoNombre("ActaConstitutiva");
        $proveedorArchivos->consultarArchivoNombre("ConstanciaSituacionFiscal");
        $proveedorArchivos->consultarArchivoNombre("CumplimientoSAT");
        $proveedorArchivos->consultarArchivoNombre("CumplimientoIMSS");
        $proveedorArchivos->consultarArchivoNombre("CumplimientoInfonavit");
        $proveedorArchivos->consultarArchivoNombre("AltaRepse");
        $proveedorArchivos->consultarArchivoNombre("UltimaInformativa");

        $permisoProveedor = new PermisoProveedor;
        $permisos = $permisoProveedor->consultarPermisos(usuarioAutenticadoProveedor()["id"]);

        $proveedor = New Proveedor;
        $proveedor->consultar("razonSocial", usuarioAutenticadoProveedor()["usuario"]);
 
        $categoriaPermiso = New CategoriaPermiso;
        $permisosAsignados = $categoriaPermiso->consultar($proveedor->idCategoria);

        $contenido = array('modulo' => 'vistas/modulos/datos-legales/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }

}
