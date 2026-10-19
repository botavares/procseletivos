# Auditoria Completa – Sistema ProcSeletivos

**Data:** 16/10/2026  
**Responsável:** Analista/Dev Sênior PHP 8.3+ | CodeIgniter 4  
**Escopo:** Aplicação pública (`app/`) e administrativa (`adm/`)  
**Objetivo:** Maximizar segurança, arquitetura, desempenho e escalabilidade por meio de refatoração profissional e incremental.

---

## Sumário Executivo

O sistema `procseletivos` é composto por duas aplicações CodeIgniter 4 independentes (portal público + painel administrativo) que compartilham o mesmo banco de dados. A arquitetura atual apresenta evolução parcial — com uso de DTOs, Enums e alguns Services —, porém carece de:

- **Hardening de segurança:** credenciais hardcoded, debug habilitado, CSP/HTTPS desligados, chaves de criptografia vazias.
- **Arquitetura em camadas:** ausência da camada de Repository, controllers acoplados a models, services sem contratos (interfaces).
- **Padronização de Models:** 26 dos 29 models públicos não declaram explicitamente `$useTimestamps` / `$useSoftDeletes`; 21 não possuem `$validationRules`.
- **Qualidade de código:** imports mortos, métodos vazios, lógica de negócio dispersa em controllers e models.
- **Performance/Escalabilidade:** queries complexas com múltiplos `JOINs` acumuladas em models, ausência de cache de configuração e uso de `YEAR()` em índices.

A refatoração será conduzida em **duas fases**, obedecendo dependências internas para evitar quebras: **(1) Fundação de Segurança & Infra → (2) Models → (3) Repositories → (4) Services → (5) Controllers → (6) Parte Administrativa**.

---

## 1. Achados Críticos de Segurança

| ID | Severidade | Item | Localização | Impacto |
|----|------------|------|-------------|---------|
| SEC-01 | **CRÍTICO** | Credenciais de banco hardcoded | `app/Config/Database.php`, `adm/app/Config/Database.php` | Exposição de host, usuário e senha no repositório Git. |
| SEC-02 | **CRÍTICO** | Credenciais Gov.BR hardcoded | `app/Config/Credentials.php` | App Key e App Secret de integração expostas. |
| SEC-03 | **CRÍTICO** | Dados pessoais de usuário real hardcoded | `app/Controllers/Cadastros.php` (`loginLocal`) | LGPD / vazamento de CPF, nome e e-mail. |
| SEC-04 | **ALTO** | `$DBDebug = true` em produção | `Database.php` (raiz e adm) | Exposição de queries SQL em tela em caso de erro. |
| SEC-05 | **ALTO** | `CI_ENVIRONMENT = development` ativo | `.env` (raiz e adm) | Debugbar ativa, stack traces expostos. |
| SEC-06 | **ALTO** | `forceGlobalSecureRequests = false` | `app/Config/App.php` e `adm/app/Config/App.php` | Não força HTTPS; cookies e sessões passíveis de interceptação. |
| SEC-07 | **ALTO** | `cookieSecure = false` | `App.php` (ambos) | Cookies transmitidos em canal não criptografado. |
| SEC-08 | **ALTO** | `$CSPEnabled = false` | `App.php` (ambos) | Content Security Policy desativada; maior superfície para XSS. |
| SEC-09 | **ALTO** | `Encryption.key` vazia | `app/Config/Encryption.php` e `adm/...` | Falha de criptografia de dados sensíveis se utilizada. |
| SEC-10 | **ALTO** | `$sessionRegenerateDestroy = false` | `Session.php` (ambos) | Dados antigos de sessão persistem no servidor. |
| SEC-11 | **MÉDIO** | `csrf.regenerate = false` no ADM | `adm/app/Config/Security.php` | Tokens CSRF reutilizáveis no painel administrativo. |
| SEC-12 | **MÉDIO** | `tokenRandomize = false` | `Security.php` (ambos) | Tokens CSRF previsíveis. |
| SEC-13 | **MÉDIO** | `.htaccess` não bloqueia `vendor/`, `writable/`, `index.php` raiz | Raiz do projeto | Acesso potencial a arquivos do framework e uploads. |
| SEC-14 | **MÉDIO** | `.gitignore` incompleto | Raiz e `adm/` | Versionamento de `writable/`, `vendor/`, `_notes`, `composer.lock`, etc. |
| SEC-15 | **BAIXO** | `index.php` na raiz do projeto | `C:\...\procseletivos\index.php` | Se o VirtualHost apontar para a raiz, expõe estrutura interna. |

