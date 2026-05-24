<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Socio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SendExpirationWhatsApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-expiration-whats-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía mensajes de WhatsApp automáticos a los socios cuya cuota vence mañana';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Iniciando tarea de envío de WhatsApp por vencimientos...");

        $fechaLimite = Carbon::now()->subHours(3)->addDay()->toDateString();
        $sociosExpiracion = Socio::where('expiration', $fechaLimite)->get();

        if ($sociosExpiracion->isEmpty()) {
            $this->info("No hay socios con vencimiento para mañana ($fechaLimite).");
            return Command::SUCCESS;
        }

        $instance = config('services.green_api.instance');
        $token = config('services.green_api.token');
        $host = config('services.green_api.host');

        if (!$instance || !$token) {
            $this->error("Error: Green API no está configurado correctamente en el archivo .env");
            Log::error("SendExpirationWhatsApp: Credenciales de Green API faltantes.");
            return Command::FAILURE;
        }

        $enviados = 0;
        $errores = 0;

        foreach ($sociosExpiracion as $socio) {
            if (!$socio->telefono) {
                $this->warn("El socio {$socio->nombre} no tiene número de teléfono.");
                $errores++;
                continue;
            }

            // Mismo formateo de teléfono que en AdminController
            $telefonoStr = preg_replace('/[^0-9]/', '', $socio->telefono);
            if (substr($telefonoStr, 0, 3) !== '549') {
                if (substr($telefonoStr, 0, 2) === '54') {
                    $telefonoStr = preg_replace('/^54/', '549', $telefonoStr);
                } else {
                    if (str_starts_with($telefonoStr, '0')) {
                        $telefonoStr = substr($telefonoStr, 1);
                    }
                    if (str_starts_with($telefonoStr, '15')) {
                        $telefonoStr = substr($telefonoStr, 2);
                    }
                    $telefonoStr = '549' . $telefonoStr;
                }
            }
            $telefono = $telefonoStr . "@c.us"; // Green API requiere @c.us

            $mensaje = "Hola {$socio->nombre}, te escribimos desde Neto Boxing Center. Te recordamos que tu cuota vence mañana ({$socio->expiration}). ¡Te esperamos para seguir entrenando!";

            try {
                $url = "{$host}/waInstance{$instance}/sendMessage/{$token}";
                $response = Http::post($url, [
                    'chatId'  => $telefono,
                    'message' => $mensaje
                ]);

                if ($response->successful()) {
                    $this->info("Mensaje enviado a {$socio->nombre} ($telefono) via Green API.");
                    Log::info("CRON Aviso WhatsApp enviado a: {$socio->nombre} ($telefono) via Green API");
                    $enviados++;
                } else {
                    throw new \Exception("Error en API: " . $response->body());
                }
                
                // Pausar unos 2 segundos por precaución
                sleep(2);
                
            } catch (\Exception $e) {
                $this->error("Error con {$socio->nombre}: " . $e->getMessage());
                Log::error("CRON Error Green API WhatsApp para {$socio->nombre}: " . $e->getMessage());
                $errores++;
            }
        }

        $this->info("Proceso terminado. $enviados enviados correctamente y $errores errores.");

        return Command::SUCCESS;
    }
}
