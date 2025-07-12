-- Modelagem de Banco de Dados para Sistema ITSM (PostgreSQL)
-- Multi-tenant com abordagem de TenantID (tabelas compartilhadas)

-- Tabela de Tenants (clientes da plataforma)
CREATE TABLE tenants (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    dominio VARCHAR(255) UNIQUE,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Usuários
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    tenant_id INTEGER NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    senha_hash TEXT NOT NULL,
    role VARCHAR(50) NOT NULL, -- Ex: 'admin', 'tecnico', 'gestor'
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Incidentes
CREATE TABLE incidentes (
    id SERIAL PRIMARY KEY,
    tenant_id INTEGER NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    status VARCHAR(50) NOT NULL DEFAULT 'Aberto', -- 'Aberto', 'Em andamento', 'Resolvido', 'Fechado'
    prioridade VARCHAR(50) NOT NULL, -- 'Alta', 'Média', 'Baixa'
    impacto VARCHAR(50),
    urgencia VARCHAR(50),
    solicitante_id INTEGER REFERENCES usuarios(id),
    responsavel_id INTEGER REFERENCES usuarios(id),
    problema_id INTEGER REFERENCES problemas(id),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Problemas
CREATE TABLE problemas (
    id SERIAL PRIMARY KEY,
    tenant_id INTEGER NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    status VARCHAR(50) NOT NULL DEFAULT 'Novo', -- 'Novo', 'Investigando', 'Erro Conhecido', 'Resolvido', 'Fechado'
    prioridade VARCHAR(50),
    causa_raiz TEXT,
    solucao_contorno TEXT,
    solucao_definitiva TEXT,
    responsavel_id INTEGER REFERENCES usuarios(id),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de relacionamento incidente <-> problema (opcional se quiser N:N)
-- Comentado pois estamos usando problema_id direto na tabela de incidentes
-- CREATE TABLE incidentes_problemas (
--     incidente_id INTEGER REFERENCES incidentes(id) ON DELETE CASCADE,
--     problema_id INTEGER REFERENCES problemas(id) ON DELETE CASCADE,
--     PRIMARY KEY (incidente_id, problema_id)
-- );

-- Tabela de Artigos da Base de Conhecimento
CREATE TABLE artigos_kb (
    id SERIAL PRIMARY KEY,
    tenant_id INTEGER NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    titulo VARCHAR(255) NOT NULL,
    conteudo TEXT NOT NULL,
    categoria VARCHAR(100),
    publicado_por_id INTEGER REFERENCES usuarios(id),
    publicado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    problema_id INTEGER REFERENCES problemas(id) -- se o artigo for um erro conhecido
);

-- Tabela de Comentários (para incidentes e problemas)
CREATE TABLE comentarios (
    id SERIAL PRIMARY KEY,
    tenant_id INTEGER NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    autor_id INTEGER REFERENCES usuarios(id),
    incidente_id INTEGER REFERENCES incidentes(id),
    problema_id INTEGER REFERENCES problemas(id),
    conteudo TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Histórico de Incidentes (auditoria)
CREATE TABLE historico_incidentes (
    id SERIAL PRIMARY KEY,
    incidente_id INTEGER REFERENCES incidentes(id) ON DELETE CASCADE,
    alterado_por_id INTEGER REFERENCES usuarios(id),
    campo_alterado VARCHAR(100),
    valor_anterior TEXT,
    valor_novo TEXT,
    data_alteracao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de SLA (por prioridade e tipo)
CREATE TABLE sla (
    id SERIAL PRIMARY KEY,
    tenant_id INTEGER NOT NULL REFERENCES tenants(id) ON DELETE CASCADE,
    categoria VARCHAR(100),
    prioridade VARCHAR(50),
    tempo_resolucao_em_horas INTEGER
);

-- Índices recomendados
CREATE INDEX idx_incidentes_tenant ON incidentes(tenant_id);
CREATE INDEX idx_problemas_tenant ON problemas(tenant_id);
CREATE INDEX idx_artigos_kb_tenant ON artigos_kb(tenant_id);
CREATE INDEX idx_usuarios_email ON usuarios(email);

-- RLS (Row-Level Security) - a ser ativado por política se desejado
-- ALTER TABLE incidentes ENABLE ROW LEVEL SECURITY;
-- CREATE POLICY acesso_por_tenant ON incidentes USING (tenant_id = current_setting('app.tenant_id')::INTEGER);

-- Observação: Tabelas como categorias, serviços, equipes, arquivos, mudanças etc. podem ser adicionadas conforme necessidade futura.
