<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }} {{ $numero }}</title>
</head>

<style>

/*PAPEL*/
    @page {
        margin-top: 0.85cm;
        margin-bottom: 0.92cm;
        margin-left: 0cm;
        margin-right: 0cm;
    }

     .page-break { page-break-after: always; }


/*ENCABEZADO*/
    .encabezado, .logo-cell table{
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

    /* INFORMACIÓN DE LA EMPRESA */
    .info-cell{
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

    .info-table, .infoGeneral-table, .dato-table {
        width: 100%;
        height: auto;
        border-collapse: collapse; /* pega bordes con tablas internas */
        border-spacing: 0; /* elimina espacio entre celdas */
        margin: 0;
        padding: 0;
    }

    .info-header, .dato-header {
        background-color: #05564F;
        color: white;
        font-weight: bold;
        text-align: center;
        font-size: 9pt;
        width: 100%;
        padding: 2px 0;
        border-bottom: 2px solid black;
    }

    .info-details{
        font-size: 7pt;
        line-height: 1.5;
        text-align: center;
        width: 100%;
        padding: 2px 0;
    }
    
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
        height: 0.66cm;
        padding: 2px 0;
        border-bottom: 2px solid black;
    }

    .infoGeneral-details {
        font-size: 7.5pt;
        line-height: 2;
        text-align: center;
        padding: 0;
        border: none; 
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

/*DATOS DEl CLIENTE*/

    .Datos-container {
        display: inline-block;   /* el contenedor se ajusta al contenido */
        border: 2px solid black;
        border-radius: 5px;
        overflow: hidden;        /* respeta las esquinas */
        width: auto;             /* ocupa todo el ancho disponible */
        height: 4cm;
        box-sizing: border-box;  /* el borde cuenta en el ancho */
        margin-left: 15px;
        margin-top: 5px;
    }

    .Datos{
         width: 100%;
         border-collapse: collapse; /* pega bordes con tablas internas */
         border-spacing: 0; /* elimina espacio entre celdas */
    }

    .dato-cell {
        vertical-align: top; /* alinea al top para evitar espacios extra */
        padding: 0px;
        font-family: Calibri, Arial, sans-serif;
        font-size: 7pt;
        height: 2.27cm; /* misma altura que logo-cell */
        vertical-align: top; /* alinea al top */
        border: none;
    }

    .dato-details {
        font-size: 6.5pt;
        line-height: 1.5;
        text-align: left;
        width: 100%;
        padding: 2px 0;
        margin: 0;
        height:85%;
    }
    

    .dato-table-interna {
        width: 100%;
        border-collapse: collapse; /* une bordes */
        border-spacing: 0;
        font-size: 6pt;
        font-family: Calibri, Arial, sans-serif;
        margin: 0;
        padding: 0;
    }

    .dato-table-interna td {
        border-bottom: 2px solid black; /* línea entre renglones */
        padding: 5%;
        vertical-align: top;
        
    }

    /* Quitar borde al último */
    .dato-table-interna tr:last-child td {
        border-bottom: none;
    }

    .dato-label {
        font-size: 6pt;
        font-family: Calibri, Arial, sans-serif;
        text-align: left;
        padding: 2px 4px;
    }

    #tablaintermedia {
        border-right: 2px solid black;
    }

/*ARTICULOS*/
    .articulos-container {
        height: 46%;     
        border: 2px solid black;
        border-radius: 5px;
        overflow: hidden;
        margin-left: 15px;
        margin-right: 13px;
        margin-top: 2px;
    }

    .articulos {
        width: 100%;
        border-collapse: separate; 
        border-spacing: 0; 
        margin: 0;
        border-radius: 0;
    }

    .articulos-header th{
        background-color: #05564F;
        color: white;
        font-weight: bold;
        font-size: 9pt;
        text-align: left;
        font-family: Calibri, Arial, sans-serif;
        height: 1cm;
    }

    .articulos-lista{
        font-size: 9pt;
        font-family: Calibri, Arial, sans-serif;
    }

    .articulos-lista tr:nth-child(odd) {
        background-color: rgb(231, 231, 231); 
    }

    .articulos-lista tr:nth-child(even) {
        background-color: white; 
    }

/*TOTALES*/

    .totales{
         width: calc(100% - 30px);
         border-collapse: collapse; /* pega bordes con tablas internas */
         border-spacing: 0; /* elimina espacio entre celdas */
         margin-left: 15px;
         margin-right: 15px;
         margin-top: 5px;
    }

    .total-label{
        background-color: #05564F;
        color: white;
    }

/*PIE DE PAGINAS*/
    .politicas {
        background-color: rgb(226, 226, 226);
        font-family: Calibri, Arial, sans-serif;
        font-size: 8pt;
        text-align: center;
        border-radius: 5px;
        margin: 5px 15px;
        line-height: 1.3;
        font-weight: bold;
        padding: 6px;
    }

    .politicas span {
        color: blue;
    }

    .politicas ul {
        list-style-type: disc;
        margin: 0;
        padding-left: 20px;
    }

    .footer-info {
        font-size: 6pt;
        color: #555;
        text-align: center;
        margin-top: 4px;
        font-family: Calibri, Arial, sans-serif;
    }
</style>

<body>
@foreach($lineas as $pagina => $bloque)    
<!-- ENCABEZADO-->
    <table class="encabezado">
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
                                        <td class="infoGeneral-number-fecha">{{ \Carbon\Carbon::parse($fecha)->format('d-m-Y') }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

<!-- DATOS GENERALES DEL CLIENTE -->
    <div class="Datos-container">
        <table class="Datos">
            <tr>
                <!-- DATOS DEl CLIENTE -->
                <td class="dato-cell">
                    <table class="dato-table">
                        <tr>
                            <td class="dato-header" id="tablaintermedia" style="width: 12.80cm !important;" >
                                CLIENTE / CUSTOMER
                            </td>
                            <td class="dato-header" style="width: 7.9cm !important;">
                                EMBARCAR / SHIP TO
                            </td>
                        </tr>
                        <tr>
                            <td class="dato-details" id="tablaintermedia" style="padding-left: 10px; padding-top:0px" >
                                <b>{{ $cliente['codigo'] }}</b> <br>
                                {{ $cliente['nombre'] }} <br>
                                {!! preg_replace(
                                    [
                                        '/\s*(#)/',      // antes del #
                                        '/\s*(C\.P\.)/', // antes de C.P.
                                        '/,/'            // después de la primera coma
                                    ],
                                    [
                                        '<br>$1',        // agrega salto de línea antes del #
                                        '<br>$1',        // agrega salto de línea antes de C.P.
                                        ',<br>'          // agrega salto de línea después de la primera coma
                                    ],
                                    $cliente['dir_fiscal'],
                                    1 // limita la coma a la primera ocurrencia
                                ) !!} <br>

                                {{--  correos --}}
                                Correos: <br>
                                 {{ $cliente['email'] }} <br>
                                {{ $cliente['telefono'] }}
                            </td>
                            <!-- SHIP TO con tabla interna -->
                            <td class="dato-details">
                                <table class="dato-table-interna">
                                    <tr>
                                        <td>
                                           {!! preg_replace(
                                                [
                                                    '/\s*(#)/',      // antes del #
                                                    '/\s*(C\.P\.)/', // antes de C.P.
                                                    '/,/'            // después de la primera coma
                                                ],
                                                [
                                                    '<br>$1',        // agrega salto de línea antes del #
                                                    '<br>$1',        // agrega salto de línea antes de C.P.
                                                    ',<br>'          // agrega salto de línea después de la primera coma
                                                ],
                                                $cliente['dir_envio'],
                                                1 // limita la coma a la primera ocurrencia
                                            ) !!} 
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="dato-label"><b>VÍA DE EMBARQUE:</b></td>
                                    </tr>
                                    <tr>
                                        <td class="dato-label"><b>FORMA DE ENVÍO:</b></td>
                                    </tr>
                                    <tr>
                                        <td class="dato-label"><b>CONDICIONES DE PAGO:</b></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>


<!-- ARTICULOS -->
    <div class="articulos-container">
        <table class="articulos">
            <thead class="articulos-header">
                <tr>
                    <th>Clave</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Importe</th>
                </tr>
            </thead>
            <tbody class="articulos-lista">
                @foreach($bloque  as $linea)
                    <tr>
                        <td>{{ $linea['codigo'] }}</td>
                        <td>{{ $linea['descripcion'] }}</td>
                        <td class="text-center" style="text-align: center">{{ number_format($linea['cantidad'],0) }}</td>
                        <td class="text-right">${{ number_format($linea['precio'], 2) }}</td>
                        <td class="text-right">${{ number_format($linea['cantidad'] * $linea['precio'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

<!-- TOTALES Y NOTAS -->
     <table class="totales">
        <tr>
            <!-- NOTAS -->
            <td class="info-cell">
                <div class="info-container-rounded">
                    <table class="info-table">
                        <tr>
                            <td class="info-header" style="text-align: left !important;">
                                COMENTARIOS
                            </td>
                        </tr>
                        <tr>
                            <td class="info-details" style="height: 1.15cm !important; text-align: left !important;"> {{--dejarlo en 50--}}
                                {{$vendedor}} <br>
                                {{$comentario}}
                            </td>
                        </tr>
                    </table>
                </div>
            </td>

            <!-- TOTALES-->
            <td class="infoGeneral-cell">
                <div class="infoGeneral-container-rounded">
                     <table class="infoGeneral-inner-table">
                        <tr>
                            <td class="total-label" >SUBTOTAL </td>
                            <td class="total-numero">{{ $totales['subtotal']}} {{$moneda}}</td>
                        </tr>
                        <tr>
                            <td class="total-label">IVA</td>
                            <td class="total-numero">{{ $totales['iva']}} {{$moneda}}<</td>
                        </tr>
                        <tr>
                            <td class="total-label"><b>TOTAL</b></td>
                            <td class="total-numero">{{ $totales['total']}} {{$moneda}}<</td>
                        </tr>
                    </table>
                </div>
            </td>

        </tr>
    </table>

<!-- PIE DE PAGINA -->
    <footer>
        <div class="politicas">
            <ul>
                <li>VIGENCIA DE COTIZACIÓN USD: 8 DÍAS</li>
                <li>VIGENCIA DE COTIZACIÓN MXP: MISMO DÍA AL TIPO DE CAMBIO DOF</li>
                <li>LOS PRECIOS Y LA DISPONIBILIDAD ESTÁN SUJETOS A CAMBIO SIN PREVIO AVISO</li>
                <li>FACTURAS REALIZADAS EN USD SE DEBERÁN PAGAR EN USD</li>
                <li>FACTURAS REALIZADAS EN MXP SE DEBERÁN PAGAR AL TIPO DE CAMBIO DEL DOF DE ACUERDO A LA FECHA DE FACTURACIÓN</li>
            </ul>
        </div>
        <div class="politicas">
            <p>
                EFECTUAR SU PAGO EN LA SIGUIENTE CUENTA BANCARIA <br>
                <span>BANAMEX SA M. N. SUC</span> 383 <br>
                <span>CTA:</span> 5615455 <span>CLABE:</span> 002700038356154556 <span>REFERENCIA BANCARIA:</span> C0566322 <br>
                <span>USD: SUC:</span>383 <span>CTA:</span> 9441680 <span>CLABE:</span> 002700038394416803
            </p>
        </div>
    </footer>

{{-- SALTO DE PÁGINA (excepto la última) --}}
    @if(!$loop->last)
        <div class="page-break"></div>
    @endif
    @endforeach

</body>
</html>