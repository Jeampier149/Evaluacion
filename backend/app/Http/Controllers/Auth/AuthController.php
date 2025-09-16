<?php

namespace App\Http\Controllers\Auth;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Respuesta\JSONResponseController;
use App\Models\Auth\AuthModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

    class AuthController extends JSONResponseController

    {
    
        public function __construct()
        {
    //        $this->middleware('auth:sanctum', ['only' => ['cerrarSesion']]);
        }
    
        public function register(Request $request): JsonResponse
        {
    
            // Crear el usuario
           User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
    
            ]);
    
           
            return $this->sendResponse(200, 1, 'Usuario Creado exitosamente.');
            
        }
        public function login(Request $request): JsonResponse
        {
            $validator = Validator::make($request->only(['username', 'password']), [
                'username' => 'required|string',
                'password' => 'required|string',
            ]);
    
            if ($validator->fails()) {
                return $this->sendResponse(200, 3, 'Error de validación.', $validator->errors());
            }
    
            if (!Auth::attempt(['username' => $request->post('username'), 'password' => $request->post('password')])) {
                return $this->sendResponse(200, 0, 'Usuario o contraseña incorrectos. ');
            }
    
            $expirationDate = Carbon::now()->addHours(8);
            $user = Auth::user();
            $token = $user->createToken('evaluacion', ["*"], $expirationDate)->plainTextToken;
            $auth = new AuthModel();
             $menus = $auth->obtenerMenu($user->id_perfil);
             $servicio=$user->servicio;
             $departamento=$user->departamento;
              $userData = [
                   'username' => $user->username,
                   'nombre' => $user->nombre,
                    'menu' => $menus,
                   'perfil' => $user->id_perfil,
                   'token' => $token,
                   'servicio'=> $servicio,
                  'departamento'=> $departamento,
                  'fecha_expiracion' => $expirationDate,
              ];
            
            return $this->sendResponse(200, 1, 'Sesión creada.',$userData);
        }
        public function logout(Request $request)
        {
            if ($request->user()) {
             //   $request->user()->tokens()->delete();
            }
            return $this->sendResponse(200, true, 'Sesión cerrada.');
        }
        public function validarUsuario(Request $request): JsonResponse
        {
            $validator = Validator::make($request->only(['nombreUsuario', 'contrasena']), [
                'nombreUsuario' => 'required|string|max:15',

            ]);
    
            if ($validator->fails()) {
                return $this->sendResponse(200, 3, 'Error al validar.', $validator->errors());
            }
    
            $nombreUsuario = Str::upper($request->nombreUsuario);
            $contrasena = $request->contrasena;
            
            $autenticacionModel = new AuthModel();
            $resultado = $autenticacionModel->validarUsuario($nombreUsuario);
            

            if($resultado->password==""){
               if($contrasena==$request->password && $nombreUsuario==$resultado->username){
                return $this->sendResponse(200, 1, 'Validación exitosa.');  
               }else{
                return $this->sendResponse(200, 0, 'Usuario o contraseña incorrectos.');
               }
            }else{
                if(Hash::check($contrasena,$resultado->password) && $nombreUsuario == $resultado->username){
                    return $this->sendResponse(200, 1, 'Validación exitosa.');  
                 }else{
                    return $this->sendResponse(200, 0, 'Usuario o contraseña incorrectos.');
                 }    
            }

                  
        
        }
        
    
        public function cambiarContrasena(Request $request): JsonResponse
        {
    
            $validator = Validator::make($request->only(['nombreUsuario', 'contrasena', 'nuevaContrasena']), [
                'nombreUsuario' => 'required|string|max:15',
                'nuevaContrasena' => 'required|string|min:3|max:10',
            ]);
    
            if ($validator->fails()) {
                return $this->sendResponse(200, 3, 'Error al validar.', $validator->errors());
            }
    
            $nombreUsuario = Str::upper($request->nombreUsuario);
            $contrasena = Str::upper($request->contrasena);
            $nuevaContrasena = Str::upper($request->nuevaContrasena);
            $nuevaContrasenaHash = Hash::make($request->nuevaContrasena);
            $equipo = strtoupper(explode('.', gethostbyaddr($_SERVER['REMOTE_ADDR']))[0] ?? '');
    
            $autenticacionModel = new AuthModel();
    
            
            $respuesta = $autenticacionModel->cambiarContrasena($nombreUsuario,$nuevaContrasenaHash,$equipo);
         
            return $this->sendResponse(200, 1, 'Debe iniciar sesión con su nueva contraseña.');
        }
    
    
    
    }
    

