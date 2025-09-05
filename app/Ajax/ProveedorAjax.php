<?php

namespace App\Ajax;

session_start();

// Configuración de Errores
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/control-costos/php_error_log');

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/DatosBancarios.php";
require_once "../Models/Proveedor.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveDatosBancariosRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Proveedor;
use App\Models\DatosBancarios;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveDatosBancariosRequest;

class ProveedorAjax
{

    /*=============================================
    TABLA DE PROVEEDORES
    =============================================*/
    public function mostrarTabla()
    {
        $proveedor = New Proveedor;
        $proveedores = $proveedor->consultar();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "activo" ]);
        array_push($columnas, [ "data" => "personaFisica" ]);
        array_push($columnas, [ "data" => "proveedor" ]);
        array_push($columnas, [ "data" => "nombreComercial" ]);
        array_push($columnas, [ "data" => "rfc" ]);
        array_push($columnas, [ "data" => "correo" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($proveedores as $key => $value) {
            $rutaEdit = Route::names('proveedores.edit', $value['id']);
            $rutaDestroy = Route::names('proveedores.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['proveedor']));

            array_push( $registros, [ 
                "id" => $value["id"],
                "consecutivo" => ($key + 1),
                "activo" => ( $value["activo"] ) ? 'Si' : 'No',
                "personaFisica" => ( $value["personaFisica"] ) ? 'Si' : 'No',
                "proveedor" => mb_strtoupper(fString($value["proveedor"])),
                "nombreComercial" => mb_strtoupper(fString($value["nombreComercial"])),
                "rfc" => mb_strtoupper(fString($value["rfc"])),
                "estrellas" => $value["estrellas"],
                "telefono" => fString($value["telefono"]),

                "correo" => mb_strtolower(fString($value["correo"])),
                "acciones" =>  "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>" ] );
        }

        $respuesta =     array();   
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }
    
    public function mostrarActivos()
    {
        $proveedor = New Proveedor;
        $proveedores = $proveedor->consultarActivos();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "proveedor" ]);
        array_push($columnas, [ "data" => "telefono" ]);
        array_push($columnas, [ "data" => "direccion" ]);
        array_push($columnas, [ "data" => "email" ]);
        array_push($columnas, [ "data" => "estrellas" ]);

        $registros = array();
        foreach ($proveedores as $key => $value) {
            $rutaEdit = Route::names('proveedores.edit', $value['id']);
            $rutaDestroy = Route::names('proveedores.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value['proveedor']));

            array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "id" => $value["id"],
                "proveedor" => mb_strtoupper($value["proveedor"]),
                "telefono" => fString($value["telefono"]),
                "direccion" => "",
                "email" => mb_strtolower(fString($value["correo"])),
                "estrellas" => $value["estrellas"]
                ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    /*=============================================
    TABLA DE DATOS BANCARIOS DE PROVEEDORES
    =============================================*/
    public function mostrarDatosBancarios()
    {
        try{

            $datoBancario = new DatosBancarios;
            $datosBancarios = $datoBancario->consultarDatosBancariosProveedor($this->proveedorId);

			$columnaDatosBancarios = [
				["data" => "consecutivo", "title" => "#"],
                ["data" => "nombreTitular", "title" => "Nombre Titular"],
				["data" => "nombreBanco", "title" => "Nombre Banco"],
				["data" => "cuenta", "title" => "Cuenta"],
                ["data" => "cuentaClave", "title" => "Cuenta Clabe"],
                ["data" => "divisa", "title" => "Moneda"],
				["data" => "acciones", "title" => "Acciones", "orderable" => false, "searchable" => false]
			];

			$registroDatosBancarios = []; 

			foreach ($datosBancarios as $key => $value) {

				$rutaDestroy = Route::names('inventarios.edit', $value['id']);

                if( $value["proveedorId"] != $_GET['proveedorId'] ){
                    continue;
                }

				$registroDatosBancarios[] = [
                    "consecutivo" => ($key + 1),
					"id"     => $value["id"],
					"nombreTitular"     => $value["nombreTitular"],
					"nombreBanco"     => $value["nombreBanco"],
					"cuenta"  => $value["cuenta"],
					"cuentaClave" =>  $value["cuentaClave"],
                    "divisa" => $value["divisa"] ? $value["divisa"] : "N/A",
					"acciones"     => "
                                    <button folio='{$value['id']}' type='button' target='_blank' class='btn btn-xs btn-warning editar'><i class='fas fa-pencil-alt'></i></button>
									<button folio='{$value['id']}' target='_blank' class='btn btn-xs btn-danger eliminar'><i class='fas fa-trash'></i></button>"
				];
			}
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos']['registroDatosBancarios'] = $registroDatosBancarios;
			$respuesta['datos']['columnaDatosBancarios'] = $columnaDatosBancarios;
            $respuesta['mensaje'] = "Mostrando los datos con exito";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    public function obtenerDatoBancarioPorId()
    {
        try{
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $datosBancarios = New DatosBancarios;
            $datoBancario = $datosBancarios->consultar(null,$this->datoBancarioId);
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos'] = $datoBancario;
            $respuesta['mensaje'] = "Dato bancario obtenido correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    public function agregarDatosBancarios()
    {
        try{
            $request = SaveDatosBancariosRequest::validated();

            if( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }

            $datosBancarios = New DatosBancarios;
            $datosBancarios->crear($request);
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Datos bancarios agregados correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
        echo json_encode($respuesta);
    }

    public function editarDatoBancario()
    {
        try{
            $request = SaveDatosBancariosRequest::validated();

            $datosBancarios = New DatosBancarios;
            $datosBancarios->id = $request["datoBancarioId"];
            $datosBancarios->actualizar($request);
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Dato bancario editado correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    public function eliminarDatoBancario()
    {
        try{

            $datosBancarios = New DatosBancarios;
            $datosBancarios->id = $this->datoBancarioId;
            $datosBancarios->eliminar();
            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Dato bancario eliminado correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    public function mostrarListadoDatosBancarios()
    {

        try{

            $datoBancario = new DatosBancarios;
            $datosBancarios = $datoBancario->consultarDatosBancariosProveedor($this->proveedorId);

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos'] = $datosBancarios;
            $respuesta['mensaje'] = "Mostrando los datos con exito";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}


        echo json_encode($respuesta);
    }

    // ACTUALIZAR DATOS DEL PROVEEDOR
    public function actualizarDatosProveedor()
    {
        try{
            if( !usuarioAutenticadoProveedor() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }   

            $proveedor = new Proveedor;
            $respuesta = $proveedor->actualizarDatosIncialesProveedor($this->datos);

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = "Datos actualizados correctamente";

		} catch (Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
        echo json_encode($respuesta);
    }
}

/*=============================================
TABLA DE PROVEEDORES
=============================================*/
$proveedorAjax = new ProveedorAjax;

if (isset($_GET['accion'])) {

    if($_GET['accion'] == 'listar')
    {
    $proveedorAjax->mostrarActivos();
    }
    else if($_GET['accion'] == 'selectDatosBancarios')
    {
    $proveedorAjax->proveedorId = $_GET["proveedorId"];
    $proveedorAjax->mostrarListadoDatosBancarios();
    }
    else if($_GET['accion'] == 'tableDatosBancarios')
    {
    $proveedorAjax->proveedorId = $_GET["proveedorId"];
    $proveedorAjax->mostrarDatosBancarios();
    } else if($_GET['accion'] == 'obtenerDatoBancarioPorId')
    {
        $proveedorAjax->datoBancarioId = $_GET["datoBancarioId"];
        $proveedorAjax->obtenerDatoBancarioPorId();
    }
}
elseif (isset($_POST["accion"])){
    if( $_POST['accion'] == 'agregarDatosBancarios'){
        $proveedorAjax->agregarDatosBancarios();
    }
    if( $_POST['accion'] == 'actualizarDatosProveedor'){

        foreach (json_decode($_POST['datos'], true) as $item) {
            $proveedorAjax->datos[$item['key']] = $item['value'];
        }
        $proveedorAjax->actualizarDatosProveedor();
    }
    if( $_POST['accion'] == 'editarDatoBancario'){
        $proveedorAjax->editarDatoBancario();
    }
    if( $_POST['accion'] == 'eliminarDatoBancario'){
        $proveedorAjax->datoBancarioId = $_POST["datoBancarioId"];
        $proveedorAjax->eliminarDatoBancario();
    }

}
else{

    $proveedorAjax->mostrarTabla();
}
