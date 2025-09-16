<?php

namespace App\Http\Controllers\Formatos;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Formatos\FormatoCriterioModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class FormatoCriterioController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaFormatoCriterio(Request $request): JsonResponse
    {

        $perfilModel = new FormatoCriterioModel();
        $params = new stdClass();
        $params->factor = $request->get('factor') ?? '';
        $resultado = $perfilModel->listarFormatoCriterio($params);
        return $this->sendResponse(200, true, '', $resultado);
    }
        public function listaFactor(Request $request): JsonResponse
    {

        $perfilModel = new FormatoCriterioModel();
        $params = new stdClass();
        $params->categoria = $request->get('categoria') ?? '';
        $resultado = $perfilModel->listarFactor($params);
        return $this->sendResponse(200, true, '', $resultado);
    }


   


    public function obtenerFormatoCriterio(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idFormatoCriterio']), [
            'idFormatoCriterio' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new FormatoCriterioModel();
        $idPerfil = $request->get('idFormatoCriterio') ?? '';
        [$estado, $mensaje, $resultado] = $perfilModel->obtenerFormatoCriterio($idPerfil);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarFormatoCriterio(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCriterioModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->editarFormatoCriterio($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarFormatoCriterio(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCriterioModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->guardarFormatoCriterio($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularFormatoCriterio(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCriterioModel();
        $idPerfil = $request->post('idFormatoCriterio') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->anularFormatoCriterio($idPerfil, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarFormatoCriterio(Request $request): JsonResponse
    {
        $perfilModel = new FormatoCriterioModel();
        $idPerfil = $request->post('idFormatoCriterio') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->activarFormatoCriterio($idPerfil, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
