<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Evaluacion\PeriodoModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class PeriodoController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listarPeriodo(Request $request): JsonResponse
    {
        $validacion = Validator::make($request->only(['id', 'descripcion', 'estado', 'pagina', 'longitud']), [
            'id' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'estado' => 'nullable|string',
            'pagina' => 'required|integer',
            'longitud' => 'required|integer'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new PeriodoModel();
        $params = new stdClass();
        $params->descripcion = $request->get('descripcion') ?? '';
        $params->id = $request->get('id') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->desde = $request->get('desde') ?? '';
        $params->hasta = $request->get('hasta') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarPeriodo($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

  
    public function obtenerPeriodo(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idPeriodo']), [
            'idPeriodo' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new PeriodoModel();
        $idPerfil = $request->get('idPeriodo') ?? '';
        [$estado, $mensaje, $resultado] = $perfilModel->obtenerPeriodo($idPerfil);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarPeriodo(Request $request): JsonResponse
    {
        $perfilModel = new PeriodoModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->editarPeriodo($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarPeriodo(Request $request): JsonResponse
    {
        $perfilModel = new PeriodoModel();
        $params = new stdClass();
        $params->id= $request->post('id') ?? '';
        $params->año= $request->post('ano') ?? '';
        $params->semestre= $request->post('semestre') ?? '';
        $params->nombre = $request->post('nombre') ?? '';
        $params->desde = $request->post('desde') ?? '';
        $params->hasta = $request->post('hasta') ?? '';
        $params->dividir= $request->post('dividir') ?? '';
        $params->multiplicar = $request->post('multiplicar') ?? '';
        $params->factor_asistencia = $request->post('factor_asistencia') ?? '';
        $user = $request->user();
        $usuario = $user->username;
        $perfil = $user->id_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->guardarPeriodo($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }
        public function generarFormato(Request $request): JsonResponse
    {
        $perfilModel = new PeriodoModel();
        $idPeriodo = $request->post('idPeriodo') ?? '';
        $user = $request->user();
        $usuario = $user->cc_empleado;
        $perfil = $user->id_perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->generarFormato($idPeriodo,$usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }


    public function anularPeriodo(Request $request): JsonResponse
    {
        $perfilModel = new PeriodoModel();
        $idPerfil = $request->post('idPeriodo') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->anularPeriodo($idPerfil, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarPeriodo(Request $request): JsonResponse
    {
        $perfilModel = new PeriodoModel();
        $idPerfil = $request->post('idPeriodo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->activarPeriodo($idPerfil, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
