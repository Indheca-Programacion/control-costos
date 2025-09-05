<?php

namespace App\Ajax;

require_once "../globales.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/control-costos/php_error_log');

class FormularioProveedorAjax
{
    /*=============================================
    AGREGAR ARCHVIVO TEMPORAL
    =============================================*/ 
    public function agregarArchivoTemporal() {
        try {
            $campo = $_POST['campo'] ?? '';
            $nombreTmp = $_FILES['archivo']['tmp_name'] ?? null;
            $nombreOriginal = $_FILES['archivo']['name'] ?? null;

            if (!$campo || !$nombreTmp || !$nombreOriginal) {
                throw new Exception("Datos de archivo incompletos.");
            }

            $nombreArchivoGenerado = "{$campo}_" . time() . "_" . basename($nombreOriginal);
            $rutaRelativa = "tmp/" . $nombreArchivoGenerado;

            // TODO: DEJAR SOLO / EN CONTROL-COSTOS
            $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . CONST_APP_FOLDER . $rutaRelativa;

            if (!move_uploaded_file($nombreTmp, $rutaAbsoluta)) {
                throw new Exception("No se pudo mover el archivo.");
            }

            session_start();

            // Inicializar arreglos si no existen
            if (!isset($_SESSION['archivos_subidos'])) {
                $_SESSION['archivos_subidos'] = [];
            }
            if (!isset($_SESSION['archivos_anteriores'])) {
                $_SESSION['archivos_anteriores'] = [];
            }

            // Si hay un archivo previo en 'archivos_subidos' lo pasamos a 'archivos_anteriores'
            if (isset($_SESSION['archivos_subidos'][$campo])) {
                $anterior = $_SESSION['archivos_subidos'][$campo];
                $_SESSION['archivos_anteriores'][$campo][] = $anterior;
            }

            // Sobrescribir datos con el nuevo archivo
            $_SESSION['archivos_subidos'][$campo] = [
                'nombre_archivo_generado' => $nombreArchivoGenerado,
                'nombre_archivo_original' => $nombreOriginal,
                'ruta' => $rutaAbsoluta,
            ];

            echo json_encode([
                'ok' => true,
                'nombre_archivo_original' => $nombreOriginal,
                'ruta' => $rutaAbsoluta,
            ]);
            exit;

        } catch (Exception $e) {
            echo json_encode([
                'ok' => false,
                'mensaje' => $e->getMessage()
            ]);
            exit;
        }
    }


}

$formularioProveedor = New FormularioProveedorAjax;
$formularioProveedor->agregarArchivoTemporal();
