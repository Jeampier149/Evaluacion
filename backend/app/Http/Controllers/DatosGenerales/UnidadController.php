<?php

namespace App\Http\Controllers\DatosGenerales;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\DatosGenerales\UnidadModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class UnidadController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listarUnidad(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['descripcion', 'coordinador', 'estado', 'pagina', 'longitud']), [
            'descripcion' => 'nullable|string',
            'coordinador' => 'nullable|string',
            'estado' => 'nullable|string',
            'pagina' => 'required|integer',
            'longitud' => 'required|integer'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $unidadModel = new UnidadModel();
        $params = new stdClass();
        $params->descripcion = $request->get('descripcion') ?? '';
        $params->coordinador= $request->get('coordinador') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $unidadModel->listarUnidad($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listarEmpleados(Request $request): JsonResponse
    {

        $unidadModel = new UnidadModel();
        $resultado = $unidadModel->listarEmpleados();
        return $this->sendResponse(200, true, '', $resultado);
    }


    public function obtenerUnidad(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idUnidad']), [
            'idUnidad' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $unidadModel = new UnidadModel();
        $idUnidad = $request->get('idUnidad') ?? '';
        [$estado, $mensaje, $resultado] = $unidadModel->obtenerUnidad($idUnidad);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarUnidad(Request $request): JsonResponse
    {
        $unidadModel = new UnidadModel();
        $params = new stdClass();
        $params->id = $request->post('codigo') ?? '';
        $params->unidad = $request->post('unidad') ?? '';
        $params->empleado = $request->post('empleado') ?? '';
        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $unidad = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $unidadModel->editarUnidad($params, $usuario, $unidad, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarUnidad(Request $request): JsonResponse
    {
        $unidadModel = new UnidadModel();
        $params = new stdClass();
        $params->unidad = $request->post('unidad') ?? '';
        $params->empleado = $request->post('empleado') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $unidad = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $unidadModel->guardarUnidad($params, $usuario, $unidad, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularUnidad(Request $request): JsonResponse
    {
        $unidadModel = new UnidadModel();
        $idPerfil = $request->post('idUnidad') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $unidad = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $unidadModel->anularUnidad($idPerfil, $motivo, $usuario, $unidad, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarUnidad(Request $request): JsonResponse
    {
        $unidadModel = new UnidadModel();
        $idPerfil = $request->post('idUnidad') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $unidad = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $unidadModel->activarUnidad($idPerfil, $usuario, $unidad, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
