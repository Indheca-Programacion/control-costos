<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Nominas.php";
require_once "../Models/Insumo.php";
require_once "../Models/InsumoTipo.php";
require_once "../Models/Indirecto.php";
require_once "../Models/IndirectoTipo.php";
require_once "../Models/Obra.php";
require_once "../Models/Perfil.php";
require_once "../Models/Usuario.php";
require_once "../Models/Unidad.php";
require_once "../Models/Divisa.php";
require_once "../Models/Requisicion.php";
require_once "../Models/OrdenCompra.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Nominas;
use App\Models\Insumo;
use App\Models\InsumoTipo;
use App\Models\Indirecto;
use App\Models\IndirectoTipo;
use App\Models\Obra;
use App\Models\Perfil;
use App\Models\Requisicion;
use App\Models\OrdenCompra;
use App\Models\Usuario;
use App\Models\Unidad;
use App\Models\Divisa;
use App\Controllers\Autorizacion;
// use App\Controllers\Validacion;

class CostosResumenAjax
{
	/*=============================================
	CONSULTA RESUMEN DE COSTOS
	=============================================*/
    public $obraId;

	public function mostrarResumen()
	{
        $obra = New Obra;
        // $obras = $obra->consultar();
        $obra->consultar(null , $this->obraId);
        $presupuestos = $obra->consultarLotes();
        
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        $usuario->consultarPerfiles();
        $usuario->consultarPermisos();

        $usuarioRoal = false;
        if ( $usuario->empresaId == 4) {
            $usuarioRoal = true;
        }

        $token = token();

        //Se obtienen las partidas de ordenes de compra asociadas a la obra

        $directos = array();
        $directo = New Insumo;
        // $this->anio = "2025"; //Quitar
        $directos = $directo->consultarPorObraFiltros($this->obraId, $this->divisaId, $this->presupuesto, $this->anio);
        
        $indirecto = New Indirecto;
        $indirectos = $indirecto->consultarPorObraFiltros($this->obraId, $this->divisaId, $this->presupuesto, $this->anio);

        $registrosIndirectos = array();
        $registrosInsumos = array();

        //Crea valores del resumn de costos
        $registros = [
            [
                "descripcion" => "Costo Directo",
                "presupuesto" => 0,
                "total" => 0,
            ],
            [
                "descripcion" => "Costo Indirecto de Obra",
                "presupuesto" => 0,
                "total" => 0,
            ],
            [
                "descripcion" => "Costo Total de Obra Directo + Indirecto",
                "presupuesto" => 0,
                "total" => 0,
            ]
        ];
        
        //Genera las columnas de la tabla resumen de costos
        $columnas = array();
        array_push($columnas, [ "data" => "descripcion" ]);
        if ( $this->anio !== "all" ) {
            array_push($columnas, [ "data" => "enero", "title" => "Enero", "orderable" => false ]);
            array_push($columnas, [ "data" => "febrero", "title" => "Febrero", "orderable" => false ]);
            array_push($columnas, [ "data" => "marzo", "title" => "Marzo", "orderable" => false ]);
            array_push($columnas, [ "data" => "abril", "title" => "Abril", "orderable" => false ]);
            array_push($columnas, [ "data" => "mayo", "title" => "Mayo", "orderable" => false ]);
            array_push($columnas, [ "data" => "junio", "title" => "Junio", "orderable" => false ]);
            array_push($columnas, [ "data" => "julio", "title" => "Julio", "orderable" => false ]);
            array_push($columnas, [ "data" => "agosto", "title" => "Agosto", "orderable" => false ]);
            array_push($columnas, [ "data" => "septiembre", "title" => "Septiembre", "orderable" => false ]);
            array_push($columnas, [ "data" => "octubre", "title" => "Octubre", "orderable" => false ]);
            array_push($columnas, [ "data" => "noviembre", "title" => "Noviembre", "orderable" => false ]);
            array_push($columnas, [ "data" => "diciembre", "title" => "Diciembre", "orderable" => false ]);

            $registros[0]["enero"] = 0;
            $registros[0]["febrero"] = 0;
            $registros[0]["marzo"] = 0;
            $registros[0]["abril"] = 0;
            $registros[0]["mayo"] = 0;
            $registros[0]["junio"] = 0;
            $registros[0]["julio"] = 0;
            $registros[0]["agosto"] = 0;
            $registros[0]["septiembre"] = 0;
            $registros[0]["octubre"] = 0;
            $registros[0]["noviembre"] = 0;
            $registros[0]["diciembre"] = 0;
            $registros[1]["enero"] = 0;
            $registros[1]["febrero"] = 0;
            $registros[1]["marzo"] = 0;
            $registros[1]["abril"] = 0;
            $registros[1]["mayo"] = 0;
            $registros[1]["junio"] = 0;
            $registros[1]["julio"] = 0;
            $registros[1]["agosto"] = 0;
            $registros[1]["septiembre"] = 0;
            $registros[1]["octubre"] = 0;
            $registros[1]["noviembre"] = 0;
            $registros[1]["diciembre"] = 0;
            $registros[2]["enero"] = 0;
            $registros[2]["febrero"] = 0;
            $registros[2]["marzo"] = 0;
            $registros[2]["abril"] = 0;
            $registros[2]["mayo"] = 0;
            $registros[2]["junio"] = 0;
            $registros[2]["julio"] = 0;
            $registros[2]["agosto"] = 0;
            $registros[2]["septiembre"] = 0;
            $registros[2]["octubre"] = 0;
            $registros[2]["noviembre"] = 0;
            $registros[2]["diciembre"] = 0;
        } else {
            $añoActual = date('Y');
            for ($anio = 2024; $anio <= $añoActual; $anio++) {
                array_push($columnas, [ "data" => $anio , "title" => $anio, "orderable" => false ]);

                $registros[0][$anio] = 0;
                $registros[1][$anio] = 0;
                $registros[2][$anio] = 0;
            }

        }
        array_push($columnas, [ "data" => "presupuesto", "title" => "Presupuesto", "orderable" => false ]);
        array_push($columnas, [ "data" => "total", "title" => "Total", "orderable" => false ]);

        /*==============================================
        DIRECTOS
        ==============================================*/

        //Genera los valores del presupuesto vacios
        foreach ($directos as $key => $value) {
            $rutaEdit = Route::names('obras-detalles.edit', $value['id']);
            $rutaDestroy = Route::names('obras-detalles.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['descripcion']));
            $acciones = "";
            if (Autorizacion::permiso($usuario, "requisiciones", "crear")) {
                $acciones = "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                    <button type='button' class='btn btn-xs btn-primary agregar' tipo='Insumo' codigo='{$value["codigo"]}' data-toggle='modal' data-target='#modalAgregarPartida' >
                                        <i class='fas fa-file-alt'></i>
                                    </button>
                                    </button>
                                    <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                        <input type='hidden' name='_method' value='DELETE'>
                                        <input type='hidden' name='_token' value='{$token}'>
                                        <button type='button' class='btn btn-xs btn-danger eliminar' tipo='Directo' folio='{$folio}'>
                                            <i class='far fa-times-circle'></i>
                                        </button>
                                    </form>
                                    ";
            }

            $detalleObra = [
                "acciones" => $acciones,
                "cantidad" => $value["cantidad"],
                "descripcion" => mb_strtoupper($value["descripcion"]),
                "codigo" => $value["codigo"],
                "tipoInsumo" => mb_strtoupper($value["tipo"]),
                "obraDetalleId" => $value["id"],
                "presupuesto" => formatMoney($value["presupuesto"]),
                "remanente" => formatMoney($value["presupuesto"]- $value["costo_total"] ?? 0),
                "remanente_cantidad" => floatval($value["cantidad"]) - floatval($value["cantidad_total"]),
                "unidad" => mb_strtoupper($value["unidad"]),
                "unidadId" => $value["unidadId"],
                "requisiciones" => [],
                "arrayRequis" => [],
                "arrayNominas" => [],
            ];

            if ( $this->anio !== "all" ) {
                $detalleObra["enero"] = formatMoney($value["costo_enero"]);
                $detalleObra["febrero"] = formatMoney($value["costo_febrero"]);
                $detalleObra["marzo"] = formatMoney($value["costo_marzo"]);
                $detalleObra["abril"] = formatMoney($value["costo_abril"]);
                $detalleObra["mayo"] = formatMoney($value["costo_mayo"]);
                $detalleObra["junio"] = formatMoney($value["costo_junio"]);
                $detalleObra["julio"] = formatMoney($value["costo_julio"]);
                $detalleObra["agosto"] = formatMoney($value["costo_agosto"]);
                $detalleObra["septiembre"] = formatMoney($value["costo_septiembre"]);
                $detalleObra["octubre"] = formatMoney($value["costo_octubre"]);
                $detalleObra["noviembre"] = formatMoney($value["costo_noviembre"]);
                $detalleObra["diciembre"] = formatMoney($value["costo_diciembre"]);

                $registros[0]["enero"] += $value["costo_enero"];
                $registros[0]["febrero"] += $value["costo_febrero"];
                $registros[0]["marzo"] += $value["costo_marzo"];
                $registros[0]["abril"] += $value["costo_abril"];
                $registros[0]["mayo"] += $value["costo_mayo"];
                $registros[0]["junio"] += $value["costo_junio"];
                $registros[0]["julio"] += $value["costo_julio"];
                $registros[0]["agosto"] += $value["costo_agosto"];
                $registros[0]["septiembre"] += $value["costo_septiembre"];
                $registros[0]["octubre"] += $value["costo_octubre"];
                $registros[0]["noviembre"] += $value["costo_noviembre"];
                $registros[0]["diciembre"] += $value["costo_diciembre"];

                $registros[2]["enero"] += $value["costo_enero"];
                $registros[2]["febrero"] += $value["costo_febrero"];
                $registros[2]["marzo"] += $value["costo_marzo"];
                $registros[2]["abril"] += $value["costo_abril"];
                $registros[2]["mayo"] += $value["costo_mayo"];
                $registros[2]["junio"] += $value["costo_junio"];
                $registros[2]["julio"] += $value["costo_julio"];
                $registros[2]["agosto"] += $value["costo_agosto"];
                $registros[2]["septiembre"] += $value["costo_septiembre"];
                $registros[2]["octubre"] += $value["costo_octubre"];
                $registros[2]["noviembre"] += $value["costo_noviembre"];
                $registros[2]["diciembre"] += $value["costo_diciembre"];

                $registros[0]["total"] += $value["costo_total"];
                $registros[2]["total"] += $value["costo_total"];

                $registros[0]["presupuesto"] += $value["presupuesto"];
                $registros[2]["presupuesto"] += $value["presupuesto"];
            } else {
                $añoActual = date('Y');
                for ($anio = 2024; $anio <= $añoActual; $anio++) {

                    $detalleObra[$anio] = formatMoney($value["costo_".$anio] ?? 0);

                    $registros[0]["total"] += $value["costo_".$anio] ?? 0;
                    $registros[2]["total"] += $value["costo_".$anio] ?? 0;

                    $registros[0]["presupuesto"] += $value["presupuesto"] ?? 0;
                    $registros[2]["presupuesto"] += $value["presupuesto"] ?? 0;

                    $registros[0][$anio] += $value["costo_".$anio] ?? 0;
                    $registros[2][$anio] += $value["costo_".$anio] ?? 0;

                }
            }

            $registrosInsumos[] = $detalleObra;

        }

        $columnasInsumos = array();
        // array_push($columnasInsumos, [ "data" => "consecutivo" ]);
        array_push($columnasInsumos, [ "data" => "tipoInsumo" ]);
        array_push($columnasInsumos, [ "data" => "codigo" ]);
        array_push($columnasInsumos, [ "data" => "descripcion" ]);
        array_push($columnasInsumos, [ "data" => "unidad" ]);
        array_push($columnasInsumos, [ "data" => "cantidad" ]);
        array_push($columnasInsumos, [ "data" => "presupuesto" ]);
        if ( $this->anio !== "all") {
            array_push($columnasInsumos, [ "data" => "enero", "title" => "Enero", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "febrero", "title" => "Febrero", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "marzo", "title" => "Marzo", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "abril", "title" => "Abril", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "mayo", "title" => "Mayo", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "junio", "title" => "Junio", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "julio", "title" => "Julio", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "agosto", "title" => "Agosto", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "septiembre", "title" => "Septiembre", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "octubre", "title" => "Octubre", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "noviembre", "title" => "Noviembre", "orderable" => false ]);
            array_push($columnasInsumos, [ "data" => "diciembre", "title" => "Diciembre", "orderable" => false ]);
        } else {
            $añoActual = date('Y');
            for ($anio = 2024; $anio <= $añoActual; $anio++) {

                array_push($columnasInsumos, [ "data" => $anio , "title" => $anio, "orderable" => false ]);
            }
        }
        array_push($columnasInsumos, [ "data" => "remanente", "title" => "Remanente", "orderable" => false ]);
        array_push($columnasInsumos, [ "data" => "remanente_cantidad", "title" => "Remanente Cantidad", "orderable" => false ]);

        /*==============================================
        INDIRECTOS
        ==============================================*/

        //Genera los valores del presupuesto vacios
        foreach ($indirectos as $key => $value) {
            $rutaEdit = Route::names('obras-detalles.edit', $value['id']);
            $rutaDestroy = Route::names('obras-detalles.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['descripcion']));
            $acciones = "";
            if (Autorizacion::permiso($usuario, "requisiciones", "crear")) {
                $acciones = "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                    <button type='button' class='btn btn-xs btn-primary agregar' tipo='Indirecto' numero='{$value["numero"]}' data-toggle='modal' data-target='#modalAgregarPartida' >
                                        <i class='fas fa-file-alt'></i>
                                    </button>
                                    </button>
                                    <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                        <input type='hidden' name='_method' value='DELETE'>
                                        <input type='hidden' name='_token' value='{$token}'>
                                        <button type='button' class='btn btn-xs btn-danger eliminar' tipo='Directo' folio='{$folio}'>
                                            <i class='far fa-times-circle'></i>
                                        </button>
                                    </form>
                                    ";
            }

            $detalleObra = [
                "acciones" => $acciones,
                "cantidad" => $value["cantidad"],
                "descripcion" => mb_strtoupper($value["descripcion"]),
                "numero" => $value["numero"],
                "tipoIndirecto" => mb_strtoupper($value["tipo"]),
                "obraDetalleId" => $value["id"],
                "presupuesto" => formatMoney($value["presupuesto"]),
                "remanente" => formatMoney($value["presupuesto"]- $value["costo_total"] ?? 0),
                "remanente_cantidad" => floatval($value["cantidad"]) - floatval($value["cantidad_total"]),
                "unidad" => mb_strtoupper($value["unidad"]),
                "unidadId" => $value["unidadId"],
                "requisiciones" => [],
                "arrayRequis" => [],
                "arrayNominas" => [],
            ];

            if ( $this->anio !== "all" ) {
                $detalleObra["enero"] = $value["costo_enero"];
                $detalleObra["febrero"] = $value["costo_febrero"];
                $detalleObra["marzo"] = $value["costo_marzo"];
                $detalleObra["abril"] = $value["costo_abril"];
                $detalleObra["mayo"] = $value["costo_mayo"];
                $detalleObra["junio"] = $value["costo_junio"];
                $detalleObra["julio"] = $value["costo_julio"];
                $detalleObra["agosto"] = $value["costo_agosto"];
                $detalleObra["septiembre"] = $value["costo_septiembre"];
                $detalleObra["octubre"] = $value["costo_octubre"];
                $detalleObra["noviembre"] = $value["costo_noviembre"];
                $detalleObra["diciembre"] = $value["costo_diciembre"];

                $registros[1]["enero"] += $value["costo_enero"];
                $registros[1]["febrero"] += $value["costo_febrero"];
                $registros[1]["marzo"] += $value["costo_marzo"];
                $registros[1]["abril"] += $value["costo_abril"];
                $registros[1]["mayo"] += $value["costo_mayo"];
                $registros[1]["junio"] += $value["costo_junio"];
                $registros[1]["julio"] += $value["costo_julio"];
                $registros[1]["agosto"] += $value["costo_agosto"];
                $registros[1]["septiembre"] += $value["costo_septiembre"];
                $registros[1]["octubre"] += $value["costo_octubre"];
                $registros[1]["noviembre"] += $value["costo_noviembre"];
                $registros[1]["diciembre"] += $value["costo_diciembre"];

                $registros[2]["enero"] += $value["costo_enero"];
                $registros[2]["febrero"] += $value["costo_febrero"];
                $registros[2]["marzo"] += $value["costo_marzo"];
                $registros[2]["abril"] += $value["costo_abril"];
                $registros[2]["mayo"] += $value["costo_mayo"];
                $registros[2]["junio"] += $value["costo_junio"];
                $registros[2]["julio"] += $value["costo_julio"];
                $registros[2]["agosto"] += $value["costo_agosto"];
                $registros[2]["septiembre"] += $value["costo_septiembre"];
                $registros[2]["octubre"] += $value["costo_octubre"];
                $registros[2]["noviembre"] += $value["costo_noviembre"];
                $registros[2]["diciembre"] += $value["costo_diciembre"];

                $registros[1]["total"] += $value["costo_total"];
                $registros[2]["total"] += $value["costo_total"];

                $registros[1]["presupuesto"] += $value["presupuesto"];
                $registros[2]["presupuesto"] += $value["presupuesto"];
            } else {
                $añoActual = date('Y');
                for ($anio = 2024; $anio <= $añoActual; $anio++) {

                    $detalleObra[$anio] = formatMoney($value["costo_".$anio] ?? 0);

                    $registros[1]["total"] += $value["costo_total"] ?? 0;
                    $registros[2]["total"] += $value["costo_total"] ?? 0;

                    $registros[1]["presupuesto"] += $value["presupuesto"] ?? 0;
                    $registros[2]["presupuesto"] += $value["presupuesto"] ?? 0;

                    $registros[0][$anio] += $value["costo_".$anio] ?? 0;
                    $registros[2][$anio] += $value["costo_".$anio] ?? 0;

                }
            }

            $registrosIndirectos[] = $detalleObra;

        }


        $columnasIndirectos = array();
        // array_push($columnasIndirectos, [ "data" => "consecutivo" ]);
        array_push($columnasIndirectos, [ "data" => "tipoIndirecto" ]);
        array_push($columnasIndirectos, [ "data" => "numero" ]);
        array_push($columnasIndirectos, [ "data" => "descripcion" ]);
        array_push($columnasIndirectos, [ "data" => "unidad" ]);
        array_push($columnasIndirectos, [ "data" => "cantidad"]);
        array_push($columnasIndirectos, [ "data" => "presupuesto"]);
        if ( $this->anio !== "all") {
            array_push($columnasIndirectos, [ "data" => "enero", "title" => "Enero", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "febrero", "title" => "Febrero", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "marzo", "title" => "Marzo", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "abril", "title" => "Abril", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "mayo", "title" => "Mayo", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "junio", "title" => "Junio", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "julio", "title" => "Julio", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "agosto", "title" => "Agosto", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "septiembre", "title" => "Septiembre", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "octubre", "title" => "Octubre", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "noviembre", "title" => "Noviembre", "orderable" => false ]);
            array_push($columnasIndirectos, [ "data" => "diciembre", "title" => "Diciembre", "orderable" => false ]);
        } else {
            $añoActual = date('Y');
            for ($anio = 2024; $anio <= $añoActual; $anio++) {

                array_push($columnasIndirectos, [ "data" => $anio , "title" => $anio, "orderable" => false ]);
            }
        }
        array_push($columnasIndirectos, [ "data" => "remanente", "title" => "Remanente", "orderable" => false ]);
        array_push($columnasIndirectos, [ "data" => "remanente_cantidad", "title" => "Remanente Cantidad", "orderable" => false ]);
        
        // Generar datos para actualizar los catálogos en #modalCrearInsumo y #modalCrearIndirecto
        //Se buscan los tipos de directo
        $insumoTipo = New InsumoTipo;
        $insumoTipos = $insumoTipo->consultar();

        $registrosInsumoTipos = array();
        foreach ($insumoTipos as $key => $value) {
            $registro = [
                "id" => $value["id"],
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"])),
                "orden" => $value["orden"]
            ];

            array_push($registrosInsumoTipos, $registro);
        }
        //Se buscan los tipos de indirecto
        $indirectoTipo = New IndirectoTipo;
        $indirectoTipos = $indirectoTipo->consultar();

        $registrosIndirectoTipos = array();
        foreach ($indirectoTipos as $key => $value) {
            $registro = [
                "id" => $value["id"],
                "numero" => fString($value["numero"]),
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"]))
            ];

            array_push($registrosIndirectoTipos, $registro);
        }
        //Se buscan las unidades
        $unidad = New Unidad;
        $unidades = $unidad->consultar();

        $registrosUnidades = array();
        foreach ($unidades as $key => $value) {
            $registro = [
                "id" => $value["id"],
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"]))
            ];

            array_push($registrosUnidades, $registro);
        }

        $admin = false;
        if ($usuario->checkAdmin()) {
            $admin = true;
        }
        $importPlantilla = false;
        if($usuario->checkPermiso("plantillas") || $usuario->checkAdmin()) {
            $importPlantilla = true;
        }

        $anuncios = $obra->consultarAnuncios();
        $registrosAnuncios = array();
        foreach ($anuncios as $key => $value) {
            $registro = [
                "mensaje" => mb_strtoupper(fString($value["mensaje"])),
                "fechaHora" => fFechaLargaHora($value["fechaHora"]),
                "usuarioNombre" => mb_strtoupper(fString($value["usuarioNombre"]))
            ];

            array_push($registrosAnuncios, $registro);
        }

        $respuesta = [
            'urlReporte' => Route::names('costos-resumen.print', $this->obraId),
            'codigo' => ( count($registros) > 0 ) ? 200 : 204,
            'error' => false,
            'cantidad' => count($registros),
            'datos' => [
                'columnas' => $columnas,
                'registros' => $registros,
            ],
            'insumos' => [
                'columnas' => $columnasInsumos,
                'registros' => $registrosInsumos,
            ],
            'indirectos' => [
                'columnas' => $columnasIndirectos,
                'registros' => $registrosIndirectos,
            ],
            'obra' => $obra,
            'catalogos' => [
                'insumoTipos' => $registrosInsumoTipos,
                'indirectoTipos' => $registrosIndirectoTipos,
                'unidades' => $registrosUnidades,
                'presupuestos' => $presupuestos
            ],
            'admin' => $admin,
            'impPlantilla' => $importPlantilla,
            'anuncios' => $registrosAnuncios,
            'usuarioRoal' => $usuarioRoal
        ];

        echo json_encode($respuesta);
	}

    public function mostrarRequisiciones()
    {
        $requisicion = New Requisicion;
        $insumo = New Insumo;
        $insumo->consultar("descripcion", $this->insumo);
        $requisiciones = $requisicion->consultarPorInsumo($insumo->id, $this->obraId, $this->anio, $this->fecha);

        $registros = array();

        foreach ($requisiciones as $key => $value) {
            $rutaEdit = Route::names('requisiciones.edit', $value['id']);
            $folio = $value['folio'];

            $requisicionRow = [
                "id" => $value["id"],
                "justificacion" => $value["justificacion"]??"",
                "fechaCreacion" => fString($value["fechaCreacion"]),
                "total" => ($value["costo"]),
                "verRequi" => "
                <a target='_blank' href='{$rutaEdit}' class=''>
                Requisicion Folio: {$folio} 
                 </a>"
            ];

            $registros[] = $requisicionRow;

        }

        $respuesta = [
            'codigo' => 200,
            'error' => false,
            'requisiciones' => $registros
        ];
        // Finalmente retornas el JSON con las columnas y datos
        echo json_encode($respuesta);
        exit;
    }
}

$costosResumenAjax = New CostosResumenAjax();

if ( isset($_GET["obraId"]) ) {

    /*=============================================
    CONSULTA RESUMEN DE COSTOS
    =============================================*/
    $costosResumenAjax->obraId = $_GET["obraId"];
    $costosResumenAjax->divisaId = $_GET["divisaId"];
    $costosResumenAjax->presupuesto = isset($_GET["presupuesto"]) ? $_GET["presupuesto"] : 0;
    $costosResumenAjax->anio = isset($_GET["anio"]) ? $_GET["anio"] : null;
    $costosResumenAjax->mes = isset($_GET["mes"]) ? $_GET["mes"] : null;
    $costosResumenAjax->mostrarResumen();

}else if(isset($_GET["insumo"])){

    $costosResumenAjax->insumo = $_GET["insumo"];
    $costosResumenAjax->anio = isset($_GET["anio"]) ? $_GET["anio"] : null;
    $costosResumenAjax->fecha = isset($_GET["fecha"]) ? fNumeroMes($_GET["fecha"]) : null;
    $costosResumenAjax->obraId = isset($_GET["obra"]) ? $_GET["obra"] : null;
    $costosResumenAjax->mostrarRequisiciones();

}
