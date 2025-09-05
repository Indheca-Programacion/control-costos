<?php

function usuarioAutenticado()
{
	if ( isset($_SESSION[CONST_SESSION_APP]["ingreso"]) ) {
		return $_SESSION[CONST_SESSION_APP]["ingreso"];
	} else {
		return null;
	}
}


function usuarioAutenticadoProveedor()
{
	if ( isset($_SESSION[CONST_SESSION_APP]["ingresoProveedor"]) ) {
		return $_SESSION[CONST_SESSION_APP]["ingresoProveedor"];
	} else {
		return null;
	}
}

// OBTENER UBICACION DEL USUARIO
function ubicacionUsuario(){

	if ( file_exists ( "app/Models/Usuario.php" ) ) {
		require_once "app/Models/Usuario.php";
	
	} else {
		require_once "../Models/Usuario.php";
	}

    $usuario = New App\Models\Usuario;
	$ubicacionUsuario = $usuario->consultar(null,usuarioAutenticado()["id"]);
	return  $ubicacionUsuario["ubicacionId"];
}

function aplicacionId()
{
	return isset($_SESSION[CONST_SESSION_APP]["aplicacionId"]) ? $_SESSION[CONST_SESSION_APP]["aplicacionId"] : null;
}

function empresaId()
{
	return isset($_SESSION[CONST_SESSION_APP]["ingreso"]["empresaId"]) ? $_SESSION[CONST_SESSION_APP]["ingreso"]["empresaId"] : null;
}

function sucursalId()
{
	return isset($_SESSION[CONST_SESSION_APP]["ingreso"]["sucursalId"]) ? $_SESSION[CONST_SESSION_APP]["ingreso"]["sucursalId"] : null;
}

function createToken()
{
	$dia = date('d');
	// $hora = date('H');
	// $minuto = (string) (int) ( (int) date('i') / 2 );
	$session_id = session_id();	
	// $token = hash('sha256', $hora.$minuto.$session_id);
	$token = hash('sha256', $dia.$session_id);
	$_SESSION[CONST_SESSION_APP]['token'] = $token;
	return $token;
}

function token()
{
	return isset($_SESSION[CONST_SESSION_APP]["token"]) ? $_SESSION[CONST_SESSION_APP]["token"] : null;
}

function deleteToken()
{	
	unset($_SESSION[CONST_SESSION_APP]["token"]);
}

function createTemporaryToken()
{
    $token = hash('sha256', uniqid(session_id() . microtime(), true));
    $expira = time() + (10 * 60); // 10 minutos desde ahora

    $_SESSION[CONST_SESSION_APP]['temp_token'] = $token;
    $_SESSION[CONST_SESSION_APP]['temp_token_expire'] = $expira;
	$_SESSION[CONST_SESSION_APP]['token'] = $token;

    return $token;
}


function getTemporaryToken()
{
    if (!isset($_SESSION[CONST_SESSION_APP]['temp_token'], $_SESSION[CONST_SESSION_APP]['temp_token_expire'])) {
        return null;
    }

    if ($_SESSION[CONST_SESSION_APP]['temp_token_expire'] < time()) {
        deleteTemporaryToken();
        return null;
    }

    return $_SESSION[CONST_SESSION_APP]['temp_token'];
}


function deleteTemporaryToken()
{
    unset($_SESSION[CONST_SESSION_APP]['temp_token']);
    unset($_SESSION[CONST_SESSION_APP]['temp_token_expire']);
}

function old()
{
	return isset($_SESSION[CONST_SESSION_APP]["old"]) ? $_SESSION[CONST_SESSION_APP]["old"] : null;
}

function flash()
{
	return isset($_SESSION[CONST_SESSION_APP]["flash"]) ? (object) $_SESSION[CONST_SESSION_APP]["flash"] : null;
}

// function flashAlertClass()
// {
// 	return isset($_SESSION[CONST_SESSION_APP]["flashAlertClass"]) ? $_SESSION[CONST_SESSION_APP]["flashAlertClass"] : 'alert-success';
// }

