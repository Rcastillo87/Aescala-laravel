<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Contrato de Obra Civil N.º {{ $id_proyecto }}</title>
    <style>
        @page { margin: 90px 50px 80px 50px; }
        ul {
            margin: 0 0 5px 5px;
            padding-left: 20px;
            list-style-type: disc;
            font-size: 9pt;
        }

        li {
            margin-bottom: 2px;
            text-align: justify;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif, Arial, sans-serif;
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
        /* Titulos principales */
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
        /* Subtitulos de clausulas */
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
            padding: 2px;
            border: 1px solid #e67300;
            font-size: 10pt;
        }
        .presupuesto td {
            border: 1px solid #ccc;
            padding: 2px;
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

<!-- Titulo del contrato -->
<h1 class="titulo">CONTRATO DE OBRA CIVIL N.º {{ $id_proyecto }}</h1>
@if($fecha_contrato != '')
    <h2 class="subtitulo">DEL {{ $fecha_contrato }}</h2>
@endif
<p>
    Entre los suscritos, <strong>{{ $nombre_cliente }}</strong>, mayor de edad, domiciliado en {{ $ciudad_dpt }}, identificado con {{ $tipo_doc_cliente }} N° {{ $documento_cliente }}, actuando en nombre y representacion propia, quien para efectos del presente contrato se denominara EL <strong>CONTRATANTE</strong>; y <strong>{{ env('NOMBRE_REPRESENTANTE') }}</strong>, mayor de edad, domiciliado en {{ env('IDENTI_REPRESENTANTE_EXPED') }} identificado con {{ env('TIPO_IDENT_REPRESENTANTE') }} Nº {{ env('IDENTI_REPRESENTANTE') }} expedida en {{ env('IDENTI_REPRESENTANTE_EXPED') }}, actuando en representacion legal de la empresa <strong>{{ env('RAZON') }}</strong>. persona juridica inscrita en camara de comercio de {{ env('CIU_DPT_EMPRE') }}, con NIT N° {{ env('NIT') }} quien para efectos del presente contrato se llamara <strong>CONTRATISTA</strong>, acuerdan celebrar el presente CONTRATO DE OBRA CIVIL, el cual se regira por las siguientes clausulas:
</p>

<p>
<strong>PRIMERA. OBJETO: </strong> En desarrollo del presente contrato, EL CONTRATISTA se obliga con EL CONTRATANTE a ejecutar las obras de remodelacion y/o elaboracion de OBRA BLANCA del bien inmueble ubicado en la direccion {{ $direccion_proye }}, con un area privada de {{ $area_privada_proye }} m² conforme las caracteristicas que se detallan a continuacion
</p>

<table class="presupuesto">
    <tr>
        <th style="width: 67%;">Entregables</th>
        <th style="width: 23%;">Precios</th>
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
                <td class="tiempo" rowspan="{{ $rowspan + 1}}">
                    {{ $dias_trabajo }} dias trabajables
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
    <strong>SEGUNDA. PLAZO: </strong>El plazo para la ejecucion del presente contrato, sera por el termino {{ $dias_proye }} ({{ $meses_proye }}) dias habiles, los cuales se manifiesta que igualmente que dependera de la aceptacion del diseño por parte de EL CONTRATANTE. En este caso, solo cuando se acepte por parte de EL CONTRATANTE el diseño y sus modificaciones, se entendera que debera iniciar la obra. Sin embargo, expone EL CONTRATANTE que, si aquel realiza el primer pago dispuesto en la clausula siguiente de manera tardia a la fecha de inicio de obra, el termino se entendera dispuesto desde la fecha de pago. Paragrafo primero. EL CONTRATANTE se obliga a firmar los formatos donde se expongan los diseños que se realizaran en la obra, y desde la fecha de la firma de los mismos se contara el plazo dispuesto en la obra, junto con el respectivo pago. Paragrafo segundo. EL CONTRATANTE acepta que EL CONTRATISTA suspenda la obra si a la fecha dispuesta para pago EL CONTRATANTE no realiza el pago debido. Paragrafo tercero.  Si llegase a existir suspension de la obra con ocasion de EL CONTRATANTE, este acepta con la firma de este escrito que EL CONTRATISTA retome el conteo del termino que faltare para la entrega de la obra desde el momento que EL CONTRATANTE realice el pago debidamente. Paragrafo cuarto: las partes de comun acuerdo aceptan la prorroga del plazo pactado, por el termino de 30 dias habiles en caso de que EL CONTRATISTA informe imprevistos en la obra generado por terceros como, por ejemplo: proveedores.
</p>

<p>
    <strong>COMPROMISO. - EL CONTRATISTA </strong>se compromete a la compra de materiales, entrega de la obra con las especificaciones tecnicas, dentro del presupuesto y en los tiempos pactados, detalles que se encuentran adjuntos al presente documento.
</p>
<p>
    Ademas, debera velar y sera su responsabilidad la de tomar las medidas necesarias para evitar accidentes laborales. Asi como mantener la obra con las condiciones de higiene y seguridad exigidas por la ley y los reglamentos. AESCALA ARQUITECTURA se compromete, a: corroborar que todas las personas que van a ejecutar las obras estan afiliadas al sistema de riesgos laborales; que es responsabilidad de AESCALA ARQUITECTURA supervisar que los trabajadores tienen sus implementos de seguridad; por consiguiente, en caso de accidente laboral la contratante y/o cliente no es responsable.
</p>

<p>
    <strong>TERCERA. VALOR DEL CONTRATO: </strong>EL CONTRATANTE pagara la suma total de
    <strong>{{ $tx_valor_total }} pesos (${{ $valor_total }})</strong>.
</p>

<p>
  <strong>CUARTA. FORMA DE PAGO: </strong>EL CONTRATANTE pagara de la siguiente manera:
</p>
<ul>
    @if($por_term_1!=0)
        <li>({{ $por_term_1 }}%) correspondiente a (${{ $val_term_1 }}) para dar inicio a la etapa de diseño.</li>
    @endif
    @if($por_term_2!=0)
        <li>({{ $por_term_2 }}%) correspondiente a (${{ $val_term_2 }}) al momento de aprobado diseño para dar inicio a la obra.</li>
    @endif
    @if($por_term_3 != 0)
        <li>
            ({{ $por_term_3 }}%) correspondiente a (${{ $val_term_3 }}) previo a enviar a corte la carpintería.
        </li>
    @endif
    @if($por_term_4 != 0)
        <li>
            ({{ $por_term_4 }}%) correspondiente a (${{ $val_term_4 }}) previo a iniciar la instalacion de la carpintería.
        </li>
    @endif
    @if($por_term_5 != 0)
        <li>
            ({{ $por_term_5 }}%) correspondiente a (${{ $val_term_5 }}) previo a iniciar el corte e instalacion del mesón, griferia y accesorios.
        </li>
    @endif
    @if($por_term_6!=0)
        <li>({{ $por_term_6 }}%) correspondiente a (${{ $val_term_6 }}) al momento que EL CONTRATISTA informe a EL CONTRATANTE que hara entrega de la obra.</li>
    @endif
</ul>

<p><strong>Paragrafo primero:</strong> EL CONTRATISTA no iniciara la obra hasta que EL CONTRATANTE no demuestre que ha realizado el primer pago correspondiente al CINCUENTA POR CIENTO (50%) de la obra.</p>

<p><strong>Paragrafo segundo:</strong> EL CONTRATISTA podra suspender la obra si EL CONTRATANTE no realiza los pagos al momento de ser notificado para hacerlo conforme lo estipulado en esta clausula.</p>

<p><strong>Paragrafo tercero:</strong> EL CONTRATISTA queda facultado por EL CONTRATANTE para suspender la obra si este ultimo no realiza los pagos debidos, para lo cual los plazos dispuestos en la clausula segunda se retomaran una vez se haya efectuado los pagos correspondientes por EL CONTRATANTE.</p>

<p><strong>NOTA:</strong></p>
<ul>
    @forelse ($notas as $nota)
        <li>{{ $nota->nota }}</li>
    @empty
        <li>La separacion de cupo valor $3.000.000 se restara del valor del porcentaje de la etapa de diseño; el valor de la propuesta se congelara durante seis meses a partir de la fecha de la firma.</li>
        <li>Pasado los seis meses se realizara un ajuste en el presupuesto de la propuesta de acuerdo al valor establecido en el momento.</li>
    @endforelse
</ul>

<p>
    <strong>QUINTA. SANCION POR INCUMPLIMIENTO: </strong>Las partes acuerdan que en caso de que EL CONTRATANTE no realice el pago total de la obra dispuesto en la clausula cuarta no podra reclamarse garantia de la obra.
</p>

<p>
    <strong>SEXTA: </strong>El domicilio contractual sera la ciudad de Cali, Valle.
</p>

<p>
<strong>SEPTIMA. RETRACTO: </strong> EL CONTRATANTE debera notificar del retracto a EL CONTRATISTA por escrito, una vez EL CONTRATISTA sea debidamente notificado parara las obras y entregara la obra en el estado en que se encuentre. En el evento de retracto por parte de EL CONTRATANTE, este autoriza expresamente desde ahora a EL CONTRATISTA a cobrar los valores dispuestos hasta el avance de obra en el que se encuentre al momento del retracto, cobro que podra realizarse dentro del proceso civil respectivo por EL CONTRATISTA, sin requerimiento o citacion para constituir en mora.
</p>

<p>
<strong>OCTAVA. GARANTIA: </strong>EL CONTRATISTA manifiesta que la obra realizada por aquel y la cual es objeto de este contrato tiene UN (01) AÑO de garantia desde la entrega de la obra a EL CONTRATANTE. Sin embargo, EL CONTRATANTE reconoce que no existira responsabilidad de EL CONTRATISTA cuando existan daños en la obra por manipulacion indebida de EL CONTRATANTE o sus dependientes y de terceras personas.
</p>

<p>
<strong>NOVENA. MATERIALES: </strong>EL CONTRATISTA manifiesta que los valores dispuestos en la clausula cuarta de este contrato se cubren a todo costo.
</p>

<p>
<strong>DECIMA. JUSTA CAUSA DE TERMINACION DEL CONTRATO Y/O SUSPENSION: </strong>El incumplimiento en los pagos por parte de EL CONTRATANTE es una justa causa para terminar el contrato por parte de EL CONTRATISTA. Son justas causas de suspension las que provengan de fuerza mayor y caso fortuito, o las que sean generadas por terceros y que no sean responsabilidad de EL CONTRATISTA. Asi mismo, seran justas causas para terminar el contrato por cualquiera de las partes las dispuestas en el Codigo Civil y Codigo de Comercio.
</p>

<p>
<strong>DECIMA PRIMERA. ENTREGA DE LA OBRA: </strong>La obra sera entregada con salvaguarda de calidad de la obra, y bajo los parametros de funcionalidad del bien inmueble, manifestando que si EL CONTRATISTA cumple con el 100% de lo dispuesto como objeto contractual no habra lugar por parte de EL CONTRATANTE a negarse a recibir el mismo.
</p>

<p>
<strong>DECIMA SEGUNDA. OBLIGACIONES DEL CONTRATANTE: </strong>EL CONTRATANTE se obliga a cubrir los gastos de energia que realicen las maquinas o elementos de trabajo que use EL CONTRATISTA para el desarrollo adecuado del contrato, manifestando que dichos costos se cargaran en efecto al recibo de servicios publicos de EL CONTRATANTE y seran asumidos en su totalidad por el, manifestando EL CONTRATANTE que desde ahora asume los mencionados pagos y desobliga a EL CONTRATISTA de ello. Igualmente, EL CONTRATANTE se obliga a dejar autorizacion previa para la visita de EL CONTRATISTA durante el termino de ejecucion de la obra, en caso de incumplimiento en ello, que impida la ejecucion por parte de EL CONTRATISTA, desde ahora EL CONTRATANTE asume la responsabilidad y autoriza expresamente que se suspenda la obra en el termino que dure sin ingresar al inmueble de objeto de la misma, pudiendo EL CONTRATISTA retrasar la obra, para lo cual EL CONTRATANTE lo autoriza anticipadamente.
</p>

<p>
<strong>DECIMA TERCERA. DERECHOS DE AUTOR: </strong>La obra y diseño realizada por EL CONTRATISTA es unica y corresponde a su diseño y la de sus colaboradores, y EL CONTRATANTE acepta con la firma de este contrato que EL CONTRATISTA podra tomar fotografias de la obra y publicarlas en sus redes sociales para conocimiento de su publico.
</p>

<p>
<strong>DECIMA CUARTA. SUSPENSION DE ACTIVIDADES EN TEMPORADA DE FIN DE ANO: </strong>
Las partes acuerdan que los proyectos que se encuentren en ejecucion durante la temporada de Navidad y Año Nuevo podran ser suspendidos con motivo de las vacaciones colectivas, periodo en el cual las empresas y contratistas acostumbran cesar o limitar sus actividades. En consecuencia, EL CONTRATISTA pausara la ejecucion del proyecto durante dicho lapso, el cual no sera considerado como tiempo habil ni computable dentro de los plazos y cronogramas establecidos en el presente contrato.
</p>

<p>
    En constancia se firma en dos ejemplares el dia {{ $fecha_contrato }}.
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
    Direccion: {{ env('DIREC', '---') }} | Tel: {{ env('TEL', '---') }}
</footer>
</body>
</html>
