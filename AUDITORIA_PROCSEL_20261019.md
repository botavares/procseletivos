# AUDITORIA DE ARQUITETURA - PROSEL

**Projeto:** Processo Seletivo (procseletivos)  
**Data:** 2026-10-19  
**Auditor:** Arquiteto de Software (IA Auditora)  
**Escopo:** Aplicacao Publica (raiz) + Painel Administrativo (`adm/`)  
**Framework:** CodeIgniter 4 (PHP 8.3)  
**Metodologia:** Analise estatica de codebase com foco em Clean Code, SOLID, Inversao de Dependencias e Separacao de Responsabilidades.

---

## SUMARIO EXECUTIVO

O repositorio abriga **duas aplicacoes CodeIgniter 4 distintas** (Publica e Admin) que compartilham logica de negocio identica, mas duplicam Models, Services e Helpers. A aplicacao Publica evoluiu parcialmente para padroes mais modernos (Repositories com interfaces, DTOs, Enums), enquanto o Painel Admin permanece fortemente acoplado ao framework, com controllers gordos, instanciacao direta de Models (`new Model()`), ausencia de abstracoes e vulnerabilidades de seguranca ativas.

A auditoria identificou **5 frentes evolutivas**, ordenadas para que cada etapa estabilize o sistema antes que a proxima seja iniciada, evitando regressoes e facilitando code review.

---

## ESTRATEGIA DE EVOLUCAO POR ETAPAS

| Etapa | Nome | Objetivo | Risco de Regressao |
|---|---|---|---|
| **0** | **Seguranca e Estabilidade** | Eliminar vulnerabilidades e backdoors sem alterar comportamento funcional | Nenhum (hotfixes) |
| **1** | **Fundacao Arquitetural** | Unificar Models duplicados, criar namespace compartilhado, configurar DI real no Admin | Baixo |
| **2** | **Desacoplamento de Infraestrutura** | Implementar Repository Pattern no Admin, extrair geracao de Excel/PDF, criar interfaces | Medio |
| **3** | **Clean Code e Dominio** | Refatorar controllers gordos, substituir magic numbers por Enums, corrigir retornos mistos | Medio-Alto |
| **4** | **Consolidacao e Testes** | Unificar apps (se viavel), cobrir Services/Repositories com testes unitarios, documentar | Baixo-Medio |

> **Regra de Ouro:** Nenhuma etapa pode depender da conclusao da posterior. Cada entrega deve manter o sistema 100% operacional.

---

## ETAPA 0: SEGURANCA E ESTABILIDADE CRITICA

### Objetivo
Remover do codigo-fonte todo dado pessoal hardcoded, backdoors de autenticacao e vulnerabilidades de SQL Injection. Esta etapa e puramente defensiva e nao altera arquitetura.

### Problema 0.1 - Backdoor de Login Local com Dados Pessoais Hardcoded
**Arquivo:** `app/Controllers/Publica/Cadastros.php` (aprox. linhas 314-320)  
**Severidade:** CRITICA  
**Descricao:** Metodo `loginLocal()` contem CPF, e-mail e nome reais de um desenvolvedor (`breno.o.tavares@gmail.com`, `03455783686`) e permite bypass total do Gov.BR quando `ENVIRONMENT === 'development'` e `env('LOGIN_MODE') === 'local'`. Se o ambiente for acidentalmente configurado como `development` em producao, a autenticacao e anulada.

### Prompt 0.1 - Remover Backdoor Local
```
CONTEXTO:
Voce esta trabalhando no projeto CodeIgniter 4 "procseletivos". O arquivo 
`app/Controllers/Publica/Cadastros.php` contem um metodo `loginLocal()` que 
simula login do Gov.BR em ambiente local usando dados pessoais hardcoded 
(CPF, e-mail, nome reais de um individuo).

ACAO REQUERIDA:
1. Abra o arquivo `app/Controllers/Publica/Cadastros.php`.
2. Localize o metodo `loginLocal()` e TODO bloco condicional que o invoca 
   (ex: `if (ENVIRONMENT !== 'development' || env('LOGIN_MODE') !== 'local')`).
3. Remova completamente o metodo `loginLocal()` e sua invocacao.
4. Se o ambiente local precisar de um mecanismo de login para testes, 
   substitua por uma leitura de variavel de ambiente que contenha apenas 
   um flag booleano generico (ex: `env('AUTH_BYPASS_ENABLED')`), e se ativado,
   carregue dados de um arquivo de fixture externo (`tests/fixtures/govbr_mock.json`),
   NUNCA de dados pessoais no codigo fonte.
5. Certifique-se de que o fluxo normal de autenticacao Gov.BR permaneca intacto.
6. Verifique se ha referencias ao metodo `loginLocal()` em outro lugar do projeto
   (use grep). Remova-as tambem.

CRITERIOS DE ACEITACAO:
- O metodo `loginLocal()` nao existe mais no arquivo.
- Nao ha CPF, e-mail ou nome de pessoas reais no codigo-fonte da aplicacao.
- Se `AUTH_BYPASS_ENABLED=true`, o sistema usa dados anonimizados de um fixture.
- O login via Gov.BR continua funcionando normalmente quando o bypass esta desligado.
- Nenhum teste existente quebra (rode `phpunit` na raiz e `adm/phpunit`).

COMO VERIFICAR:
- Rode `grep -r "loginLocal" app/` e `grep -r "loginLocal" adm/app/` 
  e confirme que nao ha ocorrencias.
- Rode `grep -rE "[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}" app/Controllers/` 
  e confirme ausencia de CPFs hardcoded.
```

