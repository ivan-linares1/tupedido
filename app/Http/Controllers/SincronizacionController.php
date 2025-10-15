<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\MonedaCambio;
use Illuminate\Support\Facades\Http;

class SincronizacionController extends Controller
{
    // Conexión al Web Service con manejo de errores
    private function ConexionWBS()
    {
        $url = "http://10.10.1.116:8083/KombiService.asmx?wsdl";

        try {
            $client = new \SoapClient($url, [
                'trace' => true,
                'exceptions' => true,
            ]);

           return [
                'success' => true,
                'client' => $client,
                'type' => 'success',
                'message' => 'Conexión al WS establecida correctamente.'
            ];

        } catch (\SoapFault $e) {
            return [
                'success' => false,
                'client' => null,
                'type' => 'warning',
                'message' => "ERROR SOAP: Falla en la conexion al Servicio Web" //. $e->getMessage()
            ];
        }catch (\Exception $e) {
            return [
                'success' => false,
                'client' => null,
                'type' => 'error',
                'message' => "Error general: " . $e->getMessage()
            ];
        }
    }
    
    public function Articulos($cli = false)
    {
        $conexion = $this->ConexionWBS();

        if (!$conexion['success']) {
            if ($cli) {
                echo $conexion['message'] . "\n";
                return;
            }
            return redirect()->back()->with($conexion['type'], $conexion['message']);
        }
        $client = $conexion['client'];

        try {
            $response = $client->SBOArticulos(['parameters' => []]);
            $xmlResponse = $response->SBOArticulosResult;

            foreach ($xmlResponse->Articulo as $art) {
                Articulo::updateOrCreate( 
                    ['ItemCode' => (string) $art->ItemCode],
                    [
                        'ItemName'   => (string) $art->ItemName,
                        'FrgnName'   => (string) $art->FrgnName,
                        'SalUnitMsr' => (string) $art->SalUnitMsr,
                        'Active'     => (string) ($art->validFor),
                        'ItmsGrpCod' => (int) $art->ItmsGrpCod,
                        'Id_imagen'  => 1,
                    ],
                );
            }
                 $msg = 'Sincronización de artículos completada correctamente.';
                if ($cli) {
                    echo $msg . "\n";
                } else {
                    return redirect()->back()->with('success', $msg);
                }
        } catch (\Throwable $e) {
            $msg = "Error al sincronizar artículos: " . $e->getMessage();

            if ($cli) {
                echo $msg . "\n";
            } else {
                return redirect()->back()->with('error', $msg);
            }
        }
    }

    public function insertarMonedas($cli = false)
    {
        $hoy = now()->format('Y-m-d');

        try {
            $response = Http::get('https://api.frankfurter.app/latest', [
                'from' => 'MXN',
                'to'   => 'USD,EUR'
            ]);

            if ($response->failed()) {
                throw new \Exception('No se pudo obtener el tipo de cambio.');
            }

            $rates = $response->json()['rates'] ?? null;
            if (!$rates) {
                throw new \Exception('No se encontraron tipos de cambio.');
            }

            $usdToMxn = 1 / $rates['USD'];
            $eurToMxn = 1 / $rates['EUR'];

            $successMessages = [];
            $warningMessages = [];

            // MXN
            if (!MonedaCambio::where('Currency_ID', 1)->where('RateDate', $hoy)->exists()) {
                MonedaCambio::create([
                    'Currency_ID' => 1,
                    'RateDate'    => $hoy,
                    'Rate'        => 1.0
                ]);
                $successMessages[] = 'MXN agregado.';
            } else {
                $warningMessages[] = 'MXN ya existe para hoy.';
            }

            // USD
            if (!MonedaCambio::where('Currency_ID', 2)->where('RateDate', $hoy)->exists()) {
                MonedaCambio::create([
                    'Currency_ID' => 2,
                    'RateDate'    => $hoy,
                    'Rate'        => $usdToMxn
                ]);
                $successMessages[] = 'USD→MXN agregado.';
            } else {
                $warningMessages[] = 'USD→MXN ya existe para hoy.';
            }

            // EUR
            if (!MonedaCambio::where('Currency_ID', 3)->where('RateDate', $hoy)->exists()) {
                MonedaCambio::create([
                    'Currency_ID' => 3,
                    'RateDate'    => $hoy,
                    'Rate'        => $eurToMxn
                ]);
                $successMessages[] = 'EUR→MXN agregado.';
            } else {
                $warningMessages[] = 'EUR→MXN ya existe para hoy.';
            }

            // Preparar mensajes finales
            $finalMessage = implode(' ', $successMessages);
            $finalWarning = implode(' ', $warningMessages);

            if ($cli) {
                if ($finalMessage) echo "$finalMessage\n";
                if ($finalWarning) echo "$finalWarning\n";
            } else {
                if ($finalMessage) session()->flash('success', $finalMessage);
                if ($finalWarning) session()->flash('warning', $finalWarning);
                return redirect()->back();
            }

        } catch (\Exception $e) {
            $msg = "Error al sincronizar monedas: " . $e->getMessage();
            if ($cli) {
                echo $msg . "\n";
            } else {
                return redirect()->back()->with('error', $msg);
            }
        }
    }

    
}