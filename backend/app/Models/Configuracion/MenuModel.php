<?php

namespace App\Models\Configuracion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class MenuModel extends Model
{
    use HasFactory;

    private Connection $conexion;

    public function __construct()
    {
        parent::__construct();
        $this->conexion = DB::connection('evaluacion');
    }

    public function listarMenuCombo(): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.gral_sp_lst_pg_menu');
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function listarMenu(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_lst_pg_menu ?,?,?,?,?,?');
        $smtp->bindParam(1, $params->nombre);
        $smtp->bindParam(2, $params->padre);
        $smtp->bindParam(3, $params->orden);
        $smtp->bindParam(4, $params->estado);
        $smtp->bindParam(5, $params->longitud);
        $smtp->bindParam(6, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function obtenerMenu(string $idMenu): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_get_pg_menu ?,?,?');
        $smtp->bindParam(1, $idMenu);
        $smtp->bindParam(2, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(3, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados ?? []];
    }

    public function editarMenu(stdClass $params, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_insupd_pg_menu 2,?,?,?,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->nombre);
        $smtp->bindParam(3, $params->orden);
        $smtp->bindParam(4, $params->icono);
        $smtp->bindParam(5, $params->padre);
        $smtp->bindParam(6, $params->ruta);
        $smtp->bindParam(7, $usuario);
        $smtp->bindParam(8, $perfil);
        $smtp->bindParam(9, $equipo);
        $smtp->bindParam(10, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(11, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados ?? ''];
    }

    public function guardarMenu(stdClass $params, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_insupd_pg_menu 1,?,?,?,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->id);
        $smtp->bindParam(2, $params->nombre);
        $smtp->bindParam(3, $params->orden);
        $smtp->bindParam(4, $params->icono);
        $smtp->bindParam(5, $params->padre);
        $smtp->bindParam(6, $params->ruta);
        $smtp->bindParam(7, $usuario);
        $smtp->bindParam(8, $perfil);
        $smtp->bindParam(9, $equipo);
        $smtp->bindParam(10, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        $smtp->bindParam(11, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : '';
        $smtp->closeCursor();
        return [$estado, trim($mensaje), $resultados ?? ''];
    }

    public function anularMenu(string $id, string $descripcion, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_upd_estado_pg_menu 1,?,?,?,?,?,?,?');
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

    public function activarMenu(string $id, string $descripcion, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.conf_sp_upd_estado_pg_menu 2,?,?,?,?,?,?,?');
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
