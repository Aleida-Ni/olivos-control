<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('rol')->default('cajero')->after('email');
            $table->string('pin', 255)->nullable()->after('rol');
            $table->boolean('activo')->default(true)->after('pin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'rol',
                'pin',
                'activo',
            ]);
        });
    }
};