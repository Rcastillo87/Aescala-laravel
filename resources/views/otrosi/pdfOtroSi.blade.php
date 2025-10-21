<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>OTRO SÍ N.º {{ $num_otro_si }}</title>
    <style>
        @page {
            margin: 90px 50px 80px 50px;
        }
        body {
            font-family: "Calibri", Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #222;
        }
        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        header img {
            max-height: 50px;
            float: left;
        }
        h1.titulo {
            text-align: center;
            font-size: 16pt;
            margin: 25px 0 8px;
            text-transform: uppercase;
            font-weight: bold;
            color: #243c7a;
        }
        h2.subtitulo {
            text-align: center;
            font-size: 12pt;
            margin: 0 0 25px;
            font-weight: normal;
            color: #444;
        }
        p {
            text-align: justify;
            margin: 0 0 14px;
        }
        strong {
            font-weight: bold;
            color: #000;
        }
        
        /* === TABLA DE ADICIONALES === */
        table.adicionales {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 10.5pt;
        }
        table.adicionales caption {
            background: #243c7a;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            padding: 10px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.adicionales th, 
        table.adicionales td {
            padding: 7px 9px;
            border: 1px solid #ddd;
        }
        table.adicionales th {
            background: #f1f3f9;
            color: #243c7a;
            text-align: left;
            font-size: 11px;
        }
        .right {
            text-align: right;
        }
        
        /* Secciones de área */
        .section-title td {
            background: #f9f9f9;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
        
        /* Subtotales */
        .subtotal td {
            background: #f4f4f4;
            font-weight: bold;
            text-align: right;
            color: #243c7a;
        }
        
        /* Total a pagar */
        .total td {
            background: #243c7a;
            color: #fff;
            font-weight: bold;
            font-size: 12px;
            text-align: right;
        }
        
        /* === FIRMAS (versión original) === */
        .firmas {
            margin-top: 40px;
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .firmas td {
            width: 50%;
            height: 150px;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: bottom;
            position: relative;
        }
        .firmas strong {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
        }
        .firmas img, 
        .firmas .espacio-firma {
            max-width: 100%;
            max-height: 100px;
            height: 100px;
            margin: 0 auto 5px auto;
            display: block;
        }
        .firmas .datos-firma {
            font-size: 9pt;
            height: 80px;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            font-size: 8.5pt;
            color: #666;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('img/logo.png') }}" alt="Logo Empresa">
    </header>
    
    <h1 class="titulo">OTRO SÍ N.º {{ $num_otro_si }}</h1>
    <h2 class="subtitulo">Al Contrato de Remodelación de Vivienda</h2>
    
    <p>
        Comparecen el día <strong>{{ $fecha_otro_si }}</strong> a la firma del presente "OTRO SÍ" 
        <strong>{{ env('NOMBRE_REPRESENTANTE') }}</strong>, identificado con 
        {{ env('TIPO_IDENT_REPRESENTANTE') }} Nº {{ env('IDENTI_REPRESENTANTE') }}, 
        en representación de la empresa <strong>{{ env('RAZON') }}</strong>, NIT {{ env('NIT') }}, 
        en adelante <strong>"LA CONSTRUCTORA"</strong>; y <strong>{{ $nombre_cliente }}</strong>, 
        identificado con {{ $tipo_doc_cliente }} Nº {{ $documento_cliente }}, 
        en adelante <strong>"EL CLIENTE"</strong>. Conjuntamente denominados las <strong>"Partes"</strong>, 
        hemos acordado celebrar el presente OTRO SÍ No. {{ $num_otro_si }}.
    </p>
    
    <h3 style="margin-top: 25px; color:#243c7a;">ADICIONALES</h3>
    
    <table class="adicionales">
        <caption>
            PROYECTO: {{ $nombre_proyecto }}
        <thead>
            <tr>
                <th style="width: 40px;">ITEM</th>
                <th>MATERIAL / ACTIVIDAD</th>
                <th style="width: 60px;">CANT</th>
                <th style="width: 120px;">VALOR UNITARIO</th>
                <th style="width: 120px;">VALOR TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @php $globalIndex = 1; @endphp
            @foreach($adicionales as $area => $datos)
                <tr class="section-title">
                    <td colspan="5">{{ $datos['espacio'] }}</td>
                </tr>
                @foreach($datos['items'] as $item)
                <tr>
                    <td>{{ $globalIndex++ }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td style="text-align: center;">{{ $item['cantidad'] }}</td>
                    <td class="right">$ {{ $item['valor_unitario'] }}</td>
                    <td class="right">$ {{ $item['valor_total'] }}</td>
                </tr>
                @endforeach
                <tr class="subtotal">
                    <td colspan="4" class="right">SUB TOTAL</td>
                    <td class="right">$ {{ number_format($datos['subtotal'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="4" class="right">TOTAL A PAGAR</td>
                <td class="right">$ {{ $valor_total }}</td>
            </tr>
        </tbody>
    </table>
    
    <p>
        Para constancia y en fe de aceptación, las partes firman este documento contractual en dos (2) ejemplares de igual contenido y valor.
    </p>
    
    <p style="margin-top: 25px;">{{ $ciudad_dpt }}, {{ $fecha_otro_si }}</p>
    
    <table class="firmas">
        <tr>
            <td>
                <strong>EL CLIENTE</strong>
                @if(!empty($img_firma))
                <img src="{{ $img_firma }}" alt="Firma cliente">
                @else
                <div class="espacio-firma"></div>
                @endif
                <div class="datos-firma">
                    {{ $nombre_cliente }}<br>
                    {{ $tipo_doc_cliente_acro }} N.º {{ $documento_cliente }}
                </div>
            </td>
            <td>
                <strong>LA CONSTRUCTORA</strong>
                @if(!empty($imgRepre))
                <img src="{{ $imgRepre }}" alt="Firma representante">
                @else
                <div class="espacio-firma"></div>
                @endif
                <div class="datos-firma">
                    {{ env('NOMBRE_REPRESENTANTE') }}<br>
                    {{ env('TIPO_IDENT_REPRESENTANTE_ACRO') }} N.º {{ env('IDENTI_REPRESENTANTE') }}<br>
                    Representante Legal<br>
                    {{ env('RAZON') }}
                </div>
            </td>
        </tr>
    </table>
    
    <footer>
        {{ env('RAZON') }} – NIT: {{ env('NIT') }} – {{ env('CIU_DPT_EMPRE') }} <br>
        Dirección: {{ env('DIREC', '---') }} | Tel: {{ env('TEL', '---') }}
    </footer>
</body>
</html>
