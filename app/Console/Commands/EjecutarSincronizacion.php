<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SincronizacionController;

class EjecutarSincronizacion extends Command
{
    /**
     * El nombre y firma del comando.
     */
    protected $signature = 'sincronizar:ejecutar';

    /**
     * Descripción del comando.
     */
    protected $description = 'Ejecuta la sincronización diaria desde el controlador';

    /**
     * Lógica del comando.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización...');

        // Instancia del controlador
        $controller = new SincronizacionController();

        // Llamar al método (ajusta el nombre si tu método se llama distinto)
        $controller->insertarMonedas(true);

        $this->info('Sincronización Terminada.');
    }
}
