# Sprint 8 — Prompts para Execução Automatizada por IA

## Contexto Base para Todos os Prompts

> Você está trabalhando no projeto `procseletivos` (CodeIgniter 4), localizado em `C:\laragon\www\servicos\procseletivos`. O objetivo é eliminar a pasta `adm/` e consolidar todo o código do painel administrativo dentro da estrutura principal `app/`.
>
> **Regras críticas:**
> - Nunca modificar `system/` ou `vendor/`.
> - Nunca executar `composer install/update` dentro de `adm/`.
> - Sempre preservar backups (`.bak`) antes de sobrescrever configs críticas.
> - Sempre executar testes após cada etapa.
> - Sempre usar a ferramenta `edit` para alterações parciais e `write` para novos arquivos.
> - Manter o mesmo estilo de código existente.

---

## Prompt Etapa 1 — Análise de Conflitos e Diferenças

**Objetivo:** Comparar arquivos duplicados entre `app/` (raiz) e `adm/app/` para decidir estratégia de consolidação.

**Tarefas:**
1. Compare byte a byte os arquivos `app/Controllers/BaseController.php` e `adm/app/Controllers/BaseController.php`. Reporte todas as diferenças.
2. Liste todos os models em `app/Models/` e `adm/app/Models/` que têm o mesmo nome. Para cada um, compare o conteúdo e classifique como: (a) idêntico, (b) admin tem funcionalidades extras, (c) público tem funcionalidades extras, (d) completamente diferente.
3. Faça o mesmo para services em `app/Services/` vs `adm/app/Services/` (incluindo `Base/`).
4. Compare helpers: `app/Helpers/` vs `adm/app/Helpers/`.
5. Compare `app/Config/Services.php` vs `adm/app/Config/Services.php`: quantas factories cada um define? Quais são exclusivas do admin?
6. Compare `app/Config/Routes.php` vs `adm/app/Config/Routes.php`: quantas rotas cada um define?
7. Gere um relatório JSON ou Markdown em `SPRINT8_CONFLITOS.md` com todas as diferenças documentadas.

**Teste de Integridade:** Nenhum código deve ser alterado nesta etapa. Apenas leitura e comparação.

---

## Prompt Etapa 2 — Preparação da Infraestrutura

**Objetivo:** Criar a estrutura de diretórios de destino.

**Tarefas:**
1. Crie os seguintes diretórios (sem copiar arquivos ainda):
   - `app/Controllers/Admin/`
   - `app/Models/Admin/`
   - `app/Services/Admin/`
   - `app/Services/Admin/Base/`
   - `app/Services/Admin/Aperfeicoamentos/`
   - `app/Services/Admin/Candidatos/`
   - `app/Services/Admin/Cargos/`
   - `app/Services/Admin/Classificacao/`
   - `app/Services/Admin/Classificacao/DTO/`
   - `app/Services/Admin/Contratos/`
   - `app/Services/Admin/CriteriosAdicionais/`
   - `app/Services/Admin/Editais/`
   - `app/Services/Admin/Escolaridades/`
   - `app/Services/Admin/Experiencias/`
   - `app/Services/Admin/Export/`
   - `app/Services/Admin/Relatorios/`
   - `app/Repositories/Admin/`
   - `app/Repositories/Admin/Interfaces/`
   - `app/Enums/Admin/`
   - `app/Enums/Admin/Escolaridades/`
   - `app/Exceptions/Admin/`
   - `app/Traits/Admin/`
   - `app/Commands/Admin/`
   - `app/Helpers/Admin/`
   - `app/Views/Admin/`
   - `tests/Admin/`
   - `tests/Admin/Services/`
   - `tests/Admin/Services/Classificacao/`
   - `tests/Admin/Repositories/`
2. Verifique se `app/Config/Autoload.php` já suporta sub-namespaces (o PSR-4 do CI4 já suporta automaticamente subpastas sob o namespace `App`).
3. Faça backup de `app/Config/Routes.php` como `app/Config/Routes.php.bak`.
4. Prepare um comentário no início de `app/Config/Routes.php` indicando que rotas do admin serão adicionadas na Etapa 10.

**Teste de Integridade:**
- Verifique que o site público ainda carrega (`php spark routes` na raiz).
- Confirme que `adm/` ainda funciona completamente.

---

## Prompt Etapa 3 — Migração de Controllers

**Objetivo:** Mover controllers do admin para `app/Controllers/Admin/`.

