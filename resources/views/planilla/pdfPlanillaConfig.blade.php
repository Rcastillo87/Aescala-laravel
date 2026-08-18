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
            padding-bottom: 10px;
            margin-bottom: 25px; /* Separación asegurada */
        }
        .header h1 {
            color: #242e68;
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 11px;
        }
        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .info-box p {
            margin: 4px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px; /* Separación del título */
        }
        th, td {
            padding: 7px;
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

    <div class="info-box">
        <p><strong>Nombre del Proyecto:</strong> {{ $proyecto->nombre_proyecto ?? 'Sin nombre asignado' }}</p>
        <p><strong>Valor Base:</strong> $ {{ number_format($valorBase, 0, ',', '.') }}</p>
    </div>

    <h3>Desglose de Porcentajes</h3>
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
                @php $totalPorcentaje += $item['porcentaje']; @endphp
                <tr>
                    <td>{{ $item['concepto'] }}</td>
                    <td class="text-right">{{ $item['porcentaje'] }}%</td>
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

    <footer>
        {{ env('RAZON') }} – NIT: {{ env('NIT') }} – {{ env('CIU_DPT_EMPRE') }} <br>
        Dirección: {{ env('DIREC', '---') }} | Tel: {{ env('TEL', '---') }}
    </footer>

</body>
</html>