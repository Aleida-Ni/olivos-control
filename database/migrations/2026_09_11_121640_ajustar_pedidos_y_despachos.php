<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ejecutar cambios.
     */
    public function up(): void
    {
        // 1. Agregar tipo_entrega solamente si todavía no existe
        if (!Schema::hasColumn('pedidos', 'tipo_entrega')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->string('tipo_entrega')
                    ->nullable()
                    ->after('telefono');
            });
        }

        // 2. Cambiar estado de despachos a string
        Schema::table('despachos', function (Blueprint $table) {
            $table->string('estado')
                ->default('ENVIADO')
                ->change();
        });

        // 3. Convertir posibles valores antiguos
        DB::table('despachos')
            ->where('estado', 'Pendiente')
            ->update(['estado' => 'ENVIADO']);

        DB::table('despachos')
            ->where('estado', 'Confirmado')
            ->update(['estado' => 'RECIBIDO']);
    }

    /**
     * Revertir cambios.
     */
    public function down(): void
    {
        // Eliminar tipo_entrega solamente si existe
        if (Schema::hasColumn('pedidos', 'tipo_entrega')) {
            Schema::table('pedidos', function (Blueprint $table) {
                $table->dropColumn('tipo_entrega');
            });
        }

        // Volver estado a string
        Schema::table('despachos', function (Blueprint $table) {
            $table->string('estado')
                ->default('Pendiente')
                ->change();
        });

        DB::table('despachos')
            ->where('estado', 'ENVIADO')
            ->update(['estado' => 'Pendiente']);

        DB::table('despachos')
            ->where('estado', 'RECIBIDO')
            ->update(['estado' => 'Confirmado']);
    }
};