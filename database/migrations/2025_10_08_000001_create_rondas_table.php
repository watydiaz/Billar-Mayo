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
        Schema::create('rondas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ronda', 50);
            $table->string('cliente')->nullable();
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('estado', ['pendiente', 'activa', 'finalizada', 'pagada'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['estado', 'cliente']);
            $table->index(['numero_ronda']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rondas');
    }
};
