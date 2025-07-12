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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('nome');
            $table->string('email');
            $table->string('senha_hash');
            $table->enum('role', ['admin', 'tecnico', 'gestor'])->default('tecnico');
            $table->boolean('ativo')->default(true);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamps();

            $table->unique(['tenant_id', 'email']);
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
