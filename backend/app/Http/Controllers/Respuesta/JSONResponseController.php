<?php

namespace App\Http\Controllers\Respuesta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class JSONResponseController extends Controller
{
    protected array $response = [

    ];

    public function sendResponse(int $code, int|bool $success, string $message, $result = null): JsonResponse
    {
        $this->response = [
            'estado' => $success,
            'mensaje' => $message,
        ];
        if (!is_null($result)) {
            $this->response['datos'] = $result;
        }
        return response()->json($this->response, $code);
    }
    public function getHost(Request $request = null): array
    {
        $usuario = 'SIN USUARIO';
        $perfil = 'SIN PERFIL';
        if ($request) {
            $user = $request->user();
            $usuario = $user->username;
            $perfil = $user->id_perfil;
            $nombre = $user->nombre;
        }
        $equipo = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        $equipo = strtoupper(preg_replace('/(.sbdomain.local)/', "", gethostbyaddr($equipo)));
        return [$usuario, $perfil, $equipo,$nombre];
    }
}
