<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class PeriodoModel extends Model
{
    use HasFactory;

    private Connection $conexion;

    public function __construct()
    {
        parent::__construct();
        $this->conexion = DB::connection('evaluacion');
    }

    /**
     * @param stdClass $params
     * @return array
     */
    public function listarPeriodo(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.eval_sp_lst_pg_periodo ?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->descripcion);
        $smtp->bindParam(3, $params->desde);
        $smtp->bindParam(4, $params->hasta);
        $smtp->bindParam(5, $params->estado);
        $smtp->bindParam(6, $params->longitud);
        $smtp->bindParam(7, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function listarPeriodoUsuario(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_lst_pg_perfil_usuario ?,?,?,?,?');
        $smtp->bindParam(1, $params->idPerfil);
        $smtp->bindParam(2, $params->codigoUsuario);
        $smtp->bindParam(3, $params->nombres);
        $smtp->bindParam(4, $params->longitud);
        $smtp->bindParam(5, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }


    public function obtenerPeriodo(string $idPerfil): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.gral_sp_get_cargo ?,?,?');
        $smtp->bindParam(1, $idPerfil);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados ?? []];
    }

    public function editarPeriodo(stdClass $params, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.datg_sp_insupd_pg_cargo 2,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->descripcion);
        $smtp->bindParam(3, $usuario);
        $smtp->bindParam(4, $perfil);
        $smtp->bindParam(5, $equipo);
        $smtp->bindParam(6, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(7, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }

    public function guardarPeriodo(stdClass $params, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.eval_sp_insertar_periodo 1,?,?,?,?,?,?,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->año);
        $smtp->bindParam(3, $params->semestre);
        $smtp->bindParam(4, $params->nombre);
        $smtp->bindParam(5, $params->desde);
        $smtp->bindParam(6, $params->hasta);
        $smtp->bindParam(7, $params->dividir);
        $smtp->bindParam(8, $params->multiplicar);
        $smtp->bindParam(9, $params->factor_asistencia);
        $smtp->bindParam(10, $usuario);
        $smtp->bindParam(11, $perfil);
        $smtp->bindParam(12, $equipo);
        $smtp->bindParam(13, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(14, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }
    public function generarFormato(string $id, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.eval_sp_generar_formato ?,?,?');
        $smtp->bindParam(1, $id);
        $smtp->bindParam(2, $usuario);
        $smtp->bindParam(3, $equipo);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : '';
        $smtp->closeCursor();
        return ['1', 'exito', $resultados ?? ''];
    }

    public function anularPeriodo(string $id, string $motivo, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.datg_sp_upd_estado_pg_cargo 1,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $id);
        $smtp->bindParam(2, $motivo);
        $smtp->bindParam(3, $usuario);
        $smtp->bindParam(4, $perfil);
        $smtp->bindParam(5, $equipo);
        $smtp->bindParam(6, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(7, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados ?? ''];
    }

    public function activarPeriodo(string $id, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.datg_sp_upd_estado_pg_cargo 2,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $id);
        $smtp->bindValue(2, '');
        $smtp->bindParam(3, $usuario);
        $smtp->bindParam(4, $perfil);
        $smtp->bindParam(5, $equipo);
        $smtp->bindParam(6, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(7, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados ?? ''];
    }

}
