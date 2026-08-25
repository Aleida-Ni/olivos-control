<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {

            $table->id();

            // Cajero que realizó la venta
            $table->foreignId('user_id')
                ->constrained('users');

            // Datos del cliente
            $table->string('nit_ci')->nullable();
            $table->string('cliente')->nullable();

            // Totales
            $table->decimal('subtotal', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // Forma de pago
            $table->enum('metodo_pago', [
                'EFECTIVO',
                'TARJETA',
                'QR'
            ]);

            // Solo utilizado cuando es efectivo
            $table->decimal('efectivo_recibido', 10, 2)
                ->nullable();

            $table->decimal('cambio', 10, 2)
                ->default(0);

            $table->string('estado')
                ->default('COMPLETADA');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};