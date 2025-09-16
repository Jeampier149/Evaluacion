<?php

namespace App\Models\Util;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GeneralModel extends Model
{
  use HasFactory;
  public function __construct()
  {

    parent::__construct();
    $this->conexion = DB::connection('personal');
  }

  public function listarTipoDocumento()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_documento');
  }
  public function listarTipoEmpleado()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_empleado');
  }
  public function listarGrupo()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_grupo_ocupacional');
  }
  public function listarRegimen()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_regimen');
  }
  public function listarTipoRegimen($id)
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_regimen ?', [$id]);
  }
  public function listarSexo()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_sexo');
  }
  public function listarGrupoSanguineo()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_grupo_sanguineo');
  }
  public function listarEstadoCivil()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_estado_civil');
  }
  public function listarParentesco()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_parentesco');
  }
  public function listarProfesiones()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_profesion');
  }
  public function listarNivelIdioma()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_nivel_idioma');
  }
  public function listarCargo()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_cargos');
  }
  public function listarNivel()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_niveles_cargo');
  }
  public function listarVias()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_vias');
  }
  public function listarZonas()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_tipo_zonas');
  }
  public function listarUnidad()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_unidad_organica');
  }
  public function listarServicio()
  {
    return $this->conexion->select(
    /** @lang SQL */
    'EXEC dbo.g_sp_listar_servicio');
  }
}
