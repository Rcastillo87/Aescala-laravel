<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contrato de Obra Civil N.º {{ $id_proyecto }}</title>
    <style>
        @page { margin: 90px 50px 80px 50px; }
        ul {
            margin: 0 0 10px 20px;
            padding-left: 20px;
            list-style-type: disc;
        }

        li {
            margin-bottom: 6px;
            text-align: justify;
        }

        body {
            font-family: "Arial Narrow", Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
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
            margin-top: 30px;
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            table-layout: fixed; /* fuerza columnas iguales */
        }
        .firmas td {
            width: 50%;
            height: 160px;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            vertical-align: bottom; /* texto al final */
            position: relative;
        }
        .firmas strong {
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            font-weight: bold;
        }
        .firmas img,
        .firmas .espacio-firma {
            max-width: 100%;
            max-height: 100px;
            height: 100px; /* espacio fijo */
            margin: 0 auto 5px auto;
            display: block;
        }
        .firmas .datos-firma {
            font-family: "Calibri", sans-serif;
            font-size: 9pt;
            height: 66px;
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
<h2 class="subtitulo">DEL MES DE {{ $fecha_contrato }}</h2>
  
  <p>
Entre los suscritos, <strong>{{ $nombre_cliente }}</strong>, mayor de edad, domiciliado en {{ $ciudad_dpt }}, identificado con {{ $tipo_doc_cliente }} N° {{ $documento_cliente }}, actuando en nombre y representación propia, quien para efectos del presente contrato se denominará EL <strong>EL CONTRATANTE</strong>; y <strong>{{ env('NOMBRE_REPRESENTANTE') }}</strong>, mayor de edad, domiciliado en {{ env('IDENTI_REPRESENTANTE_EXPED') }} identificado con {{ env('TIPO_IDENT_REPRESENTANTE') }} Nº {{ env('IDENTI_REPRESENTANTE') }} expedida en {{ env('IDENTI_REPRESENTANTE_EXPED') }}, actuando en representación legal de la empresa <strong>{{ env('RAZON') }}</strong>. persona jurídica inscrita en cámara de comercio de {{ env('CIU_DPT_EMPRE') }}, con NIT N° {{ env('NIT') }} quien para efectos del presente contrato se llamará <strong>EL CONTRATISTA</strong>, acuerdan celebrar el presente CONTRATO DE OBRA CIVIL, el cual se regirá por las siguientes cláusulas: 
  </p>

<p>
<strong>PRIMERA. OBJETO: </strong> En desarrollo del presente contrato, EL CONTRATISTA se obliga con EL CONTRATANTE a ejecutar las obras de remodelación y/o elaboración de OBRA BLANCA del bien inmueble ubicado en la dirección {{ $direccion_proye }}, con un área privada de {{ $area_privada_proye }} m² conforme las características que se detallan a continuación
</p>
  
<table class="presupuesto">
    <tr>
        <th style="width: 60%;">Entregables</th>
        <th style="width: 30%;">Precios</th>
        <th style="width: 10%;">Tiempos</th>
    </tr>
    @php 
        $rowspan = count($entregables);
    @endphp
    @foreach($entregables as $index => $e)
        <tr>
            <td>
                <strong style="color: #cc5200;">Cant: {{ $e['cantidad'] }} - {{ $e['titulo'] }}</strong>
                <ul style="margin-top: 6px; padding-left: 16px;">
                    @foreach($e['items'] as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ul>
            </td>
            <td>$ {{ $e['precio'] }}</td>
            @if ($index === 0)
                <td class="tiempo" rowspan="{{ $rowspan +1}}">
                    {{ $dias_trabajo }} días trabajables
                </td>
            @endif
        </tr>
    @endforeach
    <tr style="color: #ddb499;">
        <td style="color: #cc0011;"><strong>Total</strong></td>
        <td style="color: #cc0011;"><strong>$ {{ $valor_total }}</strong></td>
    </tr>
</table>

<p>
    <strong>SEGUNDA. PLAZO: </strong>El plazo para la ejecución del presente contrato, será por el término {{ $dias_proye }} días hábiles ({{ $meses_proye }}) MESES, los cuales se manifiesta que igualmente que dependerá de la aceptación del diseño por parte de EL CONTRATANTE. En este caso, solo cuando se acepte por parte de EL CONTRATANTE el diseño y sus modificaciones, se entenderá que deberá iniciar la obra. Sin embargo, expone EL CONTRATANTE que, si aquel realiza el primer pago dispuesto en la cláusula siguiente de manera tardía a la fecha de inicio de obra, el término se entenderá dispuesto desde la fecha de pago. Parágrafo primero. EL CONTRATANTE se obliga a firmar los formatos donde se expongan los diseños que se realizaran en la obra, y desde la fecha de la firma de los mismos se contará el plazo dispuesto en la obra, junto con el respectivo pago. Parágrafo segundo. EL CONTRATANTE acepta que EL CONTRATISTA suspenda la obra si a la fecha dispuesta para pago EL CONTRATANTE no realiza el pago debido. Parágrafo tercero.  Si llegase a existir suspensión de la obra con ocasión de EL CONTRATANTE, este acepta con la firma de este escrito que EL CONTRATISTA retome el conteo del término que faltare para la entrega de la obra desde el momento que EL CONTRATANTE realice el pago debidamente. Parágrafo cuarto: las partes de común acuerdo aceptan la prórroga del plazo pactado, por el término de 30 días hábiles en caso de que EL CONTRATISTA informe imprevistos en la obra generado por terceros como, por ejemplo: proveedores.
</p>

<p>
    <strong>COMPROMISO. - EL CONTRATISTA </strong>se compromete a la compra de materiales, entrega de la obra con las especificaciones técnicas, dentro del presupuesto y en los tiempos pactados, detalles que se encuentran adjuntos al presente documento. 
</p>
<p>
    Además, deberá velar y será su responsabilidad la de tomar las medidas necesarias para evitar accidentes laborales. Así como mantener la obra con las condiciones de higiene y seguridad exigidas por la ley y los reglamentos. AESCALA ARQUITECTURA se compromete, a: corroborar que todas las personas que van a ejecutar las obras están afiliadas al sistema de riesgos laborales; que es responsabilidad de AESCALA ARQUITECTURA supervisar que los trabajadores tienen sus implementos de seguridad; por consiguiente, en caso de accidente laboral la contratante y/o cliente no es responsable.
</p>
  
<p>
    <strong>TERCERA. VALOR DEL CONTRATO: </strong>EL CONTRATANTE pagará la suma total de
    <strong>{{ $tx_valor_total }} pesos (${{ $valor_total }})</strong>.
</p>

<p>
  <strong>CUARTA. FORMA DE PAGO: </strong>EL CONTRATANTE pagará de la siguiente manera:
</p>
<ul>
    @if($por_term_1!=0) 
        <li>({{ $por_term_1 }}%) correspondiente (${{ $val_term_1 }}) para dar inicio a la etapa de diseño.</li> 
    @endif
    @if($por_term_2!=0) 
        <li>({{ $por_term_2 }}%) correspondiente (${{ $val_term_2 }}) al momento de aprobado diseño para dar inicio a la obra.</li> 
    @endif
    @if($por_term_3!=0) 
        <li>({{ $por_term_3 }}%) correspondiente (${{ $val_term_3 }}) al momento que EL CONTRATISTA informe a EL CONTRATANTE que se va a enviar corte de carpintería.</li> 
    @endif
    @if($por_term_4!=0) 
        <li>({{ $por_term_4 }}%) correspondiente (${{ $val_term_4 }}) al momento que EL CONTRATISTA informe a EL CONTRATANTE que ha comenzado la instalación de carpintería.</li> 
    @endif
    @if($por_term_5!=0) 
        <li>({{ $por_term_5 }}%) correspondiente (${{ $val_term_5 }}) al momento que EL CONTRATISTA informe a EL CONTRATANTE que ha comenzado la instalación de accesorios, grifería y mesón.</li> 
    @endif
    @if($por_term_6!=0) 
        <li>({{ $por_term_6 }}%) correspondiente (${{ $val_term_6 }}) al momento que EL CONTRATISTA informe a EL CONTRATANTE que hará entrega de la obra.</li> 
    @endif
</ul>

<p><strong>Parágrafo primero:</strong> EL CONTRATISTA no iniciará la obra hasta que EL CONTRATANTE no demuestre que ha realizado el primer pago correspondiente al CINCUENTA POR CIENTO (50%) de la obra.</p>

<p><strong>Parágrafo segundo:</strong> EL CONTRATISTA podrá suspender la obra si EL CONTRATANTE no realiza los pagos al momento de ser notificado para hacerlo conforme lo estipulado en esta cláusula.</p>

<p><strong>Parágrafo tercero:</strong> EL CONTRATISTA queda facultado por EL CONTRATANTE para suspender la obra si este último no realiza los pagos debidos, para lo cual los plazos dispuestos en la cláusula segunda se retomarán una vez se haya efectuado los pagos correspondientes por EL CONTRATANTE.</p>

<p><strong>NOTA:</strong></p>
<ul>
    <li>La separación de cupo valor $3.000.000 se restará del valor del porcentaje de la etapa de diseño; el valor de la propuesta se congelará durante seis meses a partir de la fecha de la firma.</li>
    <li>Pasado los seis meses se realizará un ajuste en el presupuesto de la propuesta de acuerdo al valor establecido en el momento.</li>
</ul>

<p>
    <strong>QUINTA. SANCIÓN POR INCUMPLIMIENTO: </strong>Las partes acuerdan que en caso de que EL CONTRATANTE no realice el pago total de la obra dispuesto en la cláusula cuarta no podrá reclamarse garantía de la obra. 
</p>

<p>
    <strong>SEXTA: </strong>El domicilio contractual será la ciudad de Cali, Valle.
</p>

<p>
<strong>SÉPTIMA. RETRACTO: </strong> EL CONTRATANTE deberá notificar del retracto a EL CONTRATISTA por escrito, una vez EL CONTRATISTA sea debidamente notificado parará las obras y entregará la obra en el estado en que se encuentre. En el evento de retracto por parte de EL CONTRATANTE, este autoriza expresamente desde ahora a EL CONTRATISTA a cobrar los valores dispuestos hasta el avance de obra en el que se encuentre al momento del retracto, cobro que podrá realizarse dentro del proceso civil respectivo por EL CONTRATISTA, sin requerimiento o citación para constituir en mora.
</p>
  
<p>
<strong>OCTAVA. GARANTÍA: </strong>EL CONTRATISTA manifiesta que la obra realizada por aquel y la cual es objeto de este contrato tiene UN (01) AÑO de garantía desde la entrega de la obra a EL CONTRATANTE. Sin embargo, EL CONTRATANTE reconoce que no existirá responsabilidad de EL CONTRATISTA cuando existan daños en la obra por manipulación indebida de EL CONTRATANTE o sus dependientes y de terceras personas.
</p>
  
<p>
<strong>NOVENA. MATERIALES: </strong>EL CONTRATISTA manifiesta que los valores dispuestos en la cláusula cuarta de este contrato se cubren a todo costo.
</p>
  
<p>
<strong>DÉCIMA. JUSTA CAUSA DE TERMINACIÓN DEL CONTRATO Y/O SUSPENSIÓN: </strong>El incumplimiento en los pagos por parte de EL CONTRATANTE es una justa causa para terminar el contrato por parte de EL CONTRATISTA. Son justas causas de suspensión las que provengan de fuerza mayor y caso fortuito, o las que sean generadas por terceros y que no sean responsabilidad de EL CONTRATISTA. Así mismo, serán justas causas para terminar el contrato por cualquiera de las partes las dispuestas en el Código Civil y Código de Comercio.
</p>
  
<p>
<strong>DÉCIMA PRIMERA. ENTREGA DE LA OBRA: </strong>La obra será entregada con salvaguarda de calidad de la obra, y bajo los parámetros de funcionalidad del bien inmueble, manifestando que si EL CONTRATISTA cumple con el 100% de lo dispuesto como objeto contractual no habrá lugar por parte de EL CONTRATANTE a negarse a recibir el mismo.
</p>
  
<p>
<strong>DÉCIMA SEGUNDA. OBLIGACIONES DEL CONTRATANTE: </strong>EL CONTRATANTE se obliga a cubrir los gastos de energía que realicen las maquinas o elementos de trabajo que use EL CONTRATISTA para el desarrollo adecuado del contrato, manifestando que dichos costos se cargarán en efecto al recibo de servicios públicos de EL CONTRATANTE y serán asumidos en su totalidad por él, manifestando EL CONTRATANTE que desde ahora asume los mencionados pagos y desobliga a EL CONTRATISTA de ello. Igualmente, EL CONTRATANTE se obliga a dejar autorización previa para la visita de EL CONTRATISTA durante el término de ejecución de la obra, en caso de incumplimiento en ello, que impida la ejecución por parte de EL CONTRATISTA, desde ahora EL CONTRATANTE asume la responsabilidad y autoriza expresamente que se suspenda la obra en el término que dure sin ingresar al inmueble de objeto de la misma, pudiendo EL CONTRATISTA retrasar la obra, para lo cual EL CONTRATANTE lo autoriza anticipadamente.
</p>
  
<p>
<strong>DÉCIMA TERCERA. DERECHOS DE AUTOR: </strong>La obra y diseño realizada por EL CONTRATISTA es única y corresponde a su diseño y la de sus colaboradores, y EL CONTRATANTE acepta con la firma de este contrato que EL CONTRATISTA podrá tomar fotografías de la obra y publicarlas en sus redes sociales para conocimiento de su público.
</p>
  
<p>
    En constancia se firma en dos ejemplares el día {{ $fecha_contrato }}.
</p>

<table class="firmas"> 
    <tr>
        <td>
            <strong>EL CONTRATANTE</strong>
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
            <strong>EL CONTRATISTA</strong>
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