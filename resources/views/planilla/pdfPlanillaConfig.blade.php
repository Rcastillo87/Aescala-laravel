<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reporte de Planilla - Proyecto {{ $proyecto->id }}</title>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
            font-size: 11px;
        }
        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 50px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        header img {
            max-height: 55px;
            float: left;
        }
        .header {
            border-bottom: 2px solid #242e68;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            color: #242e68;
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 3px 0 0;
            color: #666;
            font-size: 11px;
        }
        /* --- INFO BOX COMPACTO --- */
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 3px 5px;
            border: none;
            font-size: 11px;
            vertical-align: top;
        }
        /* ------------------------- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        th, td {
            padding: 6px 7px;
            text-align: left;
            border-bottom: 1px solid #cbd5e1;
        }
        th {
            background-color: #242e68;
            color: white;
            font-size: 11px;
        }
        td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f1f5f9;
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

        table.adicionales {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 9pt;
        }
        table.adicionales caption {
            background: #243c7a;
            color: #fff;
            font-weight: bold;
            font-size: 13px;
            padding: 5px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        table.adicionales th,
        table.adicionales td {
            padding: 5px 5px;
            border: 1px solid #ddd;
        }
        table.adicionales th {
            line-height: 1.1;
            background: #f1f3f9;
            color: #243c7a;
            text-align: left;
            font-size: 11px;
        }

    </style>
</head>
<body>

    <header>
        <img src="{{ public_path('img/logo.png') }}" alt="Logo Empresa">
    </header>

    <div class="header">
        <h1>Reporte de Configuración de Planilla</h1>
        <p>Tipo de Configuración Aplicada: <strong>{{ $txTipo }}</strong></p>
    </div>

    <!-- Info-box rediseñado en 2 columnas compactas -->
    <div class="info-box">
        <table class="info-table">
            <tr>
                <td style="width: 50%;"><strong>Nombre del Proyecto:</strong> {{ $proyecto->nombre_proyecto ?? 'Sin nombre asignado' }}</td>
                <td style="width: 50%;"><strong>Área del Proyecto:</strong> {{ $proyecto->area_privada }} m²</td>
            </tr>
            <tr>
                <td><strong>Valor Base:</strong> $ {{ number_format($totalDesglose, 0, ',', '.') }}</td>
                <td><strong>Residente:</strong> {{ $proyecto->user->nombre_completo ?? 'Sin residente asignado' }}</td>
            </tr>
        </table>
    </div>

    <h3 style="margin: 0 0 5px 0; color: #242e68; font-size: 13px;">Desglose de Porcentajes</h3>
    <table>
        <thead>
            <tr>
                <th>Concepto / Entregable</th>
                <th class="text-right">Porcentaje (%)</th>
                <th class="text-right">Monto Calculado</th>
            </tr>
        </thead>
        <tbody>
            @php $totalPorcentaje = 0; @endphp
            @forelse($porcentajes as $item)
                @php $totalPorcentaje += $item['en_pesos'] == 0 ? $item['porcentaje'] : 0; @endphp
                <tr>
                    <td>{{ $item['concepto'] }}</td>
                    <td class="text-right">{{ $item['en_pesos'] == 0 ? $item['porcentaje'] . '%' : '-'}}</td>
                    <td class="text-right">$ {{ number_format($item['monto'], 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; color: #666;">No hay porcentajes configurados para este cálculo.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($porcentajes) > 0)
        <tfoot>
            <tr class="total-row">
                <td class="text-right">Total General:</td>
                <td class="text-right">{{ $totalPorcentaje }}%</td>
                <td class="text-right">$ {{ number_format($totalDesglose, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <h3 style="margin: 0 0 5px 0; color: #242e68; font-size: 13px;">Entregables</h3>

    <table class="adicionales">
        <thead>
            <tr>
                <th style="width: 40px;">ITEM</th>
                <th>MATERIAL / ACTIVIDAD</th>
                <th style="width: 50px;">UNID</th>
                <th style="width: 50px;">CANT</th>
                <th style="width: 105px;">VALOR UNITARIO</th>
                <th style="width: 105px;">SUB VALORES</th>
            </tr>
        </thead>
        <tbody>
            @php $globalIndex = 1; @endphp
            @foreach($adicionales as $area => $datos)
                <tr class="section-title">
                    <td colspan="6" {!! $datos['espacio'] == 'Descuentos'? 'style="color: #FF0000;"' : '' !!}>{{ $datos['espacio'] }}</td>
                </tr>
                @foreach($datos['items'] as $item)
                <tr>
                    <td>{{ $globalIndex++ }}</td>
                    <td>{{ $item['descripcion'] }}</td>
                    <td style="text-align: center;">{{ $unidades[$item['unidad']] }}</td>
                    <td style="text-align: center;">{{ $item['cantidad'] }}</td>
                    <td class="right">{!! $item['valor_unitario'] == 0?'<b style="color: #FF0000;">Obsequio</b>' : '$ ' . $item['valor_unitario'] !!}</td>
                    <td class="right">{!! $item['valor_total'] == 0?'<b style="color: #FF0000;">Obsequio</b>' : '$ ' . $item['valor_total'] !!}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5" class="right">SUB TOTAL</td>
                    <td class="right">$ {{ number_format($datos['subtotal'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="5" class="right" style="color: #FF0000">TOTAL</td>
                <td class="right">$ {{ number_format($valorTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>


    <footer>
        {{ env('RAZON') }} – NIT: {{ env('NIT') }} – {{ env('CIU_DPT_EMPRE') }} <br>
        Dirección: {{ env('DIREC', '---') }} | Tel: {{ env('TEL', '---') }}
    </footer>

</body>
</html>