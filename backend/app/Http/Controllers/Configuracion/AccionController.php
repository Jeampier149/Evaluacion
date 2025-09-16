<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Configuracion\AccionModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AccionController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaAccionXMenu(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idMenu']), [
            'idMenu' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $accionModel = new AccionModel();
        $idMenu = $request->get('idMenu') ?? 0;
        $resultado = $accionModel->listarAccionXMenu($idMenu);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function guardarAccion(Request $request): JsonResponse
    {
        $accionModel = new AccionModel();
        $idMenu = $request->post('idMenu') ?? 0;
        $descripcion = $request->post('descripcionAccion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $accionModel->guardarAccion($idMenu, $descripcion, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularAccion(Request $request): JsonResponse
    {
        $accionModel = new AccionModel();
        $idAccion = $request->post('id') ?? '';
        $descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $accionModel->anularAccion($idAccion, $descripcion, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
