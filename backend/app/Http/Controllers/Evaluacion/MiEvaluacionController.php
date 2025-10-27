<?php

namespace App\Http\Controllers\Evaluacion;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Evaluacion\EvaluarModel;
use App\Models\Evaluacion\MiEvaluacionModel;
use App\Models\Utilidades\Compresor7ZipModel;
use App\Models\Utilidades\FTPModel;
use App\Models\Utilidades\RefirmaModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use stdClass;

class MiEvaluacionController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listarPeriodosMiEvaluacion(Request $request): JsonResponse
    {

        $perfilModel = new MiEvaluacionModel();
        $user = $request->user();
        $empleado = $user->cc_empleado;
        $resultado = $perfilModel->listarPeriodosMiEvaluacion($empleado);
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
   

      public function guardarConformidad(Request $request)
    {
        $perfilModel = new MiEvaluacionModel();
        $data = $request->post('data');
        $user = $request->user();
        $usuario = $user->cc_empleado;
        $equipo = Str::upper(explode(':', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
         $equipo = substr($equipo, 0, 10); // TRUNCAR A 10 CARACTERES
        if (is_string($data)) {
            $data = json_decode($data, true); // Agregar true para convertir a array
        }
        // Acceder como array en lugar de objeto
        $periodo =$request->post('periodo');;
        $empleado = $data['info_evaluacion'][0]['cc_empleado'];
        $cfl_conforme = $data['info_evaluacion'][0]['cfl_conforme_eva'];
        $ct_conforme = $data['info_evaluacion'][0]['ct_conforme_eva'];

                 
            [$estado, $mensaje, $resultado] = $perfilModel->guardarConformidad(
                $periodo,
                $empleado,
                $cfl_conforme,
                $ct_conforme,
                $usuario,
                $equipo
            );
        

        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }
     public function obtenerArgumentosFirma(Request $request) {
        $empleado = $request->post('empleado');
        $periodo = $request->post('periodo');
        $archivoFTP = $request->post('archivo');
        $compresor = new Compresor7ZipModel();
            $nombre7z = 'comprimido_' . time();
            $carpetaTemp = 'carpeta_' . time();
            mkdir(storage_path() . '/app/temp/' . $carpetaTemp);
            $compresor->crearComprimido($nombre7z, storage_path('app/public'));           
        if ($archivoFTP) {
        $ruta = "evalu/".$periodo;
        
        $ftpModel = new FTPModel();
        $ftpModel->obtenerArchivo($ruta, $archivoFTP, storage_path('app/temp/'. $carpetaTemp ));

        $filePath = storage_path('app/temp/'. $carpetaTemp );
        
        // Verificar que el archivo existe
        if (!file_exists($filePath)) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Archivo no encontrado',
                'datos' => null
            ], 404);
        }
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
                ->setPosX(230)
                ->setPosY(650)
                ->setPagina(1)
                ->setIsVisible(true);
            $argumento = $refirmaModel->obtenerArgumentosZIPInvoker($archivo,
                $archivoF,
                '/storage/',
                '/api/evaluacion/miEvaluacion/guardar-pdf-firmado-conformidad?archivo=' . $archivo  .'&periodo=' .$periodo   .'&empleado=' .$empleado);
        return $this->sendResponse(200, true, 'Argumento', $argumento);


    }
    public function guardarPdfFirmado(Request $request){
        $archivo = (string)$request->get('archivo');
        $periodo = (string)$request->get('periodo');
        $empleado = (string)$request->get('empleado');
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
                    // $resultadoModel = new EvaluarModel();
                    // $resultadoModel->guardarEvaluacionFirmado($periodo, $cc_empleado, $nombre);
             }
             //Borrar archivos y carpetas
             unlink($rutaTemp . $carpeta . '/' . $nombre);
         }
         rmdir($rutaTemp . $carpeta);
        }
    }
  
}
