# AGENTS.md — Processo Seletivo (procseletivos)

## Visão Geral

Este repositório abriga duas aplicações CodeIgniter 4 que compartilham uma única base de framework:

- **Site Público** (`/`) — Candidatos se inscrevem, consultam editais e acompanham processos seletivos.
- **Painel Administrativo** (`/adm`) — Gerência de editais, cargos, candidatos, classificações, convocações e contratos.

Após o **Sprint 6**, ambas as aplicações usam **um único `system/` e `vendor/`** na raiz. A pasta `adm/` contém apenas o código específico do admin (`app/`, `public/`, `writable/`, `tests/`).

---

## Estrutura de Diretórios (pós-Sprint 6)

```
procseletivos/
├── app/                    # Aplicação Pública (CI4)
│   ├── Config/
│   ├── Controllers/Publica/
│   ├── Models/
│   ├── Repositories/
│   ├── Services/
│   ├── Views/Publica/
│   └── ...
├── system/                 # Framework CI4 (compartilhado)
├── vendor/                 # Dependências Composer (compartilhado)
├── public/                 # Document root do site público
├── writable/               # Cache, logs, uploads do site público
├── tests/                  # Testes do site público
├── composer.json           # Dependências unificadas (inclui phpmailer, phpspreadsheet)
├── phpunit.xml.dist        # Testes do site público
│
├── adm/                    # Painel Administrativo (CI4)
│   ├── app/                # Código específico do admin
│   │   ├── Config/
│   │   ├── Controllers/Admin/
│   │   ├── Models/
│   │   ├── Services/
│   │   ├── Views/Admin/
│   │   └── ...
│   ├── public/             # Document root do admin (acessado via /adm)
│   ├── writable/           # Cache, logs, uploads do admin
│   ├── tests/              # Testes do admin
│   └── composer.json       # vendor-dir aponta para "../vendor"
│
├── SPRINTS_PUBLICO_2.md    # Planejamento de sprints
├── AUDITORIA_PROCSEL_20261019.md  # Auditoria arquitetural
└── AGENTS.md               # Este arquivo
```

---

## Convenções Importantes

### 1. Caminhos Compartilhados

O `adm/` **não possui** `system/` nem `vendor/` próprios. Os ponteiros estão em:

- `adm/app/Config/Paths.php` → `$systemDirectory = __DIR__ . '/../../../system'`
- `adm/app/Config/Constants.php` → `COMPOSER_PATH = ROOTPATH . '../vendor/autoload.php'`
- `adm/phpunit.xml.dist` → `bootstrap="../system/Test/bootstrap.php"`
- `adm/composer.json` → `"vendor-dir": "../vendor"`

**Nunca recrie `adm/system/` ou `adm/vendor/`.**

### 2. Autoload Compartilhado

O namespace `App\Common\` é compartilhado entre as duas aplicações via:
- **Pública**: `app/Common/` (PSR-4 `App\Common\` → `app/Common/`)
- **Admin**: `adm/app/Config/Autoload.php` mapeia `App\Common\` → `ROOTPATH . '../app/Common/'`

Classes colocadas em `app/Common/` são acessíveis por ambas as camadas.

### 3. Dependências

Todas as dependências Composer são gerenciadas **apenas na raiz**:

- `composer update` → execute sempre na raiz (`procseletivos/`)
- `composer.json` da raiz já inclui dependências exclusivas do admin (`phpmailer/phpmailer`, `phpoffice/phpspreadsheet`)
- `composer.json` do `adm/` existe apenas para CI/CD e validação; o `vendor-dir` aponta para `"../vendor"`

### 4. Rotas

- **Site público**: rotas definidas em `app/Config/Routes.php` (namespace `App\Controllers\Publica`)
- **Admin**: rotas definidas em `adm/app/Config/Routes.php` (namespace `App\Controllers\Admin`)
- Acesso ao admin: `http://localhost/servicos/procseletivos/adm` (pasta `adm/public` servida como subdiretório)

### 5. Testes

**Localização dos testes:**
- Site público: `tests/` (raiz)
- Admin: `adm/tests/` — Services (`adm/tests/Services/`) e Repositories (`adm/tests/Repositories/`)

**Comandos:**

```bash
# Testes do site público (raiz)
cd C:\laragon\www\servicos\procseletivos
php vendor\bin\phpunit

# Testes do admin (usa system/vendor da raiz)
cd C:\laragon\www\servicos\procseletivos\adm
php ..\vendor\bin\phpunit

# Com cobertura de código (requer xdebug)
cd C:\laragon\www\servicos\procseletivos\adm
php ..\vendor\bin\phpunit --coverage-text
```

**Suites criadas no Sprint 7:**
- `OrdenacaoDinamicaServiceTest` — ordenação e desempate dinâmico
- `PontuacaoCalculatorServiceTest` — regras de teto de pontuação
- `DesempateConfigDTOTest` — geração de chaves de score
- `CandidatoRepositoryTest` — delegação ao Model
- `ClassificacaoRepositoryTest` — delegação ao Model
- `ContratoRepositoryTest` — delegação ao Model

### 6. CLI (spark)

```bash
# Site público
cd C:\laragon\www\servicos\procseletivos
php spark list

# Admin
cd C:\laragon\www\servicos\procseletivos\adm
php spark list
```

Ambos usam o mesmo `system/` da raiz.

---

## Regras para Agentes de IA

1. **Evite over-engineering** — Prefira soluções simples que mantenham o sistema operacional.
2. **Sempre verifique paths** — O `adm/` depende de `system/` e `vendor/` da raiz.
3. **Não duplique código** — Use `app/Common/` para compartilhar Models, Services ou Utils.
4. **Nunca rode `composer install/update` dentro de `adm/`** — Execute sempre na raiz.
5. **Mantenha backups** — Ao modificar configs críticas (`Paths.php`, `Constants.php`), preserve `.bak`.
6. **Teste ambas as camadas** — Uma mudança na raiz pode afetar o admin (e vice-versa).

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
| **Sprint 6** | **Concluído** | **Consolidação de `system/` e `vendor/` (Estratégia B)** |
| **Sprint 7** | **Concluído** | **Testes unitários para 6 Services/Repositories + cobertura configurada** |

---

*Atualizado em: 2026-10-20*
