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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();

            $table->string('numero_pedido')->nullable()->unique();
            $table->string('cliente')->nullable();
            $table->string('telefono')->nullable();

            $table->foreignId('producto_id')
                ->nullable()
                ->constrained('productos')
                ->nullOnDelete();

            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('saldo', 10, 2)->default(0);
            $table->string('estado')->default('PENDIENTE');
            $table->text('observacion')->nullable();

            $table->foreignId('despacho_id')
                ->nullable()
                ->constrained('despachos')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
