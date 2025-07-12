# SIA - Sistema de Incidentes e Atendimentos

Este projeto é um protótipo de sistema ITSM desenvolvido em Laravel 10 com Inertia.js e Vue 3. Ele demonstra cadastro de incidentes, problemas e artigos de base de conhecimento em um ambiente multi-tenant.

## Requisitos

- PHP >= 8.1
- Composer
- Node.js e npm
- PostgreSQL

## Instalação

1. Clone o repositório e instale as dependências PHP:
   ```bash
   composer install
   ```
2. Copie o arquivo `.env.example` para `.env` e ajuste as credenciais do banco PostgreSQL.
3. Execute as migrations e seeds:
   ```bash
   php artisan migrate --seed
   ```
4. (Opcional) instale as dependências front-end:
   ```bash
   npm install && npm run build
   ```
5. Adicione em seu `/etc/hosts` os domínios de exemplo para testes:
   ```
   127.0.0.1 demo.sia.local
   127.0.0.1 ifto.sia.local
   ```
6. Inicie o servidor:
   ```bash
   php artisan serve
   ```
   Acesse `http://demo.sia.local:8000`.

## Multi‑tenant por domínio

O middleware `TenantMiddleware` identifica o tenant através do domínio acessado. Cada registro em `tenants` possui o campo `dominio`. Ao acessar `demo.sia.local` ou `ifto.sia.local` o sistema carrega automaticamente os dados do respectivo tenant.

## Autorização por papéis

Há três papéis de usuário: `admin`, `tecnico` e `gestor`. O middleware `RoleMiddleware` garante que somente usuários autorizados possam gerenciar a base de conhecimento. Exemplo de uso:

```php
Route::resource('base-conhecimento', ArtigoKbController::class)
    ->middleware('role:admin,tecnico');
```

## Testes

Os testes automatizados podem ser executados com:

```bash
php artisan test
```

*(dependências devem estar instaladas)*
