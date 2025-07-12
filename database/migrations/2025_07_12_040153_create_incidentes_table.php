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
        Schema::create('incidentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->enum('status', ['Aberto', 'Em andamento', 'Resolvido', 'Fechado'])->default('Aberto');
            $table->enum('prioridade', ['Alta', 'Média', 'Baixa'])->default('Média');
            $table->enum('impacto', ['Alto', 'Médio', 'Baixo'])->nullable();
            $table->enum('urgencia', ['Alta', 'Média', 'Baixa'])->nullable();
            $table->foreignId('solicitante_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('problema_id')->nullable()->constrained('problemas')->onDelete('set null');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'prioridade']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidentes');
    }
};
