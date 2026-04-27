<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cortes', function (Blueprint $table) {
            $table->id();
            $table->string('folio')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('total_ventas', 10, 2)->default(0);
            $table->integer('total_pedidos')->default(0);
            $table->decimal('efectivo', 10, 2)->default(0);
            $table->decimal('tarjeta', 10, 2)->default(0);
            $table->decimal('transferencia', 10, 2)->default(0);
            $table->decimal('otro', 10, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamp('cerrado_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cortes');
    }
};
