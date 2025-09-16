<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class AccesoModel extends Model
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
    public function listarAcceso(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_lst_pg_acceso ?,?,?,?');
        $smtp->bindParam(1, $params->idPerfil);
        $smtp->bindParam(2, $params->idMenu);
        $smtp->bindParam(3, $params->longitud);
        $smtp->bindParam(4, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function agregarAcceso(stdClass $params, $usuario, $equipo, $perfil): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_insupd_pg_menu_perfil ?,?,?,?,?,?,?,?,?');
        $smtp->bindValue(1, 1);
        $smtp->bindParam(2, $params->idPerfil);
        $smtp->bindParam(3, $params->idMenu);
        $smtp->bindValue(4, '');
        $smtp->bindValue(5, $usuario);
        $smtp->bindValue(6, $perfil);
        $smtp->bindValue(7, $equipo);
        $smtp->bindParam(8, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(9, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : [];
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }

    public function anularAcceso(stdClass $params, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_insupd_pg_menu_perfil ?,?,?,?,?,?,?,?,?');
        $smtp->bindValue(1, 2);
        $smtp->bindParam(2, $params->idPerfil);
        $smtp->bindParam(3, $params->idMenu);
        $smtp->bindParam(4, $params->motivo);
        $smtp->bindParam(5, $usuario);
        $smtp->bindParam(6, $perfil);
        $smtp->bindParam(7, $equipo);
        $smtp->bindParam(8, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(9, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados];
    }

}
