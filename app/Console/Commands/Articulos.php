<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SincronizacionController;

class Articulos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Sincronizar_Articulos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta la sincronización desde el controlador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Iniciando sincronización de artículos...');

        // Instancia del controlador
        $controller = new SincronizacionController();

        // Llamar al método (ajusta el nombre si tu método se llama distinto)
        $controller->probarXML(true);

        $this->info('✅ Sincronización completada.');
    }
}
