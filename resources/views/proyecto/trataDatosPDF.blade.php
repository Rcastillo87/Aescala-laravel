<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Autorización Tratamiento de Datos</title>

    <style>
        @page {
            margin: 90px 50px 80px 50px;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.45;
            color: #222;
        }

        /* ================= HEADER ================= */
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
        }

        /* ================= TITULOS ================= */
        h1 {
            text-align: center;
            font-size: 14.5pt;
            margin: 20px 0 6px;
            text-transform: uppercase;
            font-weight: bold;
        }

        h2 {
            text-align: center;
            font-size: 11pt;
            margin-bottom: 18px;
            font-weight: normal;
        }

        /* ================= TEXTO ================= */
        p {
            margin: 0 0 10px;
            text-align: justify;
        }

        ul {
            margin: 6px 0 10px 20px;
            padding: 0;
            font-size: 9.5pt;
        }

        ul li {
            margin-bottom: 4px;
            text-align: justify;
        }

        ul ul {
            margin-top: 6px;
        }

        /* ================= FIRMAS ================= */
        table.firmas {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
            border: 1px solid #000;
            table-layout: fixed;
        }

        table.firmas td {
            height: 160px;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: bottom;
            position: relative;
        }

        table.firmas strong {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10pt;
        }

        .firma-imagen {
            max-width: 100%;
            height: 100px;
            margin: 0 auto 6px;
            display: block;
        }

        .datos-firma {
            font-size: 9pt;
            line-height: 1.3;
        }

        /* ================= FOOTER ================= */
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
    <img src="{{ public_path('img/logo.png') }}" alt="Logo empresa">
</header>

<h1>
    AUTORIZACIÓN PARA EL TRATAMIENTO DE DATOS PERSONALES DE COLABORADORES, PROVEEDORES Y CONTRATISTAS DE {{ env('RAZON') }}
</h1>
<h2>DEL {{ $fecha_contrato }}</h2>

<p>
    Yo, <strong>{{ $nombre_cliente }}</strong>, identificado(a) con {{ $tipo_doc_cliente }}
    N.º {{ $documento_cliente }}, actuando en nombre propio, en mi calidad de titular de los
    datos personales (en adelante, el <strong>TITULAR</strong>), autorizo de manera libre, expresa,
    voluntaria e informada a la empresa {{ env('RAZON') }}, identificada con NIT
    {{ env('NIT') }} (en adelante, la <strong>EMPRESA</strong>), para el tratamiento de mis
    datos personales conforme a la Ley 1581 de 2012 y demás normas concordantes.
</p>

<p>
    La <strong>EMPRESA</strong> me ha informado de forma clara sobre mis derechos como titular,
    incluyendo que no estoy obligado a otorgar esta autorización y que puedo revocarla
    en cualquier momento.
</p>

<p>La <strong>EMPRESA</strong> informa que:</p>

<ul>
    <li>Los datos recolectados no serán cedidos, vendidos ni compartidos con terceros.</li>
    <li>El tratamiento se realizará exclusivamente para las finalidades propias del objeto social de la empresa.</li>
    <li>Se garantizará la confidencialidad y seguridad de los datos mediante medidas técnicas, humanas y administrativas.</li>
    <li>
        Para revocar esta autorización, el <strong>TITULAR</strong> podrá enviar una solicitud al
        correo {{ env('MAIL_FROM_ADDRESS') }} indicando su identificación y voluntad expresa.
    </li>
    <li>Mis derechos como titular incluyen:</li>
    <ul>
        <li>Conocer, actualizar y rectificar mis datos.</li>
        <li>Solicitar prueba de la autorización otorgada.</li>
        <li>Ser informado sobre el uso de mis datos.</li>
        <li>Presentar quejas ante la Superintendencia de Industria y Comercio.</li>
        <li>Revocar la autorización o solicitar la supresión de los datos.</li>
        <li>Acceder gratuitamente a mis datos personales.</li>
    </ul>
</ul>

<p>
    Declaro conocer que en las instalaciones de {{ env('RAZON') }} existen cámaras de
    videovigilancia utilizadas exclusivamente para fines de seguridad, y autorizo
    expresamente la grabación de mi imagen mientras permanezca en ellas.
</p>

<p>
    La Política de Tratamiento de Datos Personales se encuentra disponible en el
    establecimiento ubicado en {{ env('DIREC', '---') }}.
</p>

<p>
    En constancia de lo anterior, firmo la presente autorización de manera libre,
    voluntaria e informada.
</p>

<table class="firmas">
    <tr>
        <td>
            <strong>EL TITULAR</strong>
            <img src="{{ $img_firma }}" class="firma-imagen" alt="Firma">
            <div class="datos-firma">
                {{ $nombre_cliente }}<br>
                {{ $tipo_doc_cliente_acro }} N.º {{ $documento_cliente }}
            </div>
        </td>
    </tr>
</table>

<footer>
    {{ env('RAZON') }} – NIT {{ env('NIT') }} – {{ env('CIU_DPT_EMPRE') }} <br>
    Dirección: {{ env('DIREC', '---') }} | Tel: {{ env('TEL', '---') }}
</footer>

</body>
</html>