<?php

namespace App\Http\Controllers;

use App\Console\Commands\Articulos;
use App\Models\Articulo;
use Illuminate\Http\Request;

class SincronizacionController extends Controller
{
    
    public function probarXML($cli = false)
    {
        $url = "http://10.10.1.116:8083/KombiService.asmx?wsdl";

        try {
            $client = new \SoapClient($url, [
                'trace' => true,
                'exceptions' => true,
            ]);

            $response = $client->SBOArticulos(['parameters' => []]);
            $xmlResponse = $response->SBOArticulosResult;

            // $xmlResponse->Articulo ya es un array
            $articulos = [];
            foreach ($xmlResponse->Articulo as $art) {
                $articulos[] = [
                    'ItemCode'   => (string) $art->ItemCode,
                    'ItemName'   => (string) $art->ItemName,
                    'FrgnName'   => (string) $art->FrgnName,
                    'SalUnitMsr' => (string) $art->SalUnitMsr,
                    'Active'     => (string) $art->validFor,
                    'ItmsGrpCod' => (int) $art->ItmsGrpCod,
                    'Id_imagen'  => 1,
                ];
            }
            $total = count($articulos);
            $guardados = 0;

            if ($cli) echo "📦 Total de artículos recibidos: {$total}\n";

            // Insertar o actualizar cada registro
            foreach ($articulos as $art) {
                Articulo::updateOrCreate(
                    ['ItemCode' => $art['ItemCode']], // clave primaria
                    $art,
                    $guardados++
                );
            }
             echo"✅ Sincronización completada. Total guardados: {$guardados} de {$total} \n";


        } catch (\SoapFault $e) {
            echo "❌ Error SOAP: " . $e->getMessage();
        }catch (\Exception $e) {
            echo"❌ Error general: " . $e->getMessage();
        }
    }
}
