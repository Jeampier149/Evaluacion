<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Evaluacion\EvaluarModel;
use App\Models\Evaluacion\PersonalEvaluarModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use stdClass;

class PersonalEvaluarController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

  public function listarPersonal(Request $request): JsonResponse
    {
        $validacion = Validator::make($request->only(['periodo', 'pagina', 'longitud']), [

            'periodo' => 'nullable|string',

        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new PersonalEvaluarModel();
        $params = new stdClass();

        $params->periodo  = $request->get('periodo') ?? '';
        $params->apellidos = $request->get('apellidos') ?? '';
        $params->nombre = $request->get('nombre') ?? '';
        $params->cargo    = $request->get('cargo') ?? '';
        $params->categoria = $request->get('categoria') ?? '';
        $params->servicio = $request->get('servicio') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;

        $resultado = $perfilModel->listarEmpleados($params);
        return $this->sendResponse(200, true, '', $resultado);
    }
  public function listarPersonalNuevo(Request $request): JsonResponse
    {
        $validacion = Validator::make($request->only(['periodo', 'pagina', 'longitud']), [

            'periodo' => 'nullable|string',

        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new PersonalEvaluarModel();
        $params = new stdClass();

        $params->periodo  = $request->get('periodo') ?? '';
        $params->apellidos = $request->get('apellidos') ?? '';
        $params->nombre = $request->get('nombre') ?? '';
        $params->cargo    = $request->get('cargo') ?? '';
        $params->categoria = $request->get('categoria') ?? '';
        $params->servicio = $request->get('servicio') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;

        $resultado = $perfilModel->listarEmpleadosNuevos($params);
        return $this->sendResponse(200, true, '', $resultado);
    }
  

     public function agregarEmpEval(Request $request): JsonResponse
    {
        $perfilModel = new PersonalEvaluarModel();
        $data = $request->post('data') ?? '';
        $tipo = $request->post('tipo') ?? '';
        $periodo = $request->post('periodo') ?? '';

        $user = $request->user();
        $usuario = $user->cc_empleado;
        $perfil = $user->id_perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        $equipo = substr($equipo, 0, 10);
        if (count($data) > 0) {
            foreach($data as $valor){
                 [$estado, $mensaje, $resultado] = $perfilModel->agregarEmpEval($tipo,$periodo,$valor, $usuario, $perfil, $equipo);
            }
       }else{
        [$estado, $mensaje, $resultado] = $perfilModel->agregarEmpEval($tipo,$periodo,'', $usuario, $perfil, $equipo);
       }
       
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }


    public function obtenerDataEval(Request $request): JsonResponse
    {
        $perfilModel = new PersonalEvaluarModel();

        $id = $request->post('id') ?? '';
        $periodo = $request->post('periodo') ?? '';
        $resultado= $perfilModel->obtenerDataEval($id,$periodo);
        return $this->sendResponse(200, '1', 'exito', $resultado);
    }

    
  public function obtenerSelects(): JsonResponse
    {
        $perfilModel = new PersonalEvaluarModel();
        $resultado = $perfilModel->obtenerSelects();
        return $this->sendResponse(200, '1', 'datos obtenidos', $resultado);
    }



public function editarPersonalEval(Request $request): JsonResponse
    {
        $validacion = Validator::make($request->only(['periodo', 'empleado']), [

            'periodo' => 'required|string',
            'empleado' => 'required|string',


        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new PersonalEvaluarModel();
        $params = new stdClass();

        $periodo  = $request->post('periodo') ;
        $empleado=$request->post('empleado');

        $params->idUnidad = $request->post('idUnidad') ?? '';
        $params->idServicio= $request->post('idServicio') ?? '';
        $params->idCargo    = $request->post('idCargo') ?? '';
        $params->idCategoria = $request->post('idCategoria') ?? '';
        $params->idCondicion = $request->post('idCondicion') ?? '';
        $params->idNivel= $request->post('idNivel') ?? '';
        $params->evaluador= $request->post('evaluador') ?? '';
        $params->factor_asistencia= $request->post('factor_asistencia') ?? '';
        $params->puntaje_asistencia= $request->post('puntaje_asistencia') ?? '';
        $params->revisor= $request->post('revisor') ?? '';
        $params->idEstado= $request->post('idEstado') ?? '';
        $equipo = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $user = $request->user();
        $usuario = $user->cc_empleado;
        $equipo = strtoupper(preg_replace('/(.sbdomain.local)/', "", gethostbyaddr($equipo)));
        $resultado = $perfilModel->editarPersonalEval($params ,$periodo,$empleado,$usuario,$equipo);
        return $this->sendResponse(200, true, '', $resultado);
    }
  








    public function anularEvaluar(Request $request): JsonResponse
    {
        $perfilModel = new EvaluarModel();
        $idPerfil = $request->post('idEvaluar') ?? '';
        $motivo = $request->post('motivo') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->anularEvaluar($idPerfil, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }

    public function activarEvaluar(Request $request): JsonResponse
    {
        $perfilModel = new EvaluarModel();
        $idPerfil = $request->post('idEvaluar') ?? '';

        $user = $request->user();
        $usuario = $user->xg_Cod_Usuario;
        $perfil = $user->xg_Cod_Perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->activarEvaluar($idPerfil, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }
}
