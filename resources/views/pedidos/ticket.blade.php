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
            justify-content: center;
            padding: 20px;
        }

        .ticket {
            background: white;
            width: 80mm;
            padding: 8mm 5mm;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: bold; }
        .small  { font-size: 10px; }

        .logo {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .divider {
            border: none;
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        .divider-solid {
            border: none;
            border-top: 1px solid #000;
            margin: 4px 0;
        }

        table { width: 100%; border-collapse: collapse; }
        td { padding: 2px 0; vertical-align: top; }
        .col-desc { width: 55%; }
        .col-cant { width: 15%; text-align: center; }
        .col-precio { width: 15%; text-align: right; }
        .col-sub { width: 15%; text-align: right; }

        .estado-pendiente { font-weight: bold; }
        .estado-pagado    { font-weight: bold; }
        .estado-abandonado{ font-weight: bold; }

        .total-row td { font-size: 14px; font-weight: bold; padding-top: 4px; }

        .acciones {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
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
        .btn-print { background: #4f46e5; color: white; }
        .btn-whatsapp { background: #25d366; color: white; }
        .btn-back { background: #e5e7eb; color: #374151; }

        @media print {
            body { background: white; padding: 0; }
            .ticket { box-shadow: none; }
            .acciones { display: none; }
            @page { margin: 0; size: 80mm auto; }
        }
    </style>
</head>
<body>

<div>
    {{-- Acciones (no se imprimen) --}}
    <div class="acciones">
        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-back">← Regresar</a>
        <button onclick="window.print()" class="btn btn-print">
            🖨️ Imprimir
        </button>
        @php
            $telefono = preg_replace('/\D/', '', $pedido->cliente->telefono ?? '');
            $items_txt = $pedido->items->map(fn($i) => "• {$i->descripcion} x{$i->cantidad} = $" . number_format($i->subtotal, 2))->join('%0A');
            $msg = urlencode("Hola {$pedido->cliente->nombre}, le compartimos su recibo de lavandería:\n\nFolio: {$pedido->folio}\nFecha entrega: " . $pedido->entregaFormateada() . "\n\n") . $items_txt . urlencode("\n\nTotal: $" . number_format($pedido->total, 2) . "\n\nGracias por su preferencia.");
        @endphp
        @if($telefono)
        <a href="https://wa.me/52{{ $telefono }}?text={{ $msg }}" target="_blank" class="btn btn-whatsapp">
            💬 WhatsApp
        </a>
        @endif
    </div>

    {{-- Ticket --}}
    <div class="ticket" id="ticket">

        {{-- Encabezado --}}
        @php
            $logoPath   = \App\Models\Configuracion::obtener('logo_path', '');
            $negocioNom = \App\Models\Configuracion::obtener('negocio_nombre', 'Lavandería');
            $negocioDir = \App\Models\Configuracion::obtener('negocio_direccion', '');
            $negocioTel = \App\Models\Configuracion::obtener('negocio_telefono', '');
        @endphp
        <div class="center">
            @if($logoPath)
                @php
                    $absPath = storage_path('app/public/' . $logoPath);
                    $ext     = strtolower(pathinfo($absPath, PATHINFO_EXTENSION));
                    $mime    = match($ext) { 'png' => 'image/png', 'gif' => 'image/gif', default => 'image/jpeg' };
                    $b64     = file_exists($absPath) ? base64_encode(file_get_contents($absPath)) : '';
                @endphp
                @if($b64)
                    <img src="data:{{ $mime }};base64,{{ $b64 }}"
                         style="max-height:60px; max-width:180px; margin: 0 auto 4px; display:block;" alt="Logo" />
                @endif
            @endif
            <p class="logo" style="font-size:14px;">{{ strtoupper($negocioNom) }}</p>
            @if($negocioDir) <p class="small" style="margin-top:2px;">{{ $negocioDir }}</p> @endif
            @if($negocioTel) <p class="small">Tel: {{ $negocioTel }}</p> @endif
        </div>

        <hr class="divider-solid" style="margin: 6px 0;">

        {{-- Info pedido --}}
        <table>
            <tr>
                <td class="bold">Folio:</td>
                <td class="right bold">{{ $pedido->folio }}</td>
            </tr>
            <tr>
                <td class="small">Fecha:</td>
                <td class="right small">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="small">Entrega:</td>
                <td class="right small bold">{{ $pedido->entregaFormateada() }}</td>
            </tr>
        </table>

        <hr class="divider" style="margin: 5px 0;">

        {{-- Cliente --}}
        <p class="bold">{{ $pedido->cliente->nombre }}</p>
        @if($pedido->cliente->telefono)
            <p class="small">Tel: {{ $pedido->cliente->telefono }}</p>
        @endif

        <hr class="divider" style="margin: 5px 0;">

        {{-- Items --}}
        <table>
            <thead>
                <tr>
                    <td class="col-desc bold small">Concepto</td>
                    <td class="col-cant bold small">Cant</td>
                    <td class="col-precio bold small">P/U</td>
                    <td class="col-sub bold small">Sub</td>
                </tr>
            </thead>
        </table>
        <hr class="divider">
        <table>
            <tbody>
                @foreach($pedido->items as $item)
                <tr>
                    <td class="col-desc small">{{ $item->descripcion }}</td>
                    <td class="col-cant small">{{ $item->cantidad + 0 }}</td>
                    <td class="col-precio small">${{ number_format($item->precio_unitario, 2) }}</td>
                    <td class="col-sub small">${{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="divider" style="margin: 5px 0;">

        {{-- Totales --}}
        <table>
            <tr>
                <td class="small">Subtotal</td>
                <td class="right small">${{ number_format($pedido->subtotal, 2) }}</td>
            </tr>
            @if($pedido->descuento > 0)
            <tr>
                <td class="small">
                    Descuento
                    @if($pedido->descuento_nota)
                        <br><span style="font-size:9px; font-style:italic;">{{ $pedido->descuento_nota }}</span>
                    @endif
                </td>
                <td class="right small">-${{ number_format($pedido->descuento, 2) }}</td>
            </tr>
            @endif
        </table>
        <hr class="divider-solid" style="margin: 4px 0;">
        <table>
            <tr class="total-row">
                <td>TOTAL</td>
                <td class="right">${{ number_format($pedido->total, 2) }}</td>
            </tr>
        </table>

        @if($pedido->estado === 'pagado')
        <hr class="divider" style="margin: 5px 0;">
        <table>
            <tr>
                <td class="small">Estado:</td>
                <td class="right bold small">✓ PAGADO</td>
            </tr>
            <tr>
                <td class="small">Método:</td>
                <td class="right small">{{ ucfirst($pedido->metodo_pago) }}</td>
            </tr>
        </table>
        @else
        <hr class="divider" style="margin: 5px 0;">
        <p class="center bold small">⏳ PENDIENTE DE PAGO</p>
        @endif

        @if($pedido->notas)
        <hr class="divider" style="margin: 5px 0;">
        <p class="small bold">Notas:</p>
        <p class="small">{{ $pedido->notas }}</p>
        @endif

        @php
            $ticketMensaje = \App\Models\Configuracion::obtener('ticket_mensaje', '');
            $facebookUrl   = \App\Models\Configuracion::obtener('facebook_url', '');
        @endphp

        @if($ticketMensaje)
        <hr class="divider" style="margin: 5px 0;">
        <p class="center bold small" style="font-size:10px;">{{ $ticketMensaje }}</p>
        @endif

        @if($facebookUrl)
        <hr class="divider" style="margin: 5px 0;">
        <p class="center small">Síguenos en redes sociales</p>
        <div style="text-align:center; margin: 4px 0;">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($facebookUrl) }}"
                 style="width:80px; height:80px;" alt="QR" />
        </div>
        <p class="center small" style="font-size:9px;">{{ $facebookUrl }}</p>
        @endif

        <hr class="divider-solid" style="margin: 8px 0;">
        <p class="center small">¡Gracias por su preferencia!</p>
        <p class="center small" style="margin-top:2px;">{{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>

</body>
</html>
