<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\AdminController;

class EnviarAvisosExpiracion extends Command
{
    protected $signature = 'avisos:enviar';
    protected $description = 'Enviar avisos de expiración de socios';

    public function handle()
    {
        $adminController = new AdminController();
        $response = $adminController->get_expirations1();

        $this->info('Avisos de expiración enviados correctamente.');
    }
}
