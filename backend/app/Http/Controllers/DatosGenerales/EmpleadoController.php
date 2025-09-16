<?php

namespace App\Http\Controllers\DatosGenerales;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\DatosGenerales\EmpleadoModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class EmpleadoController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listarEmpleado(Request $request): JsonResponse
    {
        $empleadoModel = new EmpleadoModel();
        $params = new stdClass();
        $params->nombre = $request->get('nombre') ?? '';
        $params->tipo_doc= $request->get('tipo_doc') ?? '';
        $params->num_doc= $request->get('num_doc') ?? '';
        $params->unidad = $request->get('unidad') ?? '';
        $params->servicio= $request->get('servicio') ?? '';
        $params->condicion= $request->get('condicion') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $empleadoModel->listarEmpleado($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listarEmpleados(Request $request): JsonResponse
    {

        $empleadoModel = new EmpleadoModel();
        $resultado = $empleadoModel->listarEmpleados();
        return $this->sendResponse(200, true, '', $resultado);
    }


    public function obtenerEmpleado(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idEmpleado']), [
            'idEmpleado' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $empleadoModel = new EmpleadoModel();
        $idEmpleado = $request->get('idEmpleado') ?? '';
        [$estado, $mensaje, $resultado] = $empleadoModel->obtenerEmpleado($idEmpleado);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarEmpleado(Request $request): JsonResponse
    {
        $empleadoModel = new EmpleadoModel();
        $params = new stdClass();
        $params->id = $request->post('codigo') ?? '';
        $params->empleado = $request->post('empleado') ?? '';
        $params->empleado = $request->post('empleado') ?? '';
        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $empleado = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $empleadoModel->editarEmpleado($params, $usuario, $empleado, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarEmpleado(Request $request): JsonResponse
    {
        $empleadoModel = new EmpleadoModel();
        $params = new stdClass();
        $params->empleado = $request->post('empleado') ?? '';
        $params->empleado = $request->post('empleado') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $empleado = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $empleadoModel->guardarEmpleado($params, $usuario, $empleado, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularEmpleado(Request $request): JsonResponse
    {
        $empleadoModel = new EmpleadoModel();
        $idPerfil = $request->post('idEmpleado') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $empleado = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $empleadoModel->anularEmpleado($idPerfil, $motivo, $usuario, $empleado, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarEmpleado(Request $request): JsonResponse
    {
        $empleadoModel = new EmpleadoModel();
        $idPerfil = $request->post('idEmpleado') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $empleado = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $empleadoModel->activarEmpleado($idPerfil, $usuario, $empleado, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
