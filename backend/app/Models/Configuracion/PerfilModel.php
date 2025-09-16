<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class PerfilModel extends Model
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
    public function listarPerfil(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_lst_pg_perfil ?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->descripcion);
        $smtp->bindParam(3, $params->estado);
        $smtp->bindParam(4, $params->longitud);
        $smtp->bindParam(5, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function listarPerfilUsuario(stdClass $params): array
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

    public function listarPerfilCombo(): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_lst_all_pg_perfil');
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function obtenerPerfil(string $idPerfil): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_get_pg_perfil ?,?,?');
        $smtp->bindParam(1, $idPerfil);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados ?? []];
    }

    public function editarPerfil(stdClass $params, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_insupd_pg_perfil 2,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->descripcion);
        $smtp->bindParam(3, $params->observacion);
        $smtp->bindParam(4, $usuario);
        $smtp->bindParam(5, $perfil);
        $smtp->bindParam(6, $equipo);
        $smtp->bindParam(7, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(8, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }

    public function guardarPerfil(stdClass $params, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_insupd_pg_perfil 1,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->descripcion);
        $smtp->bindParam(3, $params->observacion);
        $smtp->bindParam(4, $usuario);
        $smtp->bindParam(5, $perfil);
        $smtp->bindParam(6, $equipo);
        $smtp->bindParam(7, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(8, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }

    public function anularPerfil(string $id, string $motivo, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_upd_estado_pg_perfil 1,?,?,?,?,?,?,?');
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

    public function activarPerfil(string $id, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_upd_estado_pg_perfil 2,?,?,?,?,?,?,?');
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
