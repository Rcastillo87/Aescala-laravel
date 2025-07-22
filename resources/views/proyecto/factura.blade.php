<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura Proyecto {{ $proyecto['nombre_proyecto'] }}</title>
    <style>
        @page {
            margin: 70px 25px 40px 25px;
        }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px;
            line-height: 1.3;
            padding: 0;
            padding-top:20px;
        }
        .header {
            position: fixed;
            top: -50px;
            left: 0;
            right: 0;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            border-bottom: 1px solid #969292;
        }
        .logo { 
            max-width: 100%;
            height: auto;
            width: 180px;
            max-height: 60px;
            object-fit: contain; 
        }
        .info-empresa { 
            text-align: right;
            font-size: 10px;
            line-height: 1.2;
        }
        .titulo { 
            text-align: center; 
            margin: 20px 0 15px 0; 
            font-size: 16px; 
            font-weight: bold;
        }
        .info-box {
            padding: 3px 0;
        }
        .tabla-despachos { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
            font-size: 10px;
        }
        .tabla-despachos th, .tabla-despachos td { 
            border: 1px solid #ddd; 
            padding: 5px; 
            text-align: left; 
        }
        .tabla-despachos th { 
            background-color: #f2f2f2; 
            font-weight: bold; 
        }
        .tabla-items { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 10px; 
            font-size: 10px;
        }
        .tabla-items th, .tabla-items td { 
            border: 1px solid #ddd; 
            padding: 4px; 
        }
        .tabla-items th { 
            background-color: #f8f8f8; 
        }
        .resumen { 
            page-break-inside: avoid;
            break-inside: avoid;
            border-top: 2px solid #333; 
            margin-top: 15px; 
            padding-top: 0px; 
        }
        .despacho-section {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .totales { 
            text-align: right; 
            margin-top: 10px; 
        }
        .footer {
            position: fixed;
            bottom: -30px;
            left: 0;
            right: 0;
            height: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
        .span-green { 
            color: #28a745; 
            font-weight: bold; 
        }
        .span-red { 
            color: #dc3545; 
            font-weight: bold; 
        }
        .page-break { 
            page-break-after: always; 
        }
        .despacho-header {
            background-color: #f2f2f2;
            padding: 4px;
            margin: 10px 0 5px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Encabezado que se repetirá en cada página -->
    <div class="header" style="display: table; width: 100%;">
        <div style="display: table-cell; vertical-align: middle; width: 50%;">
            <img src="{{ $empresa['logo'] }}" alt="Logo" class="logo">
        </div>
        <div class="info-empresa" style="display: table-cell; vertical-align: middle; width: 50%; text-align: right;">
            <div style="font-weight:bold; font-size:11px;">{{ $empresa['razon'] }}</div>
            <div>NIT: {{ $empresa['nit'] }}</div>
            <div>{{ $empresa['direccion'] }}</div>
            <div>Tel: {{ $empresa['telefono'] }}</div>
        </div>
    </div>

    <!-- Pie de página que se repetirá en cada página -->
    <div class="footer">
        <div>{{ $empresa['razon'] }} - NIT: {{ $empresa['nit'] }}</div>
        <div>Documento generado electrónicamente el {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <!-- Contenido principal -->
    <div class="titulo">
        REPORTE DE DESPACHOS - PROYECTO {{ $proyecto['nombre_proyecto'] }}
    </div>

    <!-- Información del cliente en dos columnas -->
    <table width="100%" style="margin-bottom: 15px; border-collapse: collapse;">
        <tr>
            <td style="width: 40%; padding: 8px; background-color: #f9f9f9;">
                <div class="info-box"><strong>Cliente:</strong> {{ $proyecto['nombre_cliente'] }}</div>
                <div class="info-box"><strong>Teléfono:</strong> {{ $proyecto['telefono_cliente'] }}</div>
                <div class="info-box"><strong>Dir. proyecto:</strong> {{ $proyecto['direccion'] }}</div>
            </td>
            <td style="width: 25%; padding: 8px; background-color: #f9f9f9;">
                <div class="info-box"><strong>Fec. Inicio:</strong> {{ \Carbon\Carbon::parse($proyecto['fec_inicio'])->format('d/m/Y') }}</div>
                <div class="info-box"><strong>Fec. Fin Est:</strong> {{ \Carbon\Carbon::parse($proyecto['fec_fin_estimado'])->format('d/m/Y') }}</div>
                <div class="info-box"><strong>Fec. Reporte:</strong> {{ date('d/m/Y H:i') }}</div>
            </td>
            <td style="width: 35%; padding: 8px; background-color: #f9f9f9; vertical-align: top;">
                <div class="info-box"><strong>Arq. Encargado:</strong> {{ $proyecto->user['nombre_completo'] }}</div>
                @if ($proyecto->userOB)
                    <div class="info-box"><strong>Cont. Obra Blanca:</strong> {{ $proyecto->userOB['nombre_completo'] }}</div>
                @endif
                @if ($proyecto->userCarpi)
                    <div class="info-box"><strong>Cont. Carpinteria:</strong> {{ $proyecto->userCarpi['nombre_completo'] }}</div>
                @endif
            </td>
        </tr>
    </table>

    @foreach($despacho as $index => $desp)
        <div class="despacho-section">
            <div class="despacho-header">
                Despacho #{{ $loop->iteration }}: {{ $desp['codigo'] }} - {!! $desp['spanEstado'] !!}
            </div>
            
            <div style="margin-bottom: 8px;">
                <div><strong>Fecha:</strong> {{ $desp['createdAt'] }}</div>
                <div><strong>Responsable:</strong> {{ $desp['nombre_completo'] }}</div>
            </div>
            
            <table class="tabla-items">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 40%;">Material</th>
                        <th style="width: 10%; text-align: center;">Cantidad</th>
                        <th style="width: 10%;">Se Cobra</th>
                        <th style="width: 12%; text-align: right;">Valor Unitario</th>
                        <th style="width: 15%;">Tipo</th>
                        <th style="width: 13%; text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($desp['items'] as $itemIndex => $item)
                    <tr>
                        <td style="text-align: center;">{{ $itemIndex + 1 }}</td>
                        <td>{{ $item['nombre_material'] }}</td>
                        <td style="text-align: center;">{{ $item['cantidad'] }}</td>
                        <td style="text-align: center;">{!! $item['isCobro'] !!}</td>
                        <td style="text-align: right;">${{ number_format($item['valor_unidad'], 2, ',', '.') }}</td>
                        <td style="text-align: center;">{!! $item['spanTipo'] !!}</td>
                        <td style="text-align: right;">${{ number_format($item['cantidad'] * $item['valor_unidad'], 2, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @php
                $subtotalDespacho = array_reduce($desp['items'], function($carry, $item) {
                    return $carry + ($item['cantidad'] * $item['valor_unidad']);
                }, 0);
            @endphp
            
            <div style="text-align: right; margin-top: 3px;">
                <div><strong>Subtotal Despacho:</strong> ${{ number_format($subtotalDespacho, 2, ',', '.') }}</div>
            </div>
        </div>
    @endforeach

    <div class="resumen">
        @php
            $totalDespachos = 0;
            $totalDevoluciones = 0;
            $ivaPercentage = env('IVA_PERCENTAGE', 0); // Porcentaje de IVA desde .env (0 por defecto)
            
            foreach($despacho as $desp) {
                $subtotal = array_reduce($desp['items'], function($carry, $item) {
                    return $carry + ($item['cantidad'] * $item['valor_unidad']);
                }, 0);
                
                if (strpos($desp['spanEstado'], 'Devolucion') !== false) {
                    $totalDevoluciones += $subtotal;
                } else {
                    $totalDespachos += $subtotal;
                }
            }
            
            $subtotalGeneral = $totalDespachos - $totalDevoluciones;
            $iva = $subtotalGeneral * ($ivaPercentage / 100);
            $totalGeneral = $subtotalGeneral + $iva;
        @endphp
        
        <h3 style="margin-bottom: 8px; font-size: 12px;">Resumen General</h3>
        
        <table class="tabla-despachos">
            <tbody>
                <tr>
                    <td style="width: 70%;">Total Despachos</td>
                    <td style="width: 30%; text-align: right;">${{ number_format($totalDespachos, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Total Devoluciones</td>
                    <td style="text-align: right;">-${{ number_format($totalDevoluciones, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Subtotal</strong></td>
                    <td style="text-align: right;"><strong>${{ number_format($subtotalGeneral, 2, ',', '.') }}</strong></td>
                </tr>
                @if($ivaPercentage > 0)
                <tr>
                    <td>IVA ({{ $ivaPercentage }}%)</td>
                    <td style="text-align: right;">${{ number_format($iva, 2, ',', '.') }}</td>
                </tr>
                @endif
                <tr style="background-color: #f8f8f8;">
                    <td><strong>TOTAL GENERAL</strong></td>
                    <td style="text-align: right;"><strong>${{ number_format($totalGeneral, 2, ',', '.') }}</strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>