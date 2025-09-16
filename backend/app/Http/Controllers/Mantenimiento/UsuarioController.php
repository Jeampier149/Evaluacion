<?php

namespace App\Http\Controllers\Mantenimiento;

use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Mantenimiento\FichaUsuarioModel;
use App\Models\Mantenimiento\listarModel;
use App\Models\Mantenimiento\UsuarioModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use stdClass;

class UsuarioController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function listarUsuario(Request $request): JsonResponse
    {
        $params = new stdClass();
        $params->codigo = $request->get('codigo') ?? '';
        $params->departamento = $request->get('departamento') ?? '';
        $params->nombres = $request->get('nombres') ?? '';
        $params->perfil = $request->get('perfil') ?? '';
        $params->desPerfil = $request->get('descripcionPerfil') ?? '';
        $params->estado = $request->get('estado') ?? '';
        $params->longitud = $request->get('longitud') ?? 15;
        $params->pagina = $request->get('pagina') ?? 1;

        $usuarioModel = new UsuarioModel();
        $resultado = $usuarioModel->listarUsuario($params);
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function guardarUsuario(Request $request): JsonResponse
    {
        $fichaUsuario = new FichaUsuarioModel();
        $fichaUsuario->setCodigo($request->post('codigo') ?? '')
            ->setNombres($request->post('nombres') ?? '')
            ->setPerfil($request->post('perfil') ?? '');


        [$estado, $errores] = $fichaUsuario->validarFicha();

        if ($estado) {
            [$usuario, $perfil, $equipo] = $this->getHost($request);
            $usuarioModel = new UsuarioModel();
            [$estado, $mensaje, $resultados] = $usuarioModel->guardarUsuario($fichaUsuario, $usuario, $perfil, $equipo);
            return $this->sendResponse(200, $estado, $mensaje, $resultados);
        } else {
            return $this->sendResponse(200, false, 'Existen errores.', $errores);
        }
    }

    public function editarUsuario(Request $request): JsonResponse
    {
        $fichaUsuario = new FichaUsuarioModel();
        $fichaUsuario->setCodigo($request->post('codigo') ?? '')
            ->setNombres($request->post('nombres') ?? '')
            ->setPerfil($request->post('perfil') ?? '');

        [$estado, $errores] = $fichaUsuario->validarFicha();
        if ($estado) {
            [$usuario, $perfil, $equipo] = $this->getHost($request);
            $usuarioModel = new UsuarioModel();
            [$estado, $mensaje, $resultados] = $usuarioModel->editarUsuario($fichaUsuario, $usuario, $perfil, $equipo);

            return $this->sendResponse(200, $estado, $mensaje, $resultados);
        } else {
            return $this->sendResponse(200, false, 'Existen errores.', $errores);
        }
    }

    public function obtenerUsuario(Request $request): JsonResponse
    {
        $codigo = $request->get('codigo') ?? '';

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->obtenerUsuario($codigo);
        if ($usuario) {
            return $this->sendResponse(200, true, 'Se encontraron resultado', $usuario);
        } else {
            return $this->sendResponse(200, false, 'No se encontraron resultados.');
        }
    }

    public function anularUsuario(Request $request): JsonResponse
    {
        $codigo = $request->post('codigo') ?? '';
        $motivo = $request->post('motivo') ?? '';
        [$usuario, $perfil, $equipo] = $this->getHost($request);

        $usuarioModel = new UsuarioModel();
        [$estado, $mensaje] = $usuarioModel->anularUsuario($codigo, $motivo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje);
    }

    public function activarUsuario(Request $request): JsonResponse
    {
        $codigo = $request->post('codigo') ?? '';
        [$usuario, $perfil, $equipo] = $this->getHost($request);

        $usuarioModel = new UsuarioModel();
        [$estado, $mensaje] = $usuarioModel->activarUsuario($codigo, $usuario, $perfil, $equipo);
        return $this->sendResponse(200, $estado, $mensaje);
    }

    public function reestablecerUsuario(Request $request): JsonResponse
    {
        $codigo = $request->post('codigo') ?? '';

        $usuarioModel = new UsuarioModel();
        [$estado, $mensaje] = $usuarioModel->reestrablecerUsuario($codigo);
        return $this->sendResponse(200, $estado, $mensaje);
    }

    public function obtenerPerfil(Request $request): JsonResponse
    {

        $codigo = $request->get('codigo') ?? '';

        $generalModel = new listarModel();
        [$estado, $mensaje, $resultado] = $generalModel->obtenerPerfil($codigo);

        return $this->sendResponse(200, $estado, $mensaje, $resultado);
    }


    public function reporteUsuario(Request $request)
    {

        $spreadsheet = new Spreadsheet();
        // Ruta del archivo de plantilla
        $templatePath = resource_path('templates/reporte_usuarios.xlsx');
        // Cargar la plantilla
        $spreadsheet = IOFactory::load($templatePath);
        // Obtener la hoja activa
        $report = new UsuarioModel();
        $data = $report->reporteUsuarios();
        $row = 5;
        $C = 1;

        $sheet = $spreadsheet->getActiveSheet();
        foreach ($data as $value) {
            $sheet->setCellValue('B' . $row, $C++);
            $sheet->setCellValue('C' . $row, $value->username);
            $sheet->setCellValue('D' . $row, $value->departamento);
            $sheet->setCellValue('E' . $row, $value->servicio);
            $sheet->setCellValue('F' . $row, $value->id_perfil);
            $sheet->setCellValue('G' . $row, $value->correo);
            $sheet->setCellValue('H' . $row, $value->telefono);
            $sheet->setCellValue('I' . $row, $value->estado);

            $row++;
        }
        $fileName = 'Reporte de Usuarios.xlsx';


        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'wrapText' => false,
            ]
        ];
        $rowFInal = $row - 1;
        $lastColumn = $sheet->getHighestColumn();
        $cellRange = 'B5:' . $lastColumn . $rowFInal;
        $sheet->getStyle($cellRange)->applyFromArray($styleArray);
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }
}
