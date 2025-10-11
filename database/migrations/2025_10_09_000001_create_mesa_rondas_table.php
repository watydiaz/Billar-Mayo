<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesa_rondas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ronda_id');
            $table->foreignId('mesa_id');
            $table->datetime('inicio_tiempo')->nullable();
            $table->datetime('fin_tiempo')->nullable();
            $table->integer('duracion_minutos')->nullable();
            $table->decimal('costo_tiempo', 8, 2)->default(0);
            $table->enum('estado', ['pendiente', 'activo', 'finalizado'])->default('pendiente');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->foreign('ronda_id')->references('id')->on('rondas')->onDelete('cascade');
            $table->foreign('mesa_id')->references('id')->on('mesas')->onDelete('cascade');
            
            $table->index(['ronda_id', 'mesa_id']);
            $table->index(['estado', 'mesa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesa_rondas');
    }
};