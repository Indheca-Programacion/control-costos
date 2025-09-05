<?php

namespace App\Controllers;

require_once "app/Models/Nominas.php";
// require_once "app/Policies/EstatusPolicy.php";
require_once "app/Requests/SaveNominasRequest.php";
require_once "app/Requests/SaveAsistenciasRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Asistencias.php";

use App\Models\Nominas;
use App\Models\Asistencias;
// use App\Policies\EstatusPolicy;
use App\Requests\SaveNominasRequest;
use App\Requests\SaveAsistenciasRequest;

use App\Route;

class NominasController
{
    public function index()
    {
        Autorizacion::authorize('view', New Nominas);

        $Nomina = New Nominas;
        // $Nominas = $Nomina->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/nominas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        
        $Nomina = New Nominas;
        Autorizacion::authorize('create', $Nomina);
        
        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        $contenido = array('modulo' => 'vistas/modulos/nominas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {
        $Nomina = New Nominas;

        Autorizacion::authorize('create', New Nominas);
        $request = SaveNominasRequest::validated();
        
        $datosOrdenados = array();
        $arrayNomina = $request["datos"];
        //Se genera un array para mandar los datos a crear las nominas
        $arrayDatos = array(
            "fk_obraId" => $request["obraId"],
            "semana" => $request["semana"]
        );
        $Nomina->crear($arrayDatos);
        foreach ($arrayNomina["primas"] as $index => $value) {
            $primas = $arrayNomina['primas'][$index];
            $comida = $arrayNomina['comida'][$index];
            $prestamos = $arrayNomina['prestamos'][$index];
            $descuentos = $arrayNomina['descuentos'][$index];
            $pension = $arrayNomina['pension'][$index];
            $neto = $arrayNomina['neto'][$index];
            $empleado = $arrayNomina['empleado'][$index];
            $obradetalle = $arrayNomina['obradetalle'][$index];
            $salario = $arrayNomina['salario'][$index];
            $hrsExtras = $arrayNomina['hrsExtras'][$index];
            $datosOrdenados[] = array(
                'fk_nominaId'=> $Nomina->id,
                'primas' => $primas,
                'comida' => $comida,
                'prestamos' => $prestamos,
                'descuentos' => $descuentos,
                'pension' => $pension,
                'neto' => $neto,
                'salario' => $salario,
                'hrsExtras' => $hrsExtras,
                'fk_obraDetalleId' => $obradetalle,
                'fk_empleadoId' => $empleado,
            );
        }
        
        foreach($datosOrdenados as $datos) {
            $respuesta = $Nomina->crearDetalle($datos);
        }

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Nomina',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La Nomina fue creadas correctamente' );
            header("Location:" . Route::names('nominas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Nomina',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('nominas.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Nominas);

        $nomina = New Nominas;
        $nominas = $nomina->consultar(null,$id);
        if ( count($nominas)>0 ) {

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/NominasDetalles.php";
            $nomina_detalles = New \App\Models\NominasDetalles;
            $nominas_detalles = $nomina_detalles->consultarPorNomina($nominas["id"]);

            $contenido = array('modulo' => 'vistas/modulos/nominas/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {

    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Nominas);

        // Sirve para validar el Token
        if ( !SaveNominasRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Nomina',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('nominas.index'));
            die();

        }

        $nomina = New Nominas;
        $nomina->id = $id;
        $respuesta = $nomina->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Nomina',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La nomina fue eliminada correctamente' );

            header("Location:" . Route::names('nominas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Nomina',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta obra no se podrá eliminar ***' );
            header("Location:" . Route::names('nominas.index'));

        }
        
        die();
    }
}
