<?php

namespace App\Http\Controllers\Service;
use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Service\ReniecModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class ReniecController extends JSONResponseController
{
    public function __construct()
    {
      //  $this->middleware('auth:sanctum');
    }

    public function buscarDNI(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only(['dni']), ['dni' => 'required|string|digits:8']);
        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }
        $dni = $request->get('dni');
        $servicioReniec = new ReniecModel();
        // Buscar datos en local
        $servicioReniec->buscarDatosEnLocal($dni);
        if($servicioReniec->getEstado() === 1){
            return $this->sendResponse(200, true, '', $servicioReniec->getDatos());
        }else{
            // Buscar datos en linea
            $servicioReniec->buscarDatosEnLinea($dni);
            if ($servicioReniec->getEstado() === 1) {
                return $this->sendResponse(200, true, '', $servicioReniec->getDatos());
            } else {
                return $this->sendResponse(200, false, $servicioReniec->getMensaje());
            }
        }
    }
}
