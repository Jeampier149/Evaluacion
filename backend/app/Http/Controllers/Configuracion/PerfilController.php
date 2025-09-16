<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Configuracion\PerfilModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class PerfilController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaPerfil(Request $request): JsonResponse
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

        $perfilModel = new PerfilModel();
        $params = new stdClass();
        $params->descripcion = $request->get('descripcion') ?? '';
        $params->id = $request->get('id') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarPerfil($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listaPerfilUsuario(Request $request): JsonResponse
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

        $perfilModel = new PerfilModel();
        $params = new stdClass();
        $params->idPerfil = $request->get('idPerfil') ?? '';
        $params->codigoUsuario = $request->get('codigo_usuario') ?? '';
        $params->nombres = $request->get('nombres') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarPerfilUsuario($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listaPerfilCombo(Request $request): JsonResponse
    {
        $perfilModel = new PerfilModel();
        $resultado = $perfilModel->listarPerfilCombo();
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function obtenerPerfil(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idPerfil']), [
            'idPerfil' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new PerfilModel();
        $idPerfil = $request->get('idPerfil') ?? '';
        [$estado, $mensaje, $resultado] = $perfilModel->obtenerPerfil($idPerfil);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarPerfil(Request $request): JsonResponse
    {
        $perfilModel = new PerfilModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';
        $params->observacion = $request->post('observacion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->editarPerfil($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarPerfil(Request $request): JsonResponse
    {
        $perfilModel = new PerfilModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';
        $params->observacion = $request->post('observacion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->guardarPerfil($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularPerfil(Request $request): JsonResponse
    {
        $perfilModel = new PerfilModel();
        $idPerfil = $request->post('idPerfil') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->anularPerfil($idPerfil, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarPerfil(Request $request): JsonResponse
    {
        $perfilModel = new PerfilModel();
        $idPerfil = $request->post('idPerfil') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->activarPerfil($idPerfil, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
