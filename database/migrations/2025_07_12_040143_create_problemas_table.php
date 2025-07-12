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
        Schema::create('problemas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->enum('status', ['Novo', 'Investigando', 'Erro Conhecido', 'Resolvido', 'Fechado'])->default('Novo');
            $table->enum('prioridade', ['Alta', 'Média', 'Baixa'])->nullable();
            $table->text('causa_raiz')->nullable();
            $table->text('solucao_contorno')->nullable();
            $table->text('solucao_definitiva')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('problemas');
    }
};
