<?php

namespace App\Http\Controllers\Formatos;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Formatos\FormatoPModel;
use App\Models\Formatos\FormatosPModel as FormatosPModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class FormatoPController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaFormatoP(Request $request): JsonResponse
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

        $perfilModel = new FormatoPModel();
        $params = new stdClass();
        $params->descripcion = $request->get('descripcion') ?? '';
        $params->id = $request->get('id') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarFormatosP($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listaFormatosPUsuario(Request $request): JsonResponse
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

        $perfilModel = new FormatoPModel();
        $params = new stdClass();
        $params->idPerfil = $request->get('idPerfil') ?? '';
        $params->codigoUsuario = $request->get('codigo_usuario') ?? '';
        $params->nombres = $request->get('nombres') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $perfilModel->listarFormatosPUsuario($params);
        return $this->sendResponse(200, true, '', $resultado);
    }


    public function obtenerFormatosP(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idFormatosP']), [
            'idFormatosP' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new FormatoPModel();
        $idPerfil = $request->get('idFormatosP') ?? '';
        [$estado, $mensaje, $resultado] = $perfilModel->obtenerFormatosP($idPerfil);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarFormatosP(Request $request): JsonResponse
    {
        $perfilModel = new FormatoPModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->editarFormatosP($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarFormatosP(Request $request): JsonResponse
    {
        $perfilModel = new FormatoPModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->guardarFormatosP($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularFormatosP(Request $request): JsonResponse
    {
        $perfilModel = new FormatoPModel();
        $idPerfil = $request->post('idFormatosP') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->anularFormatosP($idPerfil, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarFormatosP(Request $request): JsonResponse
    {
        $perfilModel = new FormatoPModel();
        $idPerfil = $request->post('idFormatosP') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->activarFormatosP($idPerfil, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