---

## 2. Achados de Arquitetura

| ID | Item | Localização | Impacto |
|----|------|-------------|---------|
| ARC-01 | Ausência de camada **Repository** | `app/Models/` | Models acumulam queries complexas (joins, subqueries), violando SRP. |
| ARC-02 | Controllers acessam Models diretamente | `Home.php`, `Editais.php`, `Telefones.php`, `PerguntasFrequentes.php` | Alto acoplamento; dificulta testes e manutenção. |
| ARC-03 | Services não possuem interfaces (exceto `ISessaoService`) | `app/Services/*.php` | Impossibilita mocking em testes unitários e inversão de dependências. |
| ARC-04 | Instanciação manual com `new` | Controllers e Services | Quebra o princípio de Inversão de Dependência (DIP). |
| ARC-05 | PK composta em `$primaryKey` (array) | `EditaisCargosModel.php` | CI4 não suporta nativamente; quebra `find()` e `save()`. |
| ARC-06 | FK utilizada como `$primaryKey` | `EditaisCandidatosModel.php` | Falha em operações de CRUD herdadas do Model. |
| ARC-07 | Lógica de negócio em Models | `UsuarioModel::checkPassword()`, `DadosContratosModel::getContratosExpirando()` | Models devem ser anêmicos (persistência); regras devem estar em Services. |
| ARC-08 | DTOs e Enums existem, mas sem mapeador formal | `app/DTOs/`, `app/Enums/` | Falta um `Mapper` ou `Presenter` para conversão DTO ↔ Entity. |
| ARC-09 | `loginLocal()` com bypass de autenticação | `Cadastros.php` | Porta dos fundos em ambiente de produção. |
| ARC-10 | Queries dinâmicas sem whitelist de colunas | `ProtocolosModel::protocolosdoCandidato()`, `TransparenciaService` | Risco de alteração de comportamento de query por input do usuário. |

---

## 3. Achados de Performance & Escalabilidade

| ID | Item | Localização | Impacto |
|----|------|-------------|---------|
| PERF-01 | Uso de `YEAR(coluna)` em `WHERE` | `CandidatosModel::contarCadastrados()` | Quebra índices; substituir por intervalo de datas. |
| PERF-02 | Queries massivas com ~10 joins | `DadosContratosModel`, `ConvocadosModel` | Latência crescente com volume de dados; idealmente mover para Views SQL ou Repositories otimizados. |
| PERF-03 | Recálculo de scores em tempo real | `CandidatoScoreBuilderService` | Sem cache/memoização; candidatos reprocessados a cada request. |
| PERF-04 | `usort` instável em PHP | `OrdenacaoDinamicaService` | Candidatos com scores idênticos podem trocar de ordem. |
| PERF-05 | Ausência de cache de configuração / queries | Geral | CI4 pode utilizar caching de páginas, queries e configurações. |
| PERF-06 | Conexão sem `strictOn = true` | `Database.php` | SQL silenciosamente truncado; perda de integridade de dados. |

---

## 4. Plano de Refatoração por Tópicos

> **Regra geral:** Cada tópico deve preservar compatibilidade de rotas (URLs) e dados existentes. Nenhuma migration destrutiva será aplicada sem backup.
> **Padrão de camadas obrigatório:** `Controller → Service → RepositoryInterface → RepositoryEloquent → Model CI4`
> **Padrão de Models:** Todo Model CI4 deve declarar explicitamente `protected $useSoftDeletes`, `protected $useTimestamps`, `protected $allowedFields`, `protected $validationRules` e `protected $returnType`.

