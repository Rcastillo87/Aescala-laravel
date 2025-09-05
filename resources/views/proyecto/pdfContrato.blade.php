<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Obra Civil N.º {{ $id_proyecto }}</title>
    <style>
        @page { margin: 90px 50px 80px 50px; }
        body {
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #222;
        }
        /* Header con logo fijo */
        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 60px;
            border-bottom: 1px solid #aaa;
        }
        header img {
            max-height: 50px;
            float: left;
        }
        /* Títulos principales */
        h1.titulo {
            text-align: center;
            font-size: 15pt;
            margin: 20px 0 5px;
            text-transform: uppercase;
            font-weight: bold;
        }
        h2.subtitulo {
            text-align: center;
            font-size: 11pt;
            margin: 0 0 20px;
            font-weight: normal;
        }
        /* Subtítulos de cláusulas */
        h3 {
            font-size: 11pt;
            margin: 16px 0 6px;
            text-transform: uppercase;
            font-weight: bold;
        }
        p { text-align: justify; margin: 0 0 10px; }
        /* Tabla de entregables */
        .presupuesto {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10.5pt;
        }
        .presupuesto th {
            background-color: #ff8c00;
            color: #fff;
            padding: 6px;
            border: 1px solid #e67300;
            font-size: 11pt;
        }
        .presupuesto td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
            background: #fff;
        }
        .presupuesto td:nth-child(2) {
            font-weight: bold;
            text-align: right;
        }
        .presupuesto td.tiempo {
            text-align: center;
            font-weight: bold;
            color: #cc5200;
            writing-mode: vertical-rl;
            text-orientation: upright;
            font-size: 11pt;
            background: #fff7f0;
        }
        /* Firmas */
        .firmas {
            margin-top: 60px;
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }
        .firmas td {
            width: 50%;
            height: 160px; /* más alto para firmas */
            border: 1px solid #000;
            vertical-align: top;
            padding: 8px;
            text-align: center;
            font-family: "Calibri", sans-serif;
            font-size: 11pt;
        }
        .firmas strong {
            display: block;
            margin-bottom: 40px;
        }
        /* Footer */
        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            font-size: 9pt;
            color: #555;
            text-align: center;
            border-top: 1px solid #aaa;
            padding-top: 4px;
        }
    </style>
</head>
<body>
<header>
    <img src="{{ public_path('img/logo.png') }}" alt="Logo Empresa">
</header>

<!-- Título del contrato -->
<h1 class="titulo">CONTRATO DE OBRA CIVIL N.º {{ $id_proyecto }}</h1>
<h2 class="subtitulo">Del {{ $fecha_contrato }}</h2>

<p>
    Entre los suscritos, <strong>{{ $nombre_cliente }}</strong>, mayor de edad,
    domiciliado en {{ $ciudad_dpt }}, identificado con {{ $tipo_doc_cliente }}
    N.º {{ $documento_cliente }}, actuando en nombre propio, quien en adelante se denominará
    <strong>EL CONTRATANTE</strong>; y <strong>{{ $nombre_repre }}</strong>, mayor de edad,
    identificado con {{ $tipo_doc_repre }} N.º {{ $documento_repre }} expedida en {{ $ciudad_dpt_repre }},
    en calidad de representante legal de <strong>{{ $razon_social }}</strong>, NIT {{ $nit }},
    domiciliada en {{ $ciudad_dpt_empresa }}, quien en adelante se denominará
    <strong>EL CONTRATISTA</strong>, acuerdan celebrar el presente contrato de obra civil,
    sujeto a las siguientes cláusulas:
</p>

<h3>Primera. Objeto</h3>
<p>
    EL CONTRATISTA se obliga con EL CONTRATANTE a ejecutar las obras de remodelación
    y/o elaboración de obra blanca en el inmueble ubicado en {{ $ciudad_dpt }},
    dirección {{ $direccion_proye }}, con un área privada de {{ $area_privada_proye }} m²,
    conforme a los siguientes entregables:
</p>

<table class="presupuesto">
    <tr>
        <th style="width: 60%;">Entregables</th>
        <th style="width: 30%;">Precios</th>
        <th style="width: 10%;">Tiempos</th>
    </tr>
    @php $rowspan = count($entregables); @endphp
    @foreach($entregables as $index => $e)
        <tr>
            <td>
                <strong style="color: #cc5200;">{{ $e['titulo'] }}</strong>
                <ul style="margin-top: 6px; padding-left: 16px;">
                    @foreach($e['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </td>
            <td>{{ $e['precio'] }}</td>
            @if ($index === 0)
                <td class="tiempo" rowspan="{{ $rowspan }}">
                    {{ $dias_trabajo }} días trabajables
                </td>
            @endif
        </tr>
    @endforeach
</table>

<h3>Segunda. Plazo</h3>
<p>
    El plazo de ejecución será de {{ $dias_proye }} días hábiles
    ({{ $meses_proye }} {{ $tx_meses_proye }}),
    contados a partir de la aceptación del diseño por parte de EL CONTRATANTE y del pago inicial correspondiente.
</p>

<h3>Tercera. Valor del contrato</h3>
<p>
    EL CONTRATANTE pagará la suma total de
    <strong>{{ $tx_valor_total }} pesos (${{ $valor_total }})</strong>.
</p>

<h3>Cuarta. Forma de pago</h3>
<p>Los pagos se realizarán de la siguiente manera:</p>
<ul>
    <li>50% (${{ $val_term_1 }}) al aprobar el diseño.</li>
    <li>30% (${{ $val_term_2 }}) antes de enchapar pisos.</li>
    <li>15% (${{ $val_term_3 }}) al iniciar acabados de segunda etapa.</li>
    <li>3% (${{ $val_term_4 }}) al iniciar instalación de accesorios.</li>
    <li>2% (${{ $val_term_5 }}) al momento de entrega de la obra.</li>
</ul>

<h3>Quinta. Sanción por incumplimiento</h3>
<p>
    En caso de incumplimiento en los pagos por parte de EL CONTRATANTE, no aplicará la garantía de la obra.
</p>

<h3>Sexta. Domicilio</h3>
<p>
    El domicilio contractual será la ciudad de Cali, Valle.
</p>

<h3>Séptima. Garantía</h3>
<p>
    La obra cuenta con un (1) año de garantía a partir de su entrega. EL CONTRATANTE reconoce que no existirá
    responsabilidad del CONTRATISTA por daños causados por manipulación indebida, mal uso o intervención de terceros.
</p>

<p>
    En constancia se firma en dos ejemplares el día {{ $fecha_contrato }}.
</p>

<table class="firmas">
    <tr>
        <td>
            <strong>EL CONTRATANTE</strong>
            <br><br><br>
            {{ $nombre_cliente }}<br>
            {{ $tipo_doc_cliente_acro }} N.º {{ $documento_cliente }}
        </td>
        <td>
            <strong>EL CONTRATISTA</strong>
            <br><br><br>
            {{ $nombre_repre }}<br>
            {{ $tipo_doc_repre_acro }} N.º {{ $documento_repre }}<br>
            Representante Legal<br>
            {{ $razon_social }}
        </td>
    </tr>
</table>

<footer>
    {{ env('RAZON') }} – NIT: {{ env('NIT') }} – {{ env('CIU_DPT_EMPRE') }} <br>
    Dirección: {{ env('DIREC', '---') }} | Tel: {{ env('TEL', '---') }}
</footer>
</body>
</html>
