<?php

namespace App\Models\Mantenimiento;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class UsuarioModel extends Model
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
    public function listarUsuario(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_lst_pg_usuario ?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->codigo);
        $smtp->bindParam(2, $params->nombres);
        $smtp->bindParam(3, $params->departamento);
        $smtp->bindParam(4, $params->perfil);
        $smtp->bindParam(5, $params->desPerfil);
        $smtp->bindParam(6, $params->estado);
        $smtp->bindParam(7, $params->longitud);
        $smtp->bindParam(8, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function guardarUsuario(FichaUsuarioModel $fichaUsuario, string $usuario, string $perfil, string $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_insupd_pg_usuario ?,?,?,?,?,?,?,?,?');
        $smtp->bindValue(1, 1);
        $smtp->bindValue(2, $fichaUsuario->codigo);
        $smtp->bindParam(3, $fichaUsuario->nombres);
        $smtp->bindParam(4, $fichaUsuario->perfil);
        $smtp->bindParam(5, $usuario);
        $smtp->bindParam(6, $perfil);
        $smtp->bindParam(7, $equipo);
        $smtp->bindParam(8, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(9, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : [];
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }

    public function editarUsuario(FichaUsuarioModel $fichaUsuario, string $usuario, string $perfil, string $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_insupd_pg_usuario ?,?,?,?,?,?,?,?,?');
        $smtp->bindValue(1, 2);
        $smtp->bindValue(2, $fichaUsuario->codigo);
        $smtp->bindParam(3, $fichaUsuario->nombres);
        $smtp->bindParam(4, $fichaUsuario->perfil);
        $smtp->bindParam(5, $usuario);
        $smtp->bindParam(6, $perfil);
        $smtp->bindParam(7, $equipo);
        $smtp->bindParam(8, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(9, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();

        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : [];
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }

    /**
     * @param string $codigo
     * @return array|false
     */
    public function obtenerUsuario(string $codigo): false|array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_get_pg_usuario ?');
        $smtp->bindParam(1, $codigo);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    /**
     * @param string $codigo
     * @param string $motivo
     * @param string $usuario
     * @param string $perfil
     * @param string $equipo
     * @return array
     */
    public function anularUsuario(string $codigo, string $motivo, string $usuario, string $perfil, string $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_upd_estado_pg_usuario ?,?,?,?,?,?,?,?');
        $smtp->bindValue(1, 1);
        $smtp->bindParam(2, $codigo);
        $smtp->bindParam(3, $motivo);
        $smtp->bindParam(4, $usuario);
        $smtp->bindParam(5, $perfil);
        $smtp->bindParam(6, $equipo);
        $smtp->bindParam(7, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(8, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $smtp->closeCursor();
        return [$estado, trim($mensaje)];
    }

    /**
     * @param string $codigo
     * @param string $usuario
     * @param string $perfil
     * @param string $equipo
     * @return array
     */
    public function activarUsuario(string $codigo, string $usuario, string $perfil, string $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_upd_estado_pg_usuario ?,?,?,?,?,?,?,?');
        $smtp->bindValue(1, 2);
        $smtp->bindParam(2, $codigo);
        $smtp->bindValue(3, '');
        $smtp->bindParam(4, $usuario);
        $smtp->bindParam(5, $perfil);
        $smtp->bindParam(6, $equipo);
        $smtp->bindParam(7, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(8, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $smtp->closeCursor();
        return [$estado, trim($mensaje)];
    }

    public function reestrablecerUsuario(string $codigo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.mant_sp_upd_constrasenia_pg_usuario ?,?,?');
        $smtp->bindParam(1, $codigo);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $smtp->closeCursor();
        return [$estado, trim($mensaje)];
    }
    public function reporteUsuarios()
    {
         $result=$this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_reporte_usuarios',
            []
        );
        return $result;
    }

}
