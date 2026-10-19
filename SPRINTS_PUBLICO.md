# Sprints de Refatoração – Parte Pública (ProcSeletivos)

> **Documento derivado da Auditoria:** `AUDITORIA_PROCSEL_20261016.md`  
> **Escopo:** Aplicação pública (`app/` – raiz do projeto, **excluindo** `adm/`)  
> **Objetivo:** Executar a refatoração incremental da parte pública em sprints cronológicos, estabilizando cada camada antes de avançar para a próxima.
> 
> **Nota:** Durante os sprints 1–4, foi realizada uma **reorganização estrutural** adicional (não prevista na auditoria original) para separar controllers/views por domínio e mover assets `external/` para `public/publica/` e `public/admin/`. Detalhes no arquivo `CHECKPOINT.md`.

---

## Visão Geral dos Sprints

| Sprint | Fase / Tópico | Dependência | Estimativa |
|--------|---------------|-------------|------------|
| **SPRINT 1** | FASE 1 – TÓPICO 1.1: Remoção de credenciais hardcoded + hardening `.env`, `.gitignore`, `.htaccess` | — | 2 dias |
| **SPRINT 2** | FASE 1 – TÓPICO 1.2: Hardening das configurações CI4 (App, Security, Session, Encryption, CSP, Database) | SPRINT 1 | 2 dias |
| **SPRINT 3** | FASE 2 – TÓPICO 2.1: Padronização e correção dos Models da parte pública | SPRINT 2 | 4 dias |
| **SPRINT 4** | FASE 2 – TÓPICO 2.2: Implementação da camada de Repository na parte pública | SPRINT 3 | 4 dias |
| **SPRINT 5** | FASE 3 – TÓPICO 3.1: Refatoração de Services (interfaces, DI, SRP) | SPRINT 4 | 5 dias |
| **SPRINT 6** | FASE 4 – TÓPICO 4.1: Refatoração dos Controllers da parte pública | SPRINT 5 | 4 dias |
| **SPRINT 7** | FASE 4 – TÓPICO 4.2 + 4.3: Refatoração da API (`CepController`) + ajuste final de Rotas e Filtros | SPRINT 6 | 2 dias |
| **SPRINT 8** | Validação, testes de regressão e checklist de segurança | SPRINT 7 | 2 dias |

---

## SPRINT 1 – Fundação de Segurança: Credenciais & Infraestrutura

**Duração:** 2 dias  
**Dependência:** Nenhuma (primeiro sprint)  
**Escopo:** Raiz do projeto (`app/Config/Database.php`, `app/Config/Credentials.php`, `.env`, `.env.example`, `.gitignore`, `.htaccess`, `composer.json`, `index.php` raiz)

### Entregáveis
1. `.env.example` completo na raiz com todas as variáveis necessárias (database, encryption, govbr, app baseURL).
2. Remoção de credenciais hardcoded de:
   - `app/Config/Database.php` e `adm/app/Config/Database.php`
   - `app/Config/Credentials.php` (govbrAppKey / govbrAppSecret)
3. Remoção ou blindagem do método `loginLocal()` em `Cadastros.php` (só executa em dev + flag `LOGIN_MODE=local`).
4. `.gitignore` robusto na raiz e em `adm/`.
5. `.env` removido do rastreamento Git (`git rm --cached .env`).
6. `.htaccess` na raiz protegendo `vendor/`, `writable/`, `system/`, `app/`, `adm/`, `node_modules/`; redirecionamento HTTP→HTTPS.
7. `index.php` na raiz (fora de `public/`) removido ou protegido com verificação de path real.
8. `composer.json` atualizado para exigir PHP `^8.3`.

### Critérios de Aceite
- [x] Nenhuma credencial literal permanece nos arquivos de configuração versionados.
- [x] `composer validate` executa sem erros.
- [x] `.env` não aparece em `git ls-files`.
- [x] Acesso direto a `vendor/` e `writable/` retorna 403.
- [x] Controllers e Views separados em pastas `Publica/` e `Admin/`; assets `external/` remanejados para `public/publica/` e `public/admin/`.

---

## SPRINT 2 – Hardening das Configurações do CodeIgniter 4

**Duração:** 2 dias  
**Dependência:** SPRINT 1 (`.env` pronto para receber valores)  
**Escopo:** `app/Config/` (e `adm/app/Config/` quando houver equivalente)

