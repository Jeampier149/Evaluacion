<?php

namespace App\Http\Controllers\DatosGenerales;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\DatosGenerales\ServicioModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class ServicioController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listarServicio(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['servicio','unidad', 'coordinador', 'estado', 'pagina', 'longitud']), [
            'descripcion' => 'nullable|string',
            'coordinador' => 'nullable|string',
            'estado' => 'nullable|string',
            'pagina' => 'required|integer',
            'longitud' => 'required|integer'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $servicioModel = new ServicioModel();
        $params = new stdClass();
        $params->servicio = $request->get('servicio') ?? '';
        $params->unidad = $request->get('unidad') ?? '';
        $params->coordinador= $request->get('coordinador') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $servicioModel->listarServicio($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listarEmpleados(Request $request): JsonResponse
    {

        $servicioModel = new ServicioModel();
        $resultado = $servicioModel->listarEmpleados();
        return $this->sendResponse(200, true, '', $resultado);
    }


    public function obtenerServicio(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idServicio']), [
            'idServicio' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $servicioModel = new ServicioModel();
        $idServicio = $request->get('idServicio') ?? '';
        [$estado, $mensaje, $resultado] = $servicioModel->obtenerServicio($idServicio);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarServicio(Request $request): JsonResponse
    {
        $servicioModel = new ServicioModel();
        $params = new stdClass();
        $params->id = $request->post('codigo') ?? '';
        $params->servicio = $request->post('servicio') ?? '';
        $params->empleado = $request->post('empleado') ?? '';
        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $servicio = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $servicioModel->editarServicio($params, $usuario, $servicio, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarServicio(Request $request): JsonResponse
    {
        $servicioModel = new ServicioModel();
        $params = new stdClass();
        $params->servicio = $request->post('servicio') ?? '';
        $params->empleado = $request->post('empleado') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $servicio = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $servicioModel->guardarServicio($params, $usuario, $servicio, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularServicio(Request $request): JsonResponse
    {
        $servicioModel = new ServicioModel();
        $idPerfil = $request->post('idServicio') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $servicio = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $servicioModel->anularServicio($idPerfil, $motivo, $usuario, $servicio, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarServicio(Request $request): JsonResponse
    {
        $servicioModel = new ServicioModel();
        $idPerfil = $request->post('idServicio') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $servicio = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $servicioModel->activarServicio($idPerfil, $usuario, $servicio, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
