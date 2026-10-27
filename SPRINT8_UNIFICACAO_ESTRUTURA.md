# Sprint 8 — Unificação de Estrutura (`adm/` → `app/`)

## Data: 2026-10-21
## Status: Em Planejamento

---

## 1. Resumo Executivo

Eliminar a pasta `adm/` completamente, consolidando todo o código do painel administrativo dentro da estrutura principal (`app/`). O resultado será uma única base de código com namespaces segmentados por domínio:

- `App\Controllers\Publica\*` → site público (já existe)
- `App\Controllers\Admin\*`   → painel administrativo (a migrar)
- `App\Models\Admin\*`        → models do admin (a migrar)
- `App\Services\Admin\*`      → services do admin (a migrar)
- `App\Views\Admin\*`          → views do admin (a migrar)

---

## 2. Inventário Comparativo

### 2.1 Controllers

| Contexto | Localização Atual | Namespace | Qtde |
|----------|-------------------|-----------|------|
| Público | `app/Controllers/Publica/` | `App\Controllers\Publica` | 12 |
| Admin   | `adm/app/Controllers/Admin/` | `App\Controllers\Admin` | 22 |

**Conflito:** `BaseController.php` existe em ambos (`app/Controllers/` e `adm/app/Controllers/`)

### 2.2 Models

| Contexto | Localização Atual | Namespace | Qtde |
|----------|-------------------|-----------|------|
| Público | `app/Models/` | `App\Models` | 32 |
| Admin   | `adm/app/Models/` | `App\Models` | 42 |

**Conflitos detectados (mesmo nome, possível conteúdo diferente):**
`AcademicosModel`, `AperfeicoamentosModel`, `ArquivoModel`, `BairrosModel`, `CadastradosAperfeicoamentosModel`, `CadastradosCriteriosAdicionaisModel`, `CadastradosEscolaridadesModel`, `CadastradosExperienciasModel`, `CandidatosModel`, `CargosAperfeicoamentosModel`, `CargosCriteriosAdicionaisModel`, `CargosDesempateConfigModel`, `CargosEscolaridadesModel`, `CargosExperienciasModel`, `CargosModel`, `ClassificacaoModel`, `ClassificacaoScoreModel`, `CriteriosAdicionaisModel`, `DadosContratosModel`, `EditaisCandidatosModel`, `EditaisCargosModel`, `EditaisModel`, `EscolaridadesModel`, `ExperienciasModel`, `PerguntasModel`, `ProtocolosModel`, `SecretariasModel`, `SetoresModel`, `SituacaoModel`, `UsuarioModel`, `VagasModel`, `VerificadorModel`.

> **Nota:** Existem models exclusivos do admin (e.g., `CursosModel`, `DadosPrefeituraModel`, `DadosRescisaoModel`, `EditaisAbrangenciasModel`, `InstituicoesModel`, `LogsModel`, `MigradorModel`, `MotivosRescisaoModel`, `NiveisEscolaridadeModel`, `RecursosHistoricoModel`, `SegurosModel`) e models exclusivos do público (e.g., `DeficienciasModel`, `PerguntasFrequentesModel`, `VerificadorModel` na raiz).

### 2.3 Services

| Contexto | Localização Atual | Namespace | Qtde |
|----------|-------------------|-----------|------|
| Público | `app/Services/` | `App\Services` (+sub) | 50 |
| Admin   | `adm/app/Services/` | `App\Services` (+sub) | 56 |

**Conflitos potenciais:** `AperfeicoamentoService`, `EscolaridadeService`, `ExperienciaService`, `CriterioAdicionalService` (e versões Grid), `CandidatoScoreBuilderService`, `DesempateConfigService`, `OrdenacaoDinamicaService`, `PontuacaoCalculatorService`, `DesempateConfigDTO`, `ResultadoClassificacaoDTO`.

**Base/Traits:** `AbstractEmailService`, `AbstractFormService`, `AbstractGridService`, `AbstractUploadService` existem em ambos. Precisam ser comparados.

### 2.4 Views

| Contexto | Localização Atual | Qtde |
|----------|-------------------|------|
| Público | `app/Views/Publica/` | 43 |
| Admin   | `adm/app/Views/Admin/` | 72 |

**Conflitos de layout:** `welcome_message.php`, `resume.php`, `layoutSimples.php` existem em ambos.

### 2.5 Configs

