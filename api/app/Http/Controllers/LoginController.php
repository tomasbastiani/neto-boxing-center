<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;



class LoginController extends Controller
{
    
    public function login(Request $request)
    {
        try {
            // Registra la solicitud para ver los datos que llegan
            Log::info('Datos recibidos en la solicitud:', $request->all());

            // Validar los datos del formulario
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            // Obtener usuario por nombre de usuario
            $user = User::where('username', $request->username)->first();

            // Verificar si el usuario existe
            if ($user) {
                // Comparar la contraseña en texto plano
                if ($request->password == $user->password) {
                    Log::info('Inicio de sesión exitoso para el usuario: ' . $request->username);
                    return response()->json(['success' => 'Credenciales correctas']);
                } else {
                    Log::warning('Intento de inicio de sesión fallido para el usuario: ' . $request->username);
                    return response()->json(['error' => 'Los datos introducidos no son correctos'], 401);
                }
            } else {
                Log::warning('Intento de inicio de sesión fallido para el usuario: ' . $request->username);
                return response()->json(['error' => 'Los datos introducidos no son correctos'], 401);
            }

        } catch (\Exception $e) {
            Log::error('Error durante el proceso de inicio de sesión: ' . $e->getMessage());
            return response()->json(['error' => 'Error en el servidor'], 500);
        }
    }


    public function logout()
    {
        try {
            // var_dump($request->all());
            // Cerrar la sesión del usuario
            Auth::logout();

            // Redirigir al usuario a la página de inicio o a donde desees
            return redirect("/")->withSuccess('Sesión cerrada correctamente');
        } catch (\Exception $e) {
            Log::error('Error al cerrar sesión: ' . $e->getMessage());
            return redirect("/")->withErrors(['error' => 'Error al cerrar sesión']);
        }
    }

}
