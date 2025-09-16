<?php

namespace App\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;
class AuthModel extends Model
{
    use HasFactory;
   

    protected $conexion;

    public function __construct()
    {
        parent::__construct();
        $this->conexion = DB::connection('evaluacion');
    }
    public function obtenerMenu($perfil): array
    {
        try {
            return $this->conexion->select("EXEC dbo.sp_obtener_menu ?", [$perfil]);
        } catch (\Error $e) {
            return [];
        }
    }
    public function obtenerAccesos($perfil): array
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.gral_sp_lst_pg_accion_perfil ?');
        $smtp->bindParam(1, $perfil);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetchAll(PDO::FETCH_ASSOC) : [];
        $smtp->closeCursor();
        return $resultados;
    }

    public function cambiarContrasena($usuario,$nuevaContrasenaHash,$equipo)
    {
        try {
            return $this->conexion->selectOne('EXEC dbo.sp_upd_contrasena_usuario ?, ?, ?', [$usuario,$nuevaContrasenaHash,$equipo]);
        } catch (\Error $e) {
            return 0;
        }
    }
    public function validarUsuario($usuario)
    {
        try {
            return $this->conexion->selectOne('EXEC dbo.sp_validar_usuario ?', [$usuario]);
        } catch (\Error $e) {
            return 0;
        }
    }

 
    
}
