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
        Schema::create('historico_incidentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incidente_id')->constrained('incidentes')->onDelete('cascade');
            $table->foreignId('alterado_por_id')->nullable()->constrained('usuarios')->onDelete('set null');
            $table->string('campo_alterado', 100);
            $table->text('valor_anterior')->nullable();
            $table->text('valor_novo')->nullable();
            $table->timestamp('data_alteracao')->useCurrent();
            $table->timestamps();

            $table->index('incidente_id');
            $table->index(['incidente_id', 'data_alteracao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_incidentes');
    }
};