### Problema 0.2 - SQL Injection por Interpolacao de Variavel
**Arquivo:** `adm/app/Models/CandidatosModel.php` (aprox. linha 242)  
**Severidade:** CRITICA  
**Descricao:** O metodo `contarCandidatos()` ou similar executa `$this->db->query("SELECT COUNT(*) AS total FROM tb_cadastrados WHERE YEAR(ds_datacadastro) = $ano");`. Como `$ano` e interpolado diretamente em string SQL, e vulneravel a SQL Injection.

### Prompt 0.2 - Corrigir SQL Injection no CandidatosModel
```
CONTEXTO:
O arquivo `adm/app/Models/CandidatosModel.php` possui uma vulnerabilidade de 
SQL Injection onde uma variavel `$ano` e interpolada diretamente em uma string SQL.

ACAO REQUERIDA:
1. Abra `adm/app/Models/CandidatosModel.php`.
2. Localize TODAS as ocorrencias de `$this->db->query()` que concatenam ou 
   interpolam variaveis PHP diretamente na string SQL.
3. Substitua por prepared statements do CodeIgniter 4 usando binding de parametros:
   Exemplo: `$this->db->query("SELECT ... WHERE YEAR(ds_datacadastro) = ?", [$ano]);`
   OU, se possivel, use o Query Builder com metodos como `->where("YEAR(ds_datacadastro)", $ano)`.
4. Revise TODO o arquivo `adm/app/Models/` e tambem `app/Models/` a procura 
   de interpolacoes similares. Corrija todas.
5. Se houver joins dinamicos com concatenacao de strings em 
   `app/Repositories/TransparenciaRepository.php` (metodo que usa `"{$coluna['chave']}"`),
   assegure que a chave seja validada contra uma whitelist de colunas permitidas 
   antes de ser injetada na string SQL.

CRITERIOS DE ACEITACAO:
- Nao ha mais interpolacao de variaveis em strings SQL nos Models.
- Todos os parametros dinamicos usam prepared statements ou Query Builder seguro.
- No TransparenciaRepository, chaves dinamicas sao validadas contra whitelist.
- Rode `phpunit` e confirme que testes existentes ainda passam.

COMO VERIFICAR:
- Execute uma busca por regex `->query\s*\(\s*["'].*\$` nos diretorios 
  `app/Models/` e `adm/app/Models/` para garantir ausencia de interpolacao.
- Verifique manualmente o metodo `contarCandidatos()`.
```

---

## ETAPA 1: FUNDACAO ARQUITETURAL E INJECAO DE DEPENDENCIA

### Objetivo
Criar uma base solida para que o Admin pare de usar `new Model()` e passe a usar injecao de dependencia real (construtor). Extrair Models duplicados para um namespace compartilhado, eliminando divergencia de codigo.

### Problema 1.1 - Duplicacao de Models entre Raiz e Admin
**Arquivos:** `app/Models/EditaisModel.php`, `app/Models/CandidatosModel.php`, etc. vs `adm/app/Models/EditaisModel.php`, `adm/app/Models/CandidatosModel.php`  
**Severidade:** ALTA  
**Descricao:** Os mesmos conceitos de dominio (Editais, Candidatos, Cargos) existem em dois arquivos com codigo diferente. Isso impede manutencao centralizada e propaga bugs.

### Prompt 1.1 - Criar Namespace Compartilhado de Models
```
CONTEXTO:
O repositorio possui duas aplicacoes CodeIgniter 4: a raiz (site publico) e 
`adm/` (painel administrativo). Ambas possuem Models identicos com nomes iguais 
mas codigo divergente (ex: `app/Models/EditaisModel.php` e 
`adm/app/Models/EditaisModel.php`). Isso viola o DRY e dificulta a manutencao.

ACAO REQUERIDA:
1. Analise os Models duplicados em ambas as aplicacoes. Identifique quais sao 
   de fato identicos em estrutura de tabela e quais possuem logica diferente.
2. Crie um novo namespace compartilhado: `app/Common/Models/` na raiz.
3. Para cada Model que e estritamente o mesmo conceito de dominio:
   - Mova a versao mais completa/correta para `app/Common/Models/`.
   - Ajuste o namespace para `App\Common\Models`.
   - Atualize `app/Config/Autoload.php` (ou composer.json PSR-4) para mapear 
     `App\Common\Models\` para `app/Common/Models/`.
   - No `adm/composer.json`, adicione um autoload PSR-4 apontando para o diretorio 
     compartilhado da raiz (ex: `"App\\Common\\": "../app/Common/"`), ou, preferencialmente,
     crie um symlink ou copie via build step. A solucao mais robusta e usar um 
     pacote Composer local (path repository) ou simplesmente ajustar o autoload 
     do admin para incluir `../app/Common/`.
   - Substitua as classes duplicadas em `adm/app/Models/` por `extends` da 
     versao compartilhada, caso haja pequenas diferencas especificas do admin.
   - Se houver diferencas significativas, crie um Model base compartilhado e 
     estenda nas duas aplicacoes, movendo apenas o codigo especifico para 
     `app/Models/` e `adm/app/Models/`.
4. Garanta que TODOS os arquivos que usam esses Models (Controllers, Services, 
   Repositories) em ambas as aplicacoes sejam atualizados com os novos `use` statements.

CRITERIOS DE ACEITACAO:
- Nao ha codigo identico ou quase identico duplicado entre `app/Models/` e 
  `adm/app/Models/`.
- O autoload funciona em ambas as aplicacoes sem erro de classe nao encontrada.
- Todos os controllers e services instanciam os Models compartilhados sem erros.
- Rode os testes de ambas as aplicacoes (`phpunit` na raiz e `adm/phpunit`) 
  e confirme que nenhum teste quebrou.

COMO VERIFICAR:
- Compare `diff app/Models/EditaisModel.php adm/app/Models/EditaisModel.php`
  para confirmar que nao ha mais duplicacao injustificada.
- Acesse rotas publicas e admin que utilizam esses Models e verifique 
  se os dados sao carregados corretamente.
```

### Problema 1.2 - Instanciacao Direta de Models no Admin (`new Model()`)
**Arquivos:** `adm/app/Controllers/Admin/*.php`, `adm/app/Traits/*.php`  
**Severidade:** ALTA  
**Descricao:** Controllers e Traits do Admin instanciam Models com `new CargosModel()` em vez de receber via construtor. Isso impede testes unitarios e acopla infraestrutura a camada de apresentacao.

### Prompt 1.2 - Implementar Injecao de Dependencia via Construtor no Admin
```
CONTEXTO:
O painel administrativo (`adm/`) instancia Models diretamente dentro de 
Controllers e Traits usando `new CargosModel()`, `new EditaisModel()`, etc. 
Isso viola o principio da Inversao de Dependencias (DIP) e impede testes 
unitarios isolados.

ACAO REQUERIDA:
1. Abra `adm/app/Config/Services.php`.
2. Registre factories para TODOS os Models usados nos controllers admin, 
   utilizando o container de servicos do CI4. Exemplo:
   ```php
   public static function editaisModel(bool $getShared = true): EditaisModel
   {
       return $getShared ? self::getSharedInstance('editaisModel') : new EditaisModel();
   }
   ```
3. Abra cada Controller em `adm/app/Controllers/Admin/` que usa `new ...Model()`.
4. Substitua as instanciacoes diretas por injecao no construtor:
   ```php
   public function __construct(
       private EditaisModel $editaisModel,
       private CargosModel $cargosModel,
       // ...
   ) {
       parent::__construct();
   }
   ```
   Se o CI4 nao suportar autowiring automatico para essas classes, use 
   `service('editaisModel')` como passo intermediario, mas documente que o 
   objetivo final e autowiring.
5. Atualize as Traits em `adm/app/Traits/` para receber os Models como 
   parametros de metodo em vez de instanciar internamente, ou converta-as 
   para Services injetaveis.
6. Certifique-se de que `adm/app/Config/Services.php` seja carregado corretamente.

CRITERIOS DE ACEITACAO:
- Nenhum Controller em `adm/app/Controllers/Admin/` contem `new ...Model()`.
- Todos os Models sao obtidos via construtor ou via `service()`.
- As Traits nao instanciam Models diretamente.
- O sistema admin continua funcional (listagem, edicao, exclusao de editais, 
  cargos, candidatos, classificacoes).

COMO VERIFICAR:
- Rode `grep -r "new .*Model()" adm/app/Controllers/Admin/` 
  e `grep -r "new .*Model()" adm/app/Traits/` 
  e confirme que nao ha ocorrencias.
- Acesse o painel admin e execute operacoes CRUD em Editais e Candidatos 
  para validar funcionamento.
```

---

## ETAPA 2: DESACOPLAMENTO DE INFRAESTRUTURA E PADROES

### Objetivo
Extrair toda logica de apresentacao (PDF, Excel, QRCode) dos controllers para Services especializados. Implementar Repository Pattern no Admin com interfaces. Criar abstracoes para que a camada de dominio nao dependa de bibliotecas de infraestrutura.

### Problema 2.1 - Controllers Admin Gerando Excel e PDF
**Arquivos:** `adm/app/Controllers/Admin/Candidatos.php` (linhas 264-392), `adm/app/Controllers/Admin/Classificacoes.php` (linhas 207-398)  
**Severidade:** ALTA  
**Descricao:** Controllers contem centenas de linhas de codigo de formatacao de planilha (PhpSpreadsheet) e geracao de PDF (Dompdf), incluindo estilos, cores, fontes e zebra striping. Isso e responsabilidade de um ExportService.

### Prompt 2.1 - Extrair Geracao de Excel/PDF para Services de Exportacao
```
CONTEXTO:
Os controllers `adm/app/Controllers/Admin/Candidatos.php` e 
`adm/app/Controllers/Admin/Classificacoes.php` contem codigo extensivo para 
gerar arquivos Excel (.xlsx) e PDF (.pdf) diretamente, usando PhpSpreadsheet 
e Dompdf. Esse codigo inclui formatacao visual (estilos, cores, zebra striping, 
auto-size), logotipo institucional e headers HTTP. Isso viola o SRP e impede reutilizacao.

ACAO REQUERIDA:
1. Crie o diretorio `adm/app/Services/Export/`.
2. Crie a interface `adm/app/Services/Export/ExportadorInterface.php` com metodo:
   `public function gerar(array $dados, array $configuracoes): BinaryFileResponse|string;`
3. Crie `adm/app/Services/Export/ExcelExportService.php` que encapsule TODA a 
   logica de criacao de planilhas do PhpSpreadsheet. Ele deve receber:
   - `$dados` (array de linhas)
   - `$cabecalhos` (array associativo nome_coluna => titulo)
   - `$titulo` (string para a aba e titulo do documento)
   - `$estilos` (array opcional de configuracoes de estilo)
   - `$logoPath` (string|null para logo institucional)
   O service deve retornar o caminho do arquivo temporario gerado ou o conteudo binario.
4. Crie `adm/app/Services/Export/PdfExportService.php` com abordagem similar 
   para Dompdf, recebendo template/view a ser renderizada e dados.
5. Crie `adm/app/Services/Export/QrCodeService.php` para encapsular a geracao 
   de QRCode via `chillerlan\QRCode`.
6. Nos controllers, substitua os blocos de geracao de Excel/PDF/QrCode por:
   `$arquivo = service('excelExportService')->gerar(...);`
   e entao entregue o arquivo ao browser (o controller so define headers e faz `readfile`).
7. Remova TODOS os `use PhpOffice\PhpSpreadsheet\...`, `use Dompdf\...` e 
   `use chillerlan\...` dos controllers admin.

CRITERIOS DE ACEITACAO:
- Nenhum controller admin contem logica de formatacao de celulas, estilos, 
  criacao de objetos Spreadsheet/Writer/Xlsx, Dompdf ou QROptions.
- A geracao de relatorios Excel e PDF continua funcionando e os arquivos 
  gerados mantem a mesma aparencia (logo, titulo, cores, zebra striping).
- Os novos Services sao reutilizaveis por qualquer controller admin.
- Os tests existentes (se houver) continuam passando.

COMO VERIFICAR:
- Acesse a rota de exportacao de candidatos e de classificacao no admin.
- Baixe os arquivos .xlsx e .pdf e compare visualmente com a versao anterior.
- Rode `grep -r "use PhpOffice\\PhpSpreadsheet" adm/app/Controllers/Admin/`
  e `grep -r "use Dompdf" adm/app/Controllers/Admin/` 
  e confirme ausencia de imports.
```

### Problema 2.2 - Ausencia de Repository Pattern no Admin
**Arquivo:** `adm/app/Repositories/` (inexistente)  
**Severidade:** ALTA  
**Descricao:** O Admin nao possui camada de Repository. Queries complexas ficam nos Models (que herdam de CodeIgniter\Model) ou diretamente nos Services. Isso acopla logica de acesso a dados com logica de negocio.

### Prompt 2.2 - Implementar Repository Pattern no Painel Administrativo
```
CONTEXTO:
A aplicacao Publica (raiz) ja possui Repositories com interfaces 
(ex: `app/Repositories/EditalRepository.php` implementando `IEditalRepository`).
O painel Admin (`adm/`) nao possui essa camada; os Services e Controllers 
acessam dados diretamente via Models do CodeIgniter.

ACAO REQUERIDA:
1. Crie o diretorio `adm/app/Repositories/` e `adm/app/Repositories/Interfaces/`.
2. Para cada Model do Admin que executa queries complexas (joins, subqueries, 
   agregacoes), crie uma Interface e uma implementacao de Repository. 
   Comece pelos mais criticos:
   - `ICandidatoRepository` / `CandidatoRepository`
   - `IEditalRepository` / `EditalRepository`
   - `IClassificacaoRepository` / `ClassificacaoRepository`
   - `IContratoRepository` / `ContratoRepository`
3. A interface deve declarar os metodos especificos de dominio, NAO metodos 
   genericos de CRUD (a menos que sejam necessarios). Exemplo:
   ```php
   interface ICandidatoRepository {
       public function buscarPorEditalCargo(int $editalId, int $cargoId): array;
       public function contarPorAno(int $ano): int;
       public function atualizarSituacao(int $candidatoId, string $situacao): bool;
   }
   ```
4. A implementacao deve usar o Model correspondente internamente, mas NUNCA 
   expor o Query Builder para fora. Retorne arrays de DTOs ou entidades simples.
5. Atualize `adm/app/Config/Services.php` para registrar as implementacoes 
   dos Repositories com interfaces (factory que retorna a implementacao concreta).
6. Refatore os Services do Admin para receber os Repositories via construtor 
   em vez de Models diretamente, comecando pelos services de maior complexidade 
   (`ContratoService`, `ClassificacaoService`, `CandidatosService`).

CRITERIOS DE ACEITACAO:
- Existem interfaces e implementacoes de Repository para pelo menos os 4 
  dominios criticos listados acima.
- Nenhum Service admin acessa `$this->model->where(...)->get()` diretamente; 
  ele delega ao Repository.
- O container DI do CI4 (`Services.php`) registra as interfaces apontando 
  para as implementacoes concretas.
- O painel admin continua funcional apos a refatoracao.

COMO VERIFICAR:
- Verifique a existencia de arquivos em `adm/app/Repositories/`.
- Rode `grep -r "->where\|->join\|->select\|->from" adm/app/Services/` 
  para garantir que Services nao constroem queries diretamente (exceto em 
  casos muito simples que possam ser tolerados).
- Teste as funcionalidades de listagem de candidatos, editais e classificacao 
  no painel admin.
```

### Problema 2.3 - Acoplamento a `env()` dentro de Services
**Arquivo:** `app/Services/GovBrService.php` (construtor), e possivelmente outros  
**Severidade:** MEDIA  
**Descricao:** Services leem variaveis de ambiente diretamente via `env()`. Isso acopla a camada de aplicacao ao ambiente de execucao, dificultando testes e mudancas de configuracao.

### Prompt 2.3 - Remover Chamadas a `env()` dos Services
```
CONTEXTO:
Services como `app/Services/GovBrService.php` acessam `env('GOVBR_APP_KEY')`, 
`env('GOVBR_REDIRECT_URI')` etc. diretamente no construtor ou nos metodos. 
Isso viola o principio de que configuracoes devem ser injetadas, nao buscadas globalmente.

ACAO REQUERIDA:
1. Identifique TODOS os Services (raiz e admin) que chamam `env()` ou 
   `\Config\Services::get('configuracao')` de forma acoplada.
2. No `app/Config/Services.php` (raiz) e `adm/app/Config/Services.php`, 
   leia as configuracoes necessarias e passe como parametros no construtor 
   ao instanciar o Service.
   Exemplo:
   ```php
   public static function govBrService(bool $getShared = true): GovBrService
   {
       $config = [
           'clientId'     => env('GOVBR_APP_KEY'),
           'clientSecret' => env('GOVBR_SECRET'),
           'redirectUri'  => env('GOVBR_REDIRECT_URI'),
           'scope'        => env('GOVBR_SCOPE'),
       ];
       return $getShared ? self::getSharedInstance('govBrService', $config) 
                         : new GovBrService($config);
   }
   ```
3. No Service, altere o construtor para receber um array `$config` ou um 
   objeto Value Object `GovBrConfig`, armazenando-o em propriedades privadas.
4. Remova TODAS as chamadas `env()` dos Services.
5. Para testes, isso permitira injetar configuracoes mock facilmente.

CRITERIOS DE ACEITACAO:
- Nenhum arquivo em `app/Services/` ou `adm/app/Services/` contem chamadas 
  diretas a `env()`.
- Os Services funcionam normalmente em producao porque recebem os valores 
  via container.
- E possivel instanciar o Service em um teste unitario passando um array 
  de configuracao fake, sem depender de variaveis de ambiente.

COMO VERIFICAR:
- Rode `grep -r "env(" app/Services/` e `grep -r "env(" adm/app/Services/`
  e confirme ausencia de ocorrencias (exceto se houver em comentarios ou 
  em arquivos que nao sejam Services propriamente ditos).
```

---

## ETAPA 3: CLEAN CODE, SOLID E QUALIDADE DE DOMINIO

### Objetivo
Refatorar controllers gordos, eliminar magic numbers/strings, corrigir retornos mistos, ajustar namespaces e garantir que cada classe tenha uma unica razao para mudar.

### Problema 3.1 - Controllers Gordos (Fat Controllers)
**Arquivos:** `adm/app/Controllers/Admin/Classificacoes.php` (585 linhas), `adm/app/Controllers/Admin/Candidatos.php` (409 linhas), `app/Controllers/Publica/Cadastros.php` (398 linhas)  
**Severidade:** ALTA  
**Descricao:** Controllers acumulam validacao, orquestracao de multiplos services, formatacao de dados, logica de saudacao, montagem de view, exportacao e ate insercao em banco.

### Prompt 3.1 - Refatorar Controllers para Slim Controllers ✅ (Entregue na Sprint 4)
```
CONTEXTO:
Os controllers `adm/app/Controllers/Admin/Classificacoes.php`, 
`adm/app/Controllers/Admin/Candidatos.php` e `app/Controllers/Publica/Cadastros.php`
sao excessivamente grandes, acumulando responsabilidades que nao lhes pertencem.
Um controller deve apenas: receber request, delegar para application/domain layer,
e retornar response (view/redirect/json).

ACAO REQUERIDA:
1. Para cada controller gordo listado:
   a. Identifique blocos de logica que nao sao responsabilidade do controller:
      - Formatacao de dados (ex: `formatarBairro`, `getSaudacao`) -> Helper ou ViewPresenter.
      - Validacao de regras de negocio complexas -> Service ou UseCase.
      - Construcao de queries -> Repository.
      - Geracao de arquivos -> ExportService (ja tratado na Etapa 2).
      - Logica de ordenacao/classificacao -> Domain Service.
   b. Extraia esses blocos para classes apropriadas:
      - Crie `app/Presenters/SaudacaoPresenter.php` e `app/Presenters/BairroPresenter.php`.
      - Crie UseCases ou Services de Coordenacao quando o controller precisa 
        orquestrar varios passos (ex: `CadastroInicialUseCase`, `RecursoUseCase`).
   c. Deixe o controller com no maximo 100-150 linhas por metodo de acao principal.
   d. Faca com que o controller dependa apenas de 1 a 3 services/use cases.
2. No caso especifico de `Classificacoes.php`:
   - O metodo `gerarClassificacao()` contem loops `foreach` aninhados que inserem 
     diretamente no banco. Extraia TODO esse fluxo para 
     `adm/app/Services/Classificacao/GerarClassificacaoUseCase.php`.
   - O controller deve chamar apenas:
     `$resultado = service('gerarClassificacaoUseCase')->execute($editalId, $cargoId);`
     e entao redirecionar ou exibir mensagem.
3. No caso de `Cadastros.php`:
   - Extraia a logica de `loginLocal` (se ainda existir), `getSaudacao`, 
     `formatarBairro` para classes utilitarias ou fixtures.
   - Separe o controller em sub-metodos privados pequenos ou, preferencialmente, 
     em classes separadas de UseCase para cada fluxo (cadastro pessoal, 
     classificatorio, comprovante, recurso).

CRITERIOS DE ACEITACAO:
- `Classificacoes.php` tem menos de 200 linhas e NAO contem logica de insercao 
  em banco nem geracao de Excel/PDF.
- `Candidatos.php` tem menos de 200 linhas e NAO contem logica de planilha.
- `Cadastros.php` tem menos de 250 linhas e NAO contem helper de saudacao 
  nem formatacao de bairro.
- Todas as novas classes criadas possuem nomes claros e uma unica responsabilidade.
- O comportamento funcional permanece identico.

COMO VERIFICAR:
- Execute `wc -l` nos arquivos e confirme os limites.
- Acesse as telas correspondentes no sistema e valide que nenhuma funcionalidade 
  foi perdida (cadastro, classificacao, exportacao).
```

### Problema 3.2 - Magic Numbers e Magic Strings
**Arquivos:** `adm/app/Services/Contratos/ContratoService.php`, `adm/app/Models/EditaisModel.php`, `app/Models/EditaisModel.php`, `adm/app/Controllers/Admin/Classificacoes.php`  
**Severidade:** MEDIA  
**Descricao:** Status numericos (`1`, `2`, `3`, `0`) sao usados sem contexto. Ex: `'ds_status' => '2'`, `if($experiencia->ds_status == 0)`. Isso torna o codigo ilegivel e fragil.

### Prompt 3.2 - Substituir Magic Numbers por Enums ou Constantes Semanticas ✅ (Entregue na Sprint 5)
```
CONTEXTO:
O projeto utiliza valores numericos e strings literais para representar 
status, tipos e situacoes em varios Models e Services. Isso dificulta 
compreensao e manutencao.

ACAO REQUERIDA:
1. Analise os arquivos `adm/app/Services/Contratos/ContratoService.php`, 
   `adm/app/Models/EditaisModel.php`, `app/Models/EditaisModel.php` e 
   `adm/app/Controllers/Admin/Classificacoes.php`.
2. Para cada valor literal que represente um estado de dominio, crie um Enum 
   PHP 8.1+ (se o projeto usar PHP 8.1+) ou, se nao for possivel, constantes 
   de classe em um objeto de valor.
   Exemplos:
   ```php
   enum EditalStatusEnum: int {
       case ATIVO = 1;
       case INATIVO = 0;
       case ENCERRADO = 2;
       case VIGENTE   = 3;
   }
   
   enum ContratoStatusEnum: string {
       case ATIVO = '1';
       case RESCINDIDO = '2';
       case ADITIVADO = '3';
   }
   
   enum ExperienciaTipoEnum: int {
       case CLASSIFICATORIA = 0;
       case NAO_CLASSIFICATORIA = 1;
   }
   ```
3. Substitua TODAS as ocorrencias dos valores literais pelos Enums/Constantes.
   Exemplo: `$this->where('ds_status', EditalStatusEnum::ATIVO->value)`.
4. Se o banco de dados utilizar strings ou ints inconsistentes entre raiz e admin, 
   escolha um padrao (preferencialmente int) e aplique em ambos.
5. Crie um arquivo `app/Enums/` (ja existe na raiz; replique/adapte para o admin 
   ou use o compartilhado da Etapa 1).

CRITERIOS DE ACEITACAO:
- Nao ha valores numericos ou strings literais soltos representando status 
  de dominio nos Services e Models mapeados.
- Todos os status utilizam Enums ou constantes semanticas.
- O banco de dados continua compativel (os Enums usam os mesmos valores 
  subjacentes ja existentes).
- As queries e validacoes continuam funcionando corretamente.

COMO VERIFICAR:
- Rode `grep -r "'ds_status'" adm/app/Models/ adm/app/Services/ app/Models/ app/Services/`
  e analise se todos os valores foram substituidos por referencias semanticas.
- Execute os testes ou, na ausencia deles, faca um fluxo manual de alteracao 
  de status de editais e contratos.
```

### Problema 3.3 - Retorno Misto (bool|array) em Services
**Arquivo:** `app/Services/EditaisService.php` (metodo `estaAtivo()`)  
**Severidade:** MEDIA  
**Descricao:** O metodo retorna `true` em caso de sucesso ou um `array` com mensagem de erro. Isso viola o principio de retorno consistente e dificulta tratamento por chamadores.

### Prompt 3.3 - Eliminar Retornos Mistos usando Excecoes de Dominio ✅ (Entregue na Sprint 5)
```
CONTEXTO:
O metodo `estaAtivo()` em `app/Services/EditaisService.php` retorna 
`bool|array`: `true` se o edital esta ativo, ou um array associativo 
com chave `error` caso contrario. Chamadores precisam verificar o tipo 
retornado, o que e fragil.

ACAO REQUERIDA:
1. Localize TODOS os metodos em Services (raiz e admin) que retornam 
   tipos mistos (bool|array, object|string, etc.). Comece por `estaAtivo()`.
2. Para cada metodo:
   a. Se o metodo valida uma regra de dominio e falha, lance uma excecao 
      de dominio especifica. Crie `app/Exceptions/Domain/` (se nao existir) 
      e dentro dele `EditalForaDoPrazoException.php`, `CandidatoNaoEncontradoException.php`, etc.
   b. O metodo deve ter retorno unico. No caso de `estaAtivo()`, retorne 
      apenas `bool`. Se inativo, lance `EditalForaDoPrazoException` com 
      a mensagem apropriada.
   c. Atualize TODOS os controllers que chamam esse metodo para capturar 
      a excecao e redirecionar com flashdata/mensagem de erro.
   d. Se houver necessidade de retornar dados junto com status, use um 
      objeto Result padrao (Value Object) como `OperationResult`:
      ```php
      class OperationResult {
          public function __construct(
              public bool $success,
              public string $message = '',
              public ?array $data = null
          ) {}
      }
      ```
3. Refatore o controller para usar try/catch:
   ```php
   try {
       $ativo = $this->editaisService->estaAtivo($id);
   } catch (EditalForaDoPrazoException $e) {
       return redirect()->back()->with('error', $e->getMessage());
   }
   ```

CRITERIOS DE ACEITACAO:
- Nenhum metodo de Service retorna tipos mistos como `bool|array`.
- Excecoes de dominio existem e sao usadas para casos de falha de regra de negocio.
- Controllers tratam as excecoes adequadamente.
- Nenhum codigo cliente precisa verificar `is_array($resultado)` para saber se houve erro.

COMO VERIFICAR:
- Execute busca por `function.*:.*\|` em `app/Services/` e `adm/app/Services/` 
  para localizar retornos union type mistos.
- Revise os controllers que consomem esses metodos para garantir que usam 
  try/catch ou OperationResult consistentemente.
```

### Problema 3.4 - Namespace Incorreto em Domain
**Arquivo:** `app/Domain/ClassificacaoCalculator.php`  
**Severidade:** MEDIA  
**Descricao:** O arquivo esta em `app/Domain/ClassificacaoCalculator.php`, mas seu namespace e `App\Domain\Classificacao`. Isso viola PSR-4 e pode causar falha de autoload.

### Prompt 3.4 - Corrigir Namespaces para Conformidade PSR-4 ✅ (Entregue na Sprint 5)
```
CONTEXTO:
O arquivo `app/Domain/ClassificacaoCalculator.php` declara 
`namespace App\Domain\Classificacao;`, mas esta fisicamente localizado em 
`app/Domain/ClassificacaoCalculator.php`. Isso significa que, sob PSR-4, 
o namespace deveria ser `App\Domain` (ou o arquivo deveria estar em 
`app/Domain/Classificacao/ClassificacaoCalculator.php`).

ACAO REQUERIDA:
1. Faca um levantamento completo de TODOS os arquivos em `app/` e `adm/app/` 
   cujo namespace nao corresponda ao caminho fisico segundo PSR-4.
2. Para cada inconsistencia:
   - Se o namespace indica um subdiretorio inexistente (como `App\Domain\Classificacao`), 
     crie o subdiretorio e mova o arquivo para la (`app/Domain/Classificacao/ClassificacaoCalculator.php`).
   - Se o arquivo esta no lugar certo mas o namespace esta errado, corrija o namespace.
3. Atualize TODOS os `use` statements que referenciam essas classes.
4. Rode `composer dump-autoload` na raiz e em `adm/`.

CRITERIOS DE ACEITACAO:
- Todos os namespaces correspondem ao caminho fisico do arquivo segundo PSR-4.
- O autoload funciona sem erros de classe nao encontrada.
- Todas as referencias externas (`use`, `new`, type hints) foram atualizadas.

COMO VERIFICAR:
- Rode `composer dump-autoload` e depois `phpunit` em ambas as aplicacoes.
- Use uma ferramenta de lint ou `php -l` nos arquivos movidos.
- Verifique se a aplicacao carrega corretamente apos as mudancas.
```

---

## ETAPA 4: CONSOLIDACAO, TESTES E DOCUMENTACAO

### Objetivo
Unificar as duas aplicacoes em uma unica base CI4 (se viavel), ou ao menos compartilhar todo codigo comum. Elevar a cobertura de testes dos Services e Repositories recém-refatorados. Documentar a arquitetura final.

### Problema 4.1 - Duas Instalacoes CI4 no Mesmo Repositorio
**Arquivos:** `system/` + `adm/system/`, `vendor/` + `adm/vendor/`, `writable/` + `adm/writable/`  
**Severidade:** ALTA (dificuldade de manutencao)  
**Descricao:** Ha dois diretorios `system/`, dois `vendor/`, dois `writable/` e suites de testes separadas. Isso aumenta o tamanho do repositorio, dificulta atualizacoes de seguranca do framework e propaga divergencia.

### Prompt 4.1 - Consolidar Aplicacoes em uma Unica Base CI4 com Modulos ✅ (Entregue na Sprint 6 - Estrategia B)
```
CONTEXTO:
O repositorio contem duas instalacoes completas do CodeIgniter 4: a raiz 
(para o site publico) e `adm/` (para o painel administrativo). Cada uma 
tem seu proprio `system/`, `vendor/`, `writable/` e `tests/`.

ACAO REQUERIDA:
Escolha UMA das estrategias abaixo e execute-a completamente:

ESTRATEGIA A (Recomendada - Modulos):
1. Mantenha uma UNICA instalacao CI4 na raiz (um unico `system/`, `vendor/`).
2. Converta o codigo atual da raiz em um modulo `app/Modules/Publico/` 
   e o codigo do admin em `app/Modules/Admin/`.
3. As rotas de cada modulo devem ser prefixadas (`/admin/*` para admin, `/` para publico).
4. O `public/` da raiz deve rotear tanto para o modulo publico quanto para 
   o modulo admin (usando subdiretorio ou regra de rewrite).
5. Elimine `adm/system/`, `adm/vendor/`, `adm/writable/` (ou reduza `writable/` 
   a apenas uploads especificos do admin).
6. Atualize `composer.json` da raiz para incluir as dependencias que estavam 
   apenas em `adm/composer.json`.
7. Unifique `phpunit.xml.dist` para cobrir ambos os modulos.

ESTRATEGIA B (Namespaces Separados, Mesmo Core):
1. Se a estrategia A for muito inviavel no curto prazo, mantenha a estrutura 
   de diretorios atual, mas garanta que ambas as aplicacoes usem o MESMO 
   `system/` e `vendor/` da raiz.
2. Em `adm/`, remova `adm/system/` e `adm/vendor/`.
3. Configure `adm/composer.json` para usar o `vendor/` da raiz via 
   `vendor-dir` ou symlink.
4. Compartilhe o mesmo autoload PSR-4 para classes comuns.

CRITERIOS DE ACEITACAO:
- Nao existem duas versoes do CodeIgniter 4 sendo mantidas.
- Atualizar o framework em um so lugar atualiza ambas as aplicacoes.
- O site publico e o painel admin continuam acessiveis e funcionais.
- Testes unitarios podem ser executados com um unico comando `phpunit` 
  cobrindo ambas as camadas.

COMO VERIFICAR:
- Verifique que `adm/system/` e `adm/vendor/` nao existem mais.
- Acesse o site publico e o painel admin simultaneamente.
- Rode `phpunit` na raiz e confirme que testes de ambas as camadas sao executados.
- Verifique que `composer update` na raiz atualiza dependencias compartilhadas.
```

### Problema 4.2 - Ausencia de Testes Unitarios no Admin
**Arquivo:** `adm/tests/` (quase vazio ou sem cobertura de Services)  
**Severidade:** MEDIA  
**Descricao:** A suite de testes do admin nao cobre os Services e Repositories (que nem existiam). Com a introducao de DI e interfaces na Etapa 2, torna-se trivial testar em isolamento.

### Prompt 4.2 - Criar Suite de Testes Unitarios para Services e Repositories ✅ (Entregue na Sprint 7)
```
CONTEXTO:
Apos as etapas anteriores, o painel admin possui Services injetaveis e 
Repositories com interfaces. Porem, nao existem testes automatizados para 
essas classes, o que impede refactoring seguro no futuro.

ACAO REQUERIDA:
1. Configure `adm/phpunit.xml.dist` (ou `phpunit.xml.dist` unificado se a 
   Etapa 4.1 foi concluida) para apontar para os testes de ambas as camadas.
2. Para cada Repository com interface criado na Etapa 2, crie um teste 
   de integracao leve usando SQLite em memoria ou um stub que implemente 
   a interface.
3. Para cada Service do admin que contem logica de negocio significativa 
   (ex: `ClassificacaoProcessorService`, `PontuacaoCalculatorService`, 
   `ContratoService`), crie testes unitarios que:
   - Mockam os Repositories injetados.
   - Verificam o comportamento esperado para cenarios felizes e de erro.
   - Garantem que excecoes de dominio sao lancadas corretamente (Etapa 3.3).
4. Use as convencoes de nomenclatura: `ClassificacaoProcessorServiceTest` 
   em `adm/tests/Services/Classificacao/ClassificacaoProcessorServiceTest.php`.
5. Para calculos de dominio puros (ex: `ClassificacaoCalculator`), prefira 
   testes sem mocks, passando entradas e asserindo saidas.

CRITERIOS DE ACEITACAO:
- Existem testes unitarios cobrindo pelo menos os 5 Services/Repositories 
  mais criticos do admin.
- A suite de testes executa com `phpunit` sem erro.
- Cobertura de codigo (coverage) e gerada e visivel (configurar em `phpunit.xml.dist`).
- Nenhum teste depende de banco de dados real (use SQLite :memory: ou mocks).

COMO VERIFICAR:
- Rode `vendor/bin/phpunit --coverage-text` (ou equivalente) e verifique 
  se os novos testes aparecem e passam.
- Verifique o relatorio de cobertura para os Services testados.
```

---

## ANEXO: GLOSSARIO DE RISCOS E IMPACTOS

| Codigo | Risco | Impacto |
|---|---|---|
| R-CRIT | Backdoor / SQL Injection | Exposicao de dados pessoais, vazamento LGPD, invasao |
| R-ALTA | Duplicacao de Models / Repositorios | Manutencao duplicada, bugs divergentes |
| R-ALTA | Fat Controllers | Dificuldade de teste, alto acoplamento, regressoes |
| R-MED | Magic Numbers | Erros semanticos, dificuldade de onboarding |
| R-MED | Retornos Mistos | Falhas silenciosas, comportamento imprevisivel |
| R-MED | Acoplamento a `env()` | Testes flaky, dependencia de ambiente |
| R-BAIXO | Namespace PSR-4 incorreto | Erros de autoload em atualizacoes futuras |

---

*Fim do Documento de Auditoria Arquitetural.*
