<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Configuracion\AccesoController;
use App\Http\Controllers\Configuracion\AccionController;
use App\Http\Controllers\DatosGenerales\CargoController;
use App\Http\Controllers\Configuracion\MenuController;
use App\Http\Controllers\Configuracion\PerfilController;
use App\Http\Controllers\DatosGenerales\EmpleadoController;
use App\Http\Controllers\DatosGenerales\ServicioController;
use App\Http\Controllers\DatosGenerales\UnidadController;
use App\Http\Controllers\Evaluacion\EvaluarController;
use App\Http\Controllers\Evaluacion\PeriodoController;
use App\Http\Controllers\Evaluacion\PersonalEvaluarController;
use App\Http\Controllers\Formatos\FormatoCabController;
use App\Http\Controllers\Formatos\FormatoCriterioController;
use App\Http\Controllers\Formatos\FormatoPController;
use App\Http\Controllers\Mantenimiento\ListarController;
use App\Http\Controllers\Mantenimiento\UsuarioController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
Route::post('/validar-usuario', [AuthController::class, 'validarUsuario']);
Route::post('cambiar-contrasena', [AuthController::class, 'cambiarContrasena']);


Route::controller(MenuController::class)->group(function () {
    Route::get('configuracion/lista-menu', 'listaMenu');
    Route::post('configuracion/obtener-menu', 'obtenerMenu');
    Route::post('configuracion/editar-menu', 'editarMenu');
    Route::post('configuracion/guardar-menu', 'guardarMenu');
    Route::post('configuracion/anular-menu', 'anularMenu');
    Route::post('configuracion/activar-menu', 'activarMenu');
    Route::get('configuracion/combo-menu', 'listaMenuCombo');
});


Route::controller(AccionController::class)->group(function () {
    Route::get('configuracion/lista-accion', 'listaAccionXMenu');
    Route::post('configuracion/anular-accion', 'anularAccion');
    Route::post('configuracion/guardar-accion', 'guardarAccion');
});


Route::controller(PerfilController::class)->group(function () {
    Route::get('configuracion/perfil/lista-perfil', 'listaPerfil');
    Route::get('configuracion/perfil/obtener-perfil', 'obtenerPerfil');
    Route::post('configuracion/perfil/guardar-perfil', 'guardarPerfil');
    Route::post('configuracion/perfil/editar-perfil', 'editarPerfil');
    Route::post('configuracion/perfil/anular-perfil', 'anularPerfil');
    Route::post('configuracion/perfil/activar-perfil', 'activarPerfil');

    Route::get('configuracion/perfil/lista-perfil-usuario', 'listaPerfilUsuario');
    Route::get('configuracion/perfil/lista-perfil-combo', 'listaPerfilCombo');
});

Route::controller(AccesoController::class)->group(function () {
    Route::get('configuracion/accesos/lista-acceso', 'listaAcceso');
    Route::post('configuracion/accesos/agregar-acceso', 'agregarAcceso');
    Route::post('configuracion/accesos/anular-acceso', 'anularAcceso');
});


Route::controller(UsuarioController::class)->group(function () {
    Route::get('mantenimiento/usuarios/lista-usuario', 'listarUsuario');
    Route::get('mantenimiento/usuarios/obtener-usuario', 'obtenerUsuario');
    Route::post('mantenimiento/usuarios/anular-usuario', 'anularUsuario');
    Route::post('mantenimiento/usuarios/activar-usuario', 'activarUsuario');
    Route::post('mantenimiento/usuarios/reestablecer-usuario', 'reestablecerUsuario');
    Route::post('mantenimiento/usuarios/guardar-usuario', 'guardarUsuario');
    Route::post('mantenimiento/usuarios/editar-usuario', 'editarUsuario');
    Route::get('mantenimiento/usuarios/obtener-perfil', 'obtenerPerfil');
    Route::get('mantenimiento/usuarios/obtener-profesional', 'obtenerProfesional');
    Route::get('mantenimiento/usuarios/reporte-usuarios', 'reporteUsuario');
});

