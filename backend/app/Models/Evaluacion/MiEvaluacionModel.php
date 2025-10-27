<?php

namespace App\Models\Evaluacion;

use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
use stdClass;


class MiEvaluacionModel extends Model
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

    public function listarPeriodosMiEvaluacion($empleado)
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_lst_periodos_mi_evaluacion ?');
        $smtp->bindParam(1, $empleado);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }


    public function listarEvalForm(string $idCategoria): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
            /** @lang SQL */
            'EXEC dbo.eval_sp_lst_evaluar_form ?'
        );
        $smtp->bindParam(1, $idCategoria);
        $smtp->execute();

        $json = $smtp->fetchColumn(); // <- ahora será string
        $smtp->closeCursor();

        return $json ? json_decode($json, true) : [];
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


        return $data;
    }




    public function guardarConformidad(
        $periodo,
        $empleado,
        $cfl_conforme,
        $ct_conforme
   
    ) {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.sp_insertar_conforme_evaluador ?,?,?,?,?,?');

        $smtp->bindParam(1, $periodo);
        $smtp->bindParam(2, $empleado);
        $smtp->bindParam(3, $cfl_conforme);
        $smtp->bindParam(4, $ct_conforme);
        $smtp->bindParam(5, $usuario);
        $smtp->bindParam(6, $equipo);

        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_COLUMN) : '';
        $smtp->closeCursor();
        return ['1', 'ok', $resultados];
    }




}
