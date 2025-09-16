<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Mantenimiento\listarModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use stdClass;

class ListarController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listaPerfil(Request $request): JsonResponse
    {
        $params = new stdClass();
        $params->codigo = $request->get('codigo') ?? '';
        $params->descripcion = $request->get('descripcion') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;

        $generalModel = new listarModel();
        $resultado = $generalModel->listaPerfil($params);
        return $this->sendResponse(200, true, '', $resultado);
    }


}