function errors()
{
	if ( isset($_SESSION[CONST_SESSION_APP]["errors"]) ) {
		return $_SESSION[CONST_SESSION_APP]["errors"];
	} else {
		return array();
	}
}

function fNombreDia(int $numeroDia)
{
    switch ($numeroDia) {
    	case 0:
	        return "Domingo";
	        break;
	    case 1:
	        return "Lunes";
	        break;
	    case 2:	    
	        return "Martes";
	        break;
	    case 3:
	        return "Miércoles";
	        break;
	    case 4:
	        return "Jueves";
	        break;
	    case 5:
	        return "Viernes";
	        break;
	    case 6:
	        return "Sábado";
	        break;
        default:
            return null;
    }
}

function fNumeroMes(string $nombreMes)
{
	$mes = mb_strtolower($nombreMes);

    switch ($mes) {
	    case "enero":
	        return "01";
	        break;
	    case "febrero":
	        return "02";
	        break;
	    case "marzo":
	        return "03";
	        break;
	    case "abril":
	        return "04";
	        break;
	    case "mayo":
	        return "05";
	        break;
	    case "junio":
	        return "06";
	        break;
	    case "julio":
	        return "07";
	        break;
	    case "agosto":
	        return "08";
	        break;
	    case "septiembre":
	        return "09";
	        break;
	    case "octubre":
	        return "10";
	        break;
	    case "noviembre":
	        return "11";
	        break;
	    case "diciembre":
	        return "12";
	        break;
        default:
            return null;
    }
}

function fNombreMes(int $numeroMes)
{		
    switch ($numeroMes) {
	    case 1:
	        return "Enero";
	        break;
	    case 2:	    
	        return "Febrero";
	        break;
	    case 3:
	        return "Marzo";
	        break;
	    case 4:
	        return "Abril";
	        break;
	    case 5:
	        return "Mayo";
	        break;
	    case 6:
	        return "Junio";
	        break;
	    case 7:
	        return "Julio";
	        break;
	    case 8:
	        return "Agosto";
	        break;
	    case 9:
	        return "Septiembre";
	        break;
	    case 10:
	        return "Octubre";
	        break;
	    case 11:
	        return "Noviembre";
	        break;
	    case 12:
	        return "Diciembre";
	        break;
        default:
            return null;
    }
}

function fFechaLarga(string $fecha) // El formato se debe recibir confome al tipo de campo datetime de MySql (yyyy-mm-dd hh:mm:ss)
{
	// $objetoDateTime = date_create_from_format('Y-m-d H:i:s', $fecha);
	$objetoDateTime = date_create_from_format('Y-m-d', substr($fecha, 0, 10));

	// $fechaLarga = date_format($objetoDateTime, "d/m/Y");
	$dia = date_format($objetoDateTime, "d");
	$mes = date_format($objetoDateTime, "m");
	$mes = fNombreMes($mes);
	$year = date_format($objetoDateTime, "Y");

	$fechaLarga = $dia . "/" . $mes . "/" . $year;

	return $fechaLarga;
}

function fFechaLargaHora(string $fecha) // El formato se debe recibir conforme al tipo de campo datetime de MySql (yyyy-mm-dd hh:mm:ss)
{
    // Crear objeto DateTime a partir de la fecha
    $objetoDateTime = date_create_from_format('Y-m-d H:i:s', $fecha);

    // Obtener los componentes de la fecha y la hora
    $dia = date_format($objetoDateTime, "d");
    $mes = date_format($objetoDateTime, "m");
    $mes = fNombreMes($mes); // Aquí supongo que esta función devuelve el nombre del mes en formato textual
    $year = date_format($objetoDateTime, "Y");

    // Obtener la hora, minutos y segundos
    $hora = date_format($objetoDateTime, "H");
    $minutos = date_format($objetoDateTime, "i");
    $segundos = date_format($objetoDateTime, "s");

    // Formatear la fecha completa con la hora
    $fechaLargaHora = $dia . "/" . $mes . "/" . $year . " " . $hora . ":" . $minutos . ":" . $segundos;

    return $fechaLargaHora;
}