**Tarefas:**
1. Copie todos os arquivos de `adm/app/Controllers/Admin/*.php` para `app/Controllers/Admin/`.
2. Copie `adm/app/Controllers/BaseController.php` para `app/Controllers/AdminBaseController.php` (ou faça merge com o existente se for possível).
3. Atualize todos os controllers em `app/Controllers/Admin/` para estenderem o `BaseController` correto (se necessário).
4. Verifique se há referências hardcoded a `APPPATH` ou `ROOTPATH` nos controllers que possam estar quebradas.
5. Ajuste quaisquer `return view('Admin/...')` para garantir que resolvam corretamente com `app/Views/Admin/`.

**Teste de Integridade:**
- Execute `php spark routes` na raiz e verifique se as rotas do admin aparecem (ainda não estarão ativas, mas devem ser carregáveis).
- Se houver um teste de health check, execute-o.

---

## Prompt Etapa 4 — Migração de Views

**Objetivo:** Mover views do admin para `app/Views/Admin/`.

**Tarefas:**
1. Copie recursivamente `adm/app/Views/Admin/` para `app/Views/Admin/`.
2. Copie os layouts da raiz do admin (`layoutDash.php`, `layoutLimpo.php`, `layoutLogin.php`, `layoutSimples.php`) para `app/Views/Admin/`.
3. Renomeie conflitos se necessário (ex: `welcome_message.php` do admin → `welcome_message_admin.php`).
4. Busque e substitua em `app/Views/Admin/` quaisquer referências a paths antigos.
5. Atualize controllers em `app/Controllers/Admin/` para referenciar views corretamente.

**Teste de Integridade:**
- Tente renderizar uma view simples via spark ou teste unitário.
- Verifique que views do site público continuam funcionando.

---

## Prompt Etapa 5 — Migração de Enums, Exceptions, Traits e Commands

**Objetivo:** Mover componentes auxiliares.

**Tarefas:**
1. Copie `adm/app/Enums/*.php` → `app/Enums/Admin/`.
2. Copie `adm/app/Enums/Escolaridades/*.php` → `app/Enums/Admin/Escolaridades/`.
3. Copie `adm/app/Exceptions/*.php` → `app/Exceptions/Admin/`.
4. Copie `adm/app/Traits/*.php` → `app/Traits/Admin/`.
5. Copie `adm/app/Commands/*.php` → `app/Commands/Admin/`.
6. Atualize namespaces internos: `App\Enums` → `App\Enums\Admin`, etc.
7. Atualize todos os `use App\Enums\...` nos controllers e services para `use App\Enums\Admin\...`.
8. Faça o mesmo para Exceptions, Traits e Commands.

**Teste de Integridade:**
- Execute `php spark list` e verifique se commands do admin aparecem.
- Execute testes unitários que usam Enums ou Exceptions.

---

## Prompt Etapa 6 — Consolidação de Models

**Objetivo:** Migrar models e resolver conflitos.

**Tarefas:**
1. Com base no relatório da Etapa 1, para cada model do admin:
   - Se exclusivo do admin: copiar para `app/Models/Admin/`, alterar namespace para `App\Models\Admin`.
   - Se idêntico ao público: manter em `app/Models/`, não copiar.
   - Se diferente: copiar versão admin para `app/Models/Admin/` com namespace `App\Models\Admin`.
2. Atualize TODOS os `use App\Models\...` em `app/Controllers/Admin/` e `app/Services/Admin/` para apontar para o novo namespace.
3. Verifique se há models em `app/Common/Models/` que precisam ser acessados pelo admin e garanta que o autoload funcione.

**Teste de Integridade:**
- Execute `php spark db:table` (se disponível) para verificar se os models carregam.
- Execute testes unitários que instanciam models.
- Teste uma operação CRUD simples via controller.

---

## Prompt Etapa 7 — Consolidação de Services

**Objetivo:** Migrar services e resolver conflitos (etapa mais complexa).

**Tarefas:**
1. Com base no relatório da Etapa 1, para cada service do admin:
   - Se exclusivo: copiar para `app/Services/Admin/<subpasta>/`, namespace `App\Services\Admin\<subpasta>`.
   - Se idêntico: manter em `app/Services/`, não copiar.
   - Se diferente: copiar versão admin para `app/Services/Admin/<subpasta>/`.
2. Consolidar `app/Services/Admin/Base/` com os abstracts e traits.
3. Atualizar TODOS os `use App\Services\...` nos controllers e outros services.
4. Atualizar `app/Config/Services.php` (raiz) para incluir as factories do admin. 
   > Nota: pode ser necessário renomear métodos se houver colisão (ex: `admin.candidatosService` vs `candidatosService`).

**Teste de Integridade:**
- Execute `php spark list`.
- Execute testes unitários de services (especialmente os do Sprint 7).
- Teste funcionalidades críticas: login, grid de editais, classificação.

---

## Prompt Etapa 8 — Migração de Repositories

**Objetivo:** Mover repositories.

