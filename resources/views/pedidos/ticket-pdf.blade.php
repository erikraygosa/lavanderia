<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Courier New', Courier, monospace;
        font-size: 10pt;
        color: #000;
        width: 72mm;
        padding: 4mm 3mm;
    }
    .center  { text-align: center; }
    .right   { text-align: right; }
    .bold    { font-weight: bold; }
    .small   { font-size: 8pt; }
    .large   { font-size: 13pt; }
    .divider { border-top: 1px dashed #000; margin: 4px 0; }
    .divider-solid { border-top: 1px solid #000; margin: 4px 0; }

    table { width: 100%; border-collapse: collapse; }
    td { padding: 1px 0; vertical-align: top; }
    .col-desc  { width: 52%; }
    .col-cant  { width: 12%; text-align: center; }
    .col-precio{ width: 18%; text-align: right; }
    .col-sub   { width: 18%; text-align: right; }

    .logo-img { max-height: 18mm; max-width: 60mm; }
    .total-amount { font-size: 14pt; font-weight: bold; }
</style>
</head>
<body>

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
            $logoAbsPath = storage_path('app/public/' . $logoPath);
            $logoExt     = strtolower(pathinfo($logoAbsPath, PATHINFO_EXTENSION));
            $logoMime    = match($logoExt) { 'png' => 'image/png', 'gif' => 'image/gif', default => 'image/jpeg' };
            $logoBase64  = file_exists($logoAbsPath) ? base64_encode(file_get_contents($logoAbsPath)) : '';
        @endphp
        @if($logoBase64)
            <img src="data:{{ $logoMime }};base64,{{ $logoBase64 }}" class="logo-img" alt="Logo" /><br>
        @endif
    @endif
    <p class="bold large">{{ strtoupper($negocioNom) }}</p>
    @if($negocioDir)  <p class="small">{{ $negocioDir }}</p> @endif
    @if($negocioTel)  <p class="small">Tel: {{ $negocioTel }}</p> @endif
</div>

<div class="divider-solid" style="margin: 6px 0;"></div>

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
    @if($pedido->fecha_entrega)
    <tr>
        <td class="small bold">Entrega:</td>
        <td class="right small bold">{{ $pedido->entregaFormateada() }}</td>
    </tr>
    @endif
</table>

<div class="divider"></div>

{{-- Cliente --}}
<p class="bold">{{ $pedido->cliente->nombre }}</p>
@if($pedido->cliente->telefono)
    <p class="small">Tel: {{ $pedido->cliente->telefono }}</p>
@endif

<div class="divider"></div>

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
<div class="divider"></div>
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

<div class="divider"></div>

{{-- Totales --}}
<table>
    @if($pedido->descuento > 0)
    <tr>
        <td class="small">Subtotal</td>
        <td class="right small">${{ number_format($pedido->subtotal, 2) }}</td>
    </tr>
    <tr>
        <td class="small">
            Descuento
            @if($pedido->descuento_nota)
                <br><span style="font-size:7pt; font-style:italic;">{{ $pedido->descuento_nota }}</span>
            @endif
        </td>
        <td class="right small">-${{ number_format($pedido->descuento, 2) }}</td>
    </tr>
    @endif
</table>
<div class="divider-solid" style="margin: 3px 0;"></div>
<table>
    <tr>
        <td class="bold">TOTAL</td>
        <td class="right total-amount">${{ number_format($pedido->total, 2) }}</td>
    </tr>
</table>

@if($pedido->estado === 'pagado')
<div class="divider"></div>
<table>
    <tr>
        <td class="small">Estado:</td>
        <td class="right bold small">PAGADO</td>
    </tr>
    <tr>
        <td class="small">Metodo:</td>
        <td class="right small">{{ ucfirst($pedido->metodo_pago ?? '') }}</td>
    </tr>
</table>
@else
<div class="divider"></div>
<p class="center bold small">PENDIENTE DE PAGO</p>
@endif

@if($pedido->notas)
<div class="divider"></div>
<p class="small bold">Notas:</p>
<p class="small">{{ $pedido->notas }}</p>
@endif

<div class="divider-solid" style="margin: 6px 0;"></div>
<p class="center small">Gracias por su preferencia</p>
<p class="center small">{{ now()->format('d/m/Y H:i') }}</p>

</body>
</html>
