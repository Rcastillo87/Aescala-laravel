<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <style>

        *{
            box-sizing: border-box;
        }

        body{
            font-family: DejaVu Sans;
            font-size: 10px;
            color: #1f2937;
            margin: 0;
            padding: 14px;
            background: #fff;
        }

        table{
            width: 100%;
            border-collapse: collapse;
        }

        .header{
            border-bottom: 2px solid #16a34a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .logo{
            height: 42px;
        }

        .title{
            text-align: right;
        }

        .title h1{
            margin: 0;
            font-size: 18px;
            color: #16a34a;
            font-weight: bold;
            letter-spacing: .5px;
            line-height: 1;
        }

        .title p{
            margin: 3px 0 0 0;
            font-size: 9px;
            color: #6b7280;
        }

        .card{
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px 10px;
            background: #f9fafb;
            vertical-align: top;
        }

        .section-title{
            font-size: 10px;
            font-weight: bold;
            color: #16a34a;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .info-table td{
            padding: 1px 0;
            vertical-align: top;
            line-height: 1.25;
        }

        .label{
            font-weight: bold;
            width: 80px;
            color: #374151;
        }

        .items{
            margin-top: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .items thead{
            background: #16a34a;
            color: #fff;
        }

        .items th{
            padding: 5px 6px;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 1.1;
        }

        .items td{
            padding: 5px 6px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 9px;
            line-height: 1.2;
        }

        .items tbody tr:nth-child(even){
            background: #f9fafb;
        }

        .text-right{
            text-align: right;
        }

        .total-box{
            margin-top: 10px;
            background: #16a34a;
            color: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            text-align: right;
            font-size: 12px;
            font-weight: bold;
        }

        .comentario{
            margin-top: 10px;
            border: 1px dashed #d1d5db;
            border-radius: 8px;
            padding: 8px 10px;
            background: #fcfcfc;
            font-size: 9px;
            line-height: 1.3;
        }

        .footer{
            margin-top: 14px;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }

    </style>

</head>

<body>

    {{-- HEADER --}}
    <table class="header">
        <tr>

            <td width="45%">
                <img
                    src="{{ public_path('img/logo.png') }}"
                    class="logo"
                    alt="Logo">
            </td>

            <td width="55%" class="title">

                <h1>RECIBO DE CAJA</h1>

                <p>
                    {{ now()->format('d/m/Y H:i A') }}
                </p>

            </td>

        </tr>
    </table>

    {{-- INFORMACIÓN --}}
    <table>
        <tr>

            <td width="49%" valign="top">

                <div class="card">

                    <div class="section-title">
                        Cliente
                    </div>

                    <table class="info-table">

                        <tr>
                            <td class="label">Proyecto:</td>
                            <td>{{ $proyecto->nombre_proyecto }}</td>
                        </tr>

                        <tr>
                            <td class="label">Cliente:</td>
                            <td>{{ $proyecto->nombre_cliente }}</td>
                        </tr>

                        <tr>
                            <td class="label">Documento:</td>
                            <td>
                                {{ $tipo_doc_acro }}
                                {{ $proyecto->cedula_cliente }}
                            </td>
                        </tr>

                        <tr>
                            <td class="label">Ciudad:</td>
                            <td>{{ $ciudad_dpt }}</td>
                        </tr>

                    </table>

                </div>

            </td>

            <td width="2%"></td>

            <td width="49%" valign="top">

                <div class="card">

                    <div class="section-title">
                        Empresa
                    </div>

                    <table class="info-table">

                        <tr>
                            <td class="label">Razón:</td>
                            <td>{{ env('RAZON', 'AESCALA') }}</td>
                        </tr>

                        <tr>
                            <td class="label">NIT:</td>
                            <td>{{ env('NIT', '901.451.774-2') }}</td>
                        </tr>

                        <tr>
                            <td class="label">Dirección:</td>
                            <td>{{ env('DIREC', 'Carrera 1D #46-63') }}</td>
                        </tr>

                        <tr>
                            <td class="label">Tel:</td>
                            <td>{{ env('TEL', '---') }}</td>
                        </tr>

                    </table>

                </div>

            </td>

        </tr>
    </table>

    {{-- TABLA --}}
    <table class="items">

        <thead>
            <tr>
                <th width="15%">Fecha</th>
                <th width="45%">Concepto</th>
                <th width="25%">Comentario</th>
                <th width="15%" class="text-right">Valor</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($items as $item)

                <tr>

                    <td>
                        {{ \Carbon\Carbon::parse($item->fecha_pago)->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ $item->descripcion }}
                    </td>

                    <td>
                        {{ $item->comentario ?: '--' }}
                    </td>

                    <td class="text-right">
                        $ {{ number_format($item->valor, 0, ',', '.') }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

    {{-- TOTAL --}}
    <div class="total-box">
        TOTAL PAGADO:
        $ {{ number_format($total_pago, 0, ',', '.') }}
    </div>

    {{-- OBSERVACIONES --}}
    @if(!empty($items[0]->comentario))

        <div class="comentario">

            <div class="section-title">
                Observaciones
            </div>

            {{ $items[0]->comentario }}

        </div>

    @endif

    {{-- FOOTER --}}
    <div class="footer">

        {{ env('RAZON') }}
        |
        NIT: {{ env('NIT') }}
        |
        {{ env('DIREC', '---') }}
        |
        Tel: {{ env('TEL', '---') }}

    </div>

</body>
</html>
