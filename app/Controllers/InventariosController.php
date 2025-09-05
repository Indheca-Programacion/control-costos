<?php

namespace App\Controllers;

require_once "app/Models/Partida.php";
require_once "app/Models/Inventario.php";
require_once "app/Models/InventarioDetalles.php";
require_once "app/Requests/SaveInventariosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Partida;
use App\Models\Inventario;
use App\Models\InventarioDetalles;
use App\Requests\SaveInventariosRequest;
use App\Route;

class InventariosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Inventario);

        require_once "app/Models/Almacen.php";
        $almacen = New \App\Models\Almacen;
        $almacenes = $almacen->consultarPropios();  
        
        require_once "app/Models/Genero.php";
        $genero = New \App\Models\Genero;
        $generos = $genero->consultar();

        require_once "app/Models/SubGenero.php";
        $subgenero = New \App\Models\SubGenero;
        $subgeneros = $subgenero->consultar();
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/inventarios/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create($id = null)
    {
        $inventario = New Inventario;
        Autorizacion::authorize('create', $inventario);
    
        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null,$id);

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obra->consultar(null,$requisicion->idObra);

        require_once "app/Models/Insumo.php";
        $insumo = New \App\Models\Insumo;
        $insumos = $insumo->consultar();

        require_once "app/Models/Indirecto.php";
        $indirecto = New \App\Models\Indirecto;
        $indirectos = $indirecto->consultar();

        require_once "app/Models/Almacen.php";
        $almacen = New \App\Models\Almacen;
        $almacenes = $almacen->consultar();
        
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

        $formularioEditable = true;
        $contenido = array('modulo' => 'vistas/modulos/inventarios/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New Inventario);
        
        $request = SaveInventariosRequest::validated();

        $inventario = New Inventario;
        $respuesta = $inventario->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Inventario',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El inventario fue creado correctamente' );
            header("Location:" . Route::names('inventarios.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Inventario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('inventarios.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Inventario);

        $inventario = New Inventario;

        if ( $inventario->consultar(null , $id) ) {

            require_once "app/Models/Almacen.php";
            $almacen = New \App\Models\Almacen;
            $almacenes = $almacen->consultar();

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuarios = $usuario->consultar();
            
            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null,$inventario->requisicionId);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null,$requisicion->idObra);
            
            $tiposContratoInventario = [
                [
                    'id' => 1,
                    'nombreCorto' => '640853819'
                ],
                [
                    'id' => 2,
                    'nombreCorto' => '640853819'
                ],
                        [
                    'id' => 3,
                    'nombreCorto' => '640853819'
                        ],
                [
                    'id' => 4,
                    'nombreCorto' => 'GENERAL'
                ]
            ];
            
            $contenido = array('modulo' => 'vistas/modulos/inventarios/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Inventario);

        $request = SaveInventariosRequest::validated($id);

        $inventario = New Inventario;
        $inventario->id = $id;
        $respuesta = $inventario->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Inventario',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El inventario fue actualizada correctamente' );
            header("Location:" . Route::names('inventarios.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Inventario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('inventarios.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Inventario);

        // Sirve para validar el Token
        if ( !SaveInventariosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Iventario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('inventarios.index'));
            die();

        }

        $inventario = New Inventario;
        $inventario->id = $id;

        $respuesta = $inventario->eliminar();

        if ( $respuesta ) {


            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Inventario',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El articulo fue eliminada correctamente' );


        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Inventario',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este inventario no se podrá eliminar ***' );

        }

        header("Location:" . Route::names('inventarios.index'));
        
        die();
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Inventario);

        $inventario = New Inventario;

        if ( $inventario->consultar(null , $id) ) {

            $inventarioDetalles = New InventarioDetalles;

            $inventarioDetalles->inventario = $id;
            $detalles = $inventarioDetalles->consultarDetalles();

            require_once "app/Models/Almacen.php";
            $almacen = New \App\Models\Almacen;
            $almacen->consultar(null, $inventario->almacen);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $inventario->usuarioIdCreacion);

            $usuarioNombre = mb_strtoupper($usuario->nombre);
            $solicito = mb_strtoupper($usuario->nombre . ' ' . $usuario->apellidoPaterno);
            if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . mb_strtoupper($usuario->apellidoMaterno);
            $recibioFirma = $usuario->firma;


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

            include "reportes/vale-entrada.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
