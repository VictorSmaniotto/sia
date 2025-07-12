<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Usuario;
use App\Models\Incidente;
use App\Models\Problema;
use App\Models\ArtigoKb;
use App\Models\Comentario;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar tenant de teste
        $tenant = Tenant::firstOrCreate(
            ['dominio' => 'teste.localhost'],
            [
                'nome' => 'Empresa Teste LTDA',
                'ativo' => true,
            ]
        );

        // Criar usuários de teste
        $admin = Usuario::create([
            'tenant_id' => $tenant->id,
            'nome' => 'Administrador Sistema',
            'email' => 'admin@teste.com',
            'senha_hash' => Hash::make('123456'),
            'role' => 'admin',
            'ativo' => true,
        ]);

        $tecnico = Usuario::create([
            'tenant_id' => $tenant->id,
            'nome' => 'Técnico Suporte',
            'email' => 'tecnico@teste.com',
            'senha_hash' => Hash::make('123456'),
            'role' => 'tecnico',
            'ativo' => true,
        ]);

        $usuario = Usuario::create([
            'tenant_id' => $tenant->id,
            'nome' => 'João Silva',
            'email' => 'joao@teste.com',
            'senha_hash' => Hash::make('123456'),
            'role' => 'tecnico',
            'ativo' => true,
        ]);

        // Criar problemas de teste
        $problema1 = Problema::create([
            'tenant_id' => $tenant->id,
            'titulo' => 'Lentidão no sistema de e-mail',
            'descricao' => 'Usuários relatam lentidão excessiva no sistema de e-mail corporativo.',
            'status' => 'Investigando',
            'prioridade' => 'Alta',
            'responsavel_id' => $tecnico->id,
        ]);

        // Criar incidentes de teste
        $incidente1 = Incidente::create([
            'tenant_id' => $tenant->id,
            'titulo' => 'Computador não liga',
            'descricao' => 'Computador da sala 101 não está ligando.',
            'status' => 'Aberto',
            'prioridade' => 'Alta',
            'impacto' => 'Alto',
            'urgencia' => 'Alta',
            'solicitante_id' => $usuario->id,
            'responsavel_id' => $tecnico->id,
        ]);

        $incidente2 = Incidente::create([
            'tenant_id' => $tenant->id,
            'titulo' => 'E-mail não carrega anexos',
            'descricao' => 'Não consigo abrir anexos de e-mail.',
            'status' => 'Em Andamento',
            'prioridade' => 'Média',
            'solicitante_id' => $usuario->id,
            'responsavel_id' => $tecnico->id,
            'problema_id' => $problema1->id,
        ]);

        // Criar artigos KB de teste
        ArtigoKb::create([
            'tenant_id' => $tenant->id,
            'titulo' => 'Como resetar senha do Windows',
            'conteudo' => 'Para resetar a senha do Windows: 1. Acesse Configurações...',
            'categoria' => 'Windows',
            'status' => 'Publicado',
            'autor_id' => $admin->id,
            'visualizacoes' => 45,
        ]);

        $this->command->info('Dados de teste criados com sucesso!');
        $this->command->info('Login: admin@teste.com / 123456');
    }
}
