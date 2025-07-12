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
        Schema::table('artigos_kb', function (Blueprint $table) {
            $table->text('resumo')->nullable()->after('titulo');
            $table->text('palavras_chave')->nullable()->after('categoria');
            $table->enum('status', ['rascunho', 'revisao', 'publicado', 'arquivado'])->default('rascunho')->after('palavras_chave');
            $table->foreignId('autor_id')->nullable()->constrained('usuarios')->onDelete('set null')->after('status');
            $table->integer('visualizacoes')->default(0)->after('autor_id');
            $table->integer('tempo_leitura')->nullable()->after('visualizacoes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artigos_kb', function (Blueprint $table) {
            $table->dropColumn([
                'resumo',
                'palavras_chave',
                'status',
                'autor_id',
                'visualizacoes',
                'tempo_leitura'
            ]);
        });
    }
};
