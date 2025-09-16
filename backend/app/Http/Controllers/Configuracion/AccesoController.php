<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Configuracion\AccesoModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class AccesoController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaAcceso(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idPerfil', 'idMenu', 'pagina', 'longitud']), [
            'idPerfil' => 'nullable|string',
            'idMenu' => 'nullable|string',
            'pagina' => 'required|integer',
            'longitud' => 'required|integer'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $accesoModel = new AccesoModel();
        $params = new stdClass();
        $params->idPerfil = $request->get('idPerfil') ?? '';
        $params->idMenu = $request->get('idMenu') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $accesoModel->listarAcceso($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function agregarAcceso(Request $request): JsonResponse
    {

        $accesoModel = new AccesoModel();
        $params = new stdClass();
        $params->idPerfil = $request->post('idPerfil') ?? '';
        $params->idMenu = $request->post('idMenu') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $accesoModel->agregarAcceso($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularAcceso(Request $request): JsonResponse
    {
        $accesoModel = new AccesoModel();
        $params = new stdClass();
        $params->idPerfil = $request->post('idPerfil') ?? '';
        $params->idMenu = $request->post('idMenu') ?? '';
        $params->motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $accesoModel->anularAcceso($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
