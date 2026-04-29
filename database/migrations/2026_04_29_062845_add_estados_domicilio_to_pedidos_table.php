<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL no permite alterar un ENUM directamente con Blueprint,
        // así que usamos una sentencia raw
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN estado ENUM('pendiente','terminado','entregado','pagado','abandonado') NOT NULL DEFAULT 'pendiente'");

        Schema::table('pedidos', function (Blueprint $table) {
            $table->boolean('es_domicilio')->default(false)->after('hora_entrega');
            $table->string('direccion_domicilio')->nullable()->after('es_domicilio');
            $table->timestamp('terminado_en')->nullable()->after('pagado_en');
            $table->timestamp('entregado_en')->nullable()->after('terminado_en');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['es_domicilio', 'direccion_domicilio', 'terminado_en', 'entregado_en']);
        });
        DB::statement("ALTER TABLE pedidos MODIFY COLUMN estado ENUM('pendiente','pagado','abandonado') NOT NULL DEFAULT 'pendiente'");
    }
};