### Entregáveis
1. **`App.php`**:
   - `$forceGlobalSecureRequests = true`
   - `$CSPEnabled = true`
   - `$cookieSecure = true`, `$cookieSameSite = 'Strict'`
   - `$cookieHTTPOnly = true`
2. **`Security.php`**:
   - `$tokenRandomize = true`
   - `$regenerate = true`
   - `$tokenName = 'csrf_procsel_token'`
3. **`Session.php`**:
   - `$regenerateDestroy = true`
   - `$timeToUpdate = 180`
4. **`Encryption.php`**:
   - `$key` vindo de `env('ENCRYPTION_KEY')`
   - `$driver = 'OpenSSL'`, `$digest = 'SHA512'`
5. **`Database.php`**:
   - `$DBDebug = env('CI_DB_DEBUG', false)`
   - `$strictOn = true`
6. **`ContentSecurityPolicy.php`**:
   - Diretivas restritivas (`'self'`), `upgradeInsecureRequests = true`
   - `autoNonce = true`
7. **`Filters.php`**:
   - `secureheaders` em `after` global
   - `csrf`/`honeypot` em `before`
   - Exceção de CSRF para rotas `api/*`

### Critérios de Aceite
- [x] Cookies só são transmitidos via HTTPS.
- [x] CSRF token é regenerado a cada request.
- [x] Debug de banco está desligado em produção.
- [x] Rotas `api/cep` não exigem token CSRF.
- [x] CSP desabilitado automaticamente em ambiente `development` (evita bloqueio de CDNs/scripts inline em dev).

---

## SPRINT 3 – Padronização e Correção dos Models da Parte Pública

**Duração:** 4 dias  
**Dependência:** SPRINT 2 (configurações estáveis)  
**Escopo:** Todos os arquivos em `app/Models/` (29 models)

### Entregáveis
1. Cada Model declarando explicitamente:
   - `$table`, `$primaryKey`, `$allowedFields`
   - `$useTimestamps = false|true`
   - `$useSoftDeletes = false|true`
   - `$validationRules = []`
   - `$returnType = 'object'`
   - `$useAutoIncrement = true|false`
2. Correção de PKs problemáticas:
   - `EditaisCargosModel.php`: remover array de PK (migration para surrogate `pk_id_edital_cargo` ou uso de `BaseBuilder`)
   - `EditaisCandidatosModel.php`: corrigir FK usada como PK (migration para `pk_id_vinculo` ou `BaseBuilder`)
3. Segurança de Mass Assignment: remover PK de `$allowedFields` em `VerificadorModel.php`.
4. Regras de validação (`$validationRules`) para todos os campos em `$allowedFields`.
5. Remoção de lógica de negócio dos Models para Services/Domain (ex: `UsuarioModel::checkPassword()`, `DadosContratosModel::getContratosExpirando()`).
6. Depreciação de métodos com queries massivas (joins/subqueries), com comentário `@deprecated` — serão migrados nos Repositories no Sprint 4.
7. `ClassificacaoViewModel.php` declarada como read-only (`$allowedFields = []`).
8. `declare(strict_types=1);` e type hints em todos os métodos públicos.

### Critérios de Aceite
- [x] Todos os 30 models possuem as declarações obrigatórias.
- [x] Nenhum Model possui `$primaryKey` como array.
- [x] Nenhum Model contém lógica de negócio (apenas persistência).
- [x] Métodos com queries complexados foram deprecados com `@deprecated`.

---

## SPRINT 4 – Implementação da Camada de Repository

**Duração:** 4 dias  
**Dependência:** SPRINT 3 (models limpos e estáveis)  
**Escopo:** Criar `app/Repositories/` e `app/Repositories/Interfaces/`

### Entregáveis
1. Interfaces em `app/Repositories/Interfaces/`:
   - `ICandidatoRepository`
   - `IEditalRepository`
   - `IContratoRepository`
   - `IConvocadoRepository`
   - `IClassificacaoRepository`
   - `IUsuarioRepository`
   - `IProtocoloRepository`
   - `ITransparenciaRepository`
