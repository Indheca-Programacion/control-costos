<?php

namespace App\Models;

if ( file_exists ( "app/Policies/CategoriaPermisoPolicy.php" ) ) {
    require_once "app/Policies/CategoriaPermisoPolicy.php";
} else {
    require_once "../Policies/CategoriaPermisoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\CategoriaPermisoPolicy;

class CategoriaPermiso extends CategoriaPermisoPolicy
{
    static protected $fillable = [
        'id', 'idCategoria','idGrupo','idPermiso','permisos'
    ];

    static protected $type = [
        'id' => 'integer',
        'idCategoria' => 'integer',
        'idGrupo' => 'integer',
        'idPermiso' => 'integer',
        'permisos'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "categoria_grupo_permiso";

    protected $keyName = "id";

    public $id = null;
    public $nombre;
    public $descripcion;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CATEGORIA PROVEEDORES
    =============================================*/
    public function consultar($idCategoria = null) {
        // Construir la parte de la consulta SQL
        $sql = "SELECT CGP.*, 
                PCP.nombre,
                PCP.tipo,
                PCP.grupo
                FROM $this->tableName CGP
                INNER JOIN permisos_categoria_proveedores PCP ON PCP.id = CGP.idPermiso";

        // Si existe idCategoria, agregar el WHERE
        if ($idCategoria !== null) {
            $sql .= " WHERE idCategoria = $idCategoria";
        }

        // Ejecutar la consulta
        return Conexion::queryAll($this->bdName, $sql, $error);
    }

    public function crear($datos) {

            $arrayPDOParam = array();
            $arrayPDOParam["idCategoria"] = self::$type["idCategoria"];
            $arrayPDOParam["idGrupo"] = self::$type["idGrupo"];
            $arrayPDOParam["idPermiso"] = self::$type["idPermiso"];

            $idsPermisosRecorridos = []; 
            
            if (empty($datos) || !isset($datos)) {
                // Si no mandas datos, borra todos los permisos
                return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName", [], [], $error);
            } else {
                foreach ($datos as $categorias) {
                    foreach ($categorias as $idCategoria => $grupos) {
                        foreach ($grupos as $idGrupo => $permisos) {
                            foreach ($permisos as $idPermiso) {
            
                                // Guardamos lo que se está procesando
                                $idsPermisosRecorridos[] = [
                                    "idCategoria" => $idCategoria,
                                    "idGrupo" => $idGrupo,
                                    "idPermiso" => $idPermiso
                                ];
            
                                // Verificamos si ya existe
                                $existe = Conexion::queryAll(
                                    $this->bdName,
                                    "SELECT COUNT(*) as total FROM $this->tableName 
                                     WHERE idCategoria = $idCategoria AND idGrupo = $idGrupo AND idPermiso = $idPermiso",
                                    $error
                                );
            
                                // Insertar solo si no existe
                                if (empty($existe) || $existe[0]["total"] == 0) {
                                    $datos["idCategoria"] = $idCategoria;
                                    $datos["idGrupo"] = $idGrupo;
                                    $datos["idPermiso"] = $idPermiso;
            
                                    $respuesta = Conexion::queryExecute(
                                        $this->bdName,
                                        "INSERT INTO $this->tableName (idCategoria, idGrupo, idPermiso) 
                                        VALUES (:idCategoria, :idGrupo, :idPermiso)",
                                        $datos,
                                        $arrayPDOParam,
                                        $error,
                                        $lastId
                                    );
                                }
                            }
                        }
                    }
                }
            
                // Obtener todos los permisos actuales en la base de datos
                $permisosExistentes = Conexion::queryAll(
                    $this->bdName,
                    "SELECT idCategoria, idGrupo, idPermiso FROM $this->tableName",
                    $error
                );
            
                // Comparar los permisos existentes con los que mandaste
                foreach ($permisosExistentes as $permisoBD) {
                    $encontrado = false;
            
                    foreach ($idsPermisosRecorridos as $permisoEnviado) {
                        if (
                            $permisoBD['idCategoria'] == $permisoEnviado['idCategoria'] &&
                            $permisoBD['idGrupo'] == $permisoEnviado['idGrupo'] &&
                            $permisoBD['idPermiso'] == $permisoEnviado['idPermiso']
                        ) {
                            $encontrado = true;
                            break;
                        }
                    }
            
                    if (!$encontrado) {

                        $idPermiso = $permisoBD["idPermiso"];
                        $idGrupo = $permisoBD["idGrupo"];
                        $idCategoria = $permisoBD["idCategoria"];

                         Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE idCategoria = $idCategoria AND idGrupo = $idGrupo AND idPermiso = $idPermiso", [], [], $error);
                    }
                }
            }
            
        return  true;
    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;

    }

  
}