function fFechaSQL(string $fechaLarga) // El formato se debe recibir confome al formato (dd/nombreMes/yyyy)
{
	$fechaArray = explode('/', $fechaLarga);
	
	$dia = $fechaArray[0];
	$mes = $fechaArray[1];
	$mes = fNumeroMes($mes);
	$year = $fechaArray[2];

	$fechaSQL = $year . "-" . $mes . "-" . $dia;

	return $fechaSQL;
}

function fFechaSQLConHora(string $fechaLargaHora) // El formato se debe recibir conforme al formato (dd/nombreMes/yyyy hh:mm:ss)
{
	$fechaHoraArray = explode(' ', $fechaLargaHora);
	$fechaArray = explode('/', $fechaHoraArray[0]);
	$horaArray = explode(':', $fechaHoraArray[1]);

	$dia = $fechaArray[0];
	$mes = $fechaArray[1];
	$mes = fNumeroMes($mes);
	$year = $fechaArray[2];

	$hora = $horaArray[0];
	$minuto = $horaArray[1];

	$fechaSQLConHora = $year . "-" . $mes . "-" . $dia . " " . $hora . ":" . $minuto . ":00";

	return $fechaSQLConHora;
}

function fNombreCompleto(string $nombres, $apellidoPaterno, $apellidoMaterno = null)
{
	$nombreCompleto =  trim($nombres) . " " . trim($apellidoPaterno) . ( is_null($apellidoMaterno) ? "" : " " . trim($apellidoMaterno) );

	return $nombreCompleto;
}

function in_arrayi($needle, $haystack) { 
    return in_array(mb_strtolower($needle), array_map('mb_strtolower', $haystack)); 
}

function fRandomNameFile(string $directorio, string $extension) {

	$ruta = "";
    $aleatorio = mt_rand(10000000,99999999);
    $ruta = $directorio.$aleatorio.$extension;

    return $ruta;

}

function fRandomNameImageFile(string $directorio, string $tipo) {

	$ruta = "";
    $aleatorio = mt_rand(10000000,99999999);

    if ( $tipo == "image/jpeg" ) {

        $ruta = $directorio.$aleatorio.".jpg";

    } elseif ( $tipo == "image/png" ) {

        $ruta = $directorio.$aleatorio.".png";

    }

    return $ruta;

}

function fDeleteFile(string $rutaImagen) {

	if ( $rutaImagen != "" ) {
		unlink($rutaImagen);
	}

}

function fSaveImageFile(string $tmpName, string $tipo, string $rutaImagen, string $rutaImagenAnterior = "", int $nuevoAncho = 0, int $nuevoAlto = 0) {
	
	if ( $rutaImagenAnterior != "" ) {
        fDeleteFile($rutaImagenAnterior);
    }

    // Subir la imágen al servidor
    list($ancho, $alto) = getimagesize($tmpName);
    if ( $nuevoAncho == 0 ) {
    	$nuevoAncho = $ancho;
    }
    if ( $nuevoAlto == 0 ) {
    	$nuevoAlto = $alto;
    }
        
    // DE ACUERDO AL TIPO DE IMAGEN APLICAMOS LAS FUNCIONES POR DEFECTO DE PHP
    if ($tipo == "image/jpeg") {

        // GUARDAMOS LA IMAGEN EN EL DIRECTORIO
        $origen = imagecreatefromjpeg($tmpName);

        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

        imagejpeg($destino, $rutaImagen);

    } elseif ($tipo == "image/png") {

        // GUARDAMOS LA IMAGEN EN EL DIRECTORIO
        $origen = imagecreatefrompng($tmpName);

        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

        imagepng($destino, $rutaImagen);

    }

}

function fMoveImageFiles(array $rutaTemporal, array $rutaDestino) {

	for ($i=0; $i < count($rutaTemporal); $i++) { 
		rename($rutaTemporal[$i]["foto"], $rutaDestino[$i]["foto"]);
	}

}

function fCreaCamposInsert(array $arrayCampos = []) {

    $campos = "(";
    $contCampos = 0;
    foreach ($arrayCampos as $key => $value) {
        $campos = $campos . $key;
        $contCampos ++;
        if ( $contCampos < count($arrayCampos) ) {
            $campos = $campos . ', ';
        }
    }
    $campos = $campos . ") VALUES (";
    $contCampos = 0;
    foreach ($arrayCampos as $key => $value) {
        $campos = $campos . ':' .$key;
        $contCampos ++;
        if ( $contCampos < count($arrayCampos) ) {
            $campos = $campos . ', ';
        }
    }
    $campos = $campos . ")";

	return $campos;

}

function fCreaCamposUpdate(array $arrayCampos = []) {

	$campos = "";
    $contCampos = 0;
    foreach ($arrayCampos as $key => $value) {
        // $campos = $campos . 'BI.' . $key. ' = :' .$key;
        $campos = $campos . $key. ' = :' .$key;
        $contCampos ++;
        if ( $contCampos < count($arrayCampos) ) {
            $campos = $campos . ', ';
        }
    }

    return $campos;

}

function fString($string) {
	return ( is_null($string) ) ? "" : htmlspecialchars($string);
}

function secondsToTime($inputSeconds)
{
    $secondsInAMinute = 60;
    $secondsInAnHour = 60 * $secondsInAMinute;
    $secondsInADay = 24 * $secondsInAnHour;
    // extract days
    $days = floor($inputSeconds / $secondsInADay);
    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);
    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);
    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);
    // return the final array
    $obj = array( 'd' => (int) $days, 'h' => (int) $hours, 'm' => (int) $minutes, 's' => (int) $seconds, );
    return $obj;
}

function formatearTiempo($inputSeconds)
{
    $valor = secondsToTime($inputSeconds);

    $tiempoFormateado = ( $valor['d'] > 0 ) ? $valor['d']."D " : "";
    $tiempoFormateado .= str_pad($valor['h'], 2, "0", STR_PAD_LEFT).":";
    $tiempoFormateado .= str_pad($valor['m'], 2, "0", STR_PAD_LEFT).":";
    $tiempoFormateado .= str_pad($valor['s'], 2, "0", STR_PAD_LEFT);

    return $tiempoFormateado;
}

function formatearTiempoUnidad($inputSeconds)
{
	$tiempo = $inputSeconds;

    if ( $tiempo < 60 ) $tiempoFormateado = "{$tiempo} segundo";
    else {
        $tiempo = $tiempo / 60;
        $tiempo = floor($tiempo);
        if ( $tiempo < 60 ) $tiempoFormateado = "{$tiempo} minuto";
        else {
            $tiempo = $tiempo / 60;
            $tiempo = floor($tiempo);
            if ( $tiempo < 24 ) $tiempoFormateado = "{$tiempo} hora";
            else {
                $tiempo = $tiempo / 24;
                $tiempo = floor($tiempo);
                $tiempoFormateado = "{$tiempo} día";
            }
        }
    }
    if ( $tiempo > 1 ) $tiempoFormateado = "{$tiempoFormateado}s";

    return $tiempoFormateado;
}

function formatMoney($number) {

    // 1. Eliminar ceros finales y convertir el número a su representación float "limpia"
    // Esto es crucial para que number_format no vea ceros innecesarios al inicio
    $cleanNumber = (float)$number;

    // 2. Determinar cuántos decimales significativos tiene el número
    // Convertimos el número limpio a string para trabajar con sus decimales
    $numberAsString = (string)$cleanNumber;
    $decimalPart = '';
    if (strpos($numberAsString, '.') !== false) {
        $decimalPart = substr($numberAsString, strpos($numberAsString, '.') + 1);
    }
    $actualDecimals = strlen($decimalPart);

    // 3. Usar number_format() con el número de decimales que realmente tiene,
    // o con el máximo permitido si es mayor.
    // Esto evita que se rellene con ceros innecesarios.
    $decimalsToUse = min($actualDecimals, 6);

    // Usar '0' como separador de miles si no quieres ninguno, o ',' si lo necesitas
    $formattedNumber = number_format($cleanNumber, $decimalsToUse, '.', ',');

    // 4. Añadir el símbolo de moneda
    return '$ ' . $formattedNumber;
}