2. Implementações concretas em `app/Repositories/`, recebendo Models via construtor (injeção de dependência).
3. Queries complexas (joins, subqueries, filtros dinâmicos) movidas dos Models para os Repositories.
4. Métodos de Repository retornam `array` de objetos ou DTOs — nunca `ResultInterface` exposto diretamente.
5. Registro dos bindings em `app/Config/Services.php` (ou autowiring via `Config\Autoload`).
6. Atualização dos Services que instanciavam Models manualmente para receberem Repositories via construtor (compatibilidade temporária).

### Critérios de Aceite
- [x] Todos os métodos `@deprecated` dos Models foram replicados nos Repositories.
- [x] Services passam a depender de RepositoryInterface, não de Model diretamente.
- [x] Nenhum `new Model()` dentro de Repositories — sempre via construtor.
- [x] `php spark serve` inicia sem erros de autoload.

---

## SPRINT 5 – Refatoração de Services (Interfaces, DI, SRP)

**Status:** 🟡 Próximo sprint a executar (checkpoint em 19/10/2026)

**Duração:** 5 dias  
**Dependência:** SPRINT 4 (Repositories prontos)  
**Escopo:** `app/Services/` (exceto `Base/`)

### Entregáveis
1. Interfaces para todos os Services em `app/Services/Interfaces/`:
   - `ICandidatoService`, `IClassificatorioService`, `IGovBrService`, `IProtocoloService`, `IEditaisService`, `IPdfService`, etc.
2. Injeção de dependências via construtor em todos os Services (sem `new` interno).
3. Separação de responsabilidades:
   - Quebrar `ClassificatorioService.php` em: `ExperienciaService`, `EscolaridadeService`, `AperfeicoamentoService`, `CriterioAdicionalService` (ou `ClassificatorioOrchestrator`).
   - `GovBrService.php`: credenciais via `.env`; usar `service('curlrequest')`; retornar DTO (`GovBrUsuarioDTO`).
   - `PdfService.php`: extrair `formatarDeficiencia()` para `DeficienciaFormatter`.
   - `CandidatoRecursoService.php`: queries para `RecursoRepository`; formatadores para classes `*Formatter`.
   - `TransparenciaService.php`: queries para `TransparenciaRepository`; formatação para `TransparenciaPresenter`.
4. Consistência de retornos: métodos como `EditaisService::estaAtivo()` retornam `bool` ou lançam exceção de domínio.
5. Transações: métodos com múltiplas escritas usam `transBegin`/`transCommit` ou `AbstractCrudService`.
6. `declare(strict_types=1);` e type hints completos.

### Critérios de Aceite
- [ ] Todos os Services possuem interface correspondente.
- [ ] Nenhum Service instancia Model ou outro Service com `new`.
- [ ] `GovBrService` não contém credenciais literais.
- [ ] `ClassificatorioService` foi dividido em Services menores.

---

## SPRINT 6 – Refatoração dos Controllers da Parte Pública

**Duração:** 4 dias  
**Dependência:** SPRINT 5 (Services com interfaces)  
**Escopo:** `app/Controllers/` (exceto `BaseController.php` e `Api/`)

### Entregáveis
1. Injeção via construtor: cada Controller recebe seus Services tipados (ex: `public function __construct(ICandidatoService $candidatoService, IHomeService $homeService)`).
2. Eliminação de acesso direto a Models em Controllers — apenas via Services.
3. Validação e sanitização:
   - POSTs validados com `$this->validate()` ou `AbstractFormService`.
   - AJAX sanitize com `strip_tags`, `trim`, whitelist de caracteres.
   - Tokens CSRF verificados em fluxos específicos.
4. Remoção de lógica de negócio dos Controllers para Services.
5. Remoção de código morto:
   - Imports não utilizados (`Dompdf`, `QRCode`, etc.)
   - Métodos vazios (`Dashboard::index`, `Login::index`, `Login::acesso`, `Telefones::buscarTelefones`)
6. Substituição de `$_SESSION` por `SessaoService` (ou helper encapsulado).
7. Respostas HTTP padronizadas: JSON via `$this->response->setJSON()`; nunca `echo json_encode(...)`.
8. `declare(strict_types=1);` e type hints em todos os métodos públicos.

### Critérios de Aceite
- [ ] Nenhum Controller instancia Model ou Service com `new`.
- [ ] Nenhum Controller acessa `$_SESSION` diretamente.
- [ ] Nenhum `echo json_encode` presente.
- [ ] Rotas públicas respondem normalmente (`php spark serve` + navegação manual).

---

