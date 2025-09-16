<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class PersonalEvaluarModel extends Model
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
   


    public function listarEmpleados(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_lst_pg_personal_evaluar ?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->apellidos);
        $smtp->bindParam(2, $params->nomnbre);
        $smtp->bindParam(3, $params->periodo);
        $smtp->bindParam(4, $params->categoria);
        $smtp->bindParam(5, $params->servicio);
        $smtp->bindParam(6, $params->cargo);
        $smtp->bindParam(7, $params->longitud);
        $smtp->bindParam(8, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }
        public function listarEmpleadosNuevos(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_lst_pg_personal_evaluar_nuevo ?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->apellidos);
        $smtp->bindParam(2, $params->nomnbre);
        $smtp->bindParam(3, $params->periodo);
        $smtp->bindParam(4, $params->categoria);
        $smtp->bindParam(5, $params->servicio);
        $smtp->bindParam(6, $params->cargo);
        $smtp->bindParam(7, $params->longitud);
        $smtp->bindParam(8, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function guardarEvaluar(
        $periodo,
        $empleado,
        $id_factor,
        $id_factor_criterio,
        $cfl_evaluador,
        $puntaje_asignado,
        $puntaje_rev,
        $ct_recomendacion,
        $cfl_capacitacion,
        $ct_capacitacion,
        $usuario,
        $equipo
    ) {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.sp_insertar_crit_per_emp ?,?,?,?,?,?,?,?,?,?,?,?');

        $smtp->bindParam(1, $periodo);
        $smtp->bindParam(2, $empleado);
        $smtp->bindParam(3, $id_factor);
        $smtp->bindParam(4, $id_factor_criterio);
        $smtp->bindParam(5, $cfl_evaluador);
        $smtp->bindParam(6, $puntaje_asignado);
        $smtp->bindParam(7, $puntaje_rev);
        $smtp->bindParam(8, $ct_recomendacion);
        $smtp->bindParam(9, $cfl_capacitacion);
        $smtp->bindParam(10, $ct_capacitacion);
        $smtp->bindParam(11, $usuario);
        $smtp->bindParam(12, $equipo);
        // $smtp->bindParam(6, $estado, PDO::PARAM_INT | PDO::PARAM_INPUT_OUTPUT, 1);
        // $smtp->bindParam(7, $mensaje, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 300);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : '';
        $smtp->closeCursor();
        return ['1', 'ok', $resultados];
    }



    public function anularEvaluar(string $id, string $motivo, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.datg_sp_upd_estado_pg_cargo 1,?,?,?,?,?,?,?');
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

    public function activarEvaluar(string $id, $usuario, $perfil, $equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.datg_sp_upd_estado_pg_cargo 2,?,?,?,?,?,?,?');
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
