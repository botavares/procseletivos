# Checkpoint – Refatoração ProcSeletivos (Parte Pública)

> **Data do checkpoint:** 19/10/2026  
> **Projeto:** procseletivos  
> **Responsável:** Analista/Dev Sênior PHP 8.3+ | CodeIgniter 4

---

## Sprints Concluídos

| Sprint | Status | Resumo |
|--------|--------|--------|
| **SPRINT 1** | ✅ Concluído | Credenciais removidas do código; `.env.example` criado; `.env` removido do Git; `.gitignore` e `.htaccess` robustecidos; PHP mínimo atualizado para 8.3; `loginLocal()` blindado para dev. |
| **SPRINT 2** | ✅ Concluído | Hardening de `App.php` (force HTTPS, cookies secure/Strict, CSP), `Security.php` (CSRF token randomizado), `Session.php` (regenerate destroy, timeToUpdate=180), `Encryption.php` (key via .env), `Database.php` (DBDebug via env, strictOn), `ContentSecurityPolicy.php` (upgradeInsecureRequests), `Filters.php` (secureheaders, CSRF exceção api/*). |
| **SPRINT 3** | ✅ Concluído | 30 models padronizados com `declare(strict_types=1)`, `$useTimestamps`, `$useSoftDeletes`, `$validationRules`, `$returnType`. PKs compostas corrigidas (`EditaisCargosModel`, `EditaisCandidatosModel` → BaseBuilder). Mass assignment protegido (`VerificadorModel`). Métodos com queries complexas deprecados com `@deprecated`. |
| **SPRINT 4** | ✅ Concluído | Camada de Repository criada (`app/Repositories/` + `Interfaces/`): `ICandidatoRepository`, `IContratoRepository`, `IConvocadoRepository`, `IEditalRepository`, `IProtocoloRepository`. Queries massivas migradas dos Models para Repositories. Services (`EditaisService`, `ProtocoloService`, `DadosContratadoService`) atualizados para consumir Repositories via `service()`. Bindings registrados em `Config\Services.php`. |
| **SPRINT 5** | ✅ Concluído | Interfaces criadas para todos os Services (`app/Services/Interfaces/`); DI via construtor em todos os Services; `ClassificatorioService` quebrado em `ExperienciaService`, `EscolaridadeService`, `AperfeicoamentoService`, `CriterioAdicionalService` (com transações); `GovBrService` refatorado (`curlrequest`, DTO, `.env`); `PdfService` com `DeficienciaFormatter` extraído; `CandidatoRecursoService` com `RecursoRepository` e `RecursoFormatter`; `TransparenciaService` com `TransparenciaRepository` e `TransparenciaPresenter`; `declare(strict_types=1)` e type hints completos; `Config\Services.php` atualizado. |
| **SPRINT 6** | ✅ Concluído | Controllers da parte pública refatorados: removidos todos `new Model`, `$_SESSION`, `$_GET`, `echo view`, `echo json_encode`; `session()` isolado em `SessaoService`; novos Services criados (`HomeService`, `PerguntaFrequenteService`, `SetorService`, `ServicoService`); `EditaisService` expandido com métodos faltantes; `declare(strict_types=1)` e type hints nos Controllers. |
| **SPRINT 7** | ✅ Concluído | `Routes.php` reescrito e limpo: removidas rotas duplicadas (`transparencia`, `home`), rotas para métodos inexistentes (`/Cadastros/analisar`, `/transparencia/filtrarPorCargo`, etc.) e rotas para controllers mortos (`PdfGenerator`, `Perguntas`); métodos `@deprecated` não utilizados removidos dos Models (`CandidatosModel`, `ConvocadosModel`, `ProtocolosModel`); `loginGovBr` duplicado removido de `Home.php`. |

---

## Mudanças Estruturais Extra (fora do escopo original da auditoria)

Durante a execução dos sprints 1–4, identificou-se a necessidade de reorganizar a estrutura de pastas para melhor separação de concerns:

1. **Controllers separados por domínio:**
   - `app/Controllers/Publica/*` – controllers da parte pública
   - `app/Controllers/Publica/Api/*` – controllers de API
   - `adm/app/Controllers/Admin/*` – controllers da parte administrativa
   - Namespaces atualizados: `App\Controllers\Publica`, `App\Controllers\Publica\Api`, `App\Controllers\Admin`

2. **Views separadas por domínio:**
   - `app/Views/Publica/*` – views, partials, layouts e páginas da parte pública
   - `adm/app/Views/Admin/*` – views da parte administrativa
   - Layouts (`layoutSimples.php`, `layoutLogado.php`, `layoutSimplesHeader2.php`) movidos para `Publica/`
   - Views de erro (`errors/`) mantidas em `app/Views/errors/` (compartilhadas)

3. **Assets (`external/`) remanejados:**
   - `external/` (raiz) → `public/publica/external/`
   - `adm/external/` → `public/admin/external/`
   - Referências em views atualizadas para `base_url("public/publica/external/...")` e `base_url("public/admin/external/...")`

4. **Problemas de ambiente local corrigidos:**
   - `forceGlobalSecureRequests` desabilitado em dev via `.env` (`app.forceGlobalSecureRequests = false`)
   - CSP desabilitado automaticamente quando `ENVIRONMENT === 'development'` (evita bloqueio de CDNs e scripts inline nos partialviews)
   - `.htaccess` com regra HTTP→HTTPS comentada para ambiente local (deve ser descomentada em produção)

---

## Estado Atual do Sistema

- **Tela inicial (`Home/index`)**: ✅ Acessível e renderizando
- **Assets (CSS/JS/imagens)**: ✅ Caminhos corrigidos para `public/publica/external/`
- **PHPUnit**: ✅ 5 tests, 7 assertions — OK
- **Sintaxe PHP**: ✅ Todos os models, controllers, services e repositories sem erros (`php -l`)
- **Git**: `.env` removido do tracking; `.gitignore` atualizado

---

## Pendências Conhecidas / Débito Técnico

| # | Item | Sprint Relacionado |
|---|------|-------------------|
| 1 | `EditaisCargosModel` e `EditaisCandidatosModel` usam `BaseBuilder` sem herança de `Model`. Isso quebra o uso de `save()`, `find()`, etc., mas foi aceito porque são tabelas de junção. Se necessário, criar migrations para PKs surrogate no futuro. | SPRINT 3 |
| 2 | Métodos `@deprecated` nos Models (`getContratosPorId`, `getContratosExpirando`, `getContratosAtivos`, `getContratosPorEditalCurso`, `checkPassword`) ainda existem porque a parte **administrativa** (`adm/`) ainda os consome. Serão removidos quando a parte admin for refatorada. | SPRINT 7 (escopo admin) |
| 3 | `VinculoCandidatoService` e `DadosContratadoService` na parte pública usam Models diretamente; não foram criados Repositories dedicados para seus domínios. | SPRINT 5 (débito) |

---

## Estado Atual

**Parte pública (`app/`):** ✅ Refatoração concluída (SPRINTs 1–7).  
**Parte administrativa (`adm/`):** ⏳ Fora do escopo da auditoria atual; ainda consome Models diretamente e possui métodos `@deprecated`.

---

## Como Retomar

1. Ler este `CHECKPOINT.md` para entender o estado atual.
2. Executar `composer validate` e `php vendor/bin/phpunit` para confirmar que a baseline está verde.
3. Se desejar refatorar a parte **administrativa**, criar novos SPRINTs para `adm/`.
4. Ao final de cada sprint, rodar `php vendor/bin/phpunit` para garantir que não houve regressões.

---

> **Arquivos de referência:**
> - `AUDITORIA_PROCSEL_20261016.md` – achados originais
> - `SPRINTS_PUBLICO.md` – cronograma completo
> - `.env.example` – template de variáveis de ambiente
