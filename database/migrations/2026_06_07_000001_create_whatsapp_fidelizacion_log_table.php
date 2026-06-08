<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_fidelizacion_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->string('telefono');
            $table->string('estado_cliente'); // NUEVO, ACTIVO, EN_RIESGO, INACTIVO
            $table->text('mensaje_enviado');
            $table->text('respuesta_evo')->nullable();
            $table->timestamp('enviado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_fidelizacion_log');
    }
};
