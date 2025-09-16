<?php

namespace App\Models\Mantenimiento;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
class ProcesarModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->conexion = DB::connection('evaluacion');
    }
    public function procesarEjecucion($year,$mes,$tipo,$ppr)
    {
        ini_set('max_execution_time', 420);
        return $this->conexion->select(
            /** @lang SQL */      
            'EXEC dbo.ex_procesar_ejecucion ?,?,?,?',
            [$year,$mes,$tipo,$ppr]
        );
    }
    public function bloquearEjecucion($usuario,$perfil,$equipo,$periodo,$fecha,$year,$tipo,$codigo)
    {
        return $this->conexion->update(
            /** @lang SQL */
            'EXEC dbo.ex_sp_bloquear_ejecucion ?,?,?,?,?,?,?,?',
            [$periodo,$year,$fecha,$tipo,$codigo,$usuario,$perfil,$equipo]
        );
    }
    public function registrarHistorial($usuario,$perfil,$equipo,$nombre,$ppr)
    {
        return $this->conexion->insert(
            /** @lang SQL */
            'EXEC dbo.ex_sp_registrar_historial ?,?,?,?,?',
            [$usuario,$equipo,$perfil,$nombre,$ppr]
        );
    }
    public function reporteInvalidados($year,$tipo,$periodo,$perfil,$servicio)
    {
        return $this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_reporte_invalidados ?,?,?,?,?',
            [$year,$tipo,$periodo,$perfil,$servicio]
        );
    }
    public function reporteCierre($year,$tipo,$periodo,$perfil,$servicio)
    {
        return $this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_reporte_detalle_cierre_poi ?,?,?,?,?',
            [$year,$tipo,$periodo,$perfil,$servicio]
        );
    }
    public function listarHistorial($usuario,$nombre,$perfil,$equipo,$fecha,$ppr,$longitud,$pagina)
    
    {
        return $this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_listar_historial ?,?,?,?,?,?,?,?', [$usuario,$nombre,$equipo,$perfil,$ppr,$fecha,$longitud,$pagina]
        );
       
    }
    public function reporteConsolidado($periodo,$year,$tipo)
    {
         $result=$this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.EX_SP_PRUEBA ?,?,? ',
            [$year,$periodo,$tipo]
        );
        return json_decode(json_encode($result), true);
    }
    public function reporteConsolidadoDetallado($periodo,$year,$tipo)
    {
         $result=$this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_reporte_consolidado_detalle ?,?,? ',
            [$periodo,$year,$tipo]
        );
        return json_decode(json_encode($result), true);
    }
    public function reporteTotal()
    {
         $result=$this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.EX_SP_DATA_TOTAL ',
            []
        );
        return json_decode(json_encode($result), true);
    }
    public function reporteResumenMetas($year,$tipo,$servicio)
    {
         $result=$this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_reporte_resumen_metas ?,?,? ',
            [$year,$tipo,$servicio]
        );
        return json_decode(json_encode($result), true);
    }
    public function listarBloqueos()
    {
        return $this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_listar_bloqueos ',
            []
        );
    }
    public function reporteLogros($periodo,$year,$tipo,$servicio,$perfil)
    {
         $result=$this->conexion->select(
            /** @lang SQL */
            'EXEC dbo.ex_sp_reporte_logros ?,?,?,?,? ',
            [$periodo,$year,$tipo,$servicio,$perfil]
        );
        return json_decode(json_encode($result), true);
    }
    
 public function reporteCentroCostos($year, $tipo, $periodo, $perfil, $servicio)
 {
      $result=$this->conexion->select(
         /** @lang SQL */
         'EXEC dbo.ex_sp_reporte_centro_costos ?,?,?,?,? ',
         [$periodo,$year,$tipo,$perfil,$servicio]
     );
     return json_decode(json_encode($result), true);
 }
}
