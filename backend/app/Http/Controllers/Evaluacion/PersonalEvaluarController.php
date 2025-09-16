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
  


    
    public function listarEvalForm(Request $request): JsonResponse
    {


        $perfilModel = new EvaluarModel();
        $idCategoria = $request->get('idCategoria') ?? '';
        $resultado = $perfilModel->listarEvalForm($idCategoria);
        return $this->sendResponse(200, true, '', $resultado);
    }

  

    public function editarEvaluar(Request $request): JsonResponse
    {
        $perfilModel = new EvaluarModel();
        $params = new stdClass();
        $params->id = $request->post('id') ?? '';
        $params->descripcion = $request->post('descripcion') ?? '';

        $user = $request->user();
        $usuario = $user->cc_empleado;
        $perfil = $user->id_perfil;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
        [$estado, $mensaje, $resultado] = $perfilModel->editarEvaluar($params, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }
    public function guardarEvaluar(Request $request)
    {
        $perfilModel = new EvaluarModel();
        $data = $request->post('data');
        $user = $request->user();
        $usuario = $user->cc_empleado;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
         $equipo = substr($equipo, 0, 10); // TRUNCAR A 10 CARACTERES
        if (is_string($data)) {
            $data = json_decode($data, true); // Agregar true para convertir a array
        }

        // Acceder como array en lugar de objeto
        $periodo = $data['info_evaluacion']['cc_periodo'];
        $empleado = $data['info_evaluacion']['cc_empleado'];
        $ct_recomendacion = $data['info_evaluacion']['ct_recomendacion'];
        $cfl_capacitacion = $data['info_evaluacion']['cfl_capacitacion'];
        $ct_capacitacion = $data['info_evaluacion']['ct_capacitacion'];
        $cfl_evaluador = 0;
        $puntaje_rev = 0;

        foreach ($data['factores'] as $factor) {
            $id_factor_criterio = $factor['id_factor_criterio'];
            $puntaje_asignado = $factor['puntaje_asig'];
            $id_factor = $factor['id_factor'];
           
            [$estado, $mensaje, $resultado] = $perfilModel->guardarEvaluar(
                $periodo,
                $empleado,
                $id_factor,
                $id_factor_criterio,
                $cfl_evaluador,
                $puntaje_asignado,
                $puntaje_rev,
                $ct_recomendacion,
                $cfl_capacitacion,
                $ct_capacitacion,
                $usuario,
                $equipo
            );
        }

        return $this->sendResponse(200, $estado, $mensaje, $resultado);
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
