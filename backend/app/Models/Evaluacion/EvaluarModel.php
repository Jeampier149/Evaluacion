<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class EvaluarModel extends Model
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
    public function listarEvaluar(stdClass $params, $codigo, $perfil): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_lst_pg_evaluar ?,?,?,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $codigo);
        $smtp->bindParam(2, $perfil);
        $smtp->bindParam(3, $params->empleado);
        $smtp->bindParam(4, $params->categoria);
        $smtp->bindParam(5, $params->unidad);
        $smtp->bindParam(6, $params->servicio);
        $smtp->bindParam(7, $params->cargo);
        $smtp->bindParam(8, $params->estado);
        $smtp->bindParam(9, $params->periodo);
        $smtp->bindParam(10, $params->longitud);
        $smtp->bindParam(11, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function listarEvaluarRevisor(stdClass $params, $codigo, $perfil): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_lst_pg_evaluar_revisor ?,?,?,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $codigo);
        $smtp->bindParam(2, $perfil);
        $smtp->bindParam(3, $params->empleado);
        $smtp->bindParam(4, $params->categoria);
        $smtp->bindParam(5, $params->unidad);
        $smtp->bindParam(6, $params->servicio);
        $smtp->bindParam(7, $params->cargo);
        $smtp->bindParam(8, $params->estado);
        $smtp->bindParam(9, $params->periodo);
        $smtp->bindParam(10, $params->longitud);
        $smtp->bindParam(11, $params->pagina);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function listarPeriodo(): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_lst_periodos');
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function listarEvalFormF(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare('EXEC dbo.eval_sp_lst_evaluar_completo ?,?,?');
        $smtp->bindParam(1, $params->categoria);
        $smtp->bindParam(2, $params->idEmpleado);
        $smtp->bindParam(3, $params->periodo);
        $smtp->execute();
        $json = $smtp->fetchColumn();
        $smtp->closeCursor();

        if (!$json) {
            return [];
        }

        $data = json_decode($json, true);

        if (isset($data['info_evaluacion']) && is_string($data['info_evaluacion'])) {
            $data['info_evaluacion'] = json_decode($data['info_evaluacion'], true);
        }

        return $data;
    }

    public function generarPdfEval(stdClass $params): array
    {
        $smtp = $this->conexion->getPdo()->prepare('EXEC dbo.eval_sp_lst_evaluar_completo ?,?,?');
        $smtp->bindParam(1, $params->categoria);
        $smtp->bindParam(2, $params->idEmpleado);
        $smtp->bindParam(3, $params->periodo);
        $smtp->execute();
        $json = $smtp->fetchColumn();
        $smtp->closeCursor();

        if (!$json) {
            return [];
        }

        $data = json_decode($json, true);

        if (isset($data['info_evaluacion']) && is_string($data['info_evaluacion'])) {
            $data['info_evaluacion'] = json_decode($data['info_evaluacion'], true);
        }

        return $data;
    }

    public function listarHistorial(stdClass $params, $idEmpleado): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_lst_pg_historial ?,?,?,?,?,?,?');
        $smtp->bindParam(1, $idEmpleado);
        $smtp->bindParam(2, $params->periodo);
        $smtp->bindParam(3, $params->categoria);
        $smtp->bindParam(4, $params->servicio);
        $smtp->bindParam(5, $params->cargo);
        $smtp->bindParam(6, $params->longitud);
        $smtp->bindParam(7, $params->pagina);
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

  public function guardarEvaluarRevisor(
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
        'EXEC dbo.sp_insertar_crit_per_emp_revisor ?,?,?,?,?,?,?,?,?,?,?,?');

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

    public function guardarEvaluacionFirmado(string $periodo, string $cc_empleado, string $archivo): bool
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.eval_sp_upd_firma_evaluador ?,?,?');
        $smtp->bindParam(1, $periodo);
        $smtp->bindParam(2, $cc_empleado);
        $smtp->bindParam(3, $archivo);
        return $smtp->execute();
    }

 
}
