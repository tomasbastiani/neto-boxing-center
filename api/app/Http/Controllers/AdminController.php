<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Socio;
use Carbon\Carbon;
use App\Mail\AvisoExpiracion;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Ingreso;
use Illuminate\Support\Facades\Http;

class AdminController extends Controller
{
    public function get_socios()
    {
        $socios = DB::table('socios')
        ->orderBy('id', 'asc')
        ->get();

        return response()->json(['socios' => $socios]);
    }

    public function create(Request $request)
    {
        $socio = Socio::create($request->all());

        return response()->json(['message' => 'Socio creado correctamente', 'socio' => $socio], 201);
    }

    public function edit(Request $request, $id)
    {
        $socio = Socio::find($id);

        if (!$socio) {
            return response()->json(['error' => 'Socio no encontrado'], 404);
        }

        $socio->update($request->all());

        return response()->json(['message' => 'Socio actualizado correctamente', 'socio' => $socio]);
    }

    public function delete($id)
    {
        $socio = Socio::find($id);

        if (!$socio) {
            return response()->json(['error' => 'Socio no encontrado'], 404);
        }

        // Eliminar el socio
        $socio->delete();
        
        return response()->json(['message' => 'Socio eliminado correctamente']);
    }

    public function get_expirations()
    {
        // Obtén la fecha de hoy y añade un día
        // $fechaLimite = Carbon::now()->addDay()->toDateString(); // Esto te dará '2024-10-04' si hoy es '2024-10-03'
        $fechaLimite = Carbon::now()->subHours(3)->addDay()->toDateString();

        // Realiza la consulta para obtener los socios cuya fecha de expiración es igual a la fecha límite
        $sociosExpiracion = Socio::where('expiration', $fechaLimite)->get();

        // Devuelve la vista con los datos
        return response()->json([
            'message' => 'Socios que expiran pronto',
            'fechaLimite' => $fechaLimite, // Imprime la variable $fechaLimite
            'sociosExpiracion' => $sociosExpiracion
        ]);
    }

    public function get_expirations1()
    {
        // Obtén la fecha de hoy y añade un día
        // $fechaLimite = Carbon::now()->addDay()->toDateString(); // Esto te dará '2024-10-04' si hoy es '2024-10-03'
        $fechaLimite = Carbon::now()->subHours(3)->addDay()->toDateString();

        // Realiza la consulta para obtener los socios cuya fecha de expiración es igual a la fecha límite
        $sociosExpiracion = Socio::where('expiration', $fechaLimite)->get();

        foreach ($sociosExpiracion as $socio) {
            // Llama a la función enviarAvisoEmail y pasa el socio actual
            $this->enviarAvisoEmail($socio);
        }

        // Devuelve la vista con los datos
        return response()->json([
            'message' => 'Socios que expiran pronto',
            'fechaLimite' => $fechaLimite, // Imprime la variable $fechaLimite
            'sociosExpiracion' => $sociosExpiracion
        ]);
    }

    public function enviarAvisoEmail($socio)
    {
        // Asegúrate de que $socio tenga los campos necesarios
        $data = [
            'nombre' => $socio->nombre, // Asegúrate de que este campo exista en tu modelo Socio
            'email' => $socio->email
        ];

        try {
            Mail::to($data['email'])->send(new AvisoExpiracion($data));
            Log::info("Email enviado a {$data['email']} para {$data['nombre']}"); // Log de éxito
            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error("Error al enviar email a {$data['email']}: " . $e->getMessage()); // Log de error
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function ingresos(Request $request)
    {
        try {
            DB::table('ingresos')->insert([
                'dni' => $request->dni,
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'sede' => $request->sede,
                'last_pay' => $request->last_pay,
                'expiration' => $request->expiration,
                'fecha_ingreso' => Carbon::now()->subHours(3),
            ]);

            return response()->json(['message' => 'Ingreso registrado correctamente'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al registrar ingreso', 'details' => $e->getMessage()], 500);
        }
    }
    public function enviarAvisoWhatsapp(Request $request)
    {
        $socioId = $request->input('id');
        $socio = Socio::find($socioId);

        if (!$socio) {
            return response()->json(['error' => 'Socio no encontrado'], 404);
        }

        if (!$socio->telefono) {
            return response()->json(['error' => 'El socio no tiene un número de teléfono cargado'], 400);
        }

        // Limpiar el teléfono de espacios, guiones o caracteres raros
        $telefonoStr = preg_replace('/[^0-9]/', '', $socio->telefono);
        
        // Agregar +549 si el número no empieza con ese prefijo
        if (substr($telefonoStr, 0, 3) !== '549') {
            if (substr($telefonoStr, 0, 2) === '54') {
                // si ya tiene 54 pero no 9, reemplazamos 54 por 549
                $telefonoStr = preg_replace('/^54/', '549', $telefonoStr);
            } else {
                // sacar ceros (011 -> 11) o 15 inicial si hubiere
                if (str_starts_with($telefonoStr, '0')) {
                    $telefonoStr = substr($telefonoStr, 1);
                }
                if (str_starts_with($telefonoStr, '15')) {
                    $telefonoStr = substr($telefonoStr, 2);
                }
                $telefonoStr = '549' . $telefonoStr;
            }
        }

        $telefono = $telefonoStr . "@c.us"; // Green API requiere el sufijo @c.us

        try {
            $instance = config('services.green_api.instance');
            $token = config('services.green_api.token');
            $host = config('services.green_api.host');

            if (!$instance || !$token) {
                return response()->json(['error' => 'Green API no está configurado correctamente en el servidor'], 500);
            }

            $mensaje = "Hola {$socio->nombre}, te escribimos desde Neto Boxing Center. Te recordamos que tu cuota vence mañana ({$socio->expiration}). ¡Te esperamos para seguir entrenando!";

            // Realizamos la petición HTTP POST tipo JSON para Green API
            $url = "{$host}/waInstance{$instance}/sendMessage/{$token}";
            $response = Http::post($url, [
                'chatId'  => $telefono,
                'message' => $mensaje
            ]);

            if ($response->successful()) {
                Log::info("Aviso WhatsApp enviado a: {$socio->nombre} ($telefono) via Green API");
                return response()->json(['success' => true, 'message' => 'WhatsApp enviado con éxito']);
            } else {
                throw new \Exception("Green API respondió con error: " . $response->body());
            }
            
        } catch (\Exception $e) {
            Log::error("Error Green API WhatsApp: " . $e->getMessage());
            return response()->json(['error' => 'Error al enviar whatsapp: ' . $e->getMessage()], 500);
        }
    }

}
