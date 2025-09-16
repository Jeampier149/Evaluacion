<?php

namespace App\Models\Mantenimiento;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;

/**
 * @property Connection $conexion
 */
class listarModel extends Model
{
    use HasFactory;

    public function __construct()
    {
        $this->conexion = DB::connection('evaluacion');

    }


    public function listaPerfil(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_lst_pg_perfil ?,?,?,?,?');
        $smtp->bindParam(1, $params->codigo);
        $smtp->bindParam(2, $params->descripcion);
        $smtp->bindParam(3, $params->estado);
        $smtp->bindParam(4, $params->longitud);
        $smtp->bindParam(5, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function listaProfesional(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.mant_sp_lst_profesional ?,?,?,?,?');
        $smtp->bindParam(1, $params->codigo);
        $smtp->bindParam(2, $params->nombres);
        $smtp->bindParam(3, $params->estado);
        $smtp->bindParam(4, $params->longitud);
        $smtp->bindParam(5, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function obtenerProfesional(string $codigo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo_web.mant_sp_get_profesional ?,?,?');
        $smtp->bindParam(1, $codigo);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return [$estado, $mensaje, $resultados];
    }

    public function obtenerPerfil($codigo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_get_perfil ?,?,?');
        $smtp->bindParam(1, $codigo);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return [$estado, $mensaje, $resultados];
    }
}
