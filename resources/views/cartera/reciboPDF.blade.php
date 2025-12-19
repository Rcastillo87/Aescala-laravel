<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .no-border td {
            border: none;
        }

        .header {
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .title-box {
            border: 2px solid #444;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 10px;
        }

        .section {
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .section-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }

        .info-table td {
            padding: 4px 6px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            width: 35%;
        }

        .items th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 11px;
            padding: 6px;
            border-bottom: 1px solid #999;
        }

        .items td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .total-box {
            border-top: 2px solid #444;
            padding-top: 10px;
            font-size: 13px;
            font-weight: bold;
        }

        .observaciones {
            border: 1px solid #ccc;
            padding: 8px;
            height: 60px;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">

    <!-- HEADER -->
    <table class="no-border header">
        <tr>
            <td width="60%">
                <img src="{{ $logo }}" height="55" alt="Logo">
            </td>
            <td width="40%">
                <div class="title-box">RECIBO DE CAJA</div>
            </td>
        </tr>
    </table>

    <!-- DATOS -->
    <div class="section">
        <table class="info-table">
            <tr>
                <td width="50%">
                    <div class="section-title">Datos del Cliente</div>
                    <table class="no-border info-table">
                        <tr>
                            <td class="info-label">Proyecto:</td>
                            <td>{{ $proyecto->nombre_proyecto }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Cliente:</td>
                            <td>{{ $proyecto->nombre_cliente }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Identificación:</td>
                            <td>{{ $tipo_doc_acro }} {{ $proyecto->cedula_cliente }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Ciudad:</td>
                            <td>{{ $ciudad_dpt }}</td>
                        </tr>
                    </table>
                </td>

                <td width="50%">
                    <div class="section-title">Datos de la Empresa</div>
                    <table class="no-border info-table">
                        <tr>
                            <td class="info-label">Razón Social:</td>
                            <td>{{ env('RAZON', 'AESCALA') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">NIT:</td>
                            <td>{{ env('NIT', '901.451.774-2') }}</td>
                        </tr>
                        <tr>
                            <td class="info-label">Dirección:</td>
                            <td>{{ env('DIREC', 'Carrera 1D #46-63') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <!-- ITEMS -->
    <div class="section">
        <table class="items">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th width="15%">Fecha</th>
                    <th width="55%">Descripción</th>
                    <th width="20%" class="text-right">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                <tr>
                    <td class="text-center">{{ $item->rc }}</td>
                    <td>{{ $item->fecha_pago }}</td>
                    <td>{{ $item->descripcion }}</td>
                    <td class="text-right">
                        $ {{ number_format($item->valor_pago, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- OBSERVACIONES + TOTAL -->
    <div class="section">
        <table>
            <tr>
                <td width="65%">
                    <div class="section-title">Observaciones</div>
                    <div class="observaciones"></div>
                </td>
                <td width="35%" class="text-right">
                    <div class="total-box">
                        TOTAL PAGADO<br>
                        $ {{ number_format($total_pago, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Documento generado automáticamente • {{ now()->format('d/m/Y H:i') }}
    </div>

</div>

</body>
</html>