## SPRINT 7 – API, Rotas e Filtros Finais

**Duração:** 2 dias  
**Dependência:** SPRINT 6 (Controllers refatorados)  
**Escopo:** `app/Controllers/Api/CepController.php`, `app/Config/Routes.php`, `app/Config/Filters.php`

### Entregáveis
1. **`CepController.php`**:
   - Receber `ICepService` via construtor.
   - Validar formato do CEP antes de chamar o service (regex + `preg_replace`).
   - Retornar `400 Bad Request` com JSON padronizado se inválido.
   - Usar `$this->response->setJSON()`.
2. **`Routes.php`**:
   - `$routes->setAutoRoute(false);` explicitamente.
   - Remover rotas duplicadas.
   - Agrupar rotas por controller com `$routes->group()`.
   - Adicionar nomes (`as`) para rotas relevantes.
   - Garantir que rotas `api/*` não passem por CSRF.
3. **`Filters.php`**:
   - `secureheaders` ativo em `after` global.
   - `csrf` não afeta callbacks externos (Gov.BR) nem APIs.

### Critérios de Aceite
- [ ] Chamada à API de CEP retorna 200 para CEP válido e 400 para inválido.
- [ ] Nenhuma rota duplicada em `Routes.php`.
- [ ] `api/cep` não exige token CSRF.

---

## SPRINT 8 – Validação Final e Checklist de Segurança

**Duração:** 2 dias  
**Dependência:** SPRINT 7 (todas as camadas refatoradas)  
**Escopo:** Validação cruzada de toda a parte pública

### Entregáveis
1. Execução do checklist de validação pós-refatoração:
   - [ ] `composer validate` e `composer audit` sem vulnerabilidades críticas.
   - [ ] `php spark env` retorna `production` no ambiente real.
   - [ ] Nenhum `.env` versionado (`git ls-files | grep -E '^\.env' → vazio`).
   - [ ] `.htaccess` bloqueia `vendor/`, `writable/`, `system/`, `app/`.
   - [ ] Todos os Models declaram `$useTimestamps`, `$useSoftDeletes`, `$validationRules`, `$allowedFields`, `$returnType`.
   - [ ] Nenhum Model possui `$primaryKey` como array.
   - [ ] Todos os Services possuem interfaces em `app/Services/Interfaces/`.
   - [ ] Todos os Controllers recebem Services via construtor (sem `new Model()`, `new Service()`).
   - [ ] Rotas de API isentas de CSRF; rotas POST de formulário passam por CSRF.
   - [ ] `forceGlobalSecureRequests = true`, `cookieSecure = true`, `CSPEnabled = true`.
   - [ ] `$DBDebug = false` em produção; `$strictOn = true`.
   - [ ] `Encryption.key` preenchida e vinda de `.env`.
   - [ ] Logs e sessões não versionados no Git.
2. Testes de regressão manual nas principais rotas:
   - Home, Editais, Cadastros, Login (Gov.BR), Transparência, Perguntas Frequentes, Telefones, API CEP.
3. Documentação das quebras de compatibilidade (se houver) para equipe de QA/infra.

### Critérios de Aceite
- [ ] Checklist 100% concluído.
- [ ] Nenhum erro 500 nas rotas principais navegadas manualmente.
- [ ] Ambiente pronto para iniciar a FASE 5 (parte administrativa `adm/`).

---

## Regras Transversais para Todos os Sprints

1. **Compatibilidade:** Preservar URLs, estrutura de banco e fluxos existentes. Nenhuma migration destrutiva sem backup.
2. **Padrão de camadas obrigatório:** `Controller → Service → RepositoryInterface → RepositoryEloquent → Model CI4`
3. **Padrão de Models:** Todo Model CI4 deve declarar explicitamente `$useSoftDeletes`, `$useTimestamps`, `$allowedFields`, `$validationRules` e `$returnType`.
4. **Strict Types:** `declare(strict_types=1);` no topo de todo arquivo PHP novo ou refatorado.
5. **Commits:** Cada sprint deve resultar em commits independentes e descritivos (não criar commits vazios).

---

## Próximo Passo Imediato

Iniciar **SPRINT 5** — refatoração de Services (criação de interfaces, injeção de dependências, separação de responsabilidades e SRP). Consulte `CHECKPOINT.md` para o estado detalhado do projeto até o dia 19/10/2026.
