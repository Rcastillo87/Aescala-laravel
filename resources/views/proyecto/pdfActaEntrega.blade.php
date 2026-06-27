<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 80px 60px; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11pt; line-height: 1.5; color: #333; }
        
        .header-date { margin-bottom: 30px; }
        .info-section { margin-bottom: 20px; }
        .info-row { margin-bottom: 5px; }
        .label { font-weight: bold; width: 120px; display: inline-block; }

        .tabla-entrega { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .tabla-entrega th, .tabla-entrega td { border: 1px solid #000; padding: 8px; text-align: left; }
        .tabla-entrega th { background-color: #f2f2f2; }

        .lineas-observaciones { margin-top: 10px; }
        .linea { border-bottom: 1px solid #000; height: 25px; margin-bottom: 5px; }

        .firmas { width: 100%; margin-top: 50px; }
        .firmas td { width: 50%; vertical-align: top; text-align: left; }
        .espacio-firma { border-bottom: 1px solid #000; width: 80%; margin-bottom: 5px; height: 60px; }
    </style>
</head>
<body>

    <div class="header-date">
        {{ $ciudad_completa }}, a los días <span style="border-bottom: 1px solid #000; padding: 0 15px;">{{ $dia }}</span> 
        del mes de <span style="border-bottom: 1px solid #000; padding: 0 30px;">{{ $mes }}</span> de {{ $anio }},
    </div>

    <div class="info-section">
        <div class="info-row"><span class="label">Proyecto:</span> <strong>{{ $nombre_proyecto }}</strong></div>
        <div class="info-row"><span class="label">Dirección:</span> {{ $direccion }}</div>
        <div class="info-row"><span class="label">Propietario:</span> {{ $propietario }}</div>
        <div class="info-row"><span class="label">Representante:</span> {{ $representante }}</div>
    </div>

    <p style="text-align: justify;">
        Se reunieron los anteriormente mencionados con el objetivo de realizar la entrega de la obra ejecutada, objeto del acuerdo de servicio por medio del cual la empresa contratista se comprometió a realizar la obra blanca y carpintería con las siguientes especificaciones:
    </p>

    <table class="tabla-entrega">
        <thead>
            <tr>
                <th style="width: 75%;">Obra blanca / Entregables</th>
                <th style="width: 25%;">Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entregables as $e)
            <tr>
                <td>
                    <strong>{{ $e['titulo'] }}</strong>
                    <ul style="font-size: 9pt; margin: 5px 0;">
                        @foreach($e['items'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </td>
                <td style="vertical-align: middle;">$ {{ $e['valor'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td style="text-align: right; font-weight: bold;">TOTAL</td>
                <td style="font-weight: bold;">$ {{ $total_obra }}</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 10pt; text-align: justify;">
        Se hace constar que las obras han sido entregadas por el Contratista y recibidas a conformidad por parte del Contratante... (puedes copiar el texto legal de la foto 2 aquí).
    </p>

    <div class="lineas-observaciones">
        <strong>OBSERVACIONES:</strong>
        <div class="linea"></div>
        <div class="linea"></div>
        <div class="linea"></div>
    </div>

    <table class="firmas">
        <tr>
            <td>
                <div class="espacio-firma">
                    @if($imgRepre) <img src="{{ $imgRepre }}" style="height: 50px;"> @endif
                </div>
                <strong>Quien entrega:</strong><br>
                Representante Aescala
            </td>
            <td>
                <div class="espacio-firma"></div>
                <strong>Quien recibe:</strong><br>
                {{ $propietario }}
            </td>
        </tr>
    </table>

</body>
</html>