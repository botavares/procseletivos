# AGENTS.md — Processo Seletivo (procseletivos)

## Visão Geral

Este repositório abriga **uma única aplicação CodeIgniter 4 unificada** que serve tanto o site público quanto o painel administrativo. O código do admin foi migrado de `adm/app/` para a estrutura principal (`app/`) sob namespaces segmentados `Admin/`.

- **Site Público** (`/`) — Candidatos se inscrevem, consultam editais e acompanham processos seletivos.
- **Painel Administrativo** (`/adm`) — Gerência de editais, cargos, candidatos, classificações, convocações e contratos.

Após o **Sprint 8**, todo o código-fonte do admin reside em `app/` (Models, Controllers, Services, Views, Repositories, Enums, Exceptions, Traits). A pasta `adm/` permanece apenas como **entry point de deploy** (`adm/public/index.php` aponta para a aplicação unificada da raiz).

---

## Estrutura de Diretórios (pós-Sprint 8 — Unificada)

```
procseletivos/
├── app/                    # Aplicação Unificada (CI4)
│   ├── Config/
│   │   ├── Routes.php      # Rotas públicas + rotas do admin (group 'adm')
│   │   ├── Services.php    # Services públicos + services do admin (prefixados)
│   │   └── Autoload.php    # Autoload unificado (inclui helpers do admin)
│   ├── Controllers/
│   │   ├── Publica/        # Controllers do site público
│   │   └── Admin/          # Controllers do painel administrativo
│   ├── Models/
│   │   ├── ...             # Models do site público
│   │   └── Admin/          # Models do painel administrativo
│   ├── Repositories/
│   │   ├── ...             # Repositories do site público
│   │   └── Admin/          # Repositories do painel administrativo
│   ├── Services/
│   │   ├── ...             # Services do site público
│   │   └── Admin/          # Services do painel administrativo
│   ├── Enums/
│   │   └── Admin/          # Enums do admin
│   ├── Exceptions/
│   │   └── Admin/          # Exceções de domínio do admin
│   ├── Traits/
│   │   └── Admin/          # Traits do admin
│   ├── Helpers/
│   │   └── Admin/          # Helpers específicos do admin
│   └── Views/
│       ├── Publica/        # Views do site público
│       └── Admin/          # Views do painel administrativo
├── system/                 # Framework CI4 (compartilhado)
├── vendor/                 # Dependências Composer (compartilhado)
├── public/                 # Document root do site público
├── writable/               # Cache, logs, uploads do site público
├── tests/                  # Testes unificados
│   ├── unit/               # Testes do site público
│   └── Admin/              # Testes do painel administrativo
│       ├── Repositories/
│       └── Services/
├── composer.json           # Dependências unificadas
├── phpunit.xml.dist        # Testes unificados (aponta para ./tests)
│
├── adm/                    # Entry point legado do admin (deploy)
│   ├── public/             # Document root do admin (index.php → raiz)
│   ├── writable/           # Logs/uploads do admin (runtime)
│   └── app/Config/Paths.php # Aponta $appDirectory para app/ da raiz
│
├── SPRINTS_PUBLICO_2.md    # Planejamento de sprints
├── AUDITORIA_PROCSEL_20261019.md  # Auditoria arquitetural
└── AGENTS.md               # Este arquivo
```

---

## Convenções Importantes

### 1. Namespaces Segmentados

Todo o código do admin foi migrado para sub-namespaces `Admin/` dentro de `app/`:

| Tipo | Namespace Público | Namespace Admin |
|------|-------------------|-----------------|
| Controllers | `App\Controllers\Publica` | `App\Controllers\Admin` |
| Models | `App\Models` | `App\Models\Admin` |
| Services | `App\Services` | `App\Services\Admin` |
| Repositories | `App\Repositories` | `App\Repositories\Admin` |
| Enums | — | `App\Enums\Admin` |
| Exceptions | — | `App\Exceptions\Admin` |
| Traits | — | `App\Traits\Admin` |

### 2. Rotas Unificadas

As rotas do admin estão integradas em `app/Config/Routes.php` dentro do grupo:

```php
$routes->group('adm', ['namespace' => 'App\Controllers\Admin'], function($routes) {
    $routes->get('login', 'Login::index', ['as'=>'login']);
    // ... demais rotas sem barra inicial
});
```

**Atenção:** dentro do grupo, nunca use barra inicial nas rotas (ex: `login`, não `/login`), para evitar `adm//login`.

### 3. Services Unificados

O arquivo `app/Config/Services.php` contém os services de ambas as camadas. Oito métodos que colidiam foram renomeados com prefixo `admin` no admin (ex: `adminCriarInstanciaXxx`).

### 4. Dependências

Todas as dependências Composer são gerenciadas **apenas na raiz**:

- `composer update` → execute sempre na raiz (`procseletivos/`)
- `composer.json` da raiz já inclui dependências exclusivas do admin (`phpmailer/phpmailer`, `phpoffice/phpspreadsheet`)

### 5. Testes Unificados

**Localização dos testes:**
- Site público: `tests/unit/`
- Admin: `tests/Admin/` — Services (`tests/Admin/Services/`) e Repositories (`tests/Admin/Repositories/`)

**Comandos:**

```bash
# Testes unificados (raiz)
cd C:\laragon\www\servicos\procseletivos
php vendor\bin\phpunit

# Com cobertura de código (requer xdebug)
php vendor\bin\phpunit --coverage-text
```

**Suites criadas no Sprint 7 / migradas no Sprint 8:**
- `Tests\Admin\Services\Classificacao\OrdenacaoDinamicaServiceTest`
- `Tests\Admin\Services\Classificacao\PontuacaoCalculatorServiceTest`
- `Tests\Admin\Services\Classificacao\DesempateConfigDTOTest`
- `Tests\Admin\Repositories\CandidatoRepositoryTest`
- `Tests\Admin\Repositories\ClassificacaoRepositoryTest`
- `Tests\Admin\Repositories\ContratoRepositoryTest`

### 6. CLI (spark)

Agora há apenas um ponto de entrada `spark` na raiz. O `adm/` carrega a aplicação da raiz via `Paths.php`:

```bash
cd C:\laragon\www\servicos\procseletivos
php spark routes
php spark list
```

---

## Regras para Agentes de IA

1. **Evite over-engineering** — Prefira soluções simples que mantenham o sistema operacional.
2. **Sempre verifique paths** — O `adm/` é apenas entry point; todo código-fonte está em `app/`.
3. **Não duplique código** — Use `app/Common/` para compartilhar Models, Services ou Utils entre público e admin.
4. **Nunca rode `composer install/update` dentro de `adm/`** — Execute sempre na raiz.
5. **Mantenha backups** — Ao modificar configs críticas (`Paths.php`, `Constants.php`), preserve `.bak`.
6. **Teste ambas as camadas** — Uma mudança em `app/` pode afetar tanto o público quanto o admin.
7. **Não recrie `adm/app/`, `adm/system/` ou `adm/vendor/`** — Essas pastas foram consolidadas na raiz.

---

## Stack Tecnológica

- **Framework**: CodeIgniter 4.3.1
- **PHP**: 8.3+
- **Banco**: MySQL (via MySQLi)
- **Frontend**: Bootstrap 4, AdminLTE, jQuery, Select2, Font Awesome 5
- **PDF**: Dompdf, mikehaertl/phpwkhtmltopdf
- **Excel**: PhpSpreadsheet 5.7
- **QRCode**: chillerlan/php-qrcode
- **E-mail**: PHPMailer 6.9

---

## Histórico de Sprints Relevantes

| Sprint | Data | Entrega |
|--------|------|---------|
| Sprint 0 | Concluído | Hotfixes de segurança (backdoor, SQL injection) |
| Sprint 1 | Concluído | Namespace compartilhado `App\Common\`, DI no admin |
| Sprint 2 | Concluído | Services de Exportação (Excel/PDF/QrCode) |
| Sprint 3 | Concluído | Repository Pattern no admin |
| Sprint 4 | Concluído | Slim Controllers (refatoração de gordura) |
| Sprint 5 | Concluído | Enums, Exceções de domínio, namespaces PSR-4 |
| Sprint 6 | Concluído | Consolidação de `system/` e `vendor/` (Estratégia B) |
| Sprint 7 | Concluído | Testes unitários para 6 Services/Repositories + cobertura configurada |
| **Sprint 8** | **Concluído** | **Unificação total da estrutura: código do admin migrado para `app/` sob namespaces `Admin/`** |

---

*Atualizado em: 2026-10-21*