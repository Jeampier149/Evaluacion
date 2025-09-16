<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;


class AccionModel extends Model
{
    use HasFactory;

    private Connection $conexion;

    public function __construct()
    {
        parent::__construct();
        $this->conexion = DB::connection('evaluacion');
    }

    public function listarAccionXMenu(string $idMenu): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_lst_pg_accion ?');
        $smtp->bindParam(1, $idMenu, PDO::PARAM_INT);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }


    public function guardarAccion($idMenu, $descripcion, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_ins_pg_accion ?,?,?,?,?,?,?');
        $smtp->bindParam(1, $idMenu, PDO::PARAM_INT);
        $smtp->bindParam(2, $descripcion);
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

    public function anularAccion(string $id, string $descripcion, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_upd_estado_pg_accion ?,?,?,?,?,?,?');
        $smtp->bindParam(1, $id);
        $smtp->bindParam(2, $descripcion);
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
