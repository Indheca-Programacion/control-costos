<?php

namespace App\Controllers;

require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Proveedor.php";
require_once "app/Models/OrdenCompra.php";
require_once "app/Models/CategoriaPermiso.php";


use App\Route;
use App\Conexion;
use App\Models\Proveedor;
use App\Models\OrdenCompra;
use App\Models\CategoriaPermiso;


class HomeProveedorController
{
    public function index()
    {
        if ( !usuarioAutenticadoProveedor() ) {
            include "vistas/modulos/plantilla_proveedores.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }

        // Validar Autorizacion
        $proveedor = New Proveedor;
        $proveedor->consultar("razonSocial", usuarioAutenticadoProveedor()["usuario"]);

        if ( !$proveedor->consultar("razonSocial", usuarioAutenticadoProveedor()["usuario"]) ) {
            include "vistas/modulos/plantilla_proveedores.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }


        $ordenCompra = New OrdenCompra;
        $ordenCompra->id = usuarioAutenticadoProveedor()["id"];
        $ordenCompras = $ordenCompra->consultarOrdenCompraProveedor();
        
        $cantidadOrdenes = count($ordenCompras);
        $CantidadPorPagar = 0;
        $cantidadPagadas = 0;
        $datosFiscales = 0;
        $cantidadDebidaDiligencia = 0;

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/dashboard_proveedor.php');
        include "vistas/modulos/plantilla_proveedores.php";
    }
}
