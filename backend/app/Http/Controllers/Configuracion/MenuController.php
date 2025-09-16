<?php

namespace App\Http\Controllers\Configuracion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Configuracion\MenuModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

class MenuController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaMenuCombo(): JsonResponse
    {
        $menuModel = new MenuModel();
        $resultado = $menuModel->listarMenuCombo();
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listaMenu(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['nombre', 'padre', 'orden', 'estado', 'pagina', 'longitud']), [
            'nombre' => 'nullable|string',
            'padre' => 'nullable|string',
            'orden' => 'nullable|string',
            'estado' => 'nullable|string',
            'pagina' => 'required|integer',
            'longitud' => 'required|integer'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $menuModel = new MenuModel();
        $params = new stdClass();
        $params->nombre = $request->get('nombre') ?? '';
        $params->padre = $request->get('padre') ?? '';
        $params->orden = $request->get('orden') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $resultado = $menuModel->listarMenu($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function obtenerMenu(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['idMenu']), [
            'idMenu' => 'required|string'
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $menuModel = new MenuModel();
        $idMenu = $request->post('idMenu') ?? '';
        [$estado, $mensaje, $resultado] = $menuModel->obtenerMenu($idMenu);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function editarMenu(Request $request): JsonResponse
    {
        $menuModel = new MenuModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->nombre = $request->post('nombre') ?? '';
        $params->padre = $request->post('padre') ?? '';
        $params->orden = $request->post('orden') ?? 0;
        $params->icono = $request->post('icono') ?? '';
        $params->ruta = $request->post('ruta') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $menuModel->editarMenu($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function guardarMenu(Request $request): JsonResponse
    {
        $menuModel = new MenuModel();
        $params = new stdClass();
        $params->nombre = $request->post('nombre') ?? '';
        $params->padre = $request->post('padre') ?? '';
        $params->orden = $request->post('orden') ?? 0;
        $params->icono = $request->post('icono') ?? '';
        $params->ruta = $request->post('ruta') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $menuModel->guardarMenu($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function anularMenu(Request $request): JsonResponse
    {
        $menuModel = new MenuModel();
        $idMenu = $request->post('id') ?? '';
        $descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $menuModel->anularMenu($idMenu, $descripcion, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarMenu(Request $request): JsonResponse
    {
        $menuModel = new MenuModel();
        $idMenu = $request->post('id') ?? '';
        $descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $menuModel->activarMenu($idMenu, $descripcion, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

}