| Arquivo | Raiz | Admin | Diferenças |
|---------|------|-------|------------|
| `Routes.php` | rotas públicas | rotas admin (prefixo `/`) | devem ser unificadas com prefixo `/adm` |
| `Services.php` | factories públicos | factories admin + DI extensiva | devem ser unificadas |
| `Autoload.php` | helpers: form, WKPDF, Principal, classificatorios | helpers: url, form, Principal, WKPDF, EmailHelper, MyHelper, ordinal | unificar helpers |
| `Filters.php` | padrão CI4 | CSRF + except api/* | verificar |
| `Constants.php` | padrão | aponta `../vendor/autoload.php` | ajustar |
| `Paths.php` | padrão | aponta `system/` e `writable/` relativos | tornar padrão |
| `phpwkhtmltopdf.php` | ausente | config customizada | migrar para raiz |

### 2.6 Outros Componentes

| Componente | Admin (a migrar) | Público (já existe) |
|------------|------------------|---------------------|
| Enums | 5 arquivos | 0 visível |
| Exceptions | 4 arquivos | 0 visível (público usa Domain em app/Exceptions/Domain/) |
| Traits | 3 arquivos | 0 visível |
| Repositories | 4 + 4 interfaces | 0 visível (público tem Repositories/) |
| Commands | 1 arquivo | 0 visível |
| Helpers | `EmailHelper`, `MyHelper`, `ordinal` | `WKPDF`, `Principal`, `classificatorios` |

---

## 3. Estratégia de Namespace

A pedido do usuário, os namespaces do admin serão segmentados sob `Admin\`:

| Camada Antiga | Camada Nova | Namespace Antigo | Namespace Novo |
|---------------|-------------|------------------|----------------|
| `adm/app/Controllers/Admin/` | `app/Controllers/Admin/` | `App\Controllers\Admin` | **mantido** `App\Controllers\Admin` |
| `adm/app/Models/` | `app/Models/Admin/` | `App\Models` | `App\Models\Admin` |
| `adm/app/Services/` | `app/Services/Admin/` | `App\Services` / `App\Services\X` | `App\Services\Admin` / `App\Services\Admin\X` |
| `adm/app/Repositories/` | `app/Repositories/Admin/` | `App\Repositories` | `App\Repositories\Admin` |
| `adm/app/Enums/` | `app/Enums/Admin/` | `App\Enums` | `App\Enums\Admin` |
| `adm/app/Exceptions/` | `app/Exceptions/Admin/` | `App\Exceptions` | `App\Exceptions\Admin` |
| `adm/app/Traits/` | `app/Traits/Admin/` | `App\Traits` | `App\Traits\Admin` |
| `adm/app/Views/Admin/` | `app/Views/Admin/` | — | **mantido** `app/Views/Admin/` |
| `adm/app/Commands/` | `app/Commands/Admin/` | `App\Commands` | `App\Commands\Admin` |
| `adm/app/Helpers/` | `app/Helpers/Admin/` | — | — |

**Impacto:** Todos os arquivos PHP do admin que referenciam Models, Services, Repositories, Enums, Exceptions ou Traits precisarão ter seus `use` e `type-hints` atualizados.

---

## 4. Plano de Migração (12 Etapas)

> **Pré-requisito:** Criar branch Git dedicada (`sprint8-unificacao`) antes de iniciar. Nunca modificar `main`/`master` diretamente.

### Etapa 1 — Análise de Conflitos e Diferenças
**Objetivo:** Identificar exatamente quais arquivos são duplicados e se têm diferenças.

1.1. Comparar `BaseController.php` da raiz vs. `adm/app/Controllers/BaseController.php`
1.2. Comparar models com mesmo nome (ex: `CandidatosModel.php` em ambos)
1.3. Comparar services com mesmo nome (ex: `AperfeicoamentoService.php`)
1.4. Comparar Base Services (`AbstractEmailService`, `AbstractFormService`, etc.)
1.5. Documentar resultado em `SPRINT8_CONFLITOS.md`

**Critério de Aceitação:** Lista exata de arquivos idênticos, divergentes e exclusivos.

---

### Etapa 2 — Preparação da Infraestrutura de Diretórios
**Objetivo:** Criar a estrutura de destino sem mover arquivos ainda.

2.1. Criar pastas: `app/Controllers/Admin/`, `app/Models/Admin/`, `app/Services/Admin/`, `app/Repositories/Admin/`, `app/Enums/Admin/`, `app/Exceptions/Admin/`, `app/Traits/Admin/`, `app/Commands/Admin/`, `app/Views/Admin/`, `app/Helpers/Admin/`, `tests/Admin/`
2.2. Garantir que `app/Config/Autoload.php` mapeie corretamente sub-namespaces
2.3. Preparar `app/Config/Routes.php` para receber rotas do admin com prefixo `/adm`

**Critério de Aceitação:** Estrutura de diretórios criada; zero arquivos migrados ainda.

---

### Etapa 3 — Migração de Controllers (Menor Risco)
**Objetivo:** Mover controllers do admin para `app/Controllers/Admin/`.

3.1. Copiar `adm/app/Controllers/Admin/*.php` → `app/Controllers/Admin/`
3.2. Consolidar `BaseController.php` (se divergir, criar `app/Controllers/AdminBaseController.php`)
3.3. Atualizar referências de Views nos controllers (ex: `view('Admin/pages/...')` → garantir que funcione com `app/Views/Admin/`)

**Critério de Aceitação:** Rotas do admin respondem corretamente (testar `/adm/login`, `/adm/dashboard`).

---

### Etapa 4 — Migração de Views
**Objetivo:** Mover views do admin para `app/Views/Admin/`.

4.1. Copiar `adm/app/Views/Admin/` → `app/Views/Admin/`
4.2. Copiar layouts da raiz do admin (`layoutDash.php`, `layoutLogin.php`, etc.) → `app/Views/Admin/layouts/` ou raiz `app/Views/Admin/`
4.3. Resolver conflitos de nomes de layout (`welcome_message.php`, `resume.php`, `layoutSimples.php`)
4.4. Atualizar referências internas das views (paths relativos, includes)

**Critério de Aceitação:** Renderização de páginas do admin funciona sem erros de View.

---

### Etapa 5 — Migração de Enums, Exceptions, Traits e Commands
**Objetivo:** Mover componentes auxiliares do admin.

5.1. Copiar `adm/app/Enums/` → `app/Enums/Admin/` (atualizar namespaces para `App\Enums\Admin`)
5.2. Copiar `adm/app/Exceptions/` → `app/Exceptions/Admin/` (atualizar namespaces)
5.3. Copiar `adm/app/Traits/` → `app/Traits/Admin/` (atualizar namespaces)
5.4. Copiar `adm/app/Commands/` → `app/Commands/Admin/` (atualizar namespaces)
5.5. Atualizar todos os `use` nos Controllers e Services que referenciam essas classes

**Critério de Aceitação:** `php spark list` mostra commands do admin; enums e exceptions carregam corretamente.

---

### Etapa 6 — Consolidação de Models
**Objetivo:** Resolver conflitos e migrar models.

6.1. Para cada model exclusivo do admin: copiar para `app/Models/Admin/`, namespace `App\Models\Admin`
6.2. Para cada model existente em ambos:
   - Se **idêntico**: manter na raiz (`app/Models/`), remover de `adm/app/Models/`
   - Se **diferente**: migrar versão admin para `app/Models/Admin/` (namespace `App\Models\Admin`), manter versão pública em `app/Models/`
   - Se o admin tem funcionalidades extras que a versão pública não tem: avaliar merge
6.3. Atualizar todos os `use App\Models\...` nos Controllers e Services do admin

**Critério de Aceitação:** CRUDs do admin funcionam; site público continua operacional.

---

### Etapa 7 — Consolidação de Services
**Objetivo:** Resolver conflitos e migrar services (camada mais complexa).

7.1. Para cada service exclusivo do admin: copiar para `app/Services/Admin/`, atualizar namespace
7.2. Para cada service existente em ambos:
   - Se **idêntico**: manter na raiz, remover duplicata
   - Se **diferente**: migrar versão admin para `app/Services/Admin/`
7.3. Consolidar `Base/` (`AbstractEmailService`, `AbstractFormService`, `AbstractGridService`, `AbstractUploadService`, `TransactionalTrait`):
   - Comparar versões; se o admin tem funcionalidades extras, fazer merge na raiz
7.4. Atualizar `app/Config/Services.php` (raiz) para registrar factories do admin
7.5. Atualizar todos os `use App\Services\...` nos Controllers

**Critério de Aceitação:** Funcionalidades críticas do admin (classificação, exportação, contratos) funcionam.

---

### Etapa 8 — Migração de Repositories
**Objetivo:** Mover repositories para `app/Repositories/Admin/`.

8.1. Copiar `adm/app/Repositories/` → `app/Repositories/Admin/`
8.2. Atualizar namespaces de `App\Repositories\...` para `App\Repositories\Admin\...`
8.3. Atualizar interfaces e implementações
8.4. Atualizar `app/Config/Services.php` com factories de repositories do admin

**Critério de Aceitação:** Testes de repositories do Sprint 7 passam.

---

### Etapa 9 — Migração de Helpers e Libraries
**Objetivo:** Consolidar helpers.

9.1. Copiar helpers exclusivos do admin (`EmailHelper`, `MyHelper`, `ordinal`) para `app/Helpers/Admin/`
9.2. Atualizar `app/Config/Autoload.php` para carregar helpers do admin
9.3. Verificar se `app/Common/` já atende alguma necessidade; manter compatibilidade

**Critério de Aceitação:** Helpers do admin carregam sem erros.

---

### Etapa 10 — Unificação de Configs
**Objetivo:** Consolidar configs em `app/Config/`.

10.1. `Routes.php`: incorporar rotas do admin com prefixo `/adm` (usar `$routes->group('adm', ...)`)
10.2. `Services.php`: incorporar todas as factories do admin
10.3. `Autoload.php`: incorporar mapeamentos e helpers do admin
10.4. `Filters.php`: incorporar regras do admin (CSRF, etc.)
10.5. `phpwkhtmltopdf.php`: copiar para raiz
10.6. `Constants.php`: garantir que `COMPOSER_PATH` aponte corretamente para `ROOTPATH . 'vendor/autoload.php'` (sem `../`)

**Critério de Aceitação:** Sistema inicializa sem erros de configuração.

---

### Etapa 11 — Migração de Tests
**Objetivo:** Consolidar testes PHPUnit.

11.1. Copiar `adm/tests/Services/` → `tests/Admin/Services/`
11.2. Copiar `adm/tests/Repositories/` → `tests/Admin/Repositories/`
11.3. Atualizar namespaces nos testes (`use App\Services\Admin\...`)
11.4. Ajustar `phpunit.xml.dist` da raiz para incluir suite do admin
11.5. Executar todos os testes e garantir passagem

**Critério de Aceitação:** Todos os testes do Sprint 7 passam na estrutura unificada.

---

### Etapa 12 — Migração de Assets, Writable e Limpeza Final
**Objetivo:** Eliminar `adm/` completamente.

12.1. Migrar assets de `adm/public/` → `public/adm/` (ou incorporar ao `public/` existente)
12.2. Configurar `writable/` para suportar subpastas `public/` e `admin/`
12.3. Ajustar entry point (`public/index.php`) se necessário para rotear `/adm`
12.4. Executar testes completos do site público
12.5. Executar testes completos do admin
12.6. Testes manuais de funcionalidades críticas (login, classificação, exportação, contratos)
12.7. Remover pasta `adm/` (ou renomear para `_adm_bkp/` como precaução final)
12.8. Atualizar `AGENTS.md`, `README.md`, `composer.json` do `adm/` (remover)

**Critério de Aceitação:** Zero referências a `adm/` no código; ambas as camadas operacionais.

---

## 5. Riscos e Mitigações

| Risco | Impacto | Mitigação |
|-------|---------|-----------|
| Models/Services com mesmo nome mas comportamento diferente | Quebra de funcionalidade | Comparar arquivo a arquivo antes de consolidar; manter duas versões se necessário |
| `BaseController` divergente | Comportamento inconsistente entre camadas | Criar `AdminBaseController` separado se a divergência for grande |
| `Services.php` do admin é muito extenso | Difícil unificar sem erros | Migrar em partes; testar DI container após cada grupo de factories |
| Rotas com conflito de nome | Acesso incorreto | Garantir prefixo `/adm` em todas as rotas do admin |
| Views com paths hardcoded | Erros de renderização | Buscar e substituir paths em views e controllers |
| Testes quebram após mudança de namespace | Regressão | Executar testes após cada etapa; corrigir namespaces nos testes |
| Perda de dados em `writable/` ou `uploads/` | Perda de arquivos | Nunca mover dados de produção; apenas código |
| Helpers carregados globalmente | Conflito de nomes de funções | Verificar se helpers definem funções globais com nomes conflitantes |

---

## 6. Estimativa de Tamanho

| Camada | Arquivos a Migrar | Complexidade |
|--------|-------------------|--------------|
| Controllers | ~22 | Baixa |
| Views | ~72 | Média (paths) |
| Models | ~42 | Alta (conflitos) |
| Services | ~56 | Alta (conflitos + DI) |
| Repositories | ~8 | Média |
| Configs | ~5 | Alta (Routes, Services) |
| Tests | ~6 | Média |
| Outros | ~20 | Baixa |
| **Total** | **~230 arquivos** | **Alta** |

---

## 7. Próximos Passos

1. Criar branch `sprint8-unificacao`
2. Executar Etapa 1 (análise de conflitos)
3. Aprovar plano com stakeholders
4. Executar etapas 2–12 sequencialmente com testes contínuos