---

### FASE 1 – FUNDAÇÃO DE SEGURANÇA E INFRAESTRUTURA

#### TÓPICO 1.1 – Remoção de credenciais hardcoded e hardening do `.env`, `.gitignore` e `.htaccess`
**Dependência:** Nenhuma. Deve ser o primeiro passo.  
**Escopo:** Raiz do projeto e pasta `adm/`.

**Prompt de refatoração:**
> “Atue como especialista em segurança de aplicações PHP. Execute as seguintes tarefas de forma imperativa e verificável:
> 1. Remova todas as credenciais hardcoded dos arquivos `app/Config/Database.php` e `adm/app/Config/Database.php`. Substitua por leitura de variáveis de ambiente (`env()`). Gere um arquivo `.env.example` completo com todas as variáveis necessárias (database, encryption, govbr, app baseURL).
> 2. No arquivo `app/Config/Credentials.php`, remova os valores literais de `govbrAppKey` e `govbrAppSecret`, apontando-os para `env('GOVBR_APP_KEY')` e `env('GOVBR_APP_SECRET')`.
> 3. Remova o método `loginLocal()` do controller `Cadastros.php` (ou envolva-o em um bloco que só execute se `CI_ENVIRONMENT === 'development'` E uma flag `LOGIN_MODE === 'local'`). Em produção, este método NÃO deve existir.
> 4. Atualize o `.gitignore` da raiz para incluir: `.env*`, `vendor/`, `writable/cache/*`, `writable/debugbar/*`, `writable/logs/*`, `writable/session/*`, `writable/uploads/*`, `node_modules/`, `_notes/`, `*.log`, `.idea/`, `.vscode/`, `composer.lock` (se desejável), `package-lock.json`, `phpunit.xml.dist` (se local), `preload.php` (se contiver paths locais). Faça o mesmo para `adm/.gitignore`.
> 5. Garanta que o `.env` já existente no repositório seja removido do rastreamento Git (`git rm --cached .env`) e que o `.env.example` seja versionado como template.
> 6. No `.htaccess` da raiz, adicione proteções adicionais: bloqueio de acesso direto a `vendor/`, `writable/`, `system/`, `app/`, `adm/`, `node_modules/`; forçar redirecionamento de HTTP para HTTPS (status 301); manter as proteções já existentes para `.env`, arquivos de configuração e `ServerSignature Off`.
> 7. Considere remover ou renomear o `index.php` da raiz (fora de `public/`) para evitar que se torne ponto de entrada se o VirtualHost estiver mal configurado. Se não for possível removê-lo, adicione nele uma verificação `if (realpath(__FILE__) === realpath($_SERVER['DOCUMENT_ROOT'] . '/index.php')) { header('HTTP/1.1 403 Forbidden'); exit; }`.
> 8. Verifique se `composer.json` exige PHP `^8.3` (atualmente está `^7.4 || ^8.0`). Atualize o `require` do PHP e remova pacotes obsoletos ou conflitantes. Rode `composer update --lock` para validar.

---

#### TÓPICO 1.2 – Hardening das Configurações do CodeIgniter 4 (App, Security, Session, Encryption, CSP)
**Dependência:** TÓPICO 1.1 concluído (pois `.env` deve estar pronto para receber valores).  
**Escopo:** `app/Config/` e `adm/app/Config/`.

