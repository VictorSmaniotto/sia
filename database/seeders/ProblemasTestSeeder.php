<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Usuario;
use App\Models\Problema;
use App\Models\Incidente;
use App\Models\Comentario;

class ProblemasTestSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $usuarios = Usuario::where('tenant_id', $tenant->id)->get();

        if ($usuarios->isEmpty()) {
            $this->command->error('Nenhum usuário encontrado. Execute TestDataSeeder primeiro.');
            return;
        }

        // Criar alguns problemas de exemplo
        $problemas = [
            [
                'titulo' => 'Lentidão no Sistema de E-mail',
                'descricao' => 'Usuários relatam demora excessiva para envio e recebimento de e-mails corporativos.',
                'status' => 'Investigando',
                'prioridade' => 'Alta',
                'responsavel_id' => $usuarios->where('role', 'tecnico')->first()->id,
                'causa_raiz' => null,
                'solucao_contorno' => 'Orientar usuários a usar webmail temporariamente.',
                'solucao_definitiva' => null
            ],
            [
                'titulo' => 'Falha de Conectividade VPN',
                'descricao' => 'Conexões VPN caindo frequentemente, especialmente durante horário comercial.',
                'status' => 'Erro Conhecido',
                'prioridade' => 'Alta',
                'responsavel_id' => $usuarios->where('role', 'admin')->first()->id,
                'causa_raiz' => 'Sobrecarga no servidor VPN principal devido ao aumento de usuários remotos.',
                'solucao_contorno' => 'Configurar servidor VPN secundário e distribuir carga.',
                'solucao_definitiva' => null
            ],
            [
                'titulo' => 'Erro de Timeout na Base de Dados',
                'descricao' => 'Sistema ERP apresentando timeouts frequentes ao consultar relatórios.',
                'status' => 'Resolvido',
                'prioridade' => 'Média',
                'responsavel_id' => $usuarios->where('role', 'tecnico')->first()->id,
                'causa_raiz' => 'Consultas SQL não otimizadas causando lock na base de dados.',
                'solucao_contorno' => 'Executar relatórios fora do horário comercial.',
                'solucao_definitiva' => 'Otimização das consultas SQL e criação de índices apropriados.'
            ]
        ];

        foreach ($problemas as $problemaData) {
            $problema = Problema::create([
                'tenant_id' => $tenant->id,
                ...$problemaData
            ]);

            // Criar alguns comentários para cada problema
            $comentariosData = [
                [
                    'autor_id' => $usuarios->random()->id,
                    'conteudo' => 'Iniciando investigação do problema. Coletando logs do sistema.'
                ],
                [
                    'autor_id' => $usuarios->random()->id,
                    'conteudo' => 'Identificado padrão nos horários de pico. Possível relação com alta demanda.'
                ]
            ];

            foreach ($comentariosData as $comentarioData) {
                Comentario::create([
                    'tenant_id' => $tenant->id,
                    'problema_id' => $problema->id,
                    'incidente_id' => null,
                    ...$comentarioData
                ]);
            }

            // Criar alguns incidentes relacionados
            for ($i = 1; $i <= 2; $i++) {
                $incidente = Incidente::create([
                    'tenant_id' => $tenant->id,
                    'titulo' => "Incidente relacionado ao {$problema->titulo} #{$i}",
                    'descricao' => "Usuário reportou problema relacionado: {$problema->titulo}",
                    'status' => $problema->status === 'Resolvido' ? 'Fechado' : 'Aberto',
                    'prioridade' => $problema->prioridade,
                    'impacto' => 'Médio',
                    'urgencia' => 'Média',
                    'solicitante_id' => $usuarios->random()->id,
                    'responsavel_id' => $problema->responsavel_id,
                    'problema_id' => $problema->id
                ]);

                // Comentário no incidente
                Comentario::create([
                    'tenant_id' => $tenant->id,
                    'incidente_id' => $incidente->id,
                    'problema_id' => null,
                    'autor_id' => $usuarios->random()->id,
                    'conteudo' => 'Incidente relacionado ao problema em investigação.'
                ]);
            }
        }

        $this->command->info('Problemas, incidentes relacionados e comentários criados com sucesso!');
    }
}
