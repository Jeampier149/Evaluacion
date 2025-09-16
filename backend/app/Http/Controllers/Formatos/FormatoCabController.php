<?php

namespace App\Http\Controllers\Formatos;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Formatos\FormatoCabModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class FormatoCabController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaFormatoCab(Request $request): JsonResponse
    {

        $perfilModel = new FormatoCabModel();
        $params = new stdClass();
        $params->categoria = $request->get('categoria') ?? '';
        $resultado = $perfilModel->listarFormatoCab($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listaFormatoCabUsuario(Request $request): JsonResponse
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

        $perfilModel = new FormatoCabModel();
        $params = new stdClass();
        $params->idPerfil = $request->get('idPerfil') ?? '';
        $params->codigoUsuario = $request->get('codigo_usuario') ?? '';
        $params->nombres = $request->get('nombres') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarFormatoCabUsuario($params);
        return $this->sendResponse(200, true, '', $resultado);
    }


    public function obtenerFormatoCab(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idFormatoCab']), [
            'idFormatoCab' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new FormatoCabModel();
        $idPerfil = $request->get('idFormatoCab') ?? '';
        [$estado, $mensaje, $resultado] = $perfilModel->obtenerFormatoCab($idPerfil);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarFormatoCab(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCabModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->editarFormatoCab($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarFormatoCab(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCabModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->guardarFormatoCab($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularFormatoCab(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCabModel();
        $idPerfil = $request->post('idFormatoCab') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->anularFormatoCab($idPerfil, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarFormatoCab(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCabModel();
        $idPerfil = $request->post('idFormatoCab') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->activarFormatoCab($idPerfil, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
