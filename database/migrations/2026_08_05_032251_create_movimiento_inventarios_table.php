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
    Schema::create('movimientos_inventario', function (Blueprint $table) {

        $table->id();

        $table->foreignId('producto_id')
              ->constrained('productos');

        $table->enum('tipo', [
            'Ingreso',
            'Salida'
        ]);

        $table->unsignedInteger('cantidad');

        $table->string('referencia')->nullable();

        $table->text('observacion')->nullable();

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_inventarios');
    }
};
