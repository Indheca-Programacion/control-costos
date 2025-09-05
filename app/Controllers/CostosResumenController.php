<?php

namespace App\Controllers;

require_once "app/Models/Usuario.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Obra.php";
require_once "app/Models/Insumo.php";
require_once "app/Models/Indirecto.php";
require_once "app/Models/Requisicion.php";
require_once "app/Models/ObraDetalles.php";
require_once "app/Models/Nominas.php";
require_once "app/Models/Perfil.php";
require_once "app/Models/Partida.php";

use App\Models\Obra;
use App\Models\Insumo;
use App\Models\Indirecto;
use App\Models\Usuario;
use App\Models\Requisicion;
use App\Models\ObraDetalles;
use App\Models\Partida;
use App\Models\Nominas;
use App\Models\Perfil;
use DateTime;
use App\Route;

class CostosResumenController
{
    public function index()
    {

        if ( !usuarioAutenticado() ) {
            header("Location:" . Route::routes('ingreso'));
            die();
        }

        // Validar Autorizacion
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        
        $usuarioRoal = false;
        if ( $usuario->empresaId == 4) {
            $usuarioRoal = true;
        }

        $crearAnuncio = Autorizacion::permiso($usuario, "anuncios-obra", "crear");

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "costos-resumen", "ver") ) {

            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();

        }

        require_once "app/Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisas = $divisa->consultar();

        require_once "app/Models/Plantilla.php";
        $plantilla = New \App\Models\Plantilla;
        $plantillas = $plantilla->consultarPorUsuario(usuarioAutenticado()["id"]);

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "app/Models/CategoriaOrdenes.php";
        $categoriaOrdenes = New \App\Models\CategoriaOrdenes;
        $categoriasOrdenCompra = $categoriaOrdenes->consultar();

        $contenido = array('modulo' => 'vistas/modulos/costos-resumen/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
    }

    public function store()
    {    
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

    public function print($id){
        Autorizacion::authorize('view', New Obra);
        $obra = New Obra;

        if ( $obra->consultar(null , $id) ) {
            
            $usuario = New Usuario;
            $usuario->consultar(null, $obra->usuarioIdCreacion);
            
            $mes = fNombreMes($this->obtenerMes(27,$obra->fechaInicio));
            
            $resumenCostos = [
                [
                    "descripcion"=>"Costo Directo",
                    "presupuesto"=>0,
                    "remanente"=>0
                ],
                [
                    "descripcion"=>"Costo Indirecto de Obra",
                    "presupuesto"=>0,
                    "remanente"=>0
                ],
                [
                    "descripcion"=>"Costo Total de Obra Directo + Indirecto",
                    "presupuesto"=>0,
                    "remanente"=>0
                ]
            ];

            $arrayMeses = array();
            for ($i=0; $i < $obra->periodos+$obra->semanaExtra; $i++) { 
                $mes = fNombreMes($this->obtenerMes($i+1,$obra->fechaInicio));
                if (!isset($resumenCostos[0][$mes])) $resumenCostos[0][$mes] = 0;
                if (!isset($resumenCostos[1][$mes])) $resumenCostos[1][$mes] = 0;
                if (!isset($resumenCostos[2][$mes])) $resumenCostos[2][$mes] = 0;
                if (!in_array($mes, $arrayMeses)) $arrayMeses[] = $mes;
            }
            
            
            $partida = New Partida;
            $partidas = $partida->consultarDetalles($id);

            foreach ($partidas as $key => $value) {
            
                $mes = fNombreMes($this->obtenerMes($value["periodo"],$obra->fechaInicio));
                if ($value["tipo"] == "directo") {
                    $resumenCostos[0][$mes] += $value["costo"];
                    $resumenCostos[0]["remanente"] += $value["costo"];
                }else{
                    $resumenCostos[1][$mes] += $value["costo"];
                    $resumenCostos[1]["remanente"] += $value["costo"];
                }
                $resumenCostos[2][$mes] += $value["costo"];
                $resumenCostos[2]["remanente"] += $value["costo"];

            }

            //Se obtienen las requisiicones asociadas a la obra
            $obraDetalle = New ObraDetalles;
            $obraDetalle->obraId = $id;

            $directos = $obraDetalle->consultarDirectos($id);
            foreach ($directos as $value) {
                $resumenCostos[0]["presupuesto"] += $value["presupuesto"]; 
                $resumenCostos[2]["presupuesto"] += $value["presupuesto"]; 
            }
            $resumenCostos[0]["remanente"] = $resumenCostos[0]["presupuesto"] - $resumenCostos[0]["remanente"]; 

            $indirectos = $obraDetalle->consultarIndirectos($id);
            foreach ($indirectos as $value) {
                $resumenCostos[1]["presupuesto"] += $value["presupuesto"]; 
                $resumenCostos[2]["presupuesto"] += $value["presupuesto"]; 
            }
            $resumenCostos[1]["remanente"] = $resumenCostos[1]["presupuesto"] - $resumenCostos[1]["remanente"]; 

            $resumenCostos[2]["remanente"] = $resumenCostos[2]["presupuesto"] - $resumenCostos[2]["remanente"];

            
            // echo '<pre>';
            // print_r($resumenCostos);
            // echo '</pre>';
            include "reportes/proforma.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    
    }

    private function obtenerMes($numeroSemana, $fechaInicial) {
        $timestampInicial = strtotime($fechaInicial);

        // Calculamos el timestamp del jueves de la semana inicial
        $juevesInicial = strtotime('next thursday', $timestampInicial);

        // Calculamos el timestamp del jueves de la semana deseada
        $timestampSemanaDeseada = $juevesInicial + ($numeroSemana - 1) * 7 * 24 * 60 * 60;

        // Obtenemos el mes correspondiente al timestamp
        $mes = date('m', $timestampSemanaDeseada);

        return $mes;
    }
}
