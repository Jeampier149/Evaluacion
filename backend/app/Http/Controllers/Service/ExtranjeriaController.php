<?php

namespace App\Http\Controllers\Service;

use Illuminate\Http\Request;
use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Service\ExtranjeriaModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ExtranjeriaController extends JSONResponseController
{
    public function __construct()
    {
    //    $this->middleware('auth:sanctum');
    }

    public function buscarCarneExtranjeria(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['documento']), ['documento' => 'required|string']);
        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }
        $documento = $request->get('documento');
        $pideModel = new ExtranjeriaModel();
        // Buscar datos en local
        $pideModel->buscarDatosEnLocal($documento);
        if($pideModel->getEstado() === 1){
            return $this->sendResponse(200, true, '', $pideModel->getDatos());
        }else{
            // Buscar datos en linea
            $pideModel->buscarDatosEnLinea($documento);
            if ($pideModel->getEstado() === 1) {
                return $this->sendResponse(200, true, '', $pideModel->getDatos());
            } else {
                return $this->sendResponse(200, false, $pideModel->getMensaje());
            }
        }
    }
}