function numeroALetras($numero, $moneda = 'Pesos', $divisa = "MXN") {
	$unidades = [
		'', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'
	];
	$decenas = [
		'', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'
	];
	$centenas = [
		'', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
	];
	$especiales = [
		10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce', 15 => 'quince',
		16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve'
	];

	$enteros = floor($numero);
	$decimales = round(($numero - $enteros) * 100);

	if ($enteros == 0) {
		$resultado = 'cero';
	} else {
		$resultado = convertirNumeroALetras($enteros, $unidades, $decenas, $centenas, $especiales);
	}

	return ucfirst($resultado) . " {$moneda} " . str_pad($decimales, 2, "0", STR_PAD_LEFT) . "/100 {$divisa}";
}

function convertirNumeroALetras($numero, $unidades, $decenas, $centenas, $especiales) {
	if ($numero == 0) {
		return '';
	} elseif ($numero < 10) {
		return $unidades[$numero];
	} elseif ($numero < 20) {
		return $especiales[$numero];
	} elseif ($numero < 100) {
		return $decenas[floor($numero / 10)] . ($numero % 10 > 0 ? ' y ' . convertirNumeroALetras($numero % 10, $unidades, $decenas, $centenas, $especiales) : '');
	} elseif ($numero < 1000) {
		return ($numero == 100 ? 'cien' : $centenas[floor($numero / 100)]) . ' ' . convertirNumeroALetras($numero % 100, $unidades, $decenas, $centenas, $especiales);
	} elseif ($numero < 1000000) {
		return (floor($numero / 1000) == 1 ? 'mil' : convertirNumeroALetras(floor($numero / 1000), $unidades, $decenas, $centenas, $especiales) . ' mil') . ' ' . convertirNumeroALetras($numero % 1000, $unidades, $decenas, $centenas, $especiales);
	} elseif ($numero < 1000000000) {
		return convertirNumeroALetras(floor($numero / 1000000), $unidades, $decenas, $centenas, $especiales) . ' millón' . (floor($numero / 1000000) > 1 ? 'es' : '') . ' ' . convertirNumeroALetras($numero % 1000000, $unidades, $decenas, $centenas, $especiales);
	} else {
		return convertirNumeroALetras(floor($numero / 1000000000), $unidades, $decenas, $centenas, $especiales) . ' mil millones ' . convertirNumeroALetras($numero % 1000000000, $unidades, $decenas, $centenas, $especiales);
	}
}

