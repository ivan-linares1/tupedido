<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\MonedaCambio;

class EnvioDatosController extends Controller
{
    /**
     * Envío de monedas a URL externa y ver en Postman
     */

    
    public function enviarMonedasExternas(Request $request)
    {
        $validated = $request->validate([
            'url'   => 'required|url',
            'token' => 'nullable|string'
        ]);

        $url = $validated['url'];
        $token = $validated['token'] ?? null;

        try {
            // Obtener últimas 10 monedas
            $monedas = MonedaCambio::orderBy('RateDate', 'desc')
                ->take(10)
                ->get(['Currency_ID', 'RateDate', 'Rate']);

            if ($monedas->isEmpty()) {
                return response()->json([
                    'success'=>false,
                    'message'=>'⚠️ No hay registros para enviar.'
                ], 404);
            }

            // Preparar datos
            $data = [
                'origen' => 'SistemaInterno',
                'fecha_envio' => now()->toDateTimeString(),
                'monedas' => $monedas->map(function($m){
                    return [
                        'codigo' => $this->getCodigoMoneda($m->Currency_ID),
                        'fecha'  => $m->RateDate,
                        'tasa'   => (float)$m->Rate,
                    ];
                })->values(),
            ];

            // 🔹 Enviar a URL externa si se proporciona
            if ($url) {
                $headers = ['Accept'=>'application/json'];
                if($token) $headers['Authorization'] = "Bearer $token";

                $externalResponse = Http::withHeaders($headers)
                    ->timeout(30)
                    ->post($url, $data);

                $responseJson['respuesta_externa'] = [
                    'status' => $externalResponse->status(),
                    'body'   => $externalResponse->successful() ? $externalResponse->json() : $externalResponse->body()
                ];
            }

            return response()->json($responseJson);

        } catch (\Exception $e) {
            return response()->json([
                'success'=>false,
                'message'=>'🚨 Error inesperado.',
                'detalle'=>$e->getMessage()
            ],500);
        }
    }

    /**
     * Endpoint receptor de prueba para ver los datos recibidos
     */
    public function receptor(Request $request)
    {
        // Token que tu sistema espera recibir
        $token_valido = '123456';

        // Obtener token de la cabecera Authorization
        $token_recibido = $request->header('Authorization'); // Ej: "Bearer 123456"

        // Validar token
        if (!$token_recibido || $token_recibido !== "Bearer $token_valido") {
            return response()->json([
                'status' => 'error',
                'mensaje' => '❌ Token inválido'
            ], 401); // 401 Unauthorized
        }

        // Si el token es correcto, devolver los datos
        return response()->json([
            'status' => 'OK',
            'mensaje' => 'Datos recibidos correctamente desde otro sistema.',
            'datos_recibidos' => $request->all(),
            'ip_origen' => $request->ip()
        ]);
    }


    /**
     * Obtener el código de moneda según el ID
     */
    private function getCodigoMoneda($id)
    {
        return match($id){
            1=>'MXN',
            2=>'USD',
            3=>'EUR',
            default=>'DESCONOCIDA'
        };
    }
}
