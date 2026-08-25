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
        Schema::table('detalle_despachos', function (Blueprint $table) {
            $table->decimal('precio_unitario', 10, 2)->nullable()->after('cliente');
            $table->decimal('monto_pagado', 10, 2)->default(0)->after('cantidad');
            $table->decimal('saldo', 10, 2)->default(0)->after('monto_pagado');
            $table->string('estado')->default('PENDIENTE')->after('saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_despachos', function (Blueprint $table) {
            $table->dropColumn(['precio_unitario', 'monto_pagado', 'saldo', 'estado']);
        });
    }
};
