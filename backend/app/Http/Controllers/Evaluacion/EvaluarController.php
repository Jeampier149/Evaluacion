<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Evaluacion\EvaluarModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use stdClass;

class EvaluarController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listarEvaluar(Request $request): JsonResponse
    {
        $validacion = Validator::make($request->only(['periodo', 'pagina', 'longitud']), [

            'periodo' => 'nullable|string',

        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new EvaluarModel();
        $params = new stdClass();
        $params->periodo = $request->get('periodo') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->empleado = $request->get('empleado') ?? '';
        $params->categoria = $request->get('categoria') ?? '';
        $params->unidad = $request->get('unidad') ?? '';
        $params->servicio = $request->get('servicio') ?? '';
        $params->periodo = $request->get('periodo') ?? '';
        $params->cargo = $request->get('cargo') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;
        $user = $request->user();
        $codigo = $user->cc_empleado;
        $perfil = $user->id_perfil;
        $resultado = $perfilModel->listarEvaluar($params, $codigo, $perfil);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listarPeriodos(Request $request): JsonResponse
    {



        $perfilModel = new EvaluarModel();
        $params = new stdClass();
        $resultado = $perfilModel->listarPeriodo($params);
        return $this->sendResponse(200, true, '', $resultado);
    }


    public function listarEvalFormF(Request $request): JsonResponse
    {
        $params = new stdClass();
        $params->periodo = $request->get('periodo') ?? '';
        $params->idEmpleado = $request->get('idEmpleado') ?? '';
        $params->categoria = $request->get('categoria') ?? '';
        $perfilModel = new EvaluarModel();
        $resultado = $perfilModel->listarEvalFormF($params);
        return $this->sendResponse(200, true, '', $resultado);
    }
    public function generarPdfEval(Request $request)
    {
        $params = new stdClass();
        $params->periodo = $request->get('periodo') ?? '';
        $params->idEmpleado = $request->get('idEmpleado') ?? '';
        $params->categoria = $request->get('categoria') ?? '';
        $perfilModel = new EvaluarModel();
        $resultado = $perfilModel->generarPdfEval($params);
        // echo '<pre>';
        // var_dump($resultado);
        // echo '</pre>';
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
        ]);

        $mpdf->SetDisplayMode('fullpage');

        $html = $this->getHtmlContent($resultado);

        // Output the PDF
        $css = file_get_contents(resource_path('css\\evaluacion\\reporteEval.css')); // css
        $mpdf->WriteHTML($css, 1);
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }
    private function getHtmlContent($data)
    {
        $info = $data['info_evaluacion'];
        $factores = $data['factores'];

        // Obtener semestre y año del periodo
        $semestre_anio = $info['cc_periodo'];

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Evaluación de Desempeño</title>
        </head>
        <body>
            <div class="header">
                <h2>Hospital Nacional Docente Madre Niño "San Bartolome"</h2>
                <h3>EVALUACION DE DESEMPEÑO Y CONDUCTA LABORAL</h3>
                <h4>Personal PROFESIONAL</h4>
            </div>
            
            <table class="enc">
                <tr>
                    <td width="30%">Semestre - Año:</td>
                    <td width="30%">' . $semestre_anio . '</td>
                    <td width="20%">Fecha del Evaluador</td>
                    <td width="20%">' . date('d/m/Y') . '</td>
                </tr>
        
            </table>
            
            <table class="enc">
                <tr>
                    <td width="30%">Apellidos y Nombres:</td>
                    <td width="70%">' . $info['nombre_evaluado'] . '</td>
                </tr>
                <tr>
                    <td>Cargo:</td>
                    <td>[CARGO]</td> <!-- Deberías tener este dato en tu BD -->
                </tr>
                <tr>
                    <td>Dirección/Oficina:</td>
                    <td>[DIRECCIÓN/OFICINA]</td> <!-- Deberías tener este dato en tu BD -->
                </tr>
            </table>
            
            <table>
                <thead class="ct">
                    <tr>
                        <th width="20%">FACTORES ÚNICOS</th>
                        <th width="13%">I</th>
                        <th width="13%">II</th>
                        <th width="13%">III</th>
                        <th width="13%">IV</th>
                        <th width="13%">V</th>
                        <th width="13%">Califi. del Evaluador</th>
                    </tr>
                </thead>
                <tbody>
                    ' . $this->getFactoresRows($factores) . '
                    <tr>
                        <td colspan="6" class="text-right bold">Total</td>
                        <td class="text-center">' . $info['ni_puntaje_dcl'] . '</td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top: 10px;">
                <div>Nombre y Apellido del Evaluador: ' . $info['nombre_evaluador'] . '</div>
                <div>Cargo y Nivel del Evaluador: [CARGO_EVALUADOR]</div> <!-- Deberías tener este dato en tu BD -->
            </div>
            
           
        </body>
        </html>';
    }

    private function getFactoresRows($factores)
    {
        $html = '';

        foreach ($factores as $factor) {
            $html .= '
            <tr>
                <td>
                    <div class="factor-name">' . trim($factor['factor']) . ':</div>
                    <div class="factor-desc">' . $factor['descripcion_factor'] . '</div>
                </td>';

            // Agregar las 5 columnas de criterios (I a V)
            for ($i = 0; $i < 5; $i++) {
                if (isset($factor['criterios'][$i])) {
                    $html .= '<td>' . $factor['criterios'][$i]['descripcion_factor_criterio'] . '</td>';
                } else {
                    $html .= '<td></td>';
                }
            }

            $html .= '
                <td class="text-center">' . $factor['puntaje_asig'] . '</td>

            </tr>
            <tr>
                <td colspan="7"></td>
            </tr>';
        }

        return $html;
    }



    public function listarEvalForm(Request $request): JsonResponse
    {


        $perfilModel = new EvaluarModel();
        $idCategoria = $request->get('idCategoria') ?? '';
        $resultado = $perfilModel->listarEvalForm($idCategoria);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function listarHistorial(Request $request): JsonResponse
    {
        $validacion = Validator::make($request->only(['periodo', 'pagina', 'longitud']), [

            'periodo' => 'nullable|string',

        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $perfilModel = new EvaluarModel();
        $params = new stdClass();
        $idEmpleado = $request->get('idEmpleado') ?? '';
        $params->periodo  = $request->get('periodo') ?? '';
        $params->cargo    = $request->get('cargo') ?? '';
        $params->categoria = $request->get('categoria') ?? '';
        $params->servicio = $request->get('servicio') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;

        $resultado = $perfilModel->listarHistorial($params, $idEmpleado);
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
