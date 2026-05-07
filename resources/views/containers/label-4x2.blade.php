<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta {{ $pallet->pallet_code }}</title>
    <style>
        /* Configuración para impresora térmica 4x2 pulgadas (Aprox 100x50 mm) */
        @page {
            size: 4in 2in;
            margin: 0;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 3mm;
            width: 4in;
            height: 2in;
            box-sizing: border-box;
            background-color: white;
            color: black;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid black;
            padding-bottom: 2px;
        }

        .lpn-title {
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .lpn-code {
            font-size: 14pt;
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        .container-info {
            text-align: right;
            font-size: 8pt;
            font-weight: bold;
        }

        .content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            margin-bottom: auto;
        }

        .metric {
            text-align: center;
        }

        .metric-value {
            font-size: 22pt;
            font-weight: 900;
            line-height: 1;
        }

        .metric-label {
            font-size: 7pt;
            font-weight: bold;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            font-size: 6pt;
            border-top: 1px solid black;
            padding-top: 2px;
        }

        /* Ocultar UI del navegador */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="header">
        <div>
            <div class="lpn-title">TARIMA / PALLET</div>
            <div class="lpn-code">{{ $pallet->pallet_code }}</div>
        </div>
        <div class="container-info">
            CONT:<br>{{ $pallet->container->container_number }}
        </div>
    </div>
    
    <div class="content">
        <div class="metric">
            <div class="metric-value">{{ $pallet->boxes->count() }}</div>
            <div class="metric-label">CAJAS</div>
        </div>
        
        <div style="font-size: 24pt; font-weight: 100;">|</div>

        <div class="metric">
            <div class="metric-value">{{ number_format($pallet->boxes->sum('quantity')) }}</div>
            <div class="metric-label">PIEZAS</div>
        </div>
    </div>
    
    <div class="footer">
        <div>Z: {{ $pallet->location->zone ?? 'N/A' }} P: {{ $pallet->location->code ?? 'N/A' }}</div>
        <div>{{ now()->format('d/m/y H:i') }}</div>
    </div>

</body>
</html>