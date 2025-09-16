<?php

namespace App\Http\Controllers\Mantenimiento;


use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Mantenimiento\ProcesarModel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Auth;

class ProcesarController extends JSONResponseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    public function listarHistorial(Request $request): JsonResponse
    {

        $validacion = Validator::make($request->only([
            'usuario',
            'nombre',
            'perfil',
            'equipo',
            'ppr',
            'fecha',
            'pagina',
            'longitud'
        ]), [
            'usuario' => 'nullable|string',
            'nombre' => 'nullable|string',
            'perfil' => 'nullable|string',
            'equipo' => 'nullable|string',
            'ppr' => 'nullable|string',
            'pagina' => 'required|integer',
            'longitud' => 'required|integer',

        ]);

        if ($validacion->fails()) {
            return $this->sendResponse(200, false, 'Error de validación', $validacion->errors());
        }

        $historial = new ProcesarModel();
        $usuario = $request->get('usuario') ?? '';
        $nombre = $request->get('nombre') ?? '';
        $perfil = $request->get('perfil') ?? '';
        $equipo = $request->get('equipo') ?? '';
        $fecha = $request->get('fecha') ?? '';
        $ppr = $request->get('ppr') ?? '';
        $pagina = $request->get('pagina');
        $longitud = $request->get('longitud');
        $resultado = $historial->listarHistorial($usuario, $nombre, $perfil, $equipo, $fecha, $ppr, $longitud, $pagina);

        return $this->sendResponse(200, true, '', $resultado);
    }
    public function bloquearEjecucion(Request $request)
    {
        [$usuario, $perfil, $equipo] = $this->getHost($request);
        $periodo = $request->post('periodo');
        $fecha = $request->post('fecha');
        $year = $request->post('year');
        $tipo = $request->post('tipo');
        $codigo = $request->post('actividad');
        $bloqueo = new ProcesarModel();
        $resultado = $bloqueo->bloquearEjecucion($usuario, $perfil, $equipo, $periodo, $fecha, $year, $tipo, $codigo);
        return $this->sendResponse(200, true, '', $resultado);
    }
    public function procesarEjecucion(Request $request)
    {
        [$usuario, $perfil, $equipo, $nombre] = $this->getHost($request);
        $year = $request->post('año');
        $mes = $request->post('mes');
        $tipo = $request->post('tipo');
        $ppr = $request->post('ppr');

        $proceso = new ProcesarModel();
        [$resultado] = $proceso->procesarEjecucion($year, $mes, $tipo, $ppr);
        if ($resultado->mensaje == 1) {
            $proceso->registrarHistorial($usuario, $perfil, $equipo, $nombre, $ppr);
        }
        return $this->sendResponse(200, true, '', $resultado);
    }

    public function reporteInvalidados(Request $request)
    {
        $user = $request->user();
        $perfil = $user->id_perfil;
        $servicio = $user->servicio;
        $year = $request->get('year');
        $tipo = $request->get('tipo');
        $periodo = $request->get('periodo');
        $spreadsheet = new Spreadsheet();

        // Ruta del archivo de plantilla
        $templatePath = resource_path('templates/reporte_actividades_invalidadas.xlsx');
        // Cargar la plantilla
        $spreadsheet = IOFactory::load($templatePath);

        // Obtener la hoja activa
        $sheet = $spreadsheet->getActiveSheet();

        $report = new ProcesarModel();
        $data = $report->reporteInvalidados($year, $tipo, $periodo, $perfil, $servicio);
        $row = 5;

        foreach ($data as $value) {
            $sheet->setCellValue('B' . $row, $value->mes);
            $sheet->setCellValue('C' . $row, $value->usuario);
            $sheet->setCellValue('D' . $row, $value->departamento);
            $sheet->setCellValue('E' . $row, $value->servicio);
            $sheet->setCellValue('F' . $row, $value->categoria_id);
            $sheet->setCellValue('G' . $row, $value->categoria);
            $sheet->setCellValue('H' . $row, $value->actividad);
            $sheet->setCellValue('I' . $row, $value->datos_user);
            $sheet->setCellValue('J' . $row, $value->estado);
            $sheet->setCellValue('K' . $row, $value->fecha);
            $row++;
        }
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
        $cellRange = 'B5:' . 'K' . $rowFInal;
        $sheet->getStyle($cellRange)->applyFromArray($styleArray);
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Reporte de validación de metas físicas de actividades operativas del mes xxxx.xlsx';
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }
    public function reporteCierre(Request $request)
    {
        $user = $request->user();
        $perfil = $user->id_perfil;
        $servicio = $user->servicio;
        $year = $request->get('year');
        $tipo = $request->get('tipo');
        $periodo = $request->get('periodo');
        $spreadsheet = new Spreadsheet();

        // Ruta del archivo de plantilla
        $templatePath = resource_path('templates/reporte_cierre_actividades_final.xlsx');
        // Cargar la plantilla
        $spreadsheet = IOFactory::load($templatePath);
        // Obtener la hoja activa
        $report = new ProcesarModel();
        $data = $report->reporteCierre($year, $tipo, $periodo, $perfil, $servicio);
        $row = 5;
        $C = 1;
        if ($perfil = 'ADMIN') {
            $spreadsheet->removeSheetByIndex(1);
            $sheet = $spreadsheet->getActiveSheet();
            foreach ($data as $value) {
                $sheet->setCellValue('B' . $row, $C++);
                $sheet->setCellValue('C' . $row, $value->mes);
                $sheet->setCellValue('D' . $row, $value->usuario);
                $sheet->setCellValue('E' . $row, $value->departamento);
                $sheet->setCellValue('F' . $row, $value->servicio);
                $sheet->setCellValue('G' . $row, $value->estado);
                $sheet->setCellValue('H' . $row, $value->fecha);
                $row++;
            }
            $fileName = 'Reporte de validación de metas físicas de actividades operativas del mes xxxx.xlsx';
        } else {
            $spreadsheet->removeSheetByIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            foreach ($data as $value) {
                $sheet->setCellValue('B' . $row, $value->mes);
                $sheet->setCellValue('C' . $row, $value->usuario);
                $sheet->setCellValue('D' . $row, $value->departamento);
                $sheet->setCellValue('E' . $row, $value->servicio);
                $sheet->setCellValue('F' . $row, $value->estado);
                $sheet->setCellValue('G' . $row, $value->fecha);
                $row++;
            }
            $fileName = 'Reporte de validación de metas físicas de actividades operativas del mes xxxx.xlsx';
        }

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
    public function reporteConsolidado(Request $request)
    {
        $periodo = $request->get('periodo');
        $year = $request->get('year');
        $tipo = $request->get('tipo');
        // Ruta a la plantilla de Excel
        $templatePath = resource_path('templates/NN.xlsx');
        // Cargar la plantilla de Excel con inclusión de gráficos
        $reader = IOFactory::createReader('Xlsx');
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($templatePath);
        $report = new ProcesarModel();
        $resultado = $report->reporteConsolidado($periodo, $year, $tipo);
        $resultadoT = $report->reporteTotal();
        $sheet = $spreadsheet->getSheetByName('REPORTE-CONSOLIDADO');
        //ESTILOS ENCABEZADO
        // Ajustar ancho de columna
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(50);
        $sheet->getColumnDimension('O')->setWidth(50);
        $sheet->getColumnDimension('Q')->setWidth(30);
        $sheet->getColumnDimension('R')->setWidth(23);
        $sheet->getColumnDimension('S')->setWidth(30);
        $sheet->getColumnDimension('T')->setWidth(23);
        $sheet->getColumnDimension('U')->setWidth(23);
        $sheet->getColumnDimension('V')->setWidth(23);
        $sheet->getColumnDimension('W')->setWidth(30);
        $sheet->getColumnDimension('X')->setWidth(25);
        $sheet->getColumnDimension('Y')->setWidth(30);
        $sheet->getColumnDimension('Z')->setWidth(25);
        $sheet->getColumnDimension('AA')->setWidth(30);
        $sheet->getColumnDimension('AB')->setWidth(25);
        $sheet->getColumnDimension('AC')->setWidth(25);
        $sheet->getColumnDimension('AD')->setWidth(25);
        $sheet->getColumnDimension('AE')->setWidth(30);
        $sheet->getColumnDimension('AF')->setWidth(25);
        $sheet->getColumnDimension('AG')->setWidth(25);
        // Ajustar el texto de las celdas (wrap text)
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('H')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('J')->getAlignment()->setWrapText(true);
        $sheet->getStyle('M')->getAlignment()->setWrapText(true);
        $sheet->getStyle('O')->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q')->getAlignment()->setWrapText(true);
        $sheet->getStyle('S')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AA')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AE')->getAlignment()->setWrapText(true);
        //Ajustar el alto del encabezado
        $sheet->getRowDimension('1')->setRowHeight(60);

        // Definir estilos para cada valoración
        $styles = [
            'DEFICIENTE' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFC93B5F'],
                ]
            ],
            'REGULAR' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'],
                ]
            ],
            'BUENO' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF00B050'],
                ]
            ],
            'EXCESO' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE7E200'],
                ]
            ]
        ];

        foreach ($resultado as &$valor) {
            foreach ($resultadoT as $valorT) {
                if ($valor['ACTIVIDAD_OPERATIVA_ID'] == $valorT['ACTIVIDAD_OPERATIVA_ID']) {
                    foreach ($valorT as $key => $value) {
                        $valor[$key] = $value;
                    }
                }
            }
        }
        $row = 2;

        foreach ($resultado as $valor) {
            $sheet->setCellValue('A' . $row, $valor['YEAR'] ?? '');
            $sheet->setCellValue('B' . $row, $valor['ETAPA']?? '');
            $sheet->setCellValue('C' . $row, $valor['UE_ID']?? '');
            $sheet->setCellValue('D' . $row, $valor['UE']?? '');
            $sheet->setCellValue('E' . $row, $valor['CC_RESPONSABLE_ID']?? '');
            $sheet->setCellValue('F' . $row, $valor['DEPARTAMENTO']?? '');
            $sheet->setCellValue('G' . $row, $valor['CENTRO_COSTOS_ID']?? '');
            $sheet->setCellValue('H' . $row, $valor['CENTRO_COSTOS']?? '');
            $sheet->setCellValue('I' . $row, $valor['SERVICIO']?? '');
            $sheet->setCellValue('J' . $row, $valor['USUARIO']?? '');
            $sheet->setCellValue('K' . $row, $valor['DATOS_USUARIO']?? '');
            $sheet->setCellValue('L' . $row, $valor['OEI']?? '');
            $sheet->setCellValue('M' . $row, $valor['OBJETIVO_ESTRATEGICO']?? '');
            $sheet->setCellValue('N' . $row, $valor['AEI']?? '');
            $sheet->setCellValue('O' . $row, $valor['ACCION_ESTRATEGICA']?? '');
            $sheet->setCellValue('P' . $row, $valor['CATEGORIA_ID']?? '');
            $sheet->setCellValue('Q' . $row, $valor['CATEGORIA']?? '');
            $sheet->setCellValue('R' . $row, $valor['PRODUCTO_ID']?? '');
            $sheet->setCellValue('S' . $row, $valor['PRODUCTO']?? '');
            $sheet->setCellValue('T' . $row, $valor['FUNCION_ID']?? '');
            $sheet->setCellValue('U' . $row, $valor['FUNCION']?? '');
            $sheet->setCellValue('V' . $row, $valor['DIVISION_FUNCIONAL_ID']?? '');
            $sheet->setCellValue('W' . $row, $valor['DIVISION_FUNCIONAL']?? '');
            $sheet->setCellValue('X' . $row, $valor['GRUPO_FUNCIONAL_ID']?? '');
            $sheet->setCellValue('Y' . $row, $valor['GRUPO_FUNCIONAL']?? '');
            $sheet->setCellValue('Z' . $row, $valor['ACTIVIDAD_PRESUPUESTAL_ID']?? '');
            $sheet->setCellValue('AA' . $row, $valor['ACTIVIDAD_PRESUPUESTAL']?? '');
            $sheet->setCellValue('AB' . $row, $valor['NRO_REGISTRO_POI']?? '');
            $sheet->setCellValue('AC' . $row, $valor['ACTIVIDAD_OPERATIVA_ID']?? '');
            $sheet->setCellValue('AD' . $row, $valor['CODIGO_PPR']?? '');
            $sheet->setCellValue('AE' . $row, $valor['ACTIVIDAD_OPERATIVA']?? '');
            $sheet->setCellValue('AF' . $row, $valor['UNIDAD_MEDIDA']?? '');
            $sheet->setCellValue('AG' . $row, $valor['TRAZADORA_TAREA']?? '');
            $sheet->setCellValue('AI' . $row, $valor['PR_ENERO']?? '');
            $sheet->setCellValue('AJ' . $row, $valor['PR_FEBRERO']?? '');
            $sheet->setCellValue('AK' . $row, $valor['PR_MARZO']?? '');
            $sheet->setCellValue('AL' . $row, $valor['PR_ABRIL']?? '');
            $sheet->setCellValue('AM' . $row, $valor['PR_MAYO']?? '');
            $sheet->setCellValue('AN' . $row, $valor['PR_JUNIO']?? '');
            $sheet->setCellValue('AO' . $row, $valor['PR_JULIO']?? '');
            $sheet->setCellValue('AP' . $row, $valor['PR_AGOSTO']?? '');
            $sheet->setCellValue('AQ' . $row, $valor['PR_SETIEMBRE']?? '');
            $sheet->setCellValue('AR' . $row, $valor['PR_OCTUBRE']?? '');
            $sheet->setCellValue('AS' . $row, $valor['PR_NOVIEMBRE']?? '');
            $sheet->setCellValue('AT' . $row, $valor['PR_DICIEMBRE']?? '');
            $sheet->setCellValue('AU' . $row, "=SUM(AI" . $row . ":AT" . $row . ")"?? '');
            $sheet->setCellValue('AV' . $row, $valor['EJ_ENERO']?? '');
            $sheet->setCellValue('AW' . $row, $valor['EJ_FEBRERO']?? '');
            $sheet->setCellValue('AX' . $row, $valor['EJ_MARZO']?? '');
            $sheet->setCellValue('AY' . $row, $valor['EJ_ABRIL']?? '');
            $sheet->setCellValue('AZ' . $row, $valor['EJ_MAYO']?? '');
            $sheet->setCellValue('BA' . $row, $valor['EJ_JUNIO']?? '');
            $sheet->setCellValue('BB' . $row, $valor['EJ_JULIO']?? '');
            $sheet->setCellValue('BC' . $row, $valor['EJ_AGOSTO']?? '');
            $sheet->setCellValue('BD' . $row, $valor['EJ_SETIEMBRE']?? '');
            $sheet->setCellValue('BE' . $row, $valor['EJ_OCTUBRE']?? '');
            $sheet->setCellValue('BF' . $row, $valor['EJ_NOVIEMBRE']?? '');
            $sheet->setCellValue('BG' . $row, $valor['EJ_DICIEMBRE']?? '');
            $sheet->setCellValue('BH' . $row, "=SUM(AV" . $row . ":BG" . $row . ")"?? '');
            $sheet->setCellValue('BI' . $row, "=(BH" . $row . "/AU" . $row . ")"?? '');
            $sheet->setCellValue('BJ' . $row, "=IF(BI" . $row . "<=0.85,\"DEFICIENTE\",IF(BI" . $row . "<=0.90,\"REGULAR\",IF(BI" . $row . "<=1.20,\"BUENO\",\"EXCESO\")))"?? '');
            // Obtener el valor de la celda BJ después de calcular la fórmula
            $valoracion = $sheet->getCell('BJ' . $row)->getCalculatedValue();
            // Aplicar el estilo basado en la valoración
            if (isset($styles[$valoracion])) {
                $sheet->getStyle('BJ' . $row)->applyFromArray($styles[$valoracion]);
            }

            $row++;
        }
        $sheet->getStyle('BI:BI')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $rowFInal = $row - 1;
        $highestColumn = $sheet->getHighestColumn();
        $cellRange = 'A1:' . $highestColumn . $rowFInal;
        // Definir el estilo de borde
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFADD8E6',  // Color azul claro (hex: #ADD8E6)
                ],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ]
        ]);
        // Aplicar el estilo de borde a todas las celdas con contenido
        $sheet->getStyle($cellRange)->applyFromArray($styleArray);

        // Obtener la hoja específica
   

        // Ajustar automáticamente los rangos del gráfico si es necesario
        foreach ($spreadsheet->getActiveSheet()->getChartCollection() as $chart) {
            $chart->refresh();
        }

        // Crear el escritor y asegurarse de incluir gráficos
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        // Guardar el archivo Excel actualizado en una ruta temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($tempFile);

        // Retornar la respuesta para descargar el archivo
        return response()->download($tempFile, 'reporte.xlsx')->deleteFileAfterSend(true);
    }
    public function reporteResumenMetas(Request $request)
    {

        $year = $request->get('year');
        $tipo = $request->get('tipo');
        $user = Auth::user();
        $servicio = $user->servicio;
        // Ruta a la plantilla de Excel
        $templatePath = resource_path('templates/LP.xlsx');
        // Cargar la plantilla de Excel con inclusión de gráficos
        $reader = IOFactory::createReader('Xlsx');
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($templatePath);
        $report = new ProcesarModel();
        $resultado = $report->reporteResumenMetas($year, $tipo, $servicio);
        $sheet = $spreadsheet->getSheetByName('REPORTE-CONSOLIDADO');
        // Definir estilos para cada valoración
        $styles = [
            'DEFICIENTE' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFC93B5F'],
                ]
            ],
            'REGULAR' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'],
                ]
            ],
            'BUENO' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF00B050'],
                ]
            ],
            'EXCESO' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE7E200'],
                ]
            ],
            'NO PROGRAMADO' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFb0a8b9'],
                ]
            ]
        ];

        $row = 6;
        foreach ($resultado as $valor) {

            $sheet->setCellValue('C' . $row, $valor['OEI']);
            $sheet->setCellValue('D' . $row, $valor['OBJETIVO_ESTRATEGICO']);
            $sheet->setCellValue('E' . $row, $valor['AEI']);
            $sheet->setCellValue('F' . $row, $valor['ACCION_ESTRATEGICA']);
            $sheet->setCellValue('G' . $row, $valor['CATEGORIA_ID']);
            $sheet->setCellValue('H' . $row, $valor['CATEGORIA']);
            $sheet->setCellValue('I' . $row, $valor['PRODUCTO_ID']);
            $sheet->setCellValue('J' . $row, $valor['PRODUCTO']);
            $sheet->setCellValue('K' . $row, $valor['ACTIVIDAD_PRESUPUESTAL_ID']);
            $sheet->setCellValue('L' . $row, $valor['ACTIVIDAD_PRESUPUESTAL']);
            $sheet->setCellValue('M' . $row, $valor['CODIGO_PPR']);
            $sheet->setCellValue('N' . $row, $valor['ACTIVIDAD_OPERATIVA']);
            $sheet->setCellValue('O' . $row, $valor['UNIDAD_MEDIDA']);
            $sheet->setCellValue('P' . $row, $valor['TRAZADORA_TAREA']);
            $sheet->setCellValue('Q' . $row, $valor['PR_ENERO']);
            $sheet->setCellValue('R' . $row, $valor['EJ_ENERO']);
            $sheet->setCellValue('S' . $row, $valor['PR_FEBRERO']);
            $sheet->setCellValue('T' . $row, $valor['EJ_FEBRERO']);
            $sheet->setCellValue('U' . $row, $valor['PR_MARZO']);
            $sheet->setCellValue('V' . $row, $valor['EJ_MARZO']);
            $sheet->setCellValue('W' . $row, "=Q" . $row . "+" . "S" . $row . "+" . "U" . $row);
            $sheet->setCellValue('X' . $row, "=R" . $row . "+" . "T" . $row . "+" . "V" . $row);
            $sheet->setCellValue('Y' . $row, "=X" . $row . "/" . "W" . $row);
            $sheet->setCellValue('Z' . $row, "=IF(Y" . $row . "<=0.85,\"DEFICIENTE\",IF(Y" . $row . "<=0.90,\"REGULAR\",IF(Y" . $row . "<=1.20,\"BUENO\",\"EXCESO\")))");
            $sheet->setCellValue('AA' . $row, $valor['PR_ABRIL']);
            $sheet->setCellValue('AB' . $row, $valor['EJ_ABRIL']);
            $sheet->setCellValue('AC' . $row, $valor['PR_MAYO']);
            $sheet->setCellValue('AD' . $row, $valor['EJ_MAYO']);
            $sheet->setCellValue('AE' . $row, $valor['PR_JUNIO']);
            $sheet->setCellValue('AF' . $row, $valor['EJ_JUNIO']);
            $sheet->setCellValue('AG' . $row, "=AA" . $row . "+" . "AC" . $row . "+" . "AE" . $row);
            $sheet->setCellValue('AH' . $row, "=AB" . $row . "+" . "AD" . $row . "+" . "AF" . $row);
            $sheet->setCellValue('AI' . $row, "=AH" . $row . "/" . "AG" . $row);
            $sheet->setCellValue('AJ' . $row, "=IF(AI" . $row . "<=0.85,\"DEFICIENTE\",IF(AI" . $row . "<=0.90,\"REGULAR\",IF(AI" . $row . "<=1.20,\"BUENO\",\"EXCESO\")))");
            $sheet->setCellValue('AK' . $row, $valor['PR_JULIO']);
            $sheet->setCellValue('AL' . $row, $valor['EJ_JULIO']);
            $sheet->setCellValue('AM' . $row, $valor['PR_AGOSTO']);
            $sheet->setCellValue('AN' . $row, $valor['EJ_AGOSTO']);
            $sheet->setCellValue('AO' . $row, $valor['PR_SETIEMBRE']);
            $sheet->setCellValue('AP' . $row, $valor['EJ_SETIEMBRE']);
            $sheet->setCellValue('AQ' . $row, "=AK" . $row . "+" . "AM" . $row . "+" . "AO" . $row);
            $sheet->setCellValue('AR' . $row, "=AL" . $row . "+" . "AN" . $row . "+" . "AP" . $row);
            $sheet->setCellValue('AS' . $row, "=AR" . $row . "/" . "AQ" . $row);
            $sheet->setCellValue('AT' . $row, "=IF(AS" . $row . "<=0.85,\"DEFICIENTE\",IF(AS" . $row . "<=0.90,\"REGULAR\",IF(AS" . $row . "<=1.20,\"BUENO\",\"EXCESO\")))");
            $sheet->setCellValue('AU' . $row, $valor['PR_OCTUBRE']);
            $sheet->setCellValue('AV' . $row, $valor['EJ_OCTUBRE']);
            $sheet->setCellValue('AW' . $row, $valor['PR_NOVIEMBRE']);
            $sheet->setCellValue('AX' . $row, $valor['EJ_NOVIEMBRE']);
            $sheet->setCellValue('AY' . $row, $valor['PR_DICIEMBRE']);
            $sheet->setCellValue('AZ' . $row, $valor['EJ_DICIEMBRE']);
            $sheet->setCellValue('BA' . $row, "=W" . $row . "+" . "AG" . $row . "+" . "AQ" . $row . "+" . "AU" . $row . "+" . "AW" . $row . "+" . "AY" . $row);
            $sheet->setCellValue('BB' . $row, "=X" . $row . "+" . "AH" . $row . "+" . "AR" . $row . "+" . "AV" . $row . "+" . "AX" . $row . "+" . "AZ" . $row);
            $sheet->setCellValue('BC' . $row, "=BB" . $row . "/" . "BA" . $row);
            $sheet->setCellValue('BD' . $row, "=IF(BC" . $row . "<=0.85,\"DEFICIENTE\",IF(BC" . $row . "<=0.90,\"REGULAR\",IF(BC" . $row . "<=1.20,\"BUENO\",\"EXCESO\")))");
            $sheet->setCellValue('BE' . $row, ($valor['MT_ENERO'] == 'Otros') ? $valor['MTD_ENERO'] : $valor['MT_ENERO']);
            $sheet->setCellValue('BF' . $row, ($valor['MT_FEBRERO'] == 'Otros') ? $valor['MTD_FEBRERO'] : $valor['MT_FEBRERO']);
            $sheet->setCellValue('BG' . $row, ($valor['MT_MARZO'] == 'Otros') ? $valor['MTD_MARZO'] : $valor['MT_MARZO']);
            $sheet->setCellValue('BH' . $row, ($valor['MT_ABRIL'] == 'Otros') ? $valor['MTD_ABRIL'] : $valor['MT_ABRIL']);
            $sheet->setCellValue('BI' . $row, ($valor['MT_MAYO'] == 'Otros') ? $valor['MTD_MAYO'] : $valor['MT_MAYO']);
            $sheet->setCellValue('BJ' . $row, ($valor['MT_JUNIO'] == 'Otros') ? $valor['MTD_JUNIO'] : $valor['MT_JUNIO']);
            $sheet->setCellValue('BK' . $row, ($valor['MT_JULIO'] == 'Otros') ? $valor['MTD_JULIO'] : $valor['MT_JULIO']);
            $sheet->setCellValue('BL' . $row, ($valor['MT_AGOSTO'] == 'Otros') ? $valor['MTD_AGOSTO'] : $valor['MT_AGOSTO']);
            $sheet->setCellValue('BM' . $row, ($valor['MT_SETIEMBRE'] == 'Otros') ? $valor['MTD_SETIEMBRE'] : $valor['MT_SETIEMBRE']);
            $sheet->setCellValue('BN' . $row, ($valor['MT_OCTUBRE'] == 'Otros') ? $valor['MTD_OCTUBRE'] : $valor['MT_OCTUBRE']);
            $sheet->setCellValue('BO' . $row, ($valor['MT_NOVIEMBRE'] == 'Otros') ? $valor['MTD_NOVIEMBRE'] : $valor['MT_NOVIEMBRE']);
            $sheet->setCellValue('BP' . $row, ($valor['MT_DICIEMBRE'] == 'Otros') ? $valor['MTD_DICIEMBRE'] : $valor['MT_DICIEMBRE']);

            // Obtener el valor de la celda BJ después de calcular la fórmula
            $valoracionZ = $sheet->getCell('Z' . $row)->getCalculatedValue();
            $valoracionAJ = $sheet->getCell('AJ' . $row)->getCalculatedValue();
            $valoracionAT = $sheet->getCell('AT' . $row)->getCalculatedValue();
            $valoracionBD = $sheet->getCell('BD' . $row)->getCalculatedValue();
            // Aplicar el estilo basado en la valoración
            if (isset($styles[$valoracionZ])) {
                $sheet->getStyle('Z' . $row)->applyFromArray($styles[$valoracionZ]);
            }
            if (isset($styles[$valoracionAJ])) {
                $sheet->getStyle('AJ' . $row)->applyFromArray($styles[$valoracionAJ]);
            }
            if (isset($styles[$valoracionAT])) {
                $sheet->getStyle('AT' . $row)->applyFromArray($styles[$valoracionAT]);
            }
            if (isset($styles[$valoracionBD])) {
                $sheet->getStyle('BD' . $row)->applyFromArray($styles[$valoracionBD]);
            }
            $row++;
        }
        $sheet->getStyle('Y:Y')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AI:AI')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('AS:AS')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $sheet->getStyle('BC:BC')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);

        $rowFInal = $row - 1;
        // $highestColumn = $sheet->getHighestColumn();
        $cellRange = 'C6:' . 'BP' . $rowFInal;
        // Definir el estilo de borde
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];


        // Aplicar el estilo de borde a todas las celdas con contenido
        $sheet->getStyle($cellRange)->applyFromArray($styleArray);
        // Obtener el índice de la hoja a eliminar
        $sheet_grafico = $spreadsheet->getSheetByName('GRAFICO');
        // Eliminar la hoja
        $spreadsheet->removeSheetByIndex($spreadsheet->getIndex($sheet_grafico));

        // // Obtener la hoja específica
        // $sheet_grafico = $spreadsheet->getSheetByName('GRAFICO');
        // $sheet_grafico->setCellValue('E8', '125');
        // $sheet_grafico->setCellValue('E9', '80');
        // $sheet_grafico->setCellValue('E10', '100');
        // $sheet_grafico->setCellValue('E11', '75');
        // $sheet_grafico->setCellValue('E12', '230');
        // $sheet_grafico->setCellValue('M8', '40');
        // $sheet_grafico->setCellValue('M9', '40');
        // $sheet_grafico->setCellValue('M10', '20');

        // // Ajustar automáticamente los rangos del gráfico si es necesario
        // foreach ($spreadsheet->getActiveSheet()->getChartCollection() as $chart) {
        //     $chart->refresh();
        // }

        // Crear el escritor y asegurarse de incluir gráficos
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        // Guardar el archivo Excel actualizado en una ruta temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($tempFile);

        // Retornar la respuesta para descargar el archivo
        return response()->download($tempFile, 'reporte.xlsx')->deleteFileAfterSend(true);
    }

    public function reporteConsolidadoDetallado(Request $request)
    {
        $periodo = $request->get('periodo');
        $year = $request->get('year');
        $tipo = $request->get('tipo');
        // Ruta a la plantilla de Excel
        $templatePath = resource_path('templates/reporte_consolidado_detallado.xlsx');
        // Cargar la plantilla de Excel con inclusión de gráficos
        $reader = IOFactory::createReader('Xlsx');
        $reader->setIncludeCharts(true);
        $spreadsheet = $reader->load($templatePath);
        $report = new ProcesarModel();
        $resultado = $report->reporteConsolidadoDetallado($periodo, $year, $tipo);
        $sheet = $spreadsheet->getSheetByName('REPORTE-DETALLADO');
        //ESTILOS ENCABEZADO
        // Ajustar ancho de columna
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(30);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(30);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(50);
        $sheet->getColumnDimension('O')->setWidth(50);
        $sheet->getColumnDimension('Q')->setWidth(30);
        $sheet->getColumnDimension('R')->setWidth(23);
        $sheet->getColumnDimension('S')->setWidth(30);
        $sheet->getColumnDimension('T')->setWidth(23);
        $sheet->getColumnDimension('U')->setWidth(23);
        $sheet->getColumnDimension('V')->setWidth(23);
        $sheet->getColumnDimension('W')->setWidth(30);
        $sheet->getColumnDimension('X')->setWidth(25);
        $sheet->getColumnDimension('Y')->setWidth(30);
        $sheet->getColumnDimension('Z')->setWidth(25);
        $sheet->getColumnDimension('AA')->setWidth(30);
        $sheet->getColumnDimension('AB')->setWidth(25);
        $sheet->getColumnDimension('AC')->setWidth(25);
        $sheet->getColumnDimension('AD')->setWidth(25);
        $sheet->getColumnDimension('AE')->setWidth(30);
        $sheet->getColumnDimension('AF')->setWidth(25);
        $sheet->getColumnDimension('AG')->setWidth(25);
        // Ajustar el texto de las celdas (wrap text)
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
        $sheet->getStyle('H')->getAlignment()->setWrapText(true);
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);
        $sheet->getStyle('J')->getAlignment()->setWrapText(true);
        $sheet->getStyle('M')->getAlignment()->setWrapText(true);
        $sheet->getStyle('O')->getAlignment()->setWrapText(true);
        $sheet->getStyle('Q')->getAlignment()->setWrapText(true);
        $sheet->getStyle('S')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AA')->getAlignment()->setWrapText(true);
        $sheet->getStyle('AE')->getAlignment()->setWrapText(true);
        //Ajustar el alto del encabezado
        $sheet->getRowDimension('1')->setRowHeight(60);

        // Definir estilos para cada valoración
        $styles = [
            'DEFICIENTE' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFC93B5F'],
                ]
            ],
            'REGULAR' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFED7D31'],
                ]
            ],
            'BUENO' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF00B050'],
                ]
            ],
            'EXCESO' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE7E200'],
                ]
            ]
        ];

        $row = 2;
        foreach ($resultado as $valor) {
            $sheet->setCellValue('A' . $row, $valor['YEAR']);
            $sheet->setCellValue('B' . $row, $valor['ETAPA']);
            $sheet->setCellValue('C' . $row, $valor['UE_ID']);
            $sheet->setCellValue('D' . $row, $valor['UE']);
            $sheet->setCellValue('E' . $row, $valor['CC_RESPONSABLE_ID']);
            $sheet->setCellValue('F' . $row, $valor['DEPARTAMENTO']);
            $sheet->setCellValue('G' . $row, $valor['CENTRO_COSTOS_ID']);
            $sheet->setCellValue('H' . $row, $valor['CENTRO_COSTOS']);
            $sheet->setCellValue('I' . $row, $valor['SERVICIO']);
            $sheet->setCellValue('J' . $row, $valor['USUARIO']);
            $sheet->setCellValue('K' . $row, $valor['DATOS_USUARIO']);
            $sheet->setCellValue('L' . $row, $valor['OEI']);
            $sheet->setCellValue('M' . $row, $valor['OBJETIVO_ESTRATEGICO']);
            $sheet->setCellValue('N' . $row, $valor['AEI']);
            $sheet->setCellValue('O' . $row, $valor['ACCION_ESTRATEGICA']);
            $sheet->setCellValue('P' . $row, $valor['CATEGORIA_ID']);
            $sheet->setCellValue('Q' . $row, $valor['CATEGORIA']);
            $sheet->setCellValue('R' . $row, $valor['PRODUCTO_ID']);
            $sheet->setCellValue('S' . $row, $valor['PRODUCTO']);
            $sheet->setCellValue('T' . $row, $valor['FUNCION_ID']);
            $sheet->setCellValue('U' . $row, $valor['FUNCION']);
            $sheet->setCellValue('V' . $row, $valor['DIVISION_FUNCIONAL_ID']);
            $sheet->setCellValue('W' . $row, $valor['DIVISION_FUNCIONAL']);
            $sheet->setCellValue('X' . $row, $valor['GRUPO_FUNCIONAL_ID']);
            $sheet->setCellValue('Y' . $row, $valor['GRUPO_FUNCIONAL']);
            $sheet->setCellValue('Z' . $row, $valor['ACTIVIDAD_PRESUPUESTAL_ID']);
            $sheet->setCellValue('AA' . $row, $valor['ACTIVIDAD_PRESUPUESTAL']);
            $sheet->setCellValue('AB' . $row, $valor['NRO_REGISTRO_POI']);
            $sheet->setCellValue('AC' . $row, $valor['ACTIVIDAD_OPERATIVA_ID']);
            $sheet->setCellValue('AD' . $row, $valor['CODIGO_PPR']);
            $sheet->setCellValue('AE' . $row, $valor['ACTIVIDAD_OPERATIVA']);
            $sheet->setCellValue('AF' . $row, $valor['UNIDAD_MEDIDA']);
            $sheet->setCellValue('AG' . $row, $valor['TRAZADORA_TAREA']);
         //   $sheet->setCellValue('AH' . $row, $valor['ACUMULADO']);
            $sheet->setCellValue('AI' . $row, $valor['PR_ENERO']);
            $sheet->setCellValue('AJ' . $row, $valor['PR_FEBRERO']);
            $sheet->setCellValue('AK' . $row, $valor['PR_MARZO']);
            $sheet->setCellValue('AL' . $row, $valor['PR_ABRIL']);
            $sheet->setCellValue('AM' . $row, $valor['PR_MAYO']);
            $sheet->setCellValue('AN' . $row, $valor['PR_JUNIO']);
            $sheet->setCellValue('AO' . $row, $valor['PR_JULIO']);
            $sheet->setCellValue('AP' . $row, $valor['PR_AGOSTO']);
            $sheet->setCellValue('AQ' . $row, $valor['PR_SETIEMBRE']);
            $sheet->setCellValue('AR' . $row, $valor['PR_OCTUBRE']);
            $sheet->setCellValue('AS' . $row, $valor['PR_NOVIEMBRE']);
            $sheet->setCellValue('AT' . $row, $valor['PR_DICIEMBRE']);
            $sheet->setCellValue('AU' . $row, "=SUM(AI" . $row . ":AT" . $row . ")");
            $sheet->setCellValue('AV' . $row, $valor['EJ_ENERO']);
            $sheet->setCellValue('AW' . $row, $valor['EJ_FEBRERO']);
            $sheet->setCellValue('AX' . $row, $valor['EJ_MARZO']);
            $sheet->setCellValue('AY' . $row, $valor['EJ_ABRIL']);
            $sheet->setCellValue('AZ' . $row, $valor['EJ_MAYO']);
            $sheet->setCellValue('BA' . $row, $valor['EJ_JUNIO']);
            $sheet->setCellValue('BB' . $row, $valor['EJ_JULIO']);
            $sheet->setCellValue('BC' . $row, $valor['EJ_AGOSTO']);
            $sheet->setCellValue('BD' . $row, $valor['EJ_SETIEMBRE']);
            $sheet->setCellValue('BE' . $row, $valor['EJ_OCTUBRE']);
            $sheet->setCellValue('BF' . $row, $valor['EJ_NOVIEMBRE']);
            $sheet->setCellValue('BG' . $row, $valor['EJ_DICIEMBRE']);
            $sheet->setCellValue('BH' . $row, "=SUM(AV" . $row . ":BG" . $row . ")");
            $sheet->setCellValue('BI' . $row, "=(BH" . $row . "/AU" . $row . ")");
            $sheet->setCellValue('BJ' . $row, "=IF(BI" . $row . "<=0.85,\"DEFICIENTE\",IF(BI" . $row . "<=0.90,\"REGULAR\",IF(BI" . $row . "<=1.20,\"BUENO\",\"EXCESO\")))");
            $sheet->setCellValue('BK' . $row, ($valor['MT_ENERO'] == 'Otros') ? $valor['MTD_ENERO'] : $valor['MT_ENERO']);
            $sheet->setCellValue('BL' . $row, ($valor['MT_FEBRERO'] == 'Otros') ? $valor['MTD_FEBRERO'] : $valor['MT_FEBRERO']);
            $sheet->setCellValue('BM' . $row, ($valor['MT_MARZO'] == 'Otros') ? $valor['MTD_MARZO'] : $valor['MT_MARZO']);
            $sheet->setCellValue('BN' . $row, ($valor['MT_ABRIL'] == 'Otros') ? $valor['MTD_ABRIL'] : $valor['MT_ABRIL']);
            $sheet->setCellValue('BO' . $row, ($valor['MT_MAYO'] == 'Otros') ? $valor['MTD_MAYO'] : $valor['MT_MAYO']);
            $sheet->setCellValue('BP' . $row, ($valor['MT_JUNIO'] == 'Otros') ? $valor['MTD_JUNIO'] : $valor['MT_JUNIO']);
            $sheet->setCellValue('BQ' . $row, ($valor['MT_JULIO'] == 'Otros') ? $valor['MTD_JULIO'] : $valor['MT_JULIO']);
            $sheet->setCellValue('BR' . $row, ($valor['MT_AGOSTO'] == 'Otros') ? $valor['MTD_AGOSTO'] : $valor['MT_AGOSTO']);
            $sheet->setCellValue('BS' . $row, ($valor['MT_SETIEMBRE'] == 'Otros') ? $valor['MTD_SETIEMBRE'] : $valor['MT_SETIEMBRE']);
            $sheet->setCellValue('BT' . $row, ($valor['MT_OCTUBRE'] == 'Otros') ? $valor['MTD_OCTUBRE'] : $valor['MT_OCTUBRE']);
            $sheet->setCellValue('BU' . $row, ($valor['MT_NOVIEMBRE'] == 'Otros') ? $valor['MTD_NOVIEMBRE'] : $valor['MT_NOVIEMBRE']);
            $sheet->setCellValue('BW' . $row, ($valor['MT_DICIEMBRE'] == 'Otros') ? $valor['MTD_DICIEMBRE'] : $valor['MT_DICIEMBRE']);



            // Obtener el valor de la celda BJ después de calcular la fórmula
            $valoracion = $sheet->getCell('BJ' . $row)->getCalculatedValue();
            // Aplicar el estilo basado en la valoración
            if (isset($styles[$valoracion])) {
                $sheet->getStyle('BJ' . $row)->applyFromArray($styles[$valoracion]);
            }

            $row++;
        }
        $sheet->getStyle('BI:BI')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
        $rowFInal = $row - 1;
        $highestColumn = $sheet->getHighestColumn();
        $cellRange = 'A1:' . $highestColumn . $rowFInal;
        // Definir el estilo de borde
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => Color::COLOR_BLACK],
                ],
            ],
        ];

        $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFADD8E6',  // Color azul claro (hex: #ADD8E6)
                ],
            ],
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ]
        ]);
        // Aplicar el estilo de borde a todas las celdas con contenido
        $sheet->getStyle($cellRange)->applyFromArray($styleArray);

        // Obtener la hoja específica
        $sheet_grafico = $spreadsheet->getSheetByName('GRAFICO');
        $sheet_grafico->setCellValue('E8', '125');
        $sheet_grafico->setCellValue('E9', '80');
        $sheet_grafico->setCellValue('E10', '100');
        $sheet_grafico->setCellValue('E11', '75');
        $sheet_grafico->setCellValue('E12', '230');
        $sheet_grafico->setCellValue('M8', '40');
        $sheet_grafico->setCellValue('M9', '40');
        $sheet_grafico->setCellValue('M10', '20');

        // Ajustar automáticamente los rangos del gráfico si es necesario
        foreach ($spreadsheet->getActiveSheet()->getChartCollection() as $chart) {
            $chart->refresh();
        }

        // Crear el escritor y asegurarse de incluir gráficos
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        // Guardar el archivo Excel actualizado en una ruta temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'phpspreadsheet');
        $writer->save($tempFile);

        // Retornar la respuesta para descargar el archivo
        return response()->download($tempFile, 'reporte.xlsx')->deleteFileAfterSend(true);
    }


    public function listarBloqueos()
    {
        $proceso = new ProcesarModel();
        $resultado = $proceso->listarBloqueos();
        return $this->sendResponse(200, true, '', $resultado);
    }
    public function reporteLogros(Request $request)
    {
        $user = Auth::user();
        $perfil = $user->id_perfil;
        $servicio = $user->servicio;
        $periodo = $request->get('trimestre');
        $year = $request->get('year');
        $tipo = $request->get('tipo');
        $spreadsheet = new Spreadsheet();
        $templatePath = resource_path('templates/reporte_logros.xlsx');
        // Cargar la plantilla
        $spreadsheet = IOFactory::load($templatePath);
        // Obtener la hoja activa
        $report = new ProcesarModel();
        $data = $report->reporteLogros($periodo,$year,$tipo,$servicio,$perfil);
        $row = 15;     
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('C7', $year);
       $trimestreRomano = ''; 
       switch ($periodo) { 
        case '1': $trimestreRomano = 'I';
         break; 
        case '2': 
             $trimestreRomano = 'II';
        break; 
        case '3':
             $trimestreRomano = 'III'; 
        break;
         case '4': 
             $trimestreRomano = 'IV'; 
             break; 
        default: $trimestreRomano = $periodo; 
         }
        $sheet->setCellValue('C12', $trimestreRomano . ' trimestre');
         foreach ($data as $value) {    
            $sheet->setCellValue('B'. $row, $value['servicio']);       
            $sheet->setCellValue('C'. $row, $value['OEI']);
            $sheet->setCellValue('D' . $row, $value['OBJETIVO_ESTRATEGICO']);
            $sheet->getStyle('D' . $row)->getAlignment()->setWrapText(true);
            $sheet->setCellValue('E' . $row, $value['AEI']);
            $sheet->setCellValue('F' . $row, $value['ACCION_ESTRATEGICA']);
            $sheet->getStyle('F' . $row)->getAlignment()->setWrapText(true); 
            $sheet->setCellValue('G' . $row, $value['CATEGORIA_ID']);
            $sheet->setCellValue('H' . $row, $value['CATEGORIA']);
            $sheet->setCellValue('I' . $row, $value['PRODUCTO_ID']);
            $sheet->setCellValue('J' . $row, $value['PRODUCTO']);
            $sheet->setCellValue('K' . $row, $value['ACTIVIDAD_PRESUPUESTAL_ID']);
            $sheet->setCellValue('L' . $row, $value['ACTIVIDAD_PRESUPUESTAL']);
            $sheet->setCellValue('M' . $row, $value['ACTIVIDAD_OPERATIVA_ID']);
            $sheet->setCellValue('N' . $row, $value['ACTIVIDAD_OPERATIVA']);
            $sheet->setCellValue('O' . $row, $value['UNIDAD_MEDIDA']);
            $sheet->setCellValue('P' . $row, $value['logro']);
            $sheet->setCellValue('Q' . $row, $value['dificultad']);
            $sheet->setCellValue('R' . $row, $value['accion_correctiva']);
            $sheet->setCellValue('S' . $row, $value['accion_mejora']);
    
            $row++;
         }
         $fileName = 'Reporte de Logros.xlsx';
       

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
        $cellRange = 'B15:' . 'S'. $rowFInal;
        $sheet->getStyle($cellRange)->applyFromArray($styleArray);
        $writer = new Xlsx($spreadsheet);
        $writer->save($fileName);

        return response()->download($fileName)->deleteFileAfterSend(true);
    }
    public function reporteCentroCostos(Request $request)
    {
        $user = $request->user();
        $perfil = $user->id_perfil;
        $servicio = $user->servicio;
        $year = $request->get('year');
        $tipo = $request->get('tipo');
        $periodo = $request->get('periodo');
        $spreadsheet = new Spreadsheet();

        // Ruta del archivo de plantilla
        $templatePath = resource_path('templates/reporte_centro_costos.xlsx');
        // Cargar la plantilla
        $spreadsheet = IOFactory::load($templatePath);

        $sheet = $spreadsheet->getActiveSheet();

        $report = new ProcesarModel();
        $data = $report->reporteCentroCostos($year, $tipo, $periodo, $perfil, $servicio);
        $row = 14 ;

        foreach ($data as $registro) {
           $centrosCostos = explode(',', $registro['CENTRO_COSTO_HSB']); // Separar los centros de costo por coma
            

            foreach ($centrosCostos as $centroCosto) {

                list($codigo, $nombre) = strpos($centroCosto, '-') !== false ? explode('-', trim($centroCosto), 2) : [$centroCosto, ''];
              
                $sheet->setCellValue('B' . $row, $registro['CATEGORIA_ID']?? '');
                $sheet->setCellValue('C' . $row, $registro['CATEGORIA']?? '');
                $sheet->setCellValue('D' . $row, $registro['PRODUCTO_ID'])?? '';
                $sheet->setCellValue('E' . $row, $registro['PRODUCTO']?? '');
                $sheet->setCellValue('F' . $row, $registro['ACTIVIDAD_PRESUPUESTAL_ID']?? '');
                $sheet->setCellValue('G' . $row, $registro['ACTIVIDAD_PRESUPUESTAL']?? '');
                $sheet->setCellValue('H' . $row, $registro['ACTIVIDAD_OPERATIVA_ID']?? '');
                $sheet->setCellValue('I' . $row, $registro['ACTIVIDAD_OPERATIVA']?? '');
                $sheet->setCellValue('J' . $row, $codigo?? '');
                $sheet->setCellValue('K' . $row, $nombre?? '');

                $row++;
            }
        }

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
        $cellRange = 'B14:' . 'K' . $rowFInal;
       $sheet->getStyle($cellRange)->applyFromArray($styleArray);
        $writer = new Xlsx($spreadsheet);
        $fileName = 'Reporte de alineamiento de estructura funbcional programatica y centro de costo.xlsx';
        $writer->save($fileName);

       return response()->download($fileName)->deleteFileAfterSend(true);
    }
}
