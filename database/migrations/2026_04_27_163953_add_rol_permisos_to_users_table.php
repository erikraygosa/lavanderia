<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('rol', ['admin', 'operador'])->default('operador')->after('email');
            $table->json('permisos')->nullable()->after('rol');
        });

        // El primer usuario (admin) es siempre admin
        DB::table('users')->where('id', 1)->update(['rol' => 'admin']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['rol', 'permisos']);
        });
    }
};
