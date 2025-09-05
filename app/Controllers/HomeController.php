<?php

namespace App\Controllers;

require_once "app/Models/Indicador.php";
require_once "app/Models/Usuario.php";
require_once "app/Models/Obra.php";
require_once "app/Models/Nominas.php";
require_once "app/Models/Requisicion.php";
require_once "app/Models/Tarea.php";
require_once "app/Models/Empleado.php";
require_once "app/Controllers/Autorizacion.php";

use App\Route;
use App\Conexion;
use App\Models\Indicador;
use App\Models\Usuario;
use App\Models\Tarea;
use App\Models\Obra;
use App\Models\Nominas;
use App\Models\Requisicion;
use App\Models\Empleado;

class HomeController
{
    public function index()
    {
        if ( !usuarioAutenticado() ) {
            include "vistas/modulos/plantilla.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }

        // Validar Autorizacion
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "indicadores", "ver") ) {

            $contenido = array('modulo' => 'vistas/modulos/inicio.php');
            include "vistas/modulos/plantilla.php";
            return;

        }
        $cantidadNominas = 0;
        $cantidadObras = 0;
        $cantidadRequisiciones = 0;
        $cantidadEmpleados = 0;

        $requisicion = New Requisicion;
        $requisiciones = $requisicion->consultar();
        $cantidadRequisiciones = count($requisiciones);

        $empleado = New Empleado;
        $empleados = $empleado->consultar();
        $cantidadEmpleados = count($empleados);

        $tarea = new Tarea;
        $tareas = $tarea->consultarPendientes($usuario->id);
        $arrayTareas = array();
        foreach ($tareas as $key => $value) {
            $rutaEdit = Route::names('tareas.edit', $value['id']);
            array_push($arrayTareas,[
                "descripcion" => $value["descripcion"],
                "fecha_limite" => $value["fecha_limite"],
                "ruta" => $rutaEdit,
            ]);
        }

        $obra = New Obra;
        $obras = $obra->consultar();
        $cantidadObras = count($obras);
        
        $nomina = New Nominas;
        $nominas = $nomina->consultar();
        $cantidadNominas = count($nominas);

        unset($requisicion, $requisiciones);
        unset($empleado, $empleados);
        unset($obra, $obras);
        unset($nomina, $nominas);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/dashboard.php');
        include "vistas/modulos/plantilla.php";
    }
}
