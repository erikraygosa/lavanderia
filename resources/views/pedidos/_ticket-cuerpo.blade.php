{{-- Encabezado --}}
<div class="center">
    @if($logoB64)
        <img src="data:{{ $logoMime }};base64,{{ $logoB64 }}"
             style="max-height:55px; max-width:170px; margin: 0 auto 4px; display:block;" alt="Logo" />
    @endif
    <p class="bold" style="font-size:13px; letter-spacing:1px;">{{ strtoupper($negocioNom) }}</p>
    @if($negocioDir) <p class="small" style="margin-top:1px;">{{ $negocioDir }}</p> @endif
    @if($negocioTel) <p class="small">Tel: {{ $negocioTel }}</p> @endif
</div>

<hr class="divider-solid" style="margin: 5px 0;">

{{-- Info pedido --}}
<table>
    <tr>
        <td class="bold small">Folio:</td>
        <td class="right bold small">{{ $pedido->folio }}</td>
    </tr>
    <tr>
        <td class="small">Fecha:</td>
        <td class="right small">{{ $pedido->created_at->format('d/m/Y H:i') }}</td>
    </tr>
    <tr>
        <td class="small bold">Entrega:</td>
        <td class="right small bold">{{ $pedido->entregaFormateada() }}</td>
    </tr>
    @if($pedido->es_domicilio)
    <tr>
        <td class="small" colspan="2">🛵 Envío a domicilio
            @if($pedido->direccion_domicilio)
                <br><span style="font-size:9px;">{{ $pedido->direccion_domicilio }}</span>
            @endif
        </td>
    </tr>
    @endif
</table>

<hr class="divider" style="margin: 4px 0;">

{{-- Cliente --}}
<p class="bold">{{ $pedido->cliente->nombre }}</p>
@if($pedido->cliente->telefono)
    <p class="small">Tel: {{ $pedido->cliente->telefono }}</p>
@endif

<hr class="divider" style="margin: 4px 0;">

{{-- Detalle --}}
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

<hr class="divider" style="margin: 4px 0;">

{{-- Totales --}}
@if($pedido->descuento > 0)
<table>
    <tr>
        <td class="small">Subtotal</td>
        <td class="right small">${{ number_format($pedido->subtotal, 2) }}</td>
    </tr>
    <tr>
        <td class="small">Descuento
            @if($pedido->descuento_nota)
                <br><span style="font-size:9px; font-style:italic;">{{ $pedido->descuento_nota }}</span>
            @endif
        </td>
        <td class="right small">-${{ number_format($pedido->descuento, 2) }}</td>
    </tr>
</table>
@endif

<hr class="divider-solid" style="margin: 3px 0;">
<table>
    <tr class="total-row">
        <td>TOTAL</td>
        <td class="right">${{ number_format($pedido->total, 2) }}</td>
    </tr>
</table>

{{-- Estado de pago --}}
@if($pedido->estado === 'pagado')
<hr class="divider" style="margin: 4px 0;">
<table>
    <tr>
        <td class="small">Estado:</td>
        <td class="right bold small">✓ PAGADO</td>
    </tr>
    <tr>
        <td class="small">Método:</td>
        <td class="right small">{{ ucfirst($pedido->metodo_pago ?? '') }}</td>
    </tr>
</table>
@else
<hr class="divider" style="margin: 4px 0;">
<p class="center bold small">PENDIENTE DE PAGO</p>
@endif

{{-- Notas --}}
@if($pedido->notas)
<hr class="divider" style="margin: 4px 0;">
<p class="small bold">Notas:</p>
<p class="small">{{ $pedido->notas }}</p>
@endif

{{-- Horarios de atención --}}
@if($horariosTrabajo ?? '')
<hr class="divider" style="margin: 4px 0;">
<p class="center bold small" style="font-size:9px; letter-spacing:0.5px;">HORARIOS DE ATENCIÓN</p>
@foreach(explode("\n", $horariosTrabajo) as $linea)
    @if(trim($linea))
        <p class="center small" style="font-size:8px; margin-top:1px;">{{ trim($linea) }}</p>
    @endif
@endforeach
@endif

{{-- Mensaje promocional --}}
@if($ticketMensaje)
<hr class="divider" style="margin: 4px 0;">
<p class="center bold small" style="font-size:9px;">{{ $ticketMensaje }}</p>
@endif

{{-- QR redes sociales (solo copia cliente) --}}
@if($mostrarQr && $facebookUrl)
<hr class="divider" style="margin: 4px 0;">
<p class="center small">Síguenos en redes</p>
<div style="text-align:center; margin: 3px 0;">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode($facebookUrl) }}"
         style="width:70px; height:70px;" alt="QR" />
</div>
@endif

<hr class="divider-solid" style="margin: 5px 0;">
<p class="center small">¡Gracias por su preferencia!</p>
<p class="center small" style="margin-top:2px;">{{ now()->format('d/m/Y H:i') }}</p>
