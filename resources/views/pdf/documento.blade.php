<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }} {{ $numero ?? '' }}</title>
</head>

<style>
    @page {
        margin-top: 0.85cm;
        margin-bottom: 0.92cm;
        margin-left: 0cm;
        margin-right: 0cm;
    }

    .cero, .logo-cell table{
         width: 100%;
         border-collapse: collapse; /* pega bordes con tablas internas */
         border-spacing: 0; /* elimina espacio entre celdas */
    }

    /* LOGO */
    .logo-cell {
        width: 6.05cm;
        padding: 0;
        height: 2.27cm; /* altura igual a info-cell */
        vertical-align: top; /* alinea contenido al top */
        padding-left: 15px;
    }

    .logo-container {
        width: 6.05cm;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .logo-container img {
        max-width: 6.05cm;
        max-height: 1.85cm;
        object-fit: contain;
        margin: 0;
        padding: 0;
        display: block; /* elimina espacio debajo de la imagen */
        border: none; /* quita borde del img */
    }
/************************************************************************************************************************/


    /* INFORMACIÓN DE LA EMPRESA */
    .info-cell {
        width: 8.89cm;
        vertical-align: top; /* alinea al top para evitar espacios extra */
        padding: 0px;
        font-family: Calibri, Arial, sans-serif;
        height: 2.27cm; /* misma altura que logo-cell */
        vertical-align: top; /* alinea al top */
        padding-right: 8px;
    }

    .info-container-rounded,  .infoGeneral-container-rounded{
        width: 100%;
        border: 2px solid black;
        border-radius: 5px;
        overflow: hidden;
        box-sizing: border-box;
    }

    .info-table, .infoGeneral-table {
        width: 100%;
        height: auto;
        border-collapse: collapse; /* pega bordes con tablas internas */
        border-spacing: 0; /* elimina espacio entre celdas */
        margin: 0;
        padding: 0;
    }

    .info-header {
        background-color: #05564F;
        color: white;
        font-weight: bold;
        text-align: center;
        font-size: 9pt;
        width: 100%;
        padding: 2px 0;
        border-bottom: 2px solid black;
    }

    .info-details {
        font-size: 7pt;
        line-height: 1.5;
        text-align: center;
        width: 100%;
        padding: 2px 0;
    }
/************************************************************************************************************************/
    
    /* INFORMACIÓN DE GENERAL */
    .infoGeneral-cell {
        width: 5.06cm;
        vertical-align: top; /* alinea al top para evitar espacios extra */
        padding: 0;
        font-family: Calibri, Arial, sans-serif;
        height: 2.27cm; /* misma altura que logo-cell */
        vertical-align: top; /* alinea al top */
        padding-left: 8px;
        padding-right: 15px;
    }

    .infoGeneral-header {
        background-color: #05564F;
        color: white;
        font-weight: bold;
        text-align: center;
        font-size: 9pt;
        width: 100%;
        height: 0.57cm;
        padding: 2px 0;
        border-bottom: 2px solid black;
    }

    .infoGeneral-details {
        font-size: 7.5pt;
        line-height: 2;
        text-align: center;
        padding: 2px 0;
    }

    .infoGeneral-inner-table {
        width: 100%;
        border-collapse: collapse;  /* une los bordes */
        margin: 0;
        font-size: 7.5pt;
        text-align: center;
    }

    .infoGeneral-inner-table td {
        border: 2px solid black;    /* solo bordes internos */
        padding: 2px 4px;
        text-align: center;
        vertical-align: middle;
    }

    .infoGeneral-inner-table tr:first-child td {
        border-top: none;           /* quita borde superior de la primera fila */
    }

    .infoGeneral-inner-table tr td:first-child {
        border-left: none;          /* quita borde izquierdo de la primera col */
    }

    .infoGeneral-inner-table tr:last-child td {
        border-bottom: none;        /* quita borde inferior de la última fila */
    }

    .infoGeneral-inner-table tr td:last-child {
        border-right: none;         /* quita borde derecho de la última col */
    }




</style>

<body>
{{--************************************************************************************************************************--}}
    
    <!-- ENCABEZADO-->
    <table class="cero">
        <tr>
            <!-- LOGO -->
            <td class="logo-cell">
                <div class="logo-container">
                    <img src="{{ $logo }}" alt="Logo">
                </div>
            </td>
            <!-- DATOS DE LA EMPRESA -->
            <td class="info-cell">
                <div class="info-container-rounded">
                    <table class="info-table">
                        <tr>
                            <td class="info-header">
                                KOMBITEC SA DE CV
                            </td>
                        </tr>
                        <tr>
                            <td class="info-details">
                                AV. DR. SALVADOR NAVA MARTINEZ<br>
                                No. 232 COL. EL PASEO CP. 78320, SAN LUIS POTOSI MEXICO<br>
                                RFC: KOM0702099T1<br>
                                TEL. 444 137 07 70
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- DATOS GENERALES -->
            <td class="infoGeneral-cell">
                <div class="infoGeneral-container-rounded">
                    <table class="infoGeneral-table">
                        <tr>
                            <td class="infoGeneral-header">
                                <div class="infoGeneral-titulo">
                                    {{ $titulo }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="infoGeneral-details">
                                <table class="infoGeneral-inner-table">
                                    <tr>
                                        <td class="infoGeneral-label">{{ $subtitulo }}. No.</td>
                                        <td class="infoGeneral-number"><b>{{ $numero }}</b></td>
                                    </tr>
                                    <tr>
                                        <td class="cinfoGeneral-label-fecha">Fecha</td>
                                        <td class="infoGeneral-number-fecha">{{ $fecha }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>


        
        </tr>
    </table>


</body>
</html>



{{--************************************************************************************************************************--}}
            

{{--
<!-- DATOS GENERALES -->
    <table>
        <tr>
            <td><strong>Folio:</strong> {{ $folio ?? '' }}</td>
            <td><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong> {{ $cliente['nombre'] ?? '' }}</td>
            <td><strong>Vendedor:</strong> {{ $vendedor ?? '' }}</td>
        </tr>
        <tr>
            <td><strong>Email:</strong> {{ $cliente['email'] ?? '' }}</td>
            <td><strong>Teléfono:</strong> {{ $cliente['telefono'] ?? '' }}</td>
        </tr>
    </table>
{{--************************************************************************************************************************--}}
{{--
    <!-- ARTICULOS -->
    <table>
        <thead>
            <tr>
                <th>Clave</th>
                <th>Descripción</th>
                <th class="text-center">Cantidad</th>
                <th class="text-right">Precio Unitario</th>
                <th class="text-right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lineas as $linea)
                <tr>
                    <td>{{ $linea['codigo'] }}</td>
                    <td>{{ $linea['descripcion'] }}</td>
                    <td class="text-center">{{ $linea['cantidad'] }}</td>
                    <td class="text-right">${{ number_format($linea['precio'], 2) }}</td>
                    <td class="text-right">${{ number_format($linea['cantidad'] * $linea['precio'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
{{--************************************************************************************************************************--}}
{{--

    <!-- TOTALES Y NOTAS -->
    <table class="totales">
        <tr>
            <td><strong>Subtotal</strong></td>
            <td class="text-right">${{ number_format($totales['subtotal'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td><strong>IVA</strong></td>
            <td class="text-right">${{ number_format($totales['iva'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total</strong></td>
            <td class="text-right">${{ number_format($totales['total'] ?? 0, 2) }}</td>
        </tr>
    </table>
    <div class="clear"></div>

    
    <div class="notas">
        <strong>Notas:</strong><br>
        {{ $notas ?? 'Los precios aquí indicados son sujetos a cambios sin previo aviso.' }}
    </div>
{{--************************************************************************************************************************--}}
{{--

    <!-- PIE DE PAGINA-->
    <footer>
        Este documento fue generado automáticamente el {{ now()->format('d/m/Y H:i') }}  
        &mdash; Página <span class="pagenum"></span>
    </footer>
 --}}
