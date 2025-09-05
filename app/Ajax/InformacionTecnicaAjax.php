<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/InformacionTecnica.php";
require_once "../Models/InformacionTecnicaTag.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\InformacionTecnica;
use App\Models\InformacionTecnicaTag;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class InformacionTecnicaAjax
{

	/*=============================================
	TABLA DE INFORMACION TECNICA
	=============================================*/
	public function mostrarTabla()
	{
        $usuario = new Usuario;
        $usuario->consultar(null,usuarioAutenticado()["id"]);
        $usuario->consultarPerfiles();

		$informacionTecnica = New InformacionTecnica;
        $informaciones = $informacionTecnica->consultar();

        // Consultar los Tags
        $informacionTecnicaTag = New InformacionTecnicaTag;
        $informacionTecnicaTags = $informacionTecnicaTag->consultar();
        $arrayInformacionTecnicaTags = array();
        foreach ($informacionTecnicaTags as $valor) {
            $arrayInformacionTecnicaTags[$valor['id']] = mb_strtoupper(fString($valor['descripcion']));
        }

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "titulo" ]);
        array_push($columnas, [ "data" => "archivo" ]);
        array_push($columnas, [ "data" => "formato" ]);
        array_push($columnas, [ "data" => "tags" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($informaciones as $key => $value) {
            $tags = "";
            if ( !is_null($value["tags"]) ) {
                $arrayTags = json_decode($value["tags"], true);
                foreach ($arrayTags as $key2 => $value2) {
                    if ( $key2 > 0 ) $tags .= " ";
                    $tags .= "<span class='badge badge-primary' style='font-size: 100%; margin-bottom: 1px;'>{$arrayInformacionTecnicaTags[$value2]}</span>";
                }
            }
            $creo = $value['usuarios.nombre'] . ' ' . $value['usuarios.apellidoPaterno'];
            if ( !is_null($value['usuarios.apellidoMaterno']) ) $creo .= ' ' . $value['usuarios.apellidoMaterno'];

        	$rutaDownload = Route::routes('informacion-tecnica.download', $value['id']);
        	$rutaEdit = Route::names('informacion-tecnica.edit', $value['id']);
        	$rutaDestroy = Route::names('informacion-tecnica.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['titulo']));

            $perfiles = json_decode($value["perfiles"]);

            $intersect = array_intersect($perfiles, $usuario->perfilesId);
            if(count($intersect)>0 || $usuario->checkAdmin()){

                array_push( $registros, [ "consecutivo" => ($key + 1),
                                          "titulo" => mb_strtoupper(fString($value["titulo"])),
                                          "archivo" => mb_strtoupper(fString($value["archivo"])),
                                          "formato" => mb_strtoupper(fString($value["formato"])),
                                          "tags" => $tags,
                                          "creo" => mb_strtoupper(fString($creo)),
                                          "acciones" => "<div class='d-inline-flex'><a href='{$rutaDownload}' class='btn btn-xs btn-info'><i class='fas fa-download'></i></a>
                                                          <span style='padding: 0 0.1875rem;'><a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a></span>
                                                         <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                                              <input type='hidden' name='_method' value='DELETE'>
                                                              <input type='hidden' name='_token' value='{$token}'>
                                                              <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                                                 <i class='far fa-times-circle'></i>
                                                              </button>
                                                         </form></div>" ] );

            }

        
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

}

/*=============================================
TABLA DE INFORMACION TECNICA
=============================================*/
$informacionTecnicaAjax = new InformacionTecnicaAjax();
$informacionTecnicaAjax->mostrarTabla();