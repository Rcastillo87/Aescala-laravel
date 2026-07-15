<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ACTA DE ENTREGA DE OBRA</title>
    <style>
        /* Márgenes seguros para el documento */
        @page {
            margin: 100px 50px 80px 50px;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
            color: #222;
        }
        
        /* Encabezado y Pie de página compatibles */
        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 50px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        header img {
            max-height: 50px;
            display: block;
        }
        footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 8.5pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }

        /* Títulos */
        h1.titulo {
            text-align: center;
            font-size: 15pt;
            margin: 15px 0;
            text-transform: uppercase;
            font-weight: bold;
            color: #243c7a;
        }
        p {
            text-align: justify;
            margin: 0 0 10px;
        }
        strong {
            font-weight: bold;
            color: #000;
        }
        
        /* Sección de Información General */
        .info-tabla {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9.5pt;
            border-collapse: collapse;
        }
        .info-tabla td {
            padding: 4px 0;
            vertical-align: top;
        }
        .info-tabla .label {
            font-weight: bold;
            color: #243c7a;
            width: 110px;
        }

        /* Tabla de Entregables */
        table.adicionales {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }
        table.adicionales th,
        table.adicionales td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        table.adicionales th {
            background: #f1f3f9;
            color: #243c7a;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .right {
            text-align: right;
        }
        .total td {
            background: #243c7a;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }

        /* Observaciones */
        .lineas-observaciones {
            margin: 15px 0;
        }
        .lineas-observaciones strong {
            color: #243c7a;
            font-size: 9.5pt;
            display: block;
            margin-bottom: 5px;
        }
        .linea {
            border-bottom: 1px solid #ccc;
            height: 22px;
            margin-bottom: 3px;
        }

        /* Estructura de Firmas corregida (Sin Position Absolute) */
        .firmas {
            margin-top: 25px;
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }
        .firmas td {
            width: 50%;
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }
        .firmas .titulo-firma {
            color: #243c7a;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 10px;
        }
        .firmas img {
            max-height: 90px;
            max-width: 90%;
            margin: 5px auto;
            display: block;
        }
        .firmas .espacio-firma {
            height: 90px; 
            margin: 5px auto;
        }
        .firmas .linea-firma {
            width: 80%;
            margin: 5px auto;
            border-bottom: 1px solid #000;
        }
        .firmas .datos-firma {
            font-size: 9pt;
            margin-top: 5px;
            line-height: 1.3;
        }
    </style>
</head>
<body>

    <!-- Encabezado -->
    <header>
        <img src="{{ public_path('img/logo.png') }}" alt="Logo Empresa">
    </header>

    <!-- Título Principal -->
    <h1 class="titulo">Acta de Entrega de Obra</h1>
    
    <!-- Fecha -->
    <p style="text-align: center; margin-bottom: 15px;">
        {{ $ciudad_completa }}, a los días 
        <span style="text-decoration: underline;"><strong>{{ $dia }}</strong></span> 
        del mes de 
        <span style="text-decoration: underline;"><strong>{{ $mes }}</strong></span> 
        de <strong>{{ $anio }}</strong>.
    </p>

    <!-- Información General -->
    <table class="info-tabla">
        <tr>
            <td class="label">Proyecto:</td>
            <td><strong>{{ $nombre_proyecto }}</strong></td>
        </tr>
        <tr>
            <td class="label">Dirección:</td>
            <td>{{ $direccion }}</td>
        </tr>
        <tr>
            <td class="label">Propietario:</td>
            <td>{{ $propietario }}</td>
        </tr>
        <tr>
            <td class="label">Representante:</td>
            <td>{{ $representante }}</td>
        </tr>
    </table>

    <p>
        Se reunieron los anteriormente mencionados con el objetivo de realizar la entrega de la obra ejecutada, objeto del acuerdo de servicio por medio del cual la empresa contratista se comprometió a realizar la obra blanca y carpintería con las siguientes especificaciones:
    </p>

    <!-- Tabla de Entregables -->
    <table class="adicionales">
        <thead>
            <tr>
                <th style="width: 75%;">Entregables</th>
                <th style="width: 25%; text-align: right;">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entregables as $e)
            <tr>
                <td>
                    <strong style="color: #243c7a;">Cant: {{ $e['cantidad'] }} - {{ $e['titulo'] }}</strong>
                    <ul style="font-size: 8.5pt; margin: 4px 0 0 0; padding-left: 15px; color: #444;">
                        @foreach($e['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </td>
                <td class="right" style="vertical-align: middle; font-weight: bold;">
                    $ {{ $e['valor'] }}
                </td>
            </tr>
            @endforeach
            @if($descuento > 0)
                <tr>
                    <td><strong style="color: #b60101;">DESCUENTO</strong></td>
                    <td class="right" style="vertical-align: middle; font-weight: bold;">$ - {{ number_format($descuento, 0, ',', '.') }}</td>
                </tr>
            @endif
            <tr class="total">
                <td style="text-align: right;">TOTAL OBRA</td>
                <td class="right">$ {{ number_format($total_obra - $descuento, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @foreach ($otrosi as $tabla)
    <table class="adicionales">
        <thead>
            <tr>
                <th colspan="6" style="text-align: left; color:#d86e31; font-weight: bold; font-size: 13px;">
                    OTROSI N° {{ $tabla['numero'] }}
                </th>
            </tr>
            <tr>
                <th style="width: 40px;">ITEM</th>
                <th>MATERIAL / ACTIVIDAD</th>
                <th style="width: 50px;">UNID</th>
                <th style="width: 50px;">CANT</th>
                <th style="width: 105px;">VALOR UNITARIO</th>
                <th style="width: 105px;">VALOR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $globalIndex = 1; @endphp
            
            {{-- Cambiado aquí: ahora recorremos solo las areas --}}
            @foreach($tabla['areas'] as $area => $datos)
                <tr class="section-title">
                    <td colspan="6" {!! $datos['espacio'] == 'Descuentos'? 'style="color: #FF0000;"' : '' !!}>{{ $datos['espacio'] }}</td>
                </tr>
                @foreach($datos['items'] as $item)
                <tr>
                    <td>{{ $globalIndex++ }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td style="text-align: center;">{{ $unidades[$item['unidad']] }}</td>
                    <td style="text-align: center;">{{ $item['cantidad'] }}</td>
                    <td class="right">{!! $item['valor_unitario'] == 0 ? '<b style="color: #FF0000;">Obsequio</b>' : '$ ' . $item['valor_unitario'] !!}</td>
                    <td class="right">{!! $item['valor_total'] == 0 ? '<b style="color: #FF0000;">Obsequio</b>' : '$ ' . $item['valor_total'] !!}</td>
                </tr>
                @endforeach
                <tr class="subtotal">
                    <td colspan="5" class="right">SUB TOTAL</td>
                    <td class="right">$ {{ number_format($datos['subtotal'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            
            {{-- Cambiado aquí: imprimimos el total correcto de esta tabla --}}
            <tr class="total">
                <td colspan="5" class="right">TOTAL A PAGAR</td>
                <td class="right">$ {{ $tabla['valor_total'] }}</td>
            </tr>
        </tbody>
    </table>
    @endforeach

    <p style="margin-top: 10px;">
        Se hace constar que las obras, objeto del acuerdo de servicio, han sido entregadas por el Contratista y recibidas a conformidad por parte del Contratante, a su entera satisfacción luego de ser verificado la terminación de los trabajos correspondientes a la esencia del servicio y luego de retirado los materiales y haber realizado limpieza de las áreas de trabajo. En caso de encontrarse alguna anomalía en los trabajos realizados, se describirán en el siguiente apartado observaciones, para ser corregidas lo antes posible por parte del Contratista y poder recibir la obra satisfactoriamente.    </p>
    </p>

    <!-- Observaciones -->
    <div class="lineas-observaciones">
        <strong>OBSERVACIONES:</strong>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
    </div>

    <p style="margin-top: 10px;">
        Las partes presentes manifiestan estar de acuerdo con el contenido en el presente documento por tanto proceden a firmar.    </p>
    </p>

    <!-- Tabla de Firmas Segura -->
    <table class="firmas">
        <tr>
            <td>
                <div class="espacio-firma"></div>
                <div class="linea-firma"></div>
                <div class="datos-firma">
                    <strong>{{ $residente }}</strong><br>
                    Firma del Residente
                </div>
            </td>

            <td>
                <div class="espacio-firma"></div>
                <div class="linea-firma"></div>
                <div class="datos-firma">
                    <strong>{{ $propietario }}</strong><br>
                    Propietario / Cliente
                </div>
            </td>

            <td>
                @if(!empty($imgRepre))
                    <img src="{{ $imgRepre }}" alt="Firma Representante">
                @else
                    <div class="espacio-firma"></div>
                @endif
                <div class="linea-firma"></div>
                <div class="datos-firma">
                    <strong>{{ $representante }}</strong><br>
                    Representante Aescala
                </div>
            </td>

        </tr>
    </table>

    <!-- Pie de página -->
    <footer>
        {{ env('RAZON') }} – NIT: {{ env('NIT') }} – {{ env('CIU_DPT_EMPRE') }} <br>
        Dirección: {{ env('DIREC', '---') }} | Tel: {{ env('TEL', '---') }}
    </footer>

</body>
</html>