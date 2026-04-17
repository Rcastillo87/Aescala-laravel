<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificado Paz y Salvo</title>

    <style>
        @page { margin: 90px 50px 80px 50px; }

        body {
            font-family: 'DejaVu Sans', sans-serif, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #222;
        }

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

        h1 {
            text-align: center;
            font-size: 15pt;
            margin: 20px 0 5px;
            text-transform: uppercase;
        }

        h2 {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 20px;
            font-weight: normal;
        }

        p {
            text-align: justify;
            margin-bottom: 12px;
        }

        .firma {
            margin-top: 50px;
            width: 100%;
        }

        .firma td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
            height: 160px;
        }

        .firma img {
            max-height: 100px;
            margin-bottom: 10px;
        }

        .linea {
            border-top: 1px solid #000;
            width: 70%;
            margin: 0 auto 5px auto;
        }

        .datos-firma {
            font-size: 9pt;
        }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('img/logo.png') }}" alt="Logo">
</header>

<h1>CERTIFICACIÓN DE PAZ Y SALVO</h1>
<h2>CONTRATO DE REMODELACIÓN N.º {{ $id_proyecto }}</h2>

<p>
    Por medio del presente documento, la empresa <strong>{{ env('RAZON') }}</strong>,
    identificada con NIT <strong>{{ env('NIT') }}</strong>, certifica que el(la) señor(a)
    <strong>{{ $nombre_cliente }}</strong>, identificado(a) con
    <strong>{{ $tipo_doc_cliente }} N.º {{ $documento_cliente }}</strong>,
    ha cumplido de manera íntegra con todas las obligaciones de pago derivadas del
    contrato de remodelación suscrito en fecha <strong>{{ $fecha_contrato }}</strong>.
</p>

<p>
    En consecuencia, se deja constancia de que el(la) cliente se encuentra
    <strong>a paz y salvo por todo concepto económico</strong>, no existiendo
    saldos pendientes, cuentas por pagar ni obligaciones financieras adicionales
    a la fecha de expedición del presente certificado.
</p>

<p>
    La empresa declara haber recibido a satisfacción la totalidad de los pagos
    acordados conforme a los términos contractuales establecidos.
</p>

<p>
    El presente certificado se expide a solicitud del interesado para los fines
    que estime pertinentes.
</p>

<p style="margin-top:20px;">
    Dado en {{ env('CIU_DPT_EMPRE') }}, a los {{ $fecha_contrato }}.
</p>

<table class="firma">
    <tr>
        <td>
            {{-- FIRMA GERENTE --}}
            @if(!empty($imgRepre))
                <img src="{{ $imgRepre }}" alt="Firma gerente">
            @else
                <div style="height:100px;"></div>
            @endif

            <div class="linea"></div>

            <div class="datos-firma">
                <strong>{{ env('NOMBRE_REPRESENTANTE') }}</strong><br>
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
