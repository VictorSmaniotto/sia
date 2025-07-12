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
        Schema::create('sla', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('categoria', 100)->nullable();
            $table->enum('prioridade', ['Alta', 'Média', 'Baixa']);
            $table->integer('tempo_resolucao_em_horas');
            $table->timestamps();

            $table->index('tenant_id');
            $table->unique(['tenant_id', 'categoria', 'prioridade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla');
    }
};
