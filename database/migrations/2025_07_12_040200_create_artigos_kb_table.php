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
        Schema::create('artigos_kb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('titulo');
            $table->text('conteudo');
            $table->string('categoria', 100)->nullable();
            $table->foreignId('publicado_por_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->timestamp('publicado_em')->useCurrent();
            $table->foreignId('problema_id')->nullable()->constrained('problemas')->onDelete('set null');
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'categoria']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artigos_kb');
    }
};
