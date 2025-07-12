<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AuthTestSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('Nenhum tenant encontrado. Execute TenantSeeder primeiro.');
            return;
        }

        // Usuários de teste para diferentes cenários de autenticação
        $usuarios = [
            [
                'nome' => 'Administrador Sistema',
                'email' => 'admin@sistema.com',
                'senha_hash' => Hash::make('admin123'),
                'role' => 'admin',
                'ativo' => true,
            ],
            [
                'nome' => 'Técnico TI',
                'email' => 'tecnico@sistema.com',
                'senha_hash' => Hash::make('tecnico123'),
                'role' => 'tecnico',
                'ativo' => true,
            ],
            [
                'nome' => 'Gestor TI',
                'email' => 'gestor@sistema.com',
                'senha_hash' => Hash::make('gestor123'),
                'role' => 'gestor',
                'ativo' => true,
            ],
            [
                'nome' => 'Conta Inativa',
                'email' => 'inativo@sistema.com',
                'senha_hash' => Hash::make('inativo123'),
                'role' => 'tecnico',
                'ativo' => false,
            ],
            [
                'nome' => 'João Silva',
                'email' => 'joao.silva@teste.com',
                'senha_hash' => Hash::make('senha123'),
                'role' => 'tecnico',
                'ativo' => true,
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria.santos@teste.com',
                'senha_hash' => Hash::make('senha123'),
                'role' => 'tecnico',
                'ativo' => true,
            ],
            [
                'nome' => 'Carlos Admin',
                'email' => 'carlos.admin@teste.com',
                'senha_hash' => Hash::make('admin456'),
                'role' => 'admin',
                'ativo' => true,
            ]
        ];

        foreach ($usuarios as $usuarioData) {
            // Verificar se já existe
            $existingUser = Usuario::where('tenant_id', $tenant->id)
                                  ->where('email', $usuarioData['email'])
                                  ->first();

            if (!$existingUser) {
                Usuario::create([
                    'tenant_id' => $tenant->id,
                    ...$usuarioData
                ]);

                $this->command->info("Usuário criado: {$usuarioData['email']} ({$usuarioData['role']})");
            } else {
                $this->command->info("Usuário já existe: {$usuarioData['email']}");
            }
        }

        $this->command->info('Usuários de teste criados com sucesso!');
        $this->command->info('');
        $this->command->info('Credenciais de teste:');
        $this->command->info('- admin@sistema.com / admin123 (Admin)');
        $this->command->info('- tecnico@sistema.com / tecnico123 (Técnico)');
        $this->command->info('- gestor@sistema.com / gestor123 (Gestor)');
        $this->command->info('- joao.silva@teste.com / senha123 (Técnico)');
        $this->command->info('- maria.santos@teste.com / senha123 (Técnico)');
        $this->command->info('- carlos.admin@teste.com / admin456 (Admin)');
    }
}
