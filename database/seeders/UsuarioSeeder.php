<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuários para o tenant 1 (Empresa Demo)
        \App\Models\Usuario::create([
            'tenant_id' => 1,
            'nome' => 'Administrador Sistema',
            'email' => 'admin@demo.com',
            'senha_hash' => '123456',
            'role' => 'admin',
            'ativo' => true,
        ]);

        \App\Models\Usuario::create([
            'tenant_id' => 1,
            'nome' => 'João Técnico',
            'email' => 'tecnico@demo.com',
            'senha_hash' => '123456',
            'role' => 'tecnico',
            'ativo' => true,
        ]);

        \App\Models\Usuario::create([
            'tenant_id' => 1,
            'nome' => 'Maria Gestora',
            'email' => 'gestor@demo.com',
            'senha_hash' => '123456',
            'role' => 'gestor',
            'ativo' => true,
        ]);

        // Usuários para o tenant 2 (IFTO)
        \App\Models\Usuario::create([
            'tenant_id' => 2,
            'nome' => 'Admin IFTO',
            'email' => 'admin@ifto.edu.br',
            'senha_hash' => '123456',
            'role' => 'admin',
            'ativo' => true,
        ]);

        \App\Models\Usuario::create([
            'tenant_id' => 2,
            'nome' => 'Suporte TI',
            'email' => 'suporte@ifto.edu.br',
            'senha_hash' => '123456',
            'role' => 'tecnico',
            'ativo' => true,
        ]);
    }
}
