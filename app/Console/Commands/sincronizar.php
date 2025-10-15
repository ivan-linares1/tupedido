<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SincronizacionController;

class Sincronizar extends Command
{
    protected $signature = 'sincronizar {tipo?}';
    protected $description = 'Ejecuta sincronizaciones según el tipo especificado';

    public function handle()
    {
        $tipo = $this->argument('tipo'); 
        $controller = new SincronizacionController();
        $error = false; 

        switch ($tipo) {
            case 'monedas':
                $this->info('🔄 Iniciando sincronización de monedas...');
                try {
                    $controller->insertarMonedas(true); // true indica CLI
                } catch (\Throwable $e) {
                    $error = true;
                }
            break;

            case 'articulos':
                $this->info('🔄 Iniciando sincronización de artículos...');
                try {
                    $controller->Articulos(true); // true indica CLI
                } catch (\Throwable $e) {
                    $error = true;
                }
            break;

            case null:
                $this->warn('No se especificó tipo de sincronización.');
                $error = true;
            break;

            default:
                $this->warn("Tipo de sincronización '$tipo' no reconocido.");
                $error = true;
            break;
        }

        // Mensaje final resumido
        if ($error) {
            $this->error('Comando finalizado con errores.');
        } else {
            $this->info('Comando finalizado exitosamente.');
        }
    }
}
