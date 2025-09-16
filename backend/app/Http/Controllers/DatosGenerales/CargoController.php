<?php

namespace App\Http\Controllers\DatosGenerales;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\DatosGenerales\CargoModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class CargoController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaCargos(Request $request): JsonResponse
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

        $perfilModel = new CargoModel();
        $params = new stdClass();
        $params->descripcion = $request->get('descripcion') ?? '';
        $params->id = $request->get('id') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarCargo($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listaCargoUsuario(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idPerfil', 'codigo_usuario', 'nombre', 'pagina', 'longitud']), [
            'idPerfil' => 'nullable|string',
            'codigo_usuario' => 'nullable|string',
            'nombre' => 'nullable|string',
            'pagina' => 'required|integer',
            'longitud' => 'required|integer'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new CargoModel();
        $params = new stdClass();
        $params->idPerfil = $request->get('idPerfil') ?? '';
        $params->codigoUsuario = $request->get('codigo_usuario') ?? '';
        $params->nombres = $request->get('nombres') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarCargoUsuario($params);
        return $this->sendResponse(200, true, '', $resultado);
    }


    public function obtenerCargo(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idCargo']), [
            'idCargo' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new CargoModel();
        $idPerfil = $request->get('idCargo') ?? '';
        [$estado, $mensaje, $resultado] = $perfilModel->obtenerCargo($idPerfil);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarCargo(Request $request): JsonResponse
    {
        $perfilModel = new CargoModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->editarCargo($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarCargo(Request $request): JsonResponse
    {
        $perfilModel = new CargoModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->guardarCargo($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularCargo(Request $request): JsonResponse
    {
        $perfilModel = new CargoModel();
        $idPerfil = $request->post('idCargo') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->anularCargo($idPerfil, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarCargo(Request $request): JsonResponse
    {
        $perfilModel = new CargoModel();
        $idPerfil = $request->post('idCargo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->activarCargo($idPerfil, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
