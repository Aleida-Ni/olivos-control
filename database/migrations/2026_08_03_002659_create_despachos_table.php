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
    Schema::create('despachos', function (Blueprint $table) {

        $table->id();

        $table->foreignId('chofer_id')
              ->constrained('choferes');

        $table->date('fecha');

        $table->enum('estado',[
            'Pendiente',
            'Confirmado'
        ])->default('Pendiente');

        $table->text('observacion')->nullable();

        $table->timestamp('confirmado_en')->nullable();

        $table->timestamps();

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('despachos');
    }
};
