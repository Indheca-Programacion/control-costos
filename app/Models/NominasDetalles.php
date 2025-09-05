<?php

namespace App\Models;

if ( file_exists ( "app/Policies/NominasDetallesPolicy.php" ) ) {
    require_once "app/Policies/NominasDetallesPolicy.php";
} else {
    require_once "../Policies/NominasDetallesPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\NominasDetallesPolicy;

class NominasDetalles extends NominasDetallesPolicy
{
    static protected $fillable = [
        'datos', 'semana', 'obraId','filtroObraId'
    ];

    static protected $type = [
        'id' => 'integer',
        'fk_obraId' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'primas' => 'integer',
        'comida' => 'integer',
        'prestamos' => 'integer',
        'semana' => 'integer',
        'descuentos' => 'integer',
        'pension' => 'integer',
        'neto' => 'integer',
        'fk_empleadoId' => 'integer',
        'fk_nominaId' => 'integer',
        'fk_obraDetalleId' => 'integer',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "nominas_detalles";

    protected $keyName = "id";

    public $id = null;    

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR APLICACIONES
    =============================================*/

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT ND.*
                                                    FROM $this->tableName ND 
                                                    inner join obras O on O.id = ND.fk_obraDetalleId
                                                    where O.id = $valor ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
            }

            return $respuesta;

        }

    }

    public function consultarPorNomina($id){
        return Conexion::queryAll($this->bdName,"SELECT ND.*, CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS 'nombreCompleto', IF(D.id is not null,D.descripcion,I.descripcion) as puesto
                                                FROM nominas_detalles ND 
                                                INNER JOIN empleados E ON E.id = ND.fk_empleadoId
                                                INNER JOIN obra_detalles OD ON OD.id = ND.fk_obraDetalleId
                                                LEFT JOIN insumos D on D.id = OD.insumoId
                                                LEFT JOIN indirectos I on I.id = OD.indirectoId
                                                WHERE ND.fk_nominaId = $id");
    }

    public function crear($datos) {
    }

    public function actualizar($datos) {
    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

}