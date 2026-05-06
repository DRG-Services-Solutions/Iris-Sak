<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta {{ $pallet->pallet_code }}</title>
    <style>
        /* Configuración estricta para impresora térmica 4x2 cm */
        @page {
            size: 4cm 2cm;
            margin: 0;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 2mm;
            width: 4cm;
            height: 2cm;
            box-sizing: border-box;
            background-color: white;
            color: black;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* Tipografía optimizada para no verse borrosa en bajo DPI */
        .title {
            font-size: 7.5pt;
            font-weight: 900;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .details {
            font-size: 6pt;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .legend {
            font-size: 5.5pt;
            font-weight: bold;
            text-transform: uppercase;
            background-color: black;
            color: white;
            padding: 2px 4px;
            border-radius: 2px;
            letter-spacing: 0.5px;
            width: 90%;
        }

        /* Ocultar elementos del navegador al imprimir */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="title">{{ $pallet->pallet_code }}</div>
    
    <div class="details">
        {{ $pallet->boxes->count() }} CAJAS | {{ number_format($pallet->boxes->sum('quantity')) }} PZAS
        <br>
      
    </div>
    
    <div class="legend">Lista para envío</div>

</body>
</html>