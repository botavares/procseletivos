# PLANEJAMENTO DE SPRINTS - PROSEL PUBLICO

**Projeto:** Processo Seletivo (procseletivos) - Camada Publica e Admin  
**Data:** 2026-10-19  
**Baseado em:** `AUDITORIA_PROCSEL_2021019.md`

---

## RESUMO DA ESTRATEGIA

Os sprints foram organizados seguindo a **regra de estabilidade evolutiva**:  
cada sprint entrega valor e mantem o sistema 100% operacional, sem criar  
dependencias futuras que prejudiquem entregas anteriores. A ordem reflete  
a urgencia (seguranca) para a sustentabilidade (testes).

---

## SPRINT 0 - HOTFIX DE SEGURANCA E ESTABILIDADE

**Duracao:** 3 dias  
**Dependencias:** Nenhuma (pode ser executado imediatamente)  
**Risco de Regressao:** Nenhum

### Objetivo
Eliminar vulnerabilidades ativas e dados pessoais hardcoded sem alterar  
comportamento funcional ou arquitetura.

### Prompts de Implementacao (ver `AUDITORIA_PROCSEL_2021019.md`)
- **Prompt 0.1** - Remover Backdoor `loginLocal()` e dados pessoais hardcoded
- **Prompt 0.2** - Corrigir SQL Injection por interpolacao de variavel

### Entregaveis
- [x] `app/Controllers/Publica/Cadastros.php` sem metodo `loginLocal()`
- [x] Ausencia de CPFs e e-mails reais no codigo-fonte
- [x] `adm/app/Models/CandidatosModel.php` usando prepared statements
- [x] `app/Repositories/TransparenciaRepository.php` com sanitizacao em joins dinamicos

### Criterios de Aceitacao
- `grep -r "loginLocal" app/` retorna zero ocorrencias.
- `grep -rE "[0-9]{3}\.[0-9]{3}\.[0-9]{3}-[0-9]{2}" app/Controllers/` retorna zero.
- Nenhuma string SQL interpolada em `app/Models/` ou `adm/app/Models/`.
- Suite de testes existente continua passando (`phpunit` na raiz e em `adm/`).

### Notas
Este sprint e inegociavel e deve ser entregue antes de qualquer refactoring 
arquitetural, pois trabalha sobre codigo sensivel.

---

## SPRINT 1 - FUNDACAO ARQUITETURAL (DI E MODELOS COMPARTILHADOS)

**Duracao:** 7 dias  
**Dependencias:** Sprint 0 concluido  
**Risco de Regressao:** Baixo

### Objetivo
Criar uma base solida de injecao de dependencia no Admin e eliminar a  
duplicacao de Models entre Publico e Admin.

### Prompts de Implementacao
- **Prompt 1.1** - Criar namespace compartilhado `app/Common/Models/` e eliminar duplicacao
- **Prompt 1.2** - Implementar injecao de dependencia via construtor no Admin

### Entregaveis
- [x] Diretorio `app/Common/Models/` com Models base compartilhados (SetoresModel, VerificadorModel)
- [x] `adm/app/Config/Services.php` registrando factories de Models e Services
- [x] Controllers Admin sem `new ...Model()` e sem `new ...Service()` (todos os 18 controllers refatorados)
- [x] Autoload PSR-4 configurado para `App\Common\Models` em ambas as aplicacoes

### Criterios de Aceitacao
- `grep -r "new .*Model()" adm/app/Controllers/Admin/` retorna zero.
- Rotas de listagem/criacao/edicao de Editais, Cargos e Candidatos funcionam 
  normalmente no Admin apos a mudanca.
- `diff app/Models/EditaisModel.php adm/app/Models/EditaisModel.php` mostra 
  apenas especializacoes (se houver), nao codigo duplicado.

### Notas
Este sprint prepara o terreno para o Repository Pattern (Sprint 3).  
Sem DI, nao e possivel injetar Repositories posteriormente.

---

## SPRINT 2 - DESACOPLAMENTO DE EXPORTACAO (EXCEL/PDF/QRCODE)

**Duracao:** 5 dias  
**Dependencias:** Sprint 1 concluido  
**Risco de Regressao:** Medio

### Objetivo
Extrair toda logica de geracao de documentos (Excel, PDF, QRCode) dos  
controllers para services especializados e reutilizaveis.

### Prompts de Implementacao
- **Prompt 2.1** - Extrair geracao de Excel/PDF/QrCode para `adm/app/Services/Export/`

### Entregaveis
- [x] `adm/app/Services/Export/ExportadorInterface.php`
- [x] `adm/app/Services/Export/ExcelExportService.php`
- [x] `adm/app/Services/Export/PdfExportService.php`
- [x] `adm/app/Services/Export/QrCodeService.php`
- [x] Controllers `Candidatos.php` e `Classificacoes.php` delegando exportacao

### Criterios de Aceitacao
- Arquivos `.xlsx` e `.pdf` gerados pelo Admin mantem layout identico ao anterior.
- Nenhum `use PhpOffice\PhpSpreadsheet\...` ou `use Dompdf\...` presente em 
  `adm/app/Controllers/Admin/`.
- Os novos services podem ser reutilizados por outros controllers sem modificacao.

### Notas
Este sprint melhora a testabilidade (os exportadores podem ser testados em  
isolamento) e reduz drasticamente o tamanho dos controllers.

---

## SPRINT 3 - REPOSITORY PATTERN NO ADMIN

**Duracao:** 8 dias  
**Dependencias:** Sprint 1 concluido (DI de Models pronto)  
**Risco de Regressao:** Medio

### Objetivo
Implementar camada de Repository no Admin, com interfaces e implementacoes,  
desacoplando Services dos Models concretos do CodeIgniter.

### Prompts de Implementacao
- **Prompt 2.2** - Implementar Repository Pattern no painel administrativo

### Entregaveis
- [x] Interfaces em `adm/app/Repositories/Interfaces/` (minimo 4 dominios)
- [x] Implementacoes em `adm/app/Repositories/`
- [x] Services admin recebendo Repositories via construtor
- [x] Registro no container DI (`adm/app/Config/Services.php`)

### Criterios de Aceitacao
- `adm/app/Services/` nao contem chamadas a `$this->model->where(...)` 
  para logica complexa (apenas delegacao ao Repository).
- E possivel escrever um teste unitario para `ContratoService` mockando 
  `IContratoRepository` sem carregar o banco de dados.
- O painel admin continua funcional (CRUD completo testado manualmente).

### Notas
Este sprint e o coracao da evolucao arquitetural do Admin. Ele transforma 
o Admin de MVC procedural para uma arquitetura orientada a ports/adapters.

---

## SPRINT 4 - CLEAN CODE EM CONTROLLERS (SLIM CONTROLLERS)

**Duracao:** 7 dias  
**Dependencias:** Sprint 2 e Sprint 3 concluidos (exportacao e repositories prontos)  
**Risco de Regressao:** Medio-Alto

### Objetivo
Reduzir controllers gordos, extrair logica de dominio e apresentacao,  
e aplicar SRP na camada de entrada.

### Prompts de Implementacao
- **Prompt 3.1** - Refatorar controllers para Slim Controllers (max 150-200 linhas)

### Entregaveis
- [ ] `Classificacoes.php` < 200 linhas (sem insercao em banco nem exportacao)
- [ ] `Candidatos.php` < 200 linhas (sem logica de planilha)
- [ ] `Cadastros.php` < 250 linhas (sem helpers de saudacao/formatacao)
- [ ] Novas classes: `GerarClassificacaoUseCase`, `SaudacaoPresenter`, etc.

### Criterios de Aceitacao
- `wc -l adm/app/Controllers/Admin/Classificacoes.php` <= 200.
- `wc -l adm/app/Controllers/Admin/Candidatos.php` <= 200.
- `wc -l app/Controllers/Publica/Cadastros.php` <= 250.
- Fluxos de cadastro, classificacao e exportacao permanecem funcionais.

### Notas
Somente apos os Services e Repositories estarem desacoplados (Sprints 2 e 3) 
e seguro reduzir os controllers. Tentar antes seria mover codigo para o nada.

---

## SPRINT 5 - DOMINIO E SEMANTICA (ENUMS, EXCECOES, NAMESPACES)

**Duracao:** 5 dias  
**Dependencias:** Sprint 4 concluido (controllers ja delegam tudo)  
**Risco de Regressao:** Medio

### Objetivo
Eliminar magic numbers, retornos mistos e corrigir namespaces, tornando o  
codigo autodocumentado e consistente.

### Prompts de Implementacao
- **Prompt 3.2** - Substituir magic numbers/strings por Enums ou constantes
- **Prompt 3.3** - Eliminar retornos mistos usando excecoes de dominio
- **Prompt 3.4** - Corrigir namespaces para conformidade PSR-4

### Entregaveis
- [ ] Enums criados: `EditalStatusEnum`, `ContratoStatusEnum`, `ExperienciaTipoEnum`, etc.
- [ ] Excecoes de dominio em `app/Exceptions/Domain/` (ou shared)
- [ ] Services com retornos unitipicos (ex: `estaAtivo(): bool`)
- [ ] Namespaces PSR-4 corrigidos em todos os arquivos inconsistentes

### Criterios de Aceitacao
- Nenhum valor literal solto representando status de dominio em Services/Models.
- `grep -r "function .* : .*\\|" app/Services/ adm/app/Services/` 
  nao retorna retornos mistos como `bool|array`.
