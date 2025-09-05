<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/CategoriaProveedor.php";
require_once "../Controllers/Autorizacion.php";
// require_once "../Controllers/Validacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\CategoriaProveedor;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class CategoriaProveedorAjax
{
        /*=============================================
        TABLA DE PERMISOS
        =============================================*/
	public function mostrarTabla()
	{
                try{
                        $categoriaProveedor = New CategoriaProveedor;
                        $categorias = $categoriaProveedor->consultar();
        
                        $columnas = array();
                        array_push($columnas, [ "data" => "consecutivo" ]);
                        array_push($columnas, [ "data" => "nombre" ]);
                        array_push($columnas, [ "data" => "descripcion" ]);
                        array_push($columnas, [ "data" => "acciones" ]);
                        
                        $token = createToken();
                        
                        $registros = array();
                        foreach ($categorias as $key => $value) {
                                $rutaEdit = Route::names('categoria-proveedores.edit', $value['id']);
                                $rutaDestroy = Route::names('categoria-proveedores.destroy', $value['id']);
                                $folio = mb_strtoupper(fString($value['nombre']));
        
                                array_push( $registros, [ "consecutivo" => ($key + 1),
                                                          "nombre" => fString($value["nombre"]),
                                                          "descripcion" => fString($value["descripcion"]),
                                                          "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                                                        <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                                                                <input type='hidden' name='_method' value='DELETE'>
                                                                                <input type='hidden' name='_token' value='{$token}'>
                                                                                <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                                                                <i class='far fa-times-circle'></i>
                                                                                </button>
                                                                        </form>" ] );
                        }
        
                        $respuesta = array();
                        $respuesta['codigo'] = 200;
                        $respuesta['error'] = false;
                        $respuesta['datos']['columnas'] = $columnas;
                        $respuesta['datos']['registros'] = $registros;
                }catch (\Exception $e) {
                        $errorMessage = $e->getMessage();
                        $respuesta = [
                            'codigo' => 500,
                            'error' => true,
                            'errorMessage' => $errorMessage
                        ];
                }

                echo json_encode($respuesta);
	}	

}

/*=============================================
TABLA DE PERMISOS
=============================================*/
$activar = new CategoriaProveedorAjax();
$activar -> mostrarTabla();
