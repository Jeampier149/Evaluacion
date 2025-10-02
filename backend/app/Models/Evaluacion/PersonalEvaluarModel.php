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

    public function agregarEmpEval($tipo,$periodo,$empleado, $usuario, $perfil, $equipo)
    {   
 
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.sp_insertar_eval_empleado ?,?,?,?,?,?');
        $smtp->bindParam(1, $periodo);
        $smtp->bindParam(2, $tipo);
        $smtp->bindParam(3, $empleado);
        $smtp->bindParam(4, $usuario);
        $smtp->bindParam(5, $equipo);
        // El parámetro OUTPUT necesita tratamiento especial
        $smtp->bindParam(6, $resultado, PDO::PARAM_STR, 100);
    
    $smtp->execute();
    $smtp->closeCursor();
    
    return ['200', '', $resultado ?? ''];
    }

    public function obtenerSelects()
    {
        try {
        $smtp = $this->conexion->getPdo()->prepare('EXEC dbo.sp_obtener_todas_tablas_json');
        $smtp->execute();
        
        // Como el SP retorna una sola fila con columnas JSON
        $resultado = $smtp->fetch(PDO::FETCH_ASSOC);
        $smtp->closeCursor();
        
        // Si no hay resultados
        if (!$resultado) {
            return [
                'unidades' => [],
                'servicios' => [],
                'categorias' => [],
                'cargos' => [],
                'niveles' => []
            ];
        }
        
        // Convertir cada columna JSON a array
        $datosProcesados = [];
        $columnas = [
            'unidades',
            'servicios', 
            'categorias',
            'cargos',
            'niveles',
            'condicion_laboral',
            'empleado',
            'estados'
        ];
        
        foreach ($columnas as $columna) {
            if (isset($resultado[$columna]) && $resultado[$columna] !== null) {
                $datosProcesados[$columna] = json_decode($resultado[$columna], true) ?: [];
            } else {
                $datosProcesados[$columna] = [];
            }
        }
        
        return $datosProcesados;
        
    } catch (\Exception $e) {
        // Log del error
        error_log("Error en obtenerSelects: " . $e->getMessage());
        
        return [
            'unidad_organica' => [],
            'servicios' => [],
            'categorias' => [],
            'cargos' => [],
            'niveles' => []
        ];
    }
    }
 
   public function obtenerDataEval($id,$periodo)
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_obtener_datos_evaluado ?,?');
        $smtp->bindParam(1, $id);
        $smtp->bindParam(2, $periodo);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }


     public function editarPersonalEval(stdClass $params,$periodo,$empleado,$usuario,$equipo): array
    {
        $smtp = $this->conexion->getPdo()->prepare(
        /** @lang SQL */
        'EXEC dbo.eval_sp_upd_personal_evaluar ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $params->idUnidad);
        $smtp->bindParam(2, $params->idServicio);
        $smtp->bindParam(3, $params->idCargo);
        $smtp->bindParam(4, $params->idCategoria);
        $smtp->bindParam(5, $params->idCondicion);
        $smtp->bindParam(6, $params->idNivel);
        $smtp->bindParam(7, $params->evaluador);
        $smtp->bindParam(8, $params->factor_asistencia);
        $smtp->bindParam(9, $params->puntaje_asistencia);
        $smtp->bindParam(10, $params->revisor);
        $smtp->bindParam(11, $params->idEstado);
        $smtp->bindParam(12, $periodo);
        $smtp->bindParam(13, $empleado);
        $smtp->bindParam(14, $usuario);
        $smtp->bindParam(15, $equipo);

        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
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