- `composer dump-autoload` executa sem erro de autoload.
- Aplicacao carrega sem `Class not found`.

### Notas
Este sprint depende de que os controllers ja estejam delegando (Sprint 4), 
pois a mudanca de retorno (Prompt 3.3) exige atualizacao dos chamadores.

---

## SPRINT 6 - CONSOLIDACAO DA INFRAESTRUTURA (UNIFICACAO CI4)

**Duracao:** 10 dias  
**Dependencias:** Sprint 1 concluido (codigo compartilhado ja existe)  
**Risco de Regressao:** Alto (impacto em deploy e paths)

### Objetivo
Eliminar a dupla instalacao CodeIgniter 4 (raiz + adm/) e unificar em  
uma unica base com modulos ou namespaces compartilhados.

### Prompts de Implementacao
- **Prompt 4.1** - Consolidar aplicacoes em uma unica base CI4 com modulos

### Entregaveis
- [ ] Uma unica pasta `system/` e `vendor/` na raiz
- [ ] Modulos `app/Modules/Publico/` e `app/Modules/Admin/` (ou estrategia B)
- [ ] Rotas unificadas funcionando para site e painel
- [ ] `adm/system/` e `adm/vendor/` removidos ou convertidos em symlinks

### Criterios de Aceitacao
- `phpunit` na raiz executa testes de ambas as camadas (se unificados).
- Site publico acessivel em `/`.
- Painel admin acessivel em `/admin` (ou subdominio/rota configurada).
- Atualizar CI4 via `composer update` na raiz reflete em ambas as camadas.

### Notas
Este sprint pode ser executado em paralelo com Sprints 4-5 desde que o 
codigo compartilhado (Sprint 1) ja esteja estabilizado. No entanto, 
recomenda-se executa-lo apos Sprints 0-3 para evitar conflitos de merge 
grandes.

---

## SPRINT 7 - QUALIDADE E TESTES UNITARIOS

**Duracao:** 7 dias  
**Dependencias:** Sprint 3 (Repositories prontos) e Sprint 5 (Dominio limpo)  
**Risco de Regressao:** Baixo

### Objetivo
Cobrir Services e Repositories criticos com testes unitarios, garantindo  
que futuros refatorings nao quebrem regras de negocio.

### Prompts de Implementacao
- **Prompt 4.2** - Criar suite de testes unitarios para Services e Repositories

### Entregaveis
- [ ] Testes para `ClassificacaoProcessorService`
- [ ] Testes para `PontuacaoCalculatorService`
- [ ] Testes para `ContratoService`
- [ ] Testes para Repositories com SQLite `:memory:`
- [ ] Relatorio de cobertura de codigo configurado em `phpunit.xml.dist`

### Criterios de Aceitacao
- Pelo menos 5 Services/Repositories criticos possuem testes unitarios.
- `vendor/bin/phpunit --coverage-text` gera relatorio sem falhas.
- Testes nao dependem de banco MySQL real (usem SQLite ou mocks).
- CI/CD (se existir) executa a suite automaticamente.

### Notas
Este sprint fecha o ciclo de qualidade. Com Repositories e DI em vigor, 
os testes sao simples de escrever e mantem alto valor.

---

## CRONOGRAMA SUGERIDO (VISUAL)

```
Semana 1  |  SPRINT 0  |  Seguranca (3d)
Semana 1  |  SPRINT 1  |  Fundacao DI/Models (7d)
Semana 2  |  SPRINT 2  |  Exportacao (5d)
Semana 3  |  SPRINT 3  |  Repositories (8d)
Semana 4  |  SPRINT 4  |  Slim Controllers (7d)
Semana 5  |  SPRINT 5  |  Enums/Excecoes/Namespaces (5d)
Semana 6  |  SPRINT 6  |  Consolidacao CI4 (10d)
Semana 7  |  SPRINT 7  |  Testes Unitarios (7d)
```

**Duracao Total Estimada:** 7 semanas (aprox. 45 dias corridos)

---

## DEFINICAO DE PRONTO (Definition of Done)

Para que uma sprint seja considerada concluida, TODOS os itens abaixo devem 
ser verdadeiros:

1. Todos os prompts da sprint foram implementados e revisados.
2. O sistema esta operacional (site publico e painel admin acessiveis).
3. `phpunit` (raiz e/ou admin) executa sem falhas.
4. Nao ha dados pessoais hardcoded introduzidos no sprint.
5. Nao ha novas vulnerabilidades de SQL Injection introduzidas.
6. O codigo segue PSR-4 e PSR-12 (formatacao automatica via `php-cs-fixer` 
   ou equivalente, se disponivel).
7. O arquivo `AUDITORIA_PROCSEL_2021019.md` e atualizado com checkboxes 
   indicando quais prompts ja foram entregues.

---

*Fim do Planejamento de Sprints.*
