<?php

namespace App\Http\Controllers;

use App\Models\Articulo;
use App\Models\Clientes;
use App\Models\DireccionesClientes;
use App\Models\Marcas;
use App\Models\Moneda;
use App\Models\Precios;
use Illuminate\Support\Facades\DB;

class SincronizacionController extends Controller
{
    // Conexión al Web Service con manejo de errores
    private function ConexionWBS()
    {
        $url = "http://10.10.1.107:8083/KombiService.asmx?wsdl";

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

    //$servicio, es el nombre con el que se llenaran los mensajes 
    //$metodo, Es el nombre del metodo del servico web al que se accedera
    //$cli es la bandera para saber si es por consola o por sistema web
    public function ServicioWeb($servicio, $metodo, $cli = false) 
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
            $response = $client->$metodo(['parameters' => []]);
            $xmlResponse = $response->{$metodo.'Result'};

            switch($servicio){
                case 'Monedas': $this->Monedas($xmlResponse); break;
                case 'Articulos': $this->Articulos($xmlResponse); break;
                case 'Categoria_Lista_Precios': $this->Categoria_Lista_Precios($xmlResponse); break;
                case 'Marcas': $this->Marcas($xmlResponse); break;
                case 'ListaPrecios': $this->ListaPrecio($xmlResponse); break;
                case 'Clientes': $this->Clientes($xmlResponse); break;
                case 'Direcciones': $this->Direcciones($xmlResponse); break;
            }

            $msg = 'Sincronización de '.$servicio.' completada correctamente.';

            if ($cli) {
                echo $msg . "\n";
            } else {
                return redirect()->back()->with('success', $msg);
            }

        } catch (\Throwable $e) {//Marca errores que esten por mala recepcion de datos
            $msg = "Error al sincronizar ".$servicio . $e->getMessage();

            if ($cli) {
                echo $msg . "\n";
            } else {
                return redirect()->back()->with('error', $msg);
            }
        }
    }

    private function Monedas($xmlResponse) //OCRN
    {
        foreach ($xmlResponse->Moneda as $moneda) {
                Moneda::updateOrCreate( 
                    ['Currency' => (string) $moneda->CurrCode],
                    [
                        'CurrName'   => (string) $moneda->CurrName,
                    ],
                );
            }
    }
    
    private function Articulos($xmlResponse) //OITM
    {
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
    }

    private function Categoria_Lista_Precios($xmlResponse) //OPLN
    {
        foreach ($xmlResponse->CAT_LP as $lista) {
            DB::table('OPLN')->updateOrInsert(
                ['ListNum' => (int) $lista->ListNum],
                ['ListName' => (string) $lista->ListName]
            );
        }
    }

    private function Marcas($xmlResponse) //OITB
    {
        foreach ($xmlResponse->Marcas as $marca) {
            Marcas::updateOrCreate(
                ['ItmsGrpCod' => (string) $marca->ItmsGrpCod],
                [
                    'ItmsGrpNam' => (string) $marca->ItmsGrpNam,
                    'Locked'     => (string) $marca->Locked,
                    'Object'     => (string) $marca->Object,
                ]
            );
        }
    }

    private function ListaPrecio($xmlResponse)//ITM1
    {
        foreach ($xmlResponse->ListaP as $precio) {
            // Obtener Currency_ID desde OCRN
            $currency = Moneda::where('Currency', (string)$precio->Currency)->first();
            if (!$currency) {
                // Si no existe la moneda, puedes saltarla o manejar el error
                continue;
            }

            // Insertar o actualizar precio
            Precios::updateOrInsert(
                [
                    'ItemCode' => (string)$precio->ItemCode,
                    'PriceList' => (int)$precio->PriceList
                ],
                [
                    'Price' => (float)$precio->Price,
                    'Currency_ID' => $currency->Currency_ID
                ]
            );
        }
    }

    private function Clientes($xmlResponse)//OCRD
    {
        foreach ($xmlResponse->Clientes as $cliente) {
            // Insertar o actualizar registro
            Clientes::updateOrInsert(
                ['CardCode' => (string)$cliente->CardCode],
                [
                    'CardName' => (string)$cliente->CardName,
                    'GroupNum' => (int)$cliente->GroupNum,
                    'phone1'   => (string)$cliente->Phone1,
                    'e-mail'   => (string)$cliente->E_Mail,
                    'Active'   => (string)$cliente->validFor,
                ]
            );
        }
    }

    private function Direcciones($xmlResponse) // CRD1
    {
        try {
            foreach ($xmlResponse->Direcciones as $direccion) {

                // Verificar que el cliente exista en OCRD
                $clienteExiste = DB::table('OCRD')
                    ->where('CardCode', (string)$direccion->CardCode)
                    ->exists();

                if (!$clienteExiste) {
                    // Si no existe, lo omitimos y seguimos con el siguiente
                    continue;
                }

                // Insertar o actualizar dirección
                DB::table('CRD1')->updateOrInsert(
                    [
                        'CardCode'  => (string)$direccion->CardCode,
                        'Address'   => (string)$direccion->Address,
                        'AdresType' => (string)$direccion->AdresType
                    ],
                    [
                        'Street'   => (string)$direccion->Street,
                        'Block'    => (string)$direccion->Block,
                        'ZipCode'  => (string)$direccion->ZipCode,
                        'City'     => (string)$direccion->City,
                        'Country'  => (string)$direccion->Country,
                        'County'   => (string)$direccion->County,
                        'State'    => (string)$direccion->State
                    ]
                );
            }

            return true; // ✅ Sincronización exitosa
        } catch (\Exception $e) {
            return false;
        }
    }

}