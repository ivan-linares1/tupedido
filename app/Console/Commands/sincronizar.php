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

        if (!$tipo) {
            $this->warn('⚠️ No se especificó tipo de sincronización.');
            return;
        }

        $this->info("🔄 Iniciando sincronización de: $tipo ...");
        $this->comment('⏳ Por favor espere...');

        // Llamamos al método general ServicioWeb
        $metodo = [
            'Monedas' => 'SBOMonedas_OCRN',
            'Articulos' => 'SBOArticulos_OITM',
            'Marcas' => 'SBO_GPO_Articulo_OITB',
            'Categoria_Lista_Precios' => 'SBO_CAT_LP_OPLN',
            'Lista_Precios' => 'SBOListaPrecios_ITM1',
            'Clientes' =>'SBO_Clientes_OCRD',
            'Direcciones' => 'SBO_Clientes_Direcciones_CRD1',
            'Grupo_Descuentos' => 'SBO_Grupos_Descuentos_OEDG',
            'Descuentos_Detalle' => 'SBO_Grupos_Descuentos_EDG1',
        ];

        if (!isset($metodo[$tipo])) {
            $this->warn("⚠️ Tipo de sincronización '$tipo' no reconocido.");
            return;
        }

        $servicio = $tipo;
        $metodo = $metodo[$tipo];

        try {
            if( $servicio === 'Descuentos_Detalle'){
                $controller->ServicioWebAux($servicio, $metodo, true); // true = CLI
            }
            else{ $controller->ServicioWeb($servicio, $metodo, true);} // true = CLI}
            
            $this->info("✅ Finalizacion de la sincronización de: $tipo ...");
        } catch (\Throwable $e) {
            $this->error("❌ Error ejecutando la sincronización de $tipo: " . $e->getMessage());
        }

        return;
    }
}
