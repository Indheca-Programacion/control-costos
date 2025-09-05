<?php

namespace App\Controllers;
use Exception;  

require_once "app/Controllers/Autorizacion.php";

require_once "app/Models/Proveedor.php";
require_once "app/Models/ProveedorArchivos.php";
require_once "app/Models/PermisoProveedor.php";
require_once "app/Models/CategoriaPermiso.php";

use App\Route;
use App\Models\Proveedor;
use App\Models\PermisoProveedor;
use App\Models\ProveedorArchivos;
use App\Models\CategoriaPermiso;

class DatosFinancierosProveedorController
{
    public function index()
    {

        $proveedorArchivos = new ProveedorArchivos;
        $proveedorArchivos->consultarArchivoNombre("EstadoCuenta");
        $proveedorArchivos->consultarArchivoNombre("EstadoFinanciero");
        $proveedorArchivos->consultarArchivoNombre("UltimaDeclaracionAnual");

        $permisoProveedor = new PermisoProveedor;
        $permisos = $permisoProveedor->consultarPermisos(usuarioAutenticadoProveedor()["id"]);

        $proveedor = New Proveedor;
        $proveedor->consultar("razonSocial", usuarioAutenticadoProveedor()["usuario"]);
 
        $categoriaPermiso = New CategoriaPermiso;
        $permisosAsignados = $categoriaPermiso->consultar($proveedor->idCategoria);

        $contenido = array('modulo' => 'vistas/modulos/datos-financieros/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }
}