function calcularNumeroSemana($fechaInicial, $fechaFinal) {
	$fechaInicio = new DateTime($fechaInicial);
	$fechaFin = new DateTime($fechaFinal);

	// Calcular la diferencia en días entre las dos fechas
	$diferenciaDias = $fechaInicio->diff($fechaFin)->days;

	// Calcular el número de semana (dividir días entre 7 y redondear hacia arriba)
	$numeroSemana = ceil(($diferenciaDias + 1) / 7);

	return $numeroSemana;
}
function generarCodigo() {
	return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * FUNCION ENCARGADA DE VERIFICAR SI EL PERMISO
 * EXISTE EN EL ARREGLO
 * 
 * @param array Arreglo de permisos
 * @param string  Nombre del permiso
 *
 * @return boolean
 */

function permisoAsignado($permisos, $nombrePermiso) {
    $permisoEncontrado = array_filter($permisos, function($permiso) use ($nombrePermiso) {
        return $permiso['nombre'] == $nombrePermiso;
    });

    return !empty($permisoEncontrado);
}

/**
 * FUNCION ENCARGADA DE VERIFICAR SI EL TIPO
 * EXISTE EN EL ARREGLO
 * 
 * @param array Arreglo de permisos
 * @param string  Nombre del tipo
 *
 * @return boolean
 */
function tipoAsignado($tipos, $nombreTipo) {

    // Filtra los elementos que coincidan con el nombre del tipo
    $tipoEncontrado = array_filter($tipos, function($tipo) use ($nombreTipo) {
        return isset($tipo['tipo']) && $tipo['tipo'] == $nombreTipo;
    });

    // Devuelve true si se encontraron elementos con ese tipo
    return !empty($tipoEncontrado);
}

/**
 * FUNCION ENCARGADA DE VERIFICAR SI EL TIPO
 * Y EL PERMISO EXISTEN EN EL ARREGLO
 * 
 * @param array Arreglo de permisos
 * @param string  Nombre del tipo
 * @param string  Nombre del permiso
 *
 * @return boolean
 */
function verificarTipoYPermiso($permisos, $nombreTipo, $nombrePermiso) {
    // Filtra por tipo
    $tipoEncontrado = array_filter($permisos, function($permiso) use ($nombreTipo) {
        return isset($permiso['tipo']) && $permiso['tipo'] == $nombreTipo;
    });

    // Si hay datos con ese tipo
    if (!empty($tipoEncontrado)) {
        // Verifica si alguno de los elementos tiene el permiso buscado
        foreach ($tipoEncontrado as $permiso) {
            if (isset($permiso['nombre']) && $permiso['nombre'] == $nombrePermiso) {
                return true;
            }
        }
    }

    return false;
}

/**
 * FUNCION ENCARGADA DE VERIFICAR SI EL NOMBRE
 * DEL GRUPO EXISTE
 * 
 * @param array Arreglo de permisos
 * @param string  Nombre del grupo
 *
 * @return boolean
 */
function grupoAsignado($datos, $nombreGrupo) {
    // Filtra los elementos que pertenezcan al grupo con el ID buscado
    $grupoEncontrado = array_filter($datos, function($item) use ($nombreGrupo) {
        return isset($item['grupo']) && $item['grupo'] == $nombreGrupo;
    });

    // Retorna true si hay al menos un elemento del grupo
    return !empty($grupoEncontrado);
}

/**
 * FUNCION ENCARGADA DE MANDAR CORREOS GLOBALMENTE
 * 
 * @param array Arreglo de usuario de envio
 * @param array  Datos del correo
 *
 * @return boolean
 */
function enviarCorreo($userSendMessageArray,$datosCorreo) {

	// LLAMADA A MODELOS DESDE FUNCIONES PARA ARCHIVOS AJAX
	$configuracionCorreoElectronico = new \App\Models\ConfiguracionCorreoElectronico;

    if ( $configuracionCorreoElectronico->consultar(null , 1)) {

        $arrayRecipients = array();
        array_push($arrayRecipients, $userSendMessageArray);    

        $message = New  \App\Models\Mensaje;
        $liga =  \App\Route::names('requisiciones.edit',1);

        $datos = [ "mensajeTipoId" => 3,
                    "mensajeEstatusId" => 1,
                    "asunto" => $datosCorreo["asunto"],
                    "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                    "mensaje" => $datosCorreo["mensaje"],
                    "liga" => $liga,
                    "destinatarios" => $arrayRecipients                
                ];

		$respuestaEnvioCorreo = $message->crear($datos);

		if ( $respuestaEnvioCorreo ) {
                
            $message->consultar(null , $message->id);
            $message->mensajeHTML = $datosCorreo["mensajeHTML"];

            $send =  \App\Controllers\MailController::send($message);
            if ( $send["error"] ) $message->noEnviado([ "error" => $send["errorMessage"] ]);
            else $message->enviado();
        }
	}

	return $respuestaEnvioCorreo;
}

function generarContrasenaProveedor($longitud = 10) {
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=';
    $contrasena = '';
    $maxIndex = strlen($caracteres) - 1;

    for ($i = 0; $i < $longitud; $i++) {
        $contrasena .= $caracteres[random_int(0, $maxIndex)];
    }

    return $contrasena;
}

// FUNCION GLOBAL PARA MOVER ARCHIVOS
function moverArchivos($directorioOrigen,$directorioDestino){

	if (file_exists($directorioOrigen)) {
        if (!rename($directorioOrigen, $directorioDestino)) {
            echo "No se pudo mover el archivo.";
			return false;
        }
    }
	return true;
}
