# Cálculo de Força de Trabalho — Sprint Processos Seletivos (Refatoração Completa)

> **Desenvolvedor:** 1 (pleno PHP/CodeIgniter 4)
> **Base:** 8 horas/dia útil
> **Data de emissão:** 26/10/2026

---

## Resumo Executivo

| Total Estimado | Dias Úteis | Semanas | Meses (22 dias) |
|----------------|------------|---------|-----------------|
| **212 horas**  | **26,5**   | **~5,3**| **~1,2**        |

---

## Etapa 1: Modelagem e DDL do Banco
**Tempo:** 12 horas (1,5 dias)

- Criar `tb_processos_documentos`.
- Criar `tb_processos_cargos` (com vagas, carga_horaria, remuneração específicos do processo).
- Adicionar campos na `tb_processos` (`dt_homologacao`, status calculado, etc.).
- Alterar `tb_cargos_experiencias`, `escolaridades`, `critérios`, `aperfeiçoamentos` para vincular ao processo (ou criar tabelas intermediárias).
- Criar tabela de `status_log` (rastreabilidade da rotina automática).
- Revisão de índices e foreign keys.
- Migrations e seeders do CI4.

---

## Etapa 2: Enum + Lógica de Status do Processo
**Tempo:** 8 horas (1 dia)

- Criar enum `SituacaoProcessoSeletivo` (AGENDADO, INSC_ABERTAS, ENCERRADAS, RES_PUBL, VIGENTE, ENCERRADO).
- Service `StatusCalculatorService` com as 6 regras de data.
- Regra de prorrogação (validade × 2).
- Lógica: homologação + meses de validade vs data atual.
- Cálculo de comparação de datas.

---

## Etapa 3: Rotina Automática (Cron/Job)
**Tempo:** 16 horas (2 dias)

- Comando CLI do CI4: `php spark processos:atualizar-status`.
- Query otimizada para atualizar status em massa sem disparar pelo usuário.
- Transação com rollback em caso de erro.
- Log de execução da rotina.
- Configuração do servidor (cron Linux ou Agendador Windows) às 00h.
- Testes isolados da rotina.

---

## Etapa 4: Tela Pública — Processos Seletivos
**Tempo:** 12 horas (1,5 dias)

- Controller `Publica/ProcessosSeletivos`.
- View com cards/tabela mostrando status calculado.
- Filtros por secretaria, situação, período.
- Paginação.
- Layout responsivo para cidadão.

---

## Etapa 5: Formulário Processo Seletivo (Refinamentos)
**Tempo:** 8 horas (1 dia)

- Adicionar campo `dt_homologacao` ao form e controller.
- Integrar botões de acesso: Documentos, Cargos, Classificatórios, Desempates.
- Lógica condicional dos botões (ex: Classificatório só aparece se houver cargo vinculado).
- Ajustar DTO, Model, Validation Rules.
- Manter dados salvos e permitir edição contínua.

---

## Etapa 6: Módulo Documentos do Processo
**Tempo:** 16 horas (2 dias)

- Controller `Admin/ProcessosDocumentos` (listar por processo).
- View de listagem.
- Formulário de cadastro/edição.
- Restrição: apenas **EDITAL DE ABERTURA** para processo novo.
- Upload e validação de PDF.
- Acesso público dos documentos.
- Service de regras de negócio.

---

## Etapa 7: Módulo Cargos do Processo (Novo)
**Tempo:** 20 horas (2,5 dias)

- Controller `Admin/ProcessosCargos`.
- Service + Repository + Model (`tb_processos_cargos`).
- View de listagem por processo.
- Formulário com vagas, carga_horaria, remuneração, observações.
- Associação cargo-existente + dados específicos do processo.
- CRUD completo.
- Validações (ex: não permitir cargo duplicado no mesmo processo).

---

## Etapa 8: Dados Classificatórios (Migração)
**Tempo:** 28 horas (3,5 dias) — *Mais complexo*

- Alterar estrutura: chave composta `fk_id_processo` + `fk_id_cargo`.
- Refatorar controller `Admin/Classificacoes`.
- Refatorar services e repositories existentes.
- View listagem: mostrar apenas cargos do processo selecionado.
- Ocultar botão "Adicionar" quando vindo do formulário de processos.
- Formulário de lançamento de pontuações adaptado.
- Ajustar queries de relatórios e exports (Excel/PDF).
- Testes de integração pesados.

---

## Etapa 9: Configuração de Desempates (Migração)
**Tempo:** 20 horas (2,5 dias)

