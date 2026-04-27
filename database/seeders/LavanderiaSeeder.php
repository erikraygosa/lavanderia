<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\Servicio;
use Illuminate\Database\Seeder;

class LavanderiaSeeder extends Seeder
{
    public function run(): void
    {
        // Servicios
        $servicios = [
            ['nombre' => 'Lavado y secado', 'precio' => 45.00, 'unidad' => 'kg'],
            ['nombre' => 'Lavado, secado y planchado', 'precio' => 65.00, 'unidad' => 'kg'],
            ['nombre' => 'Planchado de camisa', 'precio' => 18.00, 'unidad' => 'pieza'],
            ['nombre' => 'Planchado de pantalón', 'precio' => 20.00, 'unidad' => 'pieza'],
            ['nombre' => 'Lavado de edredón sencillo', 'precio' => 120.00, 'unidad' => 'pieza'],
            ['nombre' => 'Lavado de edredón matrimonial', 'precio' => 150.00, 'unidad' => 'pieza'],
            ['nombre' => 'Lavado de cobija', 'precio' => 80.00, 'unidad' => 'pieza'],
            ['nombre' => 'Lavado de tenis', 'precio' => 90.00, 'unidad' => 'par'],
            ['nombre' => 'Lavado en seco', 'precio' => 85.00, 'unidad' => 'pieza'],
            ['nombre' => 'Lavado a mano especial', 'precio' => 55.00, 'unidad' => 'pieza'],
        ];

        foreach ($servicios as $s) {
            Servicio::create(array_merge($s, ['activo' => true]));
        }

        // Productos
        $productos = [
            ['nombre' => 'Bolsa plástica chica', 'precio' => 3.00, 'stock' => 100, 'unidad' => 'pieza'],
            ['nombre' => 'Bolsa plástica grande', 'precio' => 5.00, 'stock' => 80, 'unidad' => 'pieza'],
            ['nombre' => 'Ganchos de ropa', 'precio' => 25.00, 'stock' => 50, 'unidad' => 'docena'],
            ['nombre' => 'Suavizante extra', 'precio' => 8.00, 'stock' => 30, 'unidad' => 'pieza'],
        ];

        foreach ($productos as $p) {
            Producto::create(array_merge($p, ['activo' => true]));
        }

        // Clientes
        $clientes = [
            ['nombre' => 'María González López', 'telefono' => '5551234567', 'email' => 'maria@ejemplo.com'],
            ['nombre' => 'Juan Carlos Pérez', 'telefono' => '5557654321', 'email' => 'juan@ejemplo.com'],
            ['nombre' => 'Ana Sofía Ramírez', 'telefono' => '5559876543'],
            ['nombre' => 'Roberto Martínez Díaz', 'telefono' => '5553456789'],
            ['nombre' => 'Claudia Hernández', 'telefono' => '5554567890'],
            ['nombre' => 'Luis Alberto Torres', 'telefono' => '5556789012'],
        ];

        $clienteModels = [];
        foreach ($clientes as $c) {
            $clienteModels[] = Cliente::create(array_merge($c, ['activo' => true]));
        }

        // Pedidos de ejemplo
        $estados = ['pagado', 'pagado', 'pendiente', 'pagado', 'abandonado', 'pendiente'];
        $metodos = ['efectivo', 'tarjeta', null, 'transferencia', null, null];

        foreach ($clienteModels as $i => $cliente) {
            $servicio = Servicio::inRandomOrder()->first();
            $cantidad = rand(1, 5);
            $subtotal = $servicio->precio * $cantidad;

            $estado  = $estados[$i];
            $metodo  = $metodos[$i];
            $pagadoEn = $estado === 'pagado' ? now()->subDays(rand(0, 7)) : null;

            $pedido = Pedido::create([
                'folio'         => Pedido::generarFolio(),
                'cliente_id'    => $cliente->id,
                'estado'        => $estado,
                'fecha_entrega' => now()->addDays(rand(1, 5)),
                'subtotal'      => $subtotal,
                'descuento'     => 0,
                'total'         => $subtotal,
                'metodo_pago'   => $metodo,
                'pagado_en'     => $pagadoEn,
            ]);

            PedidoItem::create([
                'pedido_id'       => $pedido->id,
                'tipo'            => 'servicio',
                'item_id'         => $servicio->id,
                'descripcion'     => $servicio->nombre,
                'cantidad'        => $cantidad,
                'precio_unitario' => $servicio->precio,
                'subtotal'        => $subtotal,
            ]);
        }

        // Configuración por defecto
        Configuracion::establecer('negocio_nombre', 'Lavandería Express');
        Configuracion::establecer('negocio_telefono', '');
        Configuracion::establecer('negocio_direccion', '');
        Configuracion::establecer('evo_url', '');
        Configuracion::establecer('evo_instancia', '');
        Configuracion::establecer('evo_apikey', '');
    }
}