**Tarefas:**
1. Copie `adm/app/Repositories/*.php` → `app/Repositories/Admin/`.
2. Copie `adm/app/Repositories/Interfaces/*.php` → `app/Repositories/Admin/Interfaces/`.
3. Atualize namespaces para `App\Repositories\Admin` e `App\Repositories\Admin\Interfaces`.
4. Atualize todos os `use` que referenciam essas interfaces/implementações.
5. Atualize `app/Config/Services.php` com as factories de repositories do admin.

**Teste de Integridade:**
- Execute testes de repositories (`CandidatoRepositoryTest`, `ClassificacaoRepositoryTest`, `ContratoRepositoryTest`).

---

## Prompt Etapa 9 — Migração de Helpers

**Objetivo:** Consolidar helpers.

**Tarefas:**
1. Copie helpers exclusivos do admin (`EmailHelper`, `MyHelper`, `ordinal`) para `app/Helpers/Admin/`.
2. Atualize `app/Config/Autoload.php` para incluir esses helpers no array `$helpers`.
3. Verifique se há conflitos de nomes de funções globais.

**Teste de Integridade:**
- Carregue uma página do admin que usa helpers.
- Verifique que não há erros de função já declarada.

---

## Prompt Etapa 10 — Unificação de Configs

**Objetivo:** Consolidar configs críticas.

**Tarefas:**
1. Faça backup de `app/Config/Routes.php`, `app/Config/Services.php`, `app/Config/Autoload.php`.
2. Em `app/Config/Routes.php`, adicione um grupo `$routes->group('adm', ...)` contendo todas as rotas do admin (copiadas de `adm/app/Config/Routes.php`).
3. Em `app/Config/Services.php`, adicione as factories do admin (pode criar métodos separados ou prefixados).
4. Em `app/Config/Autoload.php`, adicione helpers do admin.
5. Copie `adm/app/Config/phpwkhtmltopdf.php` → `app/Config/phpwkhtmltopdf.php` (se não existir na raiz).
6. Ajuste `app/Config/Constants.php` se necessário.

**Teste de Integridade:**
- Execute `php spark routes` e confirme que rotas do admin aparecem com prefixo `/adm`.
- Teste uma rota do admin via navegador ou curl.
- Teste uma rota do público para garantir que não houve regressão.

---

## Prompt Etapa 11 — Migração de Tests

**Objetivo:** Consolidar testes PHPUnit.

**Tarefas:**
1. Copie `adm/tests/Services/` → `tests/Admin/Services/`.
2. Copie `adm/tests/Repositories/` → `tests/Admin/Repositories/`.
3. Atualize namespaces nos arquivos de teste (`use App\Services\Admin\...` etc.).
4. Atualize `phpunit.xml.dist` da raiz para incluir `tests/Admin/` como suite.
5. Execute `php vendor\bin\phpunit` na raiz.

**Teste de Integridade:**
- Todos os testes do Sprint 7 devem passar.
- Testes do site público também devem passar.

---

## Prompt Etapa 12 — Assets, Writable e Limpeza Final

**Objetivo:** Eliminar `adm/` e validar tudo.

**Tarefas:**
1. Migre assets de `adm/public/` → `public/adm/` (se houver assets específicos).
2. Configure `writable/` para ter subpastas `admin/` se necessário.
3. Execute testes completos: `php vendor\bin\phpunit`.
4. Teste manualmente: login no admin, dashboard, classificação, exportação PDF/XLSX.
5. Teste manualmente o site público: cadastro, consulta de editais, transparência.
6. Renomeie `adm/` para `_adm_bkp_20261021/`.
7. Execute testes novamente.
8. Se tudo passar, remova `_adm_bkp_20261021/`.
9. Atualize `AGENTS.md` para refletir a nova estrutura unificada.

**Teste de Integridade:**
- Zero erros em logs (`writable/logs/`).
- 100% dos testes passando.
- Ambas as camadas acessíveis e funcionais.

---

## Checklist Final

- [ ] Branch Git criada
- [ ] Etapa 1 concluída (relatório de conflitos)
- [ ] Etapa 2 concluída (diretórios criados)
- [ ] Etapa 3 concluída (controllers migrados)
- [ ] Etapa 4 concluída (views migradas)
- [ ] Etapa 5 concluída (enums, exceptions, traits, commands)
- [ ] Etapa 6 concluída (models consolidados)
- [ ] Etapa 7 concluída (services consolidados)
- [ ] Etapa 8 concluída (repositories migrados)
- [ ] Etapa 9 concluída (helpers migrados)
- [ ] Etapa 10 concluída (configs unificadas)
- [ ] Etapa 11 concluída (tests migrados e passando)
- [ ] Etapa 12 concluída (limpeza e validação)
- [ ] `AGENTS.md` atualizado
