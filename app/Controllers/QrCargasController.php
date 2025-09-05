<?php

namespace App\Controllers;

require_once "app/Models/QrCarga.php";
require_once "app/Models/Obra.php";
require_once "app/Models/Carga.php";
require_once "app/Models/Ubicacion.php";
require_once "app/Models/MaterialCarga.php";
require_once "app/Models/OperadorMaquinaria.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveQrCargasRequest.php";


use App\Conexion;
use App\Models\QrCarga;
use App\Models\Obra;
use App\Models\Ubicacion;
use App\Models\Carga;
use App\Models\OperadorMaquinaria;
use App\Models\MaterialCarga;

use App\Requests\SaveQrCargasRequest;

use App\Route;

class QrCargasController
{
    public function index()
    {
        $qrCargas = New QrCarga;
        Autorizacion::authorize('create', $qrCargas);

        $operadorMaquinaria = new OperadorMaquinaria;
        $operadoresMaquinaria = $operadorMaquinaria->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/qr-cargas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $qrCargas = New QrCarga;
        Autorizacion::authorize('create', $qrCargas);

        $operadorMaquinaria = new OperadorMaquinaria;
        $operadoresMaquinaria = $operadorMaquinaria->consultar();

        $contenido = array('modulo' => 'vistas/modulos/qr-cargas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New QrCarga);

        $request = SaveQrCargasRequest::validated();

        $qrCarga = new QrCarga;

        $respuesta = $qrCarga->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Asignar QR',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La asignación del QR fue exitosa,' );
            header("Location:" . Route::names('cargas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Asignar QR',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('cargas.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new QrCarga);

        $qrCargas = New QrCarga;
        $carga = New Carga;

        $obra = New Obra;
        $obras = $obra->consultar();

        // VERIFICAR CARGA ACTIVA
        $verificarCargaActiva =  $carga->verificarCargaActiva($id);

        $operadorMaquinaria = new OperadorMaquinaria;
        $operadoresMaquinaria = $operadorMaquinaria->consultar();

        $materialCarga = new MaterialCarga;
        $materialesCarga = $materialCarga->consultar();

        $ubicacion = new Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/Usuario.php";
        $usuario = new \App\Models\Usuario;
        $usuario->consultar(null, usuarioAutenticado()['id']);

        if ($qrCargas->consultar(null,$id)) {

            if($qrCargas->idMaquinaria){
                
                $qrCargas->consultarEvidencias();
                $qrCargas->consultarVerificaciones();
                $qrCargas->consultarTarjetasCirculacion();
                $qrCargas->consultarAcuedos();
            }

            $contenido = array('modulo' => 'vistas/modulos/qr-cargas/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    // FUNCION PARA ACTUALIZAR LOS DATOS
    // DEL QR CARGAS
    public function update($id)
    {       

        $request = SaveQrCargasRequest::validated();

        $qrCarga = New QrCarga;
        $qrCarga->id = $request["idMaquinaria"];

        $respuesta = $qrCarga->actualizar($request);
        $mensaje = 'La requisición fue actualizada correctamente';

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar QR Carga',
                                                           'subTitulo' => 'OK',
                                                           // 'mensaje' => 'La requisición fue actualizada correctamente' );
                                                           'mensaje' => $mensaje );
            header("Location:" . Route::names('qr-cargas.edit', $id));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                            'titulo' => 'Actualizar QR Carga',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('qr-cargas.edit', $id));

        }

    }

    public function destroy($id)
    {
    }
}
