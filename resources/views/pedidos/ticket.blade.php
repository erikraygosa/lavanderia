<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $pedido->folio }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            gap: 0;
        }

        .copia {
            background: white;
            width: 80mm;
            padding: 6mm 5mm;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        /* Línea de corte entre copias */
        .corte {
            width: 80mm;
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 0;
            background: #f5f5f5;
        }
        .corte-linea {
            flex: 1;
            border-top: 1px dashed #999;
        }
        .corte span {
            font-size: 14px;
            color: #999;
            white-space: nowrap;
        }

        /* Etiqueta de copia */
        .etiqueta-copia {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 2px;
            padding: 2px 0 4px;
            color: #444;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }
        .small  { font-size: 10px; }

        .divider       { border: none; border-top: 1px dashed #000; margin: 4px 0; }
        .divider-solid { border: none; border-top: 1px solid #000;  margin: 4px 0; }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .col-desc   { width: 50%; }
        .col-cant   { width: 12%; text-align: center; }
        .col-precio { width: 19%; text-align: right; }
        .col-sub    { width: 19%; text-align: right; }

        .total-row td { font-size: 14px; font-weight: bold; padding-top: 4px; }

        /* Botones (solo pantalla) */
        .acciones {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }
        .btn {
            padding: 8px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-family: sans-serif;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-print    { background: #4f46e5; color: white; }
        .btn-back     { background: #e5e7eb; color: #374151; }

        @media print {
            body { background: white; padding: 0; gap: 0; }
            .copia { box-shadow: none; padding: 4mm 3mm; }
            .acciones { display: none; }
            .corte { background: white; padding: 3px 0; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>

@php
    $logoPath      = \App\Models\Configuracion::obtener('logo_path', '');
    $negocioNom    = \App\Models\Configuracion::obtener('negocio_nombre', 'Lavandería');
    $negocioDir    = \App\Models\Configuracion::obtener('negocio_direccion', '');
    $negocioTel    = \App\Models\Configuracion::obtener('negocio_telefono', '');
    $ticketMensaje = \App\Models\Configuracion::obtener('ticket_mensaje', '');
    $facebookUrl   = \App\Models\Configuracion::obtener('facebook_url', '');

    // Logo en base64
    $logoB64  = '';
    $logoMime = 'image/jpeg';
    if ($logoPath) {
        $abs     = storage_path('app/public/' . $logoPath);
        $ext     = strtolower(pathinfo($abs, PATHINFO_EXTENSION));
        $logoMime = match($ext) { 'png' => 'image/png', 'gif' => 'image/gif', default => 'image/jpeg' };
        $logoB64 = file_exists($abs) ? base64_encode(file_get_contents($abs)) : '';
    }
@endphp

{{-- ── Botones (no se imprimen) ─────────────────────────────────────────── --}}
<div class="acciones">
    <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-back">← Regresar</a>
    <button onclick="window.print()" class="btn btn-print">🖨️ Imprimir</button>
</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- COPIA 1 — ORIGINAL (tienda)                                             --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="copia">
    <p class="etiqueta-copia">— ORIGINAL —</p>

    @include('pedidos._ticket-cuerpo', [
        'logoB64'      => $logoB64,
        'logoMime'     => $logoMime,
        'negocioNom'   => $negocioNom,
        'negocioDir'   => $negocioDir,
        'negocioTel'   => $negocioTel,
        'pedido'       => $pedido,
        'ticketMensaje'=> $ticketMensaje,
        'facebookUrl'  => $facebookUrl,
        'mostrarQr'    => false,   // QR solo en la copia del cliente
    ])
</div>

{{-- ── Línea de corte ──────────────────────────────────────────────────── --}}
<div class="corte">
    <div class="corte-linea"></div>
    <span>✂</span>
    <div class="corte-linea"></div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════ --}}
{{-- COPIA 2 — CLIENTE                                                       --}}
{{-- ════════════════════════════════════════════════════════════════════════ --}}
<div class="copia">
    <p class="etiqueta-copia">— COPIA CLIENTE —</p>

    @include('pedidos._ticket-cuerpo', [
        'logoB64'      => $logoB64,
        'logoMime'     => $logoMime,
        'negocioNom'   => $negocioNom,
        'negocioDir'   => $negocioDir,
        'negocioTel'   => $negocioTel,
        'pedido'       => $pedido,
        'ticketMensaje'=> $ticketMensaje,
        'facebookUrl'  => $facebookUrl,
        'mostrarQr'    => true,    // QR aparece solo en la copia del cliente
    ])
</div>

</body>
</html>
