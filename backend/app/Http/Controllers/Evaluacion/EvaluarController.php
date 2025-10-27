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
use App\Models\Utilidades\Compresor7ZipModel;
use App\Models\Utilidades\FTPModel;
use App\Models\Utilidades\RefirmaModel;

class EvaluarController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['guardarLoteFirmado']);
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
     public function listarEvaluarRevisor(Request $request): JsonResponse
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
        $resultado = $perfilModel->listarEvaluarRevisor($params, $codigo, $perfil);
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
    public function obtenerArgumentosFirma(Request $request) {
        $datos = $request->post('data');
        $periodo = $request->post('periodo');
        $compresor = new Compresor7ZipModel();
            $nombre7z = 'comprimido_' . time();
            $carpetaTemp = 'carpeta_' . time();
            mkdir(storage_path() . '/app/temp/' . $carpetaTemp);
            $compresor->crearComprimido($nombre7z, storage_path('app/public'));
           
            foreach ($datos as $dato) {

                $params = new stdClass();
                $params->periodo = $dato['periodo'] ?? '';
                $params->idEmpleado = $dato['idEmpleado'] ?? '';
                $params->categoria = $dato['categoria'] ?? '';
                $perfilModel = new EvaluarModel();
                $resultado = $perfilModel->generarPdfEval($params);
                
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
                $nombre = $params->idEmpleado . '_' . $params->periodo . '.pdf';
                $css = file_get_contents(resource_path('css\\evaluacion\\reporteEval.css')); // css
                $mpdf->WriteHTML($css, 1);
                $mpdf->WriteHTML($html);
                $mpdf->Output(storage_path() . '/app/temp/' . $carpetaTemp . '/' . $nombre, 'F');
            }
            
            $compresor->agregarCarpeta(storage_path() . '/app/temp/' . $carpetaTemp . '/');
            $compresor->cerrarComprimido();
            
            $pdfs = scandir(storage_path() . '/app/temp/' . $carpetaTemp);
            foreach ($pdfs as $pdf) {
                if ($pdf === '.' or $pdf === '..') {
                    continue;
                }
                unlink(storage_path() . '/app/temp/' . $carpetaTemp . '/' . $pdf);
            }
            rmdir(storage_path() . '/app/temp/' . $carpetaTemp);

            $archivo = $nombre7z . '.7z';

            $archivoF = str_replace('.7z', '[FP].7z', $archivo);
            // Generar argumento para el firmador
            $refirmaModel = new RefirmaModel();
            $refirmaModel
                ->setIsHttps(env('APP_ENV') === 'production' ? true : false)
                ->setPosX(30)
                ->setPosY(650)
                ->setPagina(1)
                ->setIsVisible(true);
            $argumento = $refirmaModel->obtenerArgumentosZIPInvoker($archivo,
                $archivoF,
                '/storage/',
                '/api/evaluacion/evaluar/guardar-lote-firmado?archivo=' . $archivo  .'&periodo=' .$periodo  );
        return $this->sendResponse(200, true, 'Argumento', $argumento);

    }
    public function generarPdfEval(Request $request)
    {
        $params = new stdClass();
        $params->periodo = $request->get('periodo') ?? '';
        $params->idEmpleado = $request->get('idEmpleado') ?? '';
        $params->categoria = $request->get('categoria') ?? '';
        $perfilModel = new EvaluarModel();
        $resultado = $perfilModel->generarPdfEval($params);
     
    
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

        $info = $data['info_evaluacion'][0];

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
                    <td>' .$info['cargo'].'</td> 
                </tr>
                <tr>
                    <td>Dirección/Oficina:</td>
                    <td>' .$info['unidad'].'</td> 
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
                        <td class="text-center">'. $info['ni_puntaje_dcl'] . '</td>
                    </tr>
                </tbody>
            </table>
            
            <div style="margin-top: 10px;">
                <div>Nombre y Apellido del Evaluador: ' . $info['nombre_evaluador'] . '</div>
                <div>Cargo y Nivel del Evaluador: ' . $info['cargo_evaluador'] . '
                <div>Recomendaciones y/o sugerencias al Evaluado: ' . $info['ct_recomendacion'] . '</div>
                <div>Cree Ud. que el evaluado mejoraría en su desempeño laboral con algunos cursos de capacitación.: ' . $info['ct_recomendacion'] . '</div>
            
            </div>
            <div style="margin-top: 10px;">
                <div>Del Evaluado:: ' . ($info['cfl_conforme_eva']==1?'<span>Conforme</span>':'<span>No Conforme</span>') . '</div>'
                .($info['cfl_conforme_eva']==1? '<div>Observaciones:'.$info['ct_conforme_eva'].'</div>' :'' ).

            '*Nota: EN CASO DE INCONFORMIDAD, ADJUNTAR LAS OBSERVACIONES Y SUSTENTOS RESPECTIVOS
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
        $periodo = $data['info_evaluacion'][0]['cc_periodo'];
        $empleado = $data['info_evaluacion'][0]['cc_empleado'];
        $ct_recomendacion = $data['info_evaluacion'][0]['ct_recomendacion'];
        $cfl_capacitacion = $data['info_evaluacion'][0]['cfl_capacitacion'];
        $ct_capacitacion = $data['info_evaluacion'][0]['ct_capacitacion'];
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
  
   public function guardarEvaluarRevisor(Request $request)
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
        $periodo = $data['info_evaluacion'][0]['cc_periodo'];
        $empleado = $data['info_evaluacion'][0]['cc_empleado'];
        $ct_recomendacion = $data['info_evaluacion'][0]['ct_recomendacion'];
        $cfl_capacitacion = $data['info_evaluacion'][0]['cfl_capacitacion'];
        $ct_capacitacion = $data['info_evaluacion'][0]['ct_capacitacion'];
        $cfl_evaluador = 1;
        $puntaje_rev = 0;

       foreach ($data['factores'] as $factor) {
            $id_factor_criterio = $factor['id_factor_criterio_revisor'];
            $puntaje_asignado = $factor['puntaje_asig_revisor'];
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
 
  
    public function guardarLoteFirmado(Request $request){
        $archivo = (string)$request->get('archivo');
        $periodo = (string)$request->get('periodo');
        $rutaArchivo = storage_path('app/public/');
        $rutaTemp = storage_path('app/temp/');
        $carpeta = str_replace('.7z', '', $archivo);
        // Borrar comprimido publico
        if (file_exists($rutaArchivo . $archivo)) {
            unlink($rutaArchivo . $archivo);
        }
        if ($_FILES['signed_file']) {
            $nombreTemp = $_FILES['signed_file']['tmp_name'];
            // Descomprimir el archivo
            move_uploaded_file($nombreTemp, storage_path('app/temp/') . $archivo);
            $compresor = new Compresor7ZipModel();
            $compresor->descomprimirArchivo($archivo, $rutaTemp, $rutaTemp . $carpeta);
            // Borrar comprimido
            if (file_exists(storage_path('app/temp/') . $archivo)) {
                unlink(storage_path('app/temp/') . $archivo);
            }
            // //Subir FTP y BD
             $archivos = scandir($rutaTemp . $carpeta);
             foreach ($archivos as $archivo) {
                 if ($archivo === '..' || $archivo === '.') {
                     continue;
                 }

            
                // Extraer del nombre del archivo datos para su guardado
             $split = explode('_', str_replace(['[FP].pdf', '[R].pdf'], '', $archivo));
             $cc_empleado = $split[0];

    
             // Agregar al nombre del archivo el final FP (Firmado)
             $nombre = str_replace(['_' . $cc_empleado . '[FP].pdf', '_' . $cc_empleado . '[R].pdf'], '[FP].pdf', $archivo);
            //Subir FTP
             $ftpModel = new FTPModel();
             rename($rutaTemp . $carpeta . '/' . $archivo, $rutaTemp . $carpeta . '/' . $nombre);
             $flag = $ftpModel->subirArchivo('evalu/'.$periodo, $nombre, $rutaTemp . $carpeta);
             if ($flag) {
                    $resultadoModel = new EvaluarModel();
                    $resultadoModel->guardarEvaluacionFirmado($periodo, $cc_empleado, $nombre);
             }
             //Borrar archivos y carpetas
             unlink($rutaTemp . $carpeta . '/' . $nombre);
         }
         rmdir($rutaTemp . $carpeta);
        }
    }
   
     public function imprimirFichaEvaluacion(Request $request) {
        $validacion = Validator::make($request->only(['periodo', 'archivo']),
        [
            'periodo' => 'required|string',
            'archivo' => 'required|string',
        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $periodo = $request->get('periodo');
        $archivo = $request->get('archivo');


        if ($archivo) {
        $ruta = "evalu/".$periodo;
        
        $ftpModel = new FTPModel();
        $ftpModel->obtenerArchivo($ruta, $archivo, storage_path('app/public/'));

        $filePath = storage_path('app/public/' . $archivo);
        
        // Verificar que el archivo existe
        if (!file_exists($filePath)) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Archivo no encontrado',
                'datos' => null
            ], 404);
        }

        // Devolver el archivo como blob
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $archivo . '"'
        ]);
    }
    }
   


}
