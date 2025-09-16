<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use SoapFault;
class ReniecModel extends Model
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

    public function buscarDatosEnLocal(string $dni): void
    {
        $conexion = DB::connection('planemaiento');
        $respuesta = $conexion->selectOne(/** @lang SQL */'EXEC dbo.gral_sp_get_rm_consultas_dni ?', [
            $dni
        ]);
        if (!isset($respuesta->codMensaje)) {
            $this->mensaje = 'No se encuentra los datos en local';
        } else {
            $this->mensaje = '';
            $this->estado = 1;
            $this->datos = (json_decode(json_encode($respuesta), true));
        }
    }

    public function buscarDatosEnLinea(string $dni): void
    {
        $url = 'http://wsminsa.minsa.gob.pe/WSRENIEC_DNI/SerDNI.asmx?wsdl';
        try {
            $opciones = ['soap_version' => SOAP_1_2, 'exception' => true, 'trace' => 1];
            $soapClient = new \SoapClient($url, $opciones);
            $parametros = ['GetReniec' => ['strDNIAuto' => $dni, 'strDNICon' => $dni]];
            $resultado = $soapClient->__soapCall('GetReniec', $parametros);
            $resultado = $resultado->GetReniecResult->string;
            $respuesta = [];
            if ($resultado[0] === '0000') {
                $this->estado = 1;
                $respuesta['modo'] = 'EN LINEA';
                $respuesta['codMensaje'] = $resultado[0];
                $respuesta['dni'] = $resultado[21];
                $respuesta['apellidoPaterno'] = $resultado[1];
                $respuesta['apellidoMaterno'] = $resultado[2];
                $respuesta['nombres'] = $resultado[3];
                $respuesta['fechaNacimiento'] = $resultado[18];
                $respuesta['fechaEmision'] = $resultado[19];
                $respuesta['sexo'] = $resultado[17];
                $respuesta['ubigeoContinente'] = $resultado[4];
                $respuesta['ubigeoPais'] = $resultado[5];
                $respuesta['ubigeoDepartamento'] = $resultado[6];
                $respuesta['ubigeoProvincia'] = $resultado[7];
                $respuesta['ubigeoDistrito'] = $resultado[8];
                $respuesta['ubigeoCentroPoblado'] = $resultado[9];
                $respuesta['continente'] = $resultado[10];
                $respuesta['pais'] = $resultado[11];
                $respuesta['departamento'] = $resultado[12];
                $respuesta['provincia'] = $resultado[13];
                $respuesta['distrito'] = $resultado[14];
                $respuesta['centroPoblado'] = $resultado[15];
                $respuesta['direccion'] = $resultado[16];
                $this->datos = $respuesta;
                $this->guardarDatosEnLocal($respuesta);
            } else if(is_null($resultado[0])) {
                $this->mensaje = 'El DNI buscado no existe o el servicio de RENIEC no se encuentra disponible.';
            }else {
                $this->mensaje = 'El dni buscado no existe';
            }
        } catch (SoapFault  $e) {
            $this->mensaje = 'No se encuentra disponible el servicio RENIEC';
        }
    }

    private function guardarDatosEnLocal($datos): void
    {
        $conexion = DB::connection('personal');
        $request = \request()->user();
        $usuario = 'SIN USUARIO';
        $perfil = 'SIN PERFIL';
        $equipo = 'SIN EQUIPO';
        if (!is_null($request)) {
            $usuario = $request->xg_Cod_Usuario;
            $perfil = $request->xg_Cod_Perfil;
            $equipo = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        }
        $conexion->select('EXEC dbo.gral_sp_insupd_rm_consultas_dni ?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?', [
            $datos['dni'], $datos['codMensaje'], $datos['codMensaje'], $datos['apellidoPaterno'],
            $datos['apellidoMaterno'], $datos['nombres'], $datos['ubigeoContinente'], $datos['ubigeoPais'],
            $datos['ubigeoDepartamento'], $datos['ubigeoProvincia'], $datos['ubigeoDistrito'], $datos['ubigeoCentroPoblado'],
            $datos['continente'], $datos['pais'], $datos['departamento'], $datos['provincia'], $datos['distrito'],
            $datos['centroPoblado'], $datos['direccion'], $datos['sexo'], $datos['fechaNacimiento'], $datos['fechaEmision'],
            $usuario, $perfil, $equipo
        ]);
    }
}
