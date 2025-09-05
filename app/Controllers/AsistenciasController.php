<?php

namespace App\Controllers;

require_once "app/Models/Asistencias.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Requests/SaveAsistenciasRequest.php";


use App\Conexion;
use App\Models\Asistencias;
use App\Requests\SaveAsistenciasRequest;

use App\Route;

class AsistenciasController
{
    public function index()
    {
        Autorizacion::authorize('view', new Asistencias);

        $asistencia = New Asistencias;
        $asistencias = $asistencia->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/asistencias/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $asistencia = New Asistencias;
        Autorizacion::authorize('create', $asistencia);

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultarActivos();
        
        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        $contenido = array('modulo' => 'vistas/modulos/asistencias/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        Autorizacion::authorize('create', New Asistencias);

        $request = SaveAsistenciasRequest::validated();
        
        $arrayAsistencias = $request["jornada"];
        // $archivos = $request["jornada_archivos"];

        $datosOrdenados = array();
        foreach ($arrayAsistencias["partida"] as $index => $value) {
            $trabajador = $arrayAsistencias['trabajador'][$index];
            $fecha = $arrayAsistencias['fecha'][$index];
            $hrEntrada = $arrayAsistencias['hrEntrada'][$index];
            $hrSalida = $arrayAsistencias['hrSalida'][$index];
            $hrExtra = $arrayAsistencias['hrExtra'][$index];
            $obraId = $arrayAsistencias['obraId'][$index];
            $incidencia = $arrayAsistencias['incidencia'][$index];
            $observacion = $arrayAsistencias['observacion'][$index];
            $datosOrdenados[] = array(
                'partida' => $arrayAsistencias['partida'][$index],
                'fk_empleado' => $trabajador,
                'fecha' => $fecha,
                'horaEntrada' => $hrEntrada,
                'horaSalida' => $hrSalida,
                'horasExtras' => $hrExtra,
                'fk_obraId' => $obraId,
                'incidencia' => $incidencia,
                'observacion' => $observacion
            );
        }
        $asistencias = New Asistencias;
        
        foreach($datosOrdenados as $datos) {
            $respuesta = $asistencias->crear($datos);
        }

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Asistencia',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'Las asistencias fueron creadas correctamente' );
            header("Location:" . Route::names('asistencias.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Asistencia',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('asistencias.create'));

        }
        
        die();
    }

    public function edit($id)
    {
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {
    }
}