Route::controller(ListarController::class)->group(function () {
    Route::get('mantenimiento/lista-perfil', 'listaPerfil');
});

Route::controller(CargoController::class)->group(function () {
    Route::get('datos-generales/cargos/lista-cargo', 'listaCargos');
    Route::post('datos-generales/cargos/guardar-cargo', 'guardarCargo');
    Route::get('datos-generales/cargos/obtener-cargo', 'obtenerCargo');
    Route::post('datos-generales/cargos/editar-cargo', 'editarCargo');
    Route::post('datos-generales/cargos/anular-cargo', 'anularCargo');
    Route::post('datos-generales/cargos/activar-cargo', 'activarCargo');

});

 Route::controller(UnidadController::class)->group(function () {
    Route::get('datos-generales/unidad/lista-unidad', 'listarUnidad');
    Route::get('datos-generales/unidad/listar-empleados', 'listarEmpleados');
    Route::post('datos-generales/unidad/guardar-unidad', 'guardarUnidad');
    Route::get('datos-generales/unidad/obtener-unidad', 'obtenerUnidad');
    Route::post('datos-generales/unidad/editar-unidad', 'editarUnidad');
    Route::post('datos-generales/unidad/anular-unidad', 'anularUnidad');
     Route::post('datos-generales/unidad/activar-unidad', 'activarUnidad');

});
 Route::controller(ServicioController::class)->group(function () {
    Route::get('datos-generales/servicio/lista-servicio', 'listarServicio');



});

 Route::controller(EmpleadoController::class)->group(function () {
    Route::get('datos-generales/empleado/lista-empleado', 'listarEmpleado');


    Route::get('datos-generales/unidad/listar-empleados', 'listarEmpleados');
    Route::post('datos-generales/unidad/guardar-unidad', 'guardarUnidad');
    Route::get('datos-generales/unidad/obtener-unidad', 'obtenerUnidad');
    Route::post('datos-generales/unidad/editar-unidad', 'editarUnidad');
    Route::post('datos-generales/unidad/anular-unidad', 'anularUnidad');
     Route::post('datos-generales/unidad/activar-unidad', 'activarUnidad');

});
Route::controller( PeriodoController::class)->group(function () {
    Route::get('evaluacion/periodo/lista-periodo', 'listarPeriodo');
    Route::post('evaluacion/periodo/guardar-periodo', 'guardarPeriodo');
    Route::post('evaluacion/periodo/generar-formatos', 'generarFormato'); 

});

Route::controller( FormatoPController::class)->group(function () {
    Route::get('formato/formato_p/lista-formato_p', 'listaFormatoP');


  

});
Route::controller( FormatoCabController::class)->group(function () {
    Route::get('formato/formato_cab/lista-formato_cab', 'listaFormatoCab');


  

});
Route::controller(FormatoCriterioController::class)->group(function () {
    Route::get('formato/formato_criterio/lista-formato_criterio', 'listaFormatoCriterio');
    Route::get('formato/formato_criterio/lista-factor', 'listaFactor');

   

});
Route::controller(EvaluarController::class)->group(function () {
    Route::get('evaluacion/evaluar/lista-evaluar', 'listarEvaluar');
    Route::get('evaluacion/evaluar/lista-periodos', 'listarPeriodos');
    Route::get('evaluacion/evaluar/lista-eval-form', 'listarEvalForm');
    Route::get('evaluacion/evaluar/lista-eval-form-f', 'listarEvalFormF');
    Route::get('evaluacion/evaluar/generarPdf-Evaluacion', 'generarPdfEval');
    Route::get('evaluacion/evaluar/listar-historial', 'listarHistorial');
    Route::post('evaluacion/evaluar/guardar-evaluar', 'guardarEvaluar');
});
Route::controller(PersonalEvaluarController::class)->group(function () {
    Route::get('evaluacion/evaluar/listar-personal-evaluar', 'listarPersonal');
    Route::get('evaluacion/evaluar/listar-personal-nuevo-evaluar', 'listarPersonalNuevo');

   

});