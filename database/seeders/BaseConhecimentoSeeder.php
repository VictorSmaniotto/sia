<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Usuario;
use App\Models\ArtigoKb;

class BaseConhecimentoSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();
        $usuarios = Usuario::where('tenant_id', $tenant->id)->get();

        if ($usuarios->isEmpty()) {
            $this->command->error('Nenhum usuário encontrado. Execute AuthTestSeeder primeiro.');
            return;
        }

        $artigos = [
            [
                'titulo' => 'Como resetar senha do usuário no Active Directory',
                'resumo' => 'Procedimento passo a passo para resetar senhas de usuários no Active Directory usando ferramentas administrativas.',
                'conteudo' => "# Como resetar senha do usuário no Active Directory\n\nEste procedimento descreve como resetar a senha de um usuário no Active Directory.\n\n## Pré-requisitos\n\n- Acesso administrativo ao servidor de domínio\n- Console Active Directory Users and Computers\n\n## Procedimento\n\n### Passo 1: Abrir o console ADUC\n\n1. Faça login no servidor de domínio\n2. Vá em Start > Administrative Tools > Active Directory Users and Computers\n\n### Passo 2: Localizar o usuário\n\n1. Navegue até a OU onde o usuário está localizado\n2. Clique com o botão direito no usuário\n3. Selecione 'Reset Password'\n\n### Passo 3: Definir nova senha\n\n1. Digite a nova senha\n2. Confirme a nova senha\n3. Marque 'User must change password at next logon' se necessário\n4. Clique em OK\n\n## Verificação\n\nTeste o login com as novas credenciais para confirmar que o reset foi bem-sucedido.",
                'categoria' => 'procedimentos',
                'palavras_chave' => 'active directory, senha, reset, password, usuário, domínio',
                'status' => 'publicado',
                'autor_id' => $usuarios->where('role', 'admin')->first()->id,
                'visualizacoes' => 45,
                'tempo_leitura' => 3,
            ],
            [
                'titulo' => 'Solução para problemas de conectividade VPN',
                'resumo' => 'Guia completo para diagnóstico e resolução de problemas comuns de conectividade VPN.',
                'conteudo' => "# Solução para problemas de conectividade VPN\n\n## Sintomas comuns\n\n- Não consegue se conectar à VPN\n- Conexão VPN cai frequentemente\n- Velocidade lenta na VPN\n- Não acessa recursos internos via VPN\n\n## Diagnóstico\n\n### Verificar configurações de rede\n\n1. Verificar se a internet está funcionando\n2. Testar conectividade com o servidor VPN\n3. Verificar configurações de DNS\n\n### Verificar logs\n\n1. Verificar logs do cliente VPN\n2. Verificar logs do servidor VPN\n3. Verificar logs de firewall\n\n## Soluções\n\n### Problema: Não consegue conectar\n\n1. Verificar credenciais\n2. Verificar configurações do servidor\n3. Verificar firewall\n4. Testar conexão manual\n\n### Problema: Conexão instável\n\n1. Verificar qualidade da conexão\n2. Ajustar configurações de timeout\n3. Verificar interferência de antivírus\n\n### Problema: Velocidade lenta\n\n1. Verificar carga do servidor\n2. Otimizar configurações de MTU\n3. Considerar servidor alternativo",
                'categoria' => 'troubleshooting',
                'palavras_chave' => 'vpn, conectividade, rede, problemas, solução, diagnóstico',
                'status' => 'publicado',
                'autor_id' => $usuarios->where('role', 'tecnico')->first()->id,
                'visualizacoes' => 78,
                'tempo_leitura' => 8,
            ],
            [
                'titulo' => 'Configuração de backup automático do servidor',
                'resumo' => 'Tutorial para configurar rotinas de backup automático usando Windows Server Backup.',
                'conteudo' => "# Configuração de backup automático do servidor\n\n## Objetivos\n\n- Configurar backup automático diário\n- Definir retenção de backups\n- Configurar notificações por email\n\n## Instalação do Windows Server Backup\n\n### Via Server Manager\n\n1. Abrir Server Manager\n2. Ir em Manage > Add Roles and Features\n3. Selecionar Windows Server Backup\n4. Instalar o feature\n\n### Via PowerShell\n\n```powershell\nInstall-WindowsFeature Windows-Server-Backup\n```\n\n## Configuração do backup\n\n### Passo 1: Definir local de backup\n\n1. Conectar disco externo ou configurar compartilhamento de rede\n2. Verificar espaço disponível\n3. Formatar se necessário\n\n### Passo 2: Configurar agendamento\n\n1. Abrir Windows Server Backup\n2. Clicar em 'Backup Schedule'\n3. Selecionar dados para backup\n4. Definir horário (recomendado: fora do horário comercial)\n5. Selecionar destino\n\n### Passo 3: Configurar notificações\n\n1. Configurar SMTP\n2. Definir destinatários\n3. Testar envio de email\n\n## Monitoramento\n\n- Verificar logs diariamente\n- Testar restauração periodicamente\n- Monitorar espaço em disco\n- Validar integridade dos backups",
                'categoria' => 'tutoriais',
                'palavras_chave' => 'backup, servidor, windows, automático, agendamento, recuperação',
                'status' => 'publicado',
                'autor_id' => $usuarios->where('role', 'admin')->first()->id,
                'visualizacoes' => 92,
                'tempo_leitura' => 10,
            ],
            [
                'titulo' => 'FAQ - Problemas comuns com impressoras',
                'resumo' => 'Perguntas frequentes e soluções para problemas comuns com impressoras de rede.',
                'conteudo' => "# FAQ - Problemas comuns com impressoras\n\n## P: A impressora não está imprimindo\n\n**R:** Verifique os seguintes pontos:\n\n1. Impressora está ligada e conectada à rede\n2. Papel e toner/tinta suficientes\n3. Fila de impressão não está travada\n4. Driver da impressora instalado corretamente\n\n## P: Impressão muito lenta\n\n**R:** Possíveis causas:\n\n1. Rede congestionada\n2. Arquivo muito grande\n3. Configurações de qualidade muito altas\n4. Memória da impressora insuficiente\n\n**Soluções:**\n- Verificar tráfego de rede\n- Reduzir qualidade de impressão\n- Imprimir em horários de menor movimento\n\n## P: Impressora offline no Windows\n\n**R:** Para resolver:\n\n1. Ir em Painel de Controle > Dispositivos e Impressoras\n2. Clicar com botão direito na impressora\n3. Desmarcar 'Usar impressora offline'\n4. Se não resolver, remover e reinstalar o driver\n\n## P: Atolamento de papel frequente\n\n**R:** Verificar:\n\n1. Tipo de papel está correto para a impressora\n2. Bandeja não está muito cheia\n3. Papel não está úmido ou danificado\n4. Cilindros de alimentação precisam de limpeza\n\n## P: Qualidade de impressão ruim\n\n**R:** Possíveis soluções:\n\n1. Verificar nível de toner/tinta\n2. Executar limpeza dos cabeçotes\n3. Calibrar impressora\n4. Verificar configurações de qualidade\n5. Substituir cartuchos se necessário",
                'categoria' => 'faq',
                'palavras_chave' => 'impressora, problemas, faq, rede, driver, papel, qualidade',
                'status' => 'publicado',
                'autor_id' => $usuarios->where('role', 'tecnico')->first()->id,
                'visualizacoes' => 156,
                'tempo_leitura' => 5,
            ],
            [
                'titulo' => 'Política de Segurança da Informação',
                'resumo' => 'Documento oficial com as diretrizes e políticas de segurança da informação da organização.',
                'conteudo' => "# Política de Segurança da Informação\n\n## 1. Objetivo\n\nEsta política estabelece as diretrizes para proteção das informações e sistemas da organização.\n\n## 2. Escopo\n\nAplica-se a todos os funcionários, terceiros e sistemas da organização.\n\n## 3. Responsabilidades\n\n### 3.1 Usuários\n\n- Proteger credenciais de acesso\n- Reportar incidentes de segurança\n- Seguir procedimentos estabelecidos\n- Participar de treinamentos\n\n### 3.2 Administradores\n\n- Manter sistemas atualizados\n- Monitorar atividades suspeitas\n- Implementar controles de segurança\n- Gerenciar acessos e permissões\n\n## 4. Controles de Acesso\n\n### 4.1 Autenticação\n\n- Senhas devem ter no mínimo 8 caracteres\n- Combinar letras, números e símbolos\n- Trocar senhas a cada 90 dias\n- Não reutilizar últimas 12 senhas\n\n### 4.2 Autorização\n\n- Princípio do menor privilégio\n- Revisão de acessos trimestral\n- Aprovação formal para novos acessos\n\n## 5. Proteção de Dados\n\n### 5.1 Classificação\n\n- **Público:** Sem restrição\n- **Interno:** Uso interno da organização\n- **Confidencial:** Acesso restrito\n- **Secreto:** Máxima proteção\n\n### 5.2 Backup\n\n- Backup diário de dados críticos\n- Teste de restauração mensal\n- Armazenamento em local seguro\n\n## 6. Incidentes de Segurança\n\n### 6.1 Reportar\n\n- Comunicar imediatamente à equipe de TI\n- Documentar o ocorrido\n- Não tentar resolver sozinho\n\n### 6.2 Resposta\n\n- Isolamento do sistema afetado\n- Análise do incidente\n- Implementação de correções\n- Documentação das lições aprendidas\n\n## 7. Conformidade\n\nO descumprimento desta política pode resultar em ações disciplinares.",
                'categoria' => 'politicas',
                'palavras_chave' => 'segurança, política, informação, acesso, senha, backup, incidente',
                'status' => 'publicado',
                'autor_id' => $usuarios->where('role', 'gestor')->first()->id,
                'visualizacoes' => 203,
                'tempo_leitura' => 12,
            ],
            [
                'titulo' => 'Manual de instalação do Office 365',
                'resumo' => 'Guia completo para instalação e configuração do Microsoft Office 365 em estações de trabalho.',
                'conteudo' => "# Manual de instalação do Office 365\n\n## Pré-requisitos\n\n- Windows 10 ou superior\n- 4GB RAM mínimo (8GB recomendado)\n- 4GB espaço em disco\n- Conexão com internet\n- Licença válida do Office 365\n\n## Download\n\n### Método 1: Portal do Office\n\n1. Acessar portal.office.com\n2. Fazer login com credenciais corporativas\n3. Clicar em 'Instalar Office'\n4. Fazer download do instalador\n\n### Método 2: Centro de administração\n\n1. Acessar admin.microsoft.com\n2. Ir em Configurações > Configurações da organização\n3. Aba Serviços > Office installation options\n4. Baixar o pacote de instalação\n\n## Instalação\n\n### Instalação padrão\n\n1. Executar o arquivo baixado como administrador\n2. Aguardar download automático dos componentes\n3. Seguir assistente de instalação\n4. Reiniciar o computador quando solicitado\n\n### Instalação personalizada\n\n1. Usar ferramenta ODT (Office Deployment Tool)\n2. Criar arquivo configuration.xml\n3. Definir aplicativos a instalar\n4. Executar setup.exe com configurações\n\n## Configuração inicial\n\n### Primeiro acesso\n\n1. Abrir qualquer aplicativo do Office\n2. Clicar em 'Entrar'\n3. Inserir credenciais corporativas\n4. Aguardar sincronização\n\n### Configurações importantes\n\n1. **OneDrive:** Configurar sincronização\n2. **Teams:** Configurar presença\n3. **Outlook:** Adicionar conta de email\n4. **Updates:** Verificar canal de atualização\n\n## Solução de problemas\n\n### Erro de ativação\n\n- Verificar conectividade\n- Confirmar licença disponível\n- Usar assistente de ativação\n\n### Instalação trava\n\n- Desabilitar antivírus temporariamente\n- Limpar cache do Windows Update\n- Reiniciar em modo seguro",
                'categoria' => 'tutoriais',
                'palavras_chave' => 'office, 365, instalação, configuração, microsoft, tutorial',
                'status' => 'rascunho',
                'autor_id' => $usuarios->where('role', 'tecnico')->first()->id,
                'visualizacoes' => 23,
                'tempo_leitura' => 7,
            ]
        ];

        foreach ($artigos as $artigoData) {
            ArtigoKb::create([
                'tenant_id' => $tenant->id,
                ...$artigoData
            ]);
        }

        $this->command->info('Artigos da base de conhecimento criados com sucesso!');
        $this->command->info('Total de artigos: ' . count($artigos));
    }
}