- Alterar `tb_cargos_desempates_config` para incluir `fk_id_processo`.
- Controller `Admin/ProcessosDesempateConfig`.
- Service e Model adaptados.
- Tela inicial mostrando apenas cargos do processo.
- Formulário existente adaptado para nova chave composta.
- Refatorar funções JS do frontend.
- Manter regra do `PONTUACAO_TOTAL` em primeiro.

---

## Etapa 10: Pontuação/Regras de Cargos por Processo
**Tempo:** 24 horas (3 dias) — *Mais complexo*

- Adaptar `tb_cargos_experiencias`, `escolaridades`, `critérios`, `aperfeiçoamentos`.
- Decisão arquitetural: duplicar registros por processo OU criar `tb_processos_cargos_*`.
- Ajustar todas as queries de pontuação para considerar o processo.
- Service de cálculo de pontuação adaptado.
- Testes unitários críticos (quebra de lógica antiga).

---

## Etapa 11: Testes Unitários e Integração
**Tempo:** 24 horas (3 dias)

- Tests para `StatusCalculatorService`.
- Tests para Rotina Automática.
- Tests para `ProcessosDocumentos`.
- Tests para `ProcessosCargos`.
- Tests para Classificação adaptada.
- Tests para Desempate adaptado.
- Tests para pontuação por processo.

---

## Etapa 12: Ajustes, Bugs e Refinamentos (Buffer)
**Tempo:** 24 horas (3 dias)

- Testes manuais no ambiente.
- Correções de layout e responsividade.
- Ajustes de performance em queries.
- Documentação do fluxo.
- Deploy e configuração do cron no servidor.

---

## Tabela Resumida

| # | Etapa | Horas | Dias Úteis |
|---|-------|-------|------------|
| 1 | Modelagem/DDL | 12h | 1,5 |
| 2 | Enum + Status | 8h | 1,0 |
| 3 | Rotina Automática | 16h | 2,0 |
| 4 | Tela Pública | 12h | 1,5 |
| 5 | Form Processo | 8h | 1,0 |
| 6 | Documentos | 16h | 2,0 |
| 7 | Cargos do Processo | 20h | 2,5 |
| 8 | Classificatórios | 28h | 3,5 |
| 9 | Desempates | 20h | 2,5 |
| 10 | Pontuação | 24h | 3,0 |
| 11 | Testes | 24h | 3,0 |
| 12 | Buffer | 24h | 3,0 |
| **TOTAL** | | **212h** | **26,5** |

---

## Observações Críticas

1. **Itens 8, 9 e 10** são os mais perigosos pois envolvem **migração de dados e regras de negócio já existentes**. Recomenda-se backup completo antes.
2. **A modelagem do banco (Etapa 1)** é pré-requisito absoluto para todas as demais. Deve ser feita e validada primeiro.
3. **Caso seja necessário migrar dados existentes** da estrutura antiga (`tb_edital`, `tb_editais_cargos`, etc.) para a nova, adicione **+8h a +16h**.
4. **A rotina automática** exige acesso ao servidor para configurar o cron/schedule. Se não houver acesso, adicione **+4h** para criar uma alternativa (ex: disparo via endpoint + chamada externa).
5. **Testes unitários** são obrigatórios para garantir que a migração não quebre o cálculo de classificação existente.

---

## Ordem Sugerida de Execução

```
1 → 2 → 3  (base estrutural)
    ↓
5 → 6 → 7  (CRUDs dependentes entre si)
    ↓
4          (tela pública, pode ser paralela após Etapa 2)
    ↓
10 → 8 → 9 (lógica de pontuação + classificação + desempate)
    ↓
11 → 12    (testes e ajustes finais)
```

---

## Situações de Status do Processo (Regras de Negócio)

| Situação | Condição |
|----------|----------|
| **AGENDADO** | Processo cadastrado, mas datas de inscrição ainda não iniciaram. |
| **INSCRIÇÕES ABERTAS** | Data atual está entre `dt_inicio_inscricao` e `dt_fim_inscricao`. |
| **ENCERRADAS** | Passou da data de fim das inscrições e **não há** data de publicação de resultado. |
| **RESULTADO PUBLICADO** | Existe data de publicação de resultado (`dt_divulga_resultado`), mas **não há** data de homologação. |
| **VIGENTE** | Existe data de homologação. Data atual <= (`dt_homologacao` + `nm_meses_validade`). Se prorrogado, validade é o dobro. |
| **ENCERRADO** | Existe data de homologação. Data atual > (`dt_homologacao` + `nm_meses_validade`). Se prorrogado, validade é o dobro. |

> **Nota:** Status VIGENTE e ENCERRADO devem ser exibidos na seção "Editais (Processos Seletivos)" e **não** na página inicial.

---

*Documento gerado automaticamente pelo agente de IA em 26/10/2026.*
