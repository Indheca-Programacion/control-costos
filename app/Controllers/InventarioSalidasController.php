<?php

namespace App\Controllers;

require_once "app/Models/Inventario.php";
require_once "app/Models/InventarioSalida.php";
require_once "app/Models/InventarioDetalles.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveInventarioSalidasRequest.php";

use App\Models\Inventario;
use App\Models\InventarioSalida;
use App\Models\InventarioDetalles;
use App\Requests\SaveInventarioSalidasRequest;
use App\Route;

class InventarioSalidasController
{
    public function print($id)
    {

        $inventarioSalida = New InventarioSalida;

        if ( $inventarioSalida->consultar(null , $id) ) {

            $inventario = New Inventario;
            $inventario->consultar(null, $inventarioSalida->inventario);

            $detalles = $inventarioSalida->consultarDetalles($id);

            require_once "app/Models/Almacen.php";
            $almacen = New \App\Models\Almacen;
            $almacen->consultar(null, $inventario->almacen);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $inventarioSalida->usuarioIdCreacion);

            $usuarioNombre = mb_strtoupper($usuario->nombre);
            $entrego = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $entrego .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $entregoFirma = $usuario->firma;
            unset($usuario);

            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $inventarioSalida->usuarioIdAutoriza);
            $usuarioNombre = mb_strtoupper($usuario->nombre);
            $autorizo = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $autorizo .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $autorizoFirma = $usuario->firma;
	    unset($usuario);


            $tiposContratoInventario = [
                [
                    'id' => 1,
                    'nombreCorto' => '640853819'
                ],
                [
                    'id' => 2,
                    'nombreCorto' => '640852802'
                ],
                        [
                    'id' => 3,
                    'nombreCorto' => '6509854810'
                        ],
                [
                    'id' => 4,
                    'nombreCorto' => 'GENERAL'
                ]
            ];

            $numeroContrato = $inventario->numeroContrato;
            $contratoEncontrado = null;

            foreach ($tiposContratoInventario as $contrato) {
                if ($contrato['id'] == $numeroContrato) {
                    $contratoEncontrado = $contrato;
                    break; // salir del ciclo al encontrar coincidencia
                }
            }


            include "reportes/vale-salida.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function edit($id)
    {
        $inventarioSalida = New InventarioSalida;
        $inventarioSalida->consultar(null, $id);

        $inventario = New Inventario;
        $inventario->consultar(null, $inventarioSalida->inventario);

        require_once "app/Models/Almacen.php";
        $almacen = New \App\Models\Almacen;
        $almacenes = $almacen->consultar();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, usuarioAutenticado()["id"]);

        $detalles = $inventarioSalida->consultarDetalles($id);


        $contenido = array(
            'modulo' => 'vistas/modulos/inventario-salidas/editar.php',
        );

        include "vistas/modulos/plantilla.php";
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New InventarioSalida);

        $request = SaveInventarioSalidasRequest::validated($id);

        $inventarioSalida = New InventarioSalida;
        $inventarioSalida->id = $id;
        $respuesta = $inventarioSalida->autorizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Salida',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La salida fue actualizado correctamente' );
            header("Location:" . Route::names('inicio.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Salida',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('inventario-salidas.edit', $id));

        }
        
        die();
    }

}