**Prompt de refatoração:**
> “Atue como arquiteto de segurança CI4. Aplique as configurações de hardening a seguir em ambos os ambientes (raiz e `adm/`), usando variáveis de ambiente sempre que possível:
> 1. Em `App.php`: defina `$forceGlobalSecureRequests = true` (ou `env('CI_FORCE_HTTPS', true)`); ative `$CSPEnabled = true`; configure `$cookieSecure = true` e `$cookieSameSite = 'Strict'` (ou `'Lax'` se houver integrações externas que exijam cookies em navegação cruzada); confirme `$cookieHTTPOnly = true`.
> 2. Em `Security.php`: altere `$tokenRandomize = true`; mantenha `$regenerate = true` na raiz e corrija para `true` no `adm/`; defina `$tokenName = 'csrf_procsel_token'` (evite nomes default óbvios).
> 3. Em `Session.php`: altere `$regenerateDestroy = true`; considere `$matchIP = true` se a infraestrutura não usar load balancer com IP flutuante (ou use whitelist de proxies confiáveis); defina `$timeToUpdate = 180`.
> 4. Em `Encryption.php`: defina `$key` a partir de `env('ENCRYPTION_KEY')`. Se vazio, gere uma chave via `php spark key:generate` e instrua o administrador a copiá-la para o `.env`. Defina `$driver = 'OpenSSL'` e `$digest = 'SHA512'` explicitamente.
> 5. Em `Database.php`: defina `$DBDebug = env('CI_DB_DEBUG', false)` (em produção deve ser `false`); ative `$strictOn = true`; configure `$encrypt = true` se o servidor de banco suportar TLS.
> 6. Em `ContentSecurityPolicy.php`: com `$CSPEnabled = true`, ajuste as diretivas para permitir `'self'` em `defaultSrc`, `scriptSrc`, `styleSrc`, `imgSrc`; adicione `upgradeInsecureRequests = true`; remova `'unsafe-inline'` e `'unsafe-eval'` de `scriptSrc`; se for necessário inline, use nonces (`autoNonce = true`).
> 7. Em `Filters.php`: adicione `'secureheaders'` na seção `after` global; mantenha `csrf` e `honeypot` em `before`; verifique se existem rotas de API (como `api/cep`) que possam precisar de exceção de CSRF via `$filters` com padrão de URI.
> 8. Documente cada alteração em comentários curtos no topo dos arquivos de configuração, indicando o motivo de segurança.”

---

### FASE 2 – ARQUITETURA DE DADOS (Models e Repositories)

#### TÓPICO 2.1 – Padronização e Correção dos Models da Parte Pública
**Dependência:** TÓPICO 1.2.  
**Escopo:** Todos os arquivos em `app/Models/`.

**Prompt de refatoração:**
> “Atue como desenvolvedor sênior CI4 especialista em modelos de domínio. Refatore cada arquivo em `app/Models/` obedecendo estritamente às regras abaixo:
> 1. **Declarações obrigatórias:** Todo model deve declarar explicitamente:
>    - `protected $table`
>    - `protected $primaryKey`
>    - `protected $allowedFields`
>    - `protected $useTimestamps = false;` (ou `true` se houver campos de timestamp na tabela)
>    - `protected $useSoftDeletes = false;` (ou `true` se houver coluna `deleted_at`)
>    - `protected $validationRules = []`
>    - `protected $returnType = 'object'` (ou `'array'` se for view read-only, documentado)
>    - `protected $useAutoIncrement = true` (ou `false` se for tabela de relacionamento)
> 2. **Correção de PKs problemáticas:**
>    - Em `EditaisCargosModel.php`: remova o array de `$primaryKey`. Adicione uma coluna surrogate `pk_id_edital_cargo` na tabela (via migration) ou remova a herança de `Model` e use `BaseBuilder` direto para esta tabela de junção. Se optar pela migration, crie-a em `app/Database/Migrations/`.
>    - Em `EditaisCandidatosModel.php`: o `$primaryKey` atual é uma FK. Corrija para uma coluna autoincrement `pk_id_vinculo` (via migration) ou use `BaseBuilder` sem herança de `Model`.
> 3. **Segurança de Mass Assignment:** Remova a PK (`pk_id_contrato`) de `$allowedFields` em `VerificadorModel.php`.
> 4. **Validação:** Crie `$validationRules` mínimas para todos os campos em `$allowedFields`, usando `required|integer`, `permit_empty|string`, etc., conforme o tipo de dado. Não deixe `$validationRules` ausente.
> 5. **Lógica de Negócio:** Remova de Models qualquer lógica de negócio (ex: `UsuarioModel::checkPassword()`, `DadosContratosModel::getContratosExpirando()` com cálculo de data). Movimente essas responsabilidades para Services ou Domain classes.
> 6. **Queries complexas:** Métodos com múltiplos joins, subqueries ou lógica de filtros dinâmicos (ex: `ConvocadosModel::getCovocadosPorEditalCurso()`, `DadosContratosModel::getContratosPorId()`, `EditaisModel::candidatosPorEdital()`, etc.) devem ser **deprecados** nos Models e migrados para Repositories no próximo tópico. Deixe um comentário `@deprecated`.
> 7. **Views:** `ClassificacaoViewModel.php` pode continuar como Model read-only, mas declare `$allowedFields = []` explicitamente e documente que é uma view.
> 8. Adicione `declare(strict_types=1);` no topo de cada arquivo e tipagem de retorno em todos os métodos públicos.”

---

#### TÓPICO 2.2 – Implementação da Camada de Repository na Parte Pública
**Dependência:** TÓPICO 2.1 (models limpos e estáveis).  
**Escopo:** Criar `app/Repositories/` e `app/Repositories/Interfaces/`.

**Prompt de refatoração:**
> “Atue como arquiteto de software PHP. Implemente a camada de Repository para a parte pública do sistema, obedecendo à hierarquia:
> `Controller → Service → RepositoryInterface → RepositoryEloquent → Model CI4`
>
> Regras:
> 1. Para cada domínio que possui queries complexas, crie uma **Interface** em `app/Repositories/Interfaces/` (ex: `ICandidatoRepository`, `IEditalRepository`, `IContratoRepository`, `IConvocadoRepository`, `IClassificacaoRepository`).
> 2. Crie a **implementação concreta** em `app/Repositories/` (ex: `CandidatoRepository`, `EditalRepository`). Cada Repository recebe o respectivo Model via construtor (injeção de dependência).
> 3. O Repository pode usar o Query Builder (`$this->model->db->table(...)`) ou o próprio Model quando a operação for simples. Para queries complexas (joins, subqueries), prefira `$this->db->table(...)->join(...)->where(...)->get()` encapsulado no Repository.
> 4. Métodos de Repository devem retornar `array` de objetos ou DTOs, nunca instâncias de CI4 `ResultInterface` expostas diretamente para Services.
> 5. Atualize os Services que hoje instanciam Models manualmente para, em vez disso, receberem o Repository via construtor (ex: `public function __construct(ICandidatoRepository $candidatoRepo)`).
> 6. Registre os bindings no `app/Config/Services.php` (ou use o sistema de autowiring do CI4 se `Config\Autoload` estiver com `$psr4` configurado para `App\Repositories\`).
> 7. Garanta que os métodos movidos dos Models para Repositories mantenham a mesma assinatura de parâmetros e comportamento, para não quebrar controllers que ainda os utilizam (até a refatoração dos controllers).”

---

### FASE 3 – CAMADA DE APLICAÇÃO (Services e DTOs)

#### TÓPICO 3.1 – Refatoração de Services da Parte Pública (Interfaces, DI e SRP)
**Dependência:** TÓPICOS 2.1 e 2.2.  
**Escopo:** `app/Services/` (exceto `Base/`).

**Prompt de refatoração:**
> “Atue como desenvolvedor sênior especialista em Domain-Driven Design com CI4. Refatore todos os Services da parte pública (`app/Services/`) para:
> 1. **Criar Interfaces:** Todo Service de domínio deve ter uma interface em `app/Services/Interfaces/` (ex: `ICandidatoService`, `IClassificatorioService`, `IGovBrService`, `IProtocoloService`). A classe concreta implementa a interface.
> 2. **Injeção de Dependências:** Nenhum Service deve instanciar Models ou outros Services com `new`. Utilize construtores tipados (ex: `public function __construct(ICandidatoRepository $repo, IProtocoloService $protocoloService)`). Os bindings de interface→implementação devem ser registrados em `Config\Services` ou via autowiring.
> 3. **Separação de Responsabilidades:**
>    - `ClassificatorioService.php` está muito acoplado (gerencia 4 domínios). Quebre-o em: `ExperienciaService`, `EscolaridadeService`, `AperfeicoamentoService`, `CriterioAdicionalService`. Mantenha um `ClassificatorioFacadeService` ou `ClassificatorioOrchestrator` se necessário para coordenar.
>    - `GovBrService.php`: mova credenciais para `.env`; substitua `curl_*` puro pelo client nativo `service('curlrequest')` do CI4; retorne um DTO (`GovBrUsuarioDTO`) em vez de `stdClass`.
>    - `PdfService.php`: extraia `formatarDeficiencia()` para um `DeficienciaFormatter` de domínio ou um enum.
>    - `CandidatoRecursoService.php`: separe a lógica de queries para `RecursoRepository`; extraia formatadores (`formatarValor`, `statusLabel`) para classes `*Formatter`.
>    - `TransparenciaService.php`: mova queries para `TransparenciaRepository`; mova formatação/labels para `TransparenciaPresenter`.
> 4. **Consistência de Retornos:** Métodos como `EditaisService::estaAtivo()` devem retornar `bool` ou lançar exceção de domínio (`EditalForaDeVigenciaException`), nunca `true|array`.
> 5. **Transações:** Todo método que executa múltiplas operações de escrita deve usar transação (`transBegin`/`transCommit`) ou estender `AbstractCrudService` (que já encapsula isso).
> 6. **Strict Types:** Adicione `declare(strict_types=1);` e type hints em todos os parâmetros e retornos.”

---

### FASE 4 – CAMADA DE APRESENTAÇÃO (Controllers da Parte Pública)

#### TÓPICO 4.1 – Refatoração dos Controllers da Parte Pública
**Dependência:** TÓPICOS 2.2 e 3.1 (Repositories e Services prontos e com interfaces).  
**Escopo:** `app/Controllers/` (exceto `BaseController.php` e `Api/`).

**Prompt de refatoração:**
> “Atue como desenvolvedor sênior CI4. Refatore todos os Controllers da parte pública para eliminar acoplamento direto a Models e centralizar a lógica de apresentação:
> 1. **Injeção via Construtor:** Todo Controller deve receber seus Services via construtor tipado (ex: `public function __construct(ICandidatoService $candidatoService, IHomeService $homeService)`). Nenhum `new Model()`, `new Service()` ou `new Repository()` dentro dos métodos de ação.
> 2. **Eliminação de Models em Controllers:** Remova todo acesso direto a Models dos controllers. Substitua por chamadas a Services (ex: `Home.php` não deve mais usar `CandidatosModel`, `EditaisModel`, etc.).
> 3. **Validação e Sanitização:**
>    - Em métodos POST, valide explicitamente inputs com `$this->validate()` ou com `AbstractFormService`.
>    - Em endpoints AJAX (ex: `PerguntasFrequentes::buscarRespostas`, `Editais::buscarServicosServidores`), verifique `$this->request->isAJAX()`; sanitize inputs com `strip_tags`, `trim` e regras de comprimento; use whitelist de caracteres quando possível.
>    - Valide tokens CSRF em formulários POST (o filtro global já ajuda, mas confirme com `$this->security->verifyCSRF()` se necessário para fluxos específicos).
> 4. **Remoção de Lógica de Negócio:** Mova para Services qualquer regra de domínio presente em controllers (ex: formatação de número de edital em `Home.php`, montagem de paths de PDF em `Editais.php`).
> 5. **Remoção de Código Morto:** Remova imports não utilizados (`Dompdf`, `QRCode`, etc.), métodos vazios (`Dashboard::index`, `Login::index`, `Login::acesso`, `Telefones::buscarTelefones`, `SetoresModel::setoresPorEdital`), e código comentado obsoleto.
> 6. **Segurança de Sessão:** Substitua acesso direto a `$_SESSION` por `SessaoService` (ou `session()` helper encapsulado). Valide existência de chaves antes de acessar.
> 7. **Respostas HTTP Padronizadas:** Em endpoints JSON, use `$this->response->setJSON($data)->setStatusCode(200)` (ou 400/404/500 conforme o caso). Nunca use `echo json_encode(...)` diretamente.
> 8. Adicione `declare(strict_types=1);` e type hints em todos os métodos públicos.”

---

#### TÓPICO 4.2 – Refatoração do Controller de API (`Api\CepController`)
**Dependência:** TÓPICO 3.1 (Service de CEP com interface).  
**Escopo:** `app/Controllers/Api/CepController.php`.

**Prompt de refatoração:**
> “Refatore `CepController.php` para:
> 1. Receber `ICepService` via construtor.
> 2. Validar o formato do CEP no controller antes de chamar o service (regex `/^[0-9]{8}$/` após `preg_replace('/\D/', '', $cep)`). Se inválido, retorne `400 Bad Request` com JSON de erro.
> 3. Retornar resposta JSON padronizada via `$this->response->setJSON()`.
> 4. Garantir que rotas de API esteam isentas de CSRF via `Filters.php` (adicionar exceção para `api/*`).”

---

#### TÓPICO 4.3 – Ajuste de Rotas e Filtros da Parte Pública
**Dependência:** TÓPICO 4.1.  
**Escopo:** `app/Config/Routes.php` e `app/Config/Filters.php`.

**Prompt de refatoração:**
> “Finalize a parte pública com:
> 1. Em `Routes.php`: descomente e defina `$routes->setAutoRoute(false);` explicitamente; remova rotas duplicadas (ex: `/transparencia` está declarada duas vezes); agrupe rotas por controller usando `$routes->group()`; adicione nomes (`as`) para todas as rotas relevantes; certifique-se de que rotas de API não passem por CSRF.
> 2. Em `Filters.php`: revise se `secureheaders` está ativo em `after`; verifique se o filtro `csrf` não afeta rotas de callback externas (Gov.BR) ou APIs.”

---

### FASE 5 – PARTE ADMINISTRATIVA (`adm/`)

> **Premissa:** Só inicie esta fase quando a parte pública estiver 100% refatorada, testada e estável. A parte administrativa deve replicar os mesmos padrões, mas pode ser feita de forma independente pois é uma aplicação CI4 separada.

#### TÓPICO 5.1 – Replicação de Fundação de Segurança no `adm/`
**Dependência:** TODOS os tópicos da FASE 1 aplicados na raiz.  
**Escopo:** `adm/`.

**Prompt de refatoração:**
> “Aplique no `adm/` exatamente o mesmo hardening realizado na raiz:
> 1. Remova credenciais hardcoded de `adm/app/Config/Database.php`, `adm/app/Config/Credentials.php` (se existir) e quaisquer controllers com bypass de login.
> 2. Crie `adm/.env.example` e `adm/.gitignore` robustos.
> 3. Ajuste `App.php`, `Security.php`, `Session.php`, `Encryption.php`, `Database.php` e `ContentSecurityPolicy.php` com os mesmos valores de hardening (force HTTPS, CSRF regenerate true, cookie secure, strictOn, etc.).
> 4. Em `adm/app/Config/Security.php`, corrija `$regenerate = true` (estava `false`).
> 5. Garanta que o VirtualHost do painel administrativo aponte estritamente para `adm/public/` e que não exista `index.php` na raiz de `adm/` acessível pelo web server.”

---

#### TÓPICO 5.2 – Padronização de Models e Criação de Repositories no `adm/`
**Dependência:** TÓPICO 5.1.  
**Escopo:** `adm/app/Models/` e criação de `adm/app/Repositories/`.

**Prompt de refatoração:**
> “Replicando o padrão da parte pública, padronize todos os Models em `adm/app/Models/`:
> 1. Declare explicitamente `$table`, `$primaryKey`, `$allowedFields`, `$useTimestamps`, `$useSoftDeletes`, `$validationRules`, `$returnType`.
> 2. Corrija qualquer PK composta (array) ou FK usada como PK (como ocorreu em `EditaisCargosModel` e `EditaisCandidatosModel` na parte pública; verifique se existe equivalente no `adm/`).
> 3. Remova lógica de negócio e queries massivas dos Models para Repositories.
> 4. Crie `adm/app/Repositories/Interfaces/` e `adm/app/Repositories/` com as interfaces e implementações necessárias para os domínios administrativos (Candidatos, Editais, Contratos, Cargos, Classificações, Convocações, etc.).”

---

#### TÓPICO 5.3 – Refatoração de Services e Controllers Administrativos
**Dependência:** TÓPICO 5.2.  
**Escopo:** `adm/app/Services/` e `adm/app/Controllers/`.

**Prompt de refatoração:**
> “Refatore o `adm/app/Services/` e `adm/app/Controllers/` para:
> 1. Criar interfaces para todos os Services (`ICandidatoService`, `IContratoService`, `IRelatorioService`, etc.).
> 2. Injetar dependências (Models via Repository, Services via interface) em todos os construtores.
> 3. Implementar um filtro de autenticação global (ex: `auth`) em `adm/app/Config/Filters.php` e aplicá-lo às rotas protegidas, evitando verificação controller a controller.
> 4. Mover lógica de relatórios, grids e uploads para Services especializados, mantendo controllers enxutos.
> 5. Remover código morto, imports não utilizados e métodos vazios.
> 6. Adicionar `declare(strict_types=1);` e type hints completos.”

---

#### TÓPICO 5.4 – Unificação e Reuso entre Público e Administrativo
**Dependência:** TÓPICO 5.3.  
**Escopo:** Cross-cutting concerns.

**Prompt de refatoração:**
> “Avalie oportunidades de reuso entre a aplicação pública e a administrativa:
> 1. Se houver código duplicado (ex: DTOs, Enums, Helpers, Libraries de PDF/QRCode), considere extrair para um namespace compartilhado (`app/Shared/` ou package Composer local) para evitar divergência.
> 2. Unifique configurações de segurança comuns em um trait ou classe base se possível.
> 3. Garanta que migrations do banco sejam centralizadas (provavelmente na raiz) e não duplicadas entre raiz e `adm/`.”

---

## 5. Checklist de Validação Pós-Refatoração

- [ ] `composer validate` e `composer audit` sem vulnerabilidades críticas.
- [ ] `php spark env` retorna `production` no ambiente real.
- [ ] Nenhum arquivo `.env` versionado no Git (`git ls-files | grep -E '^\.env' → vazio`).
- [ ] `.htaccess` bloqueia acesso direto a `vendor/`, `writable/`, `system/`, `app/`.
- [ ] Todos os Models declaram `$useTimestamps`, `$useSoftDeletes`, `$validationRules`, `$allowedFields`, `$returnType`.
- [ ] Nenhum Model possui `$primaryKey` como array.
- [ ] Todos os Services possuem interfaces em `app/Services/Interfaces/`.
> - [ ] Todos os Controllers recebem Services via construtor (sem `new Model()`, `new Service()`).
- [ ] Rotas de API estão isentas de CSRF; rotas de formulário POST passam por CSRF.
- [ ] `forceGlobalSecureRequests = true`, `cookieSecure = true`, `CSPEnabled = true`.
- [ ] `$DBDebug = false` em produção; `$strictOn = true`.
- [ ] `Encryption.key` preenchida e vinda de `.env`.
- [ ] Testes unitários cobrem pelo menos os Services e Repositories (mínimo de 70% de cobertura nos novos arquivos).
- [ ] Logs e sessões não são mais versionados no Git.

---

## 6. Considerações Finais

A refatoração proposta preserva URLs, estrutura de banco e fluxos existentes, concentrando-se em **hardening**, **separação de responsabilidades** e **padronização**. A sequência de tópicos foi desenhada para que cada camada seja estabilizada antes de servir de fundação para a próxima, minimizando regressões.

**Próximo passo imediato:** Executar o **TÓPICO 1.1** (remoção de credenciais hardcoded e hardening de `.env`/`.htaccess`/`.gitignore`).
