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
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('autor_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->foreignId('incidente_id')->nullable()->constrained('incidentes')->onDelete('cascade');
            $table->foreignId('problema_id')->nullable()->constrained('problemas')->onDelete('cascade');
            $table->text('conteudo');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index(['tenant_id', 'incidente_id']);
            $table->index(['tenant_id', 'problema_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};
