<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PDO;


class ExtranjeriaModel extends Model
{
    use HasFactory;
    private int $estado = 0;
    private string $mensaje = '';
    private array $datos = [];


    /**
     * @return int Retorna "1" para petición exitosa
     */
    public function getEstado(): int
    {
        return $this->estado;
    }

    /**
     * @return string Retorna el mensaje de error
     */
    public function getMensaje(): string
    {
        return $this->mensaje;
    }

    /**
     * @return array Retorna datos de la petición al webservice
     */
    public function getDatos(): array
    {
        return $this->datos;
    }

    public function __construct(array $attributes = [])
    {
        $this->conexion = DB::connection('personal');
        parent::__construct($attributes);
    }

    public function buscarDatosEnLocal(string $documento)
    {
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.gral_sp_get_gral_consulta_extranjeria ?');
        $smtp->bindParam(1, $documento);
        $smtp->execute();
        $resultados = $smtp->columnCount() > 0 ? $smtp->fetch(PDO::FETCH_ASSOC) : false;
        $smtp->closeCursor();
        if($resultados){
            $this->datos = $resultados;
            $this->estado = 1;
        }else{
            $this->estado = 0;
        }
    }

    public function buscarDatosEnLinea(string $documento): void
    {
        $curl = curl_init();
        $body = ['username' => env('MIGRA_REST_USER'), 'password' => env('MIGRA_REST_PASS'),
            'ip' => '201.234.52.82', 'nivelacceso' => 'personalizado', 'docconsulta' => $documento];

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://ws5.pide.gob.pe/Rest/Pide/Migraciones/CEE?out=json",
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
            ],
            CURLOPT_POSTFIELDS => json_encode(['PIDE' => $body])
        ]);


        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $this->mensaje = 'No se encuentra disponible el servicio de Migraciones';
        } else {
            $response = json_decode($response, true);
            if ($response['codRespuesta'] !== '0000') {
                $this->mensaje = $response['desRespuesta'];
            } else {
                $this->estado = 1;
                $respuesta['modo'] = 'EN LINEA';
                $respuesta['codigo'] = $response['codRespuesta'];
                $respuesta['mensaje'] = $response['desRespuesta'];
                $respuesta['documento'] = $documento;
                $respuesta['apellidoPaterno'] = $response['datosPersonales']['apepaterno'];
                $respuesta['apellidoMaterno'] = $response['datosPersonales']['apematerno'];
                $respuesta['nombres'] = $response['datosPersonales']['nombres'];
                $respuesta['calidadMigra'] = $response['datosPersonales']['calmigratoria'];
                $this->datos = $respuesta;
                $this->guardarDatosEnLocal($respuesta);
            }
        }
    }

    private function guardarDatosEnLocal($datos)
    {
        $request = \request()->user();
        $usuario = 'SIN USUARIO';
        $perfil = 'SIN PERFIL';
        $equipo = 'SIN EQUIPO';
        if (!is_null($request)) {
            $usuario = $request->xg_Cod_Usuario;
            $perfil = $request->xg_Cod_Perfil;
            $equipo = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        }
        $smtp = $this->conexion->getPdo()->prepare(/** @lang SQL */ 'EXEC dbo.gral_sp_insupd_gral_consulta_extranjeria ?,?,?,?,?,?,?,?,?,?');
        $smtp->bindParam(1, $datos['documento']);
        $smtp->bindParam(2, $datos['codigo']);
        $smtp->bindParam(3, $datos['mensaje']);
        $smtp->bindParam(4, $datos['apellidoPaterno']);
        $smtp->bindParam(5, $datos['apellidoMaterno']);
        $smtp->bindParam(6, $datos['nombres']);
        $smtp->bindParam(7, $datos['calidadMigra']);
        $smtp->bindParam(8, $usuario);
        $smtp->bindParam(9, $perfil);
        $smtp->bindParam(10, $equipo);
        $smtp->execute();
        $smtp->closeCursor();
        return true;
    }
}

