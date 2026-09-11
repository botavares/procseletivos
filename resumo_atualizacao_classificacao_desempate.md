# Resumo da Atualizacao - Classificacao com Desempate Dinamico

> **Data:** 09/09/2026  
> **Objetivo:** Permitir que cada cargo tenha sua propria regra de desempate configuravel no banco de dados, eliminando a ordenacao hard-coded e possibilitando que a equipe de expedicao de processos seletivos defina os criterios sem alterar codigo.

---

## 1. Estruturas Criadas no Banco de Dados

### 1.1. Tabela `tb_cargos_desempates_config`

Responsavel por armazenar a configuracao de desempate por cargo.

| Coluna | Tipo | Descricao |
|--------|------|-----------|
| `pk_id_desempate` | INT PK AI | ID do criterio |
| `fk_id_cargo` | INT FK | Cargo ao qual pertence |
| `ds_ordem` | INT | Ordem de aplicacao (1, 2, 3...) |
| `ds_tipo_criterio` | VARCHAR(30) | Tipo do criterio (ver enum abaixo) |
| `fk_id_referencia` | INT NULL | ID da experiencia/escolaridade/curso especifico |
| `ds_direcao` | VARCHAR(4) | 'DESC' (maior primeiro) ou 'ASC' (menor primeiro) |
| `ds_descricao` | VARCHAR(255) | Texto explicativo para exibicao |
| `ds_parametro_extra` | VARCHAR(255) NULL | Reservado para uso futuro |
| `dt_criacao` | TIMESTAMP | Data de criacao |
| `dt_atualizacao` | TIMESTAMP | Data de atualizacao |

**Enum de `ds_tipo_criterio`:**

| Valor | Significado | Usa `fk_id_referencia` |
|-------|-------------|------------------------|
| `PONTUACAO_TOTAL` | Pontuacao geral (soma de tudo) | Nao |
| `PONTUACAO_EXPERIENCIAS` | Total em experiencias (todas) | Nao |
| `PONTUACAO_ESCOLARIDADES` | Total em escolaridades (todas) | Nao |
| `PONTUACAO_CRITERIOS_ADICIONAIS` | Total em criterios adicionais | Nao |
| `PONTUACAO_CURSOS_APERFEICOAMENTOS` | Total em aperfeicoamentos | Nao |
| `PONTUACAO_GRADUACAO` | Total em graduacao | Nao |
| `PONTUACAO_POS_GRADUACAO` | Total em pos-graduacao | Nao |
| `PONTUACAO_MESTRADO` | Total em mestrado | Nao |
| `PONTUACAO_DOUTORADO` | Total em doutorado | Nao |
| `EXPERIENCIA_ESPECIFICA` | Valor de uma experiencia especifica | Sim |
| `ESCOLARIDADE_ESPECIFICA` | Valor de uma escolaridade especifica | Sim |
| `APERFEICOAMENTO_ESPECIFICO` | Valor de um curso de aperfeicoamento | Sim |
| `CRITERIO_ADICIONAL` | Valor de um criterio adicional | Sim |
| `IDADE` | Data de nascimento (mais velho primeiro) | Nao |
| `PNE` | Possui deficiencia | Nao |

### 1.2. Tabela `tb_classificacao_scores`

Responsavel por persistir os scores dinamicos de cada candidato classificado, permitindo que a transparencia publica e a area administrativa possam explicar o desempate.

| Coluna | Tipo | Descricao |
|--------|------|-----------|
| `pk_id_score` | INT PK AI | ID do score |
| `fk_id_classificacao` | INT FK | Liga a `tb_classificacao` |
| `ds_tipo_score` | VARCHAR(50) | Tipo do score (ex: `EXPERIENCIA_ESPECIFICA`) |
| `ds_chave_score` | VARCHAR(100) | Chave unica (ex: `EXPERIENCIA_ESPECIFICA_15`) |
| `ds_label` | VARCHAR(255) | Label amigavel para exibicao |
| `nr_valor` | DECIMAL(10,2) | Valor numerico do score |
| `ds_valor_texto` | VARCHAR(255) NULL | Valor em texto, quando aplicavel |
| `dt_criacao` | TIMESTAMP | Data de criacao |

---

## 2. Arquivos Criados (Novos)

### 2.1. Migrations

| Arquivo | Descricao |
|---------|-----------|
| `app/Database/Migrations/2026-09-09-130000_CreateTbCargosDesempatesConfig.php` | Migration da tabela `tb_cargos_desempates_config` |
| `app/Database/Migrations/2026-09-09-130001_CreateTbClassificacaoScores.php` | Migration da tabela `tb_classificacao_scores` |

### 2.2. Models

| Arquivo | Descricao |
|---------|-----------|
| `adm/app/Models/CargosDesempateConfigModel.php` | Model da nova tabela de configuracao |
| `adm/app/Models/ClassificacaoScoreModel.php` | Model da nova tabela de scores dinamicos |

### 2.3. DTOs (Data Transfer Objects)

| Arquivo | Descricao |
|---------|-----------|
| `adm/app/Services/Classificacao/DTO/DesempateConfigDTO.php` | DTO de configuracao de desempate com metodo `chaveScore()` |
| `adm/app/Services/Classificacao/DTO/ResultadoClassificacaoDTO.php` | DTO expandido com propriedade `$scores` e metodo `scoresParaPersistir()` |

### 2.4. Services de Classificacao

| Arquivo | Descricao |
|---------|-----------|
| `adm/app/Services/Classificacao/DesempateConfigService.php` | Busca configuracao de desempate do banco |
| `adm/app/Services/Classificacao/CandidatoScoreBuilderService.php` | Calcula scores dinamicos em batch (sem queries no loop) |
| `adm/app/Services/Classificacao/OrdenacaoDinamicaService.php` | Ordena resultados conforme configuracao via `usort` dinamico |

### 2.5. Controller de Administracao

| Arquivo | Descricao |
|---------|-----------|
| `adm/app/Controllers/CargosDesempateConfig.php` | Controller da tela de configuracao de desempate |

**Endpoints disponiveis:**
- `GET /CargosDesempateConfig` - Tela principal
- `GET /CargosDesempateConfig/listar/{idCargo}` - Lista configuracao (JSON)
- `POST /CargosDesempateConfig/salvar` - Salva um criterio
- `POST /CargosDesempateConfig/reordenar` - Atualiza ordem
- `DELETE /CargosDesempateConfig/excluir/{idDesempate}` - Remove um criterio
- `GET /CargosDesempateConfig/referencias/{idCargo}?tipo=experiencia` - Busca referencias disponiveis

### 2.6. View de Administracao

| Arquivo | Descricao |
|---------|-----------|
| `adm/app/Views/pages/cargos/DesempateConfig_view.php` | Tela de configuracao com formulario de adicao |

### 2.7. Teste CLI

| Arquivo | Descricao |
|---------|-----------|
| `adm/app/Commands/TesteClassificacaoDesempate.php` | Comando CLI `php spark teste:classificacao` para validar o sistema |

---

## 3. Arquivos Alterados

### 3.1. Area Administrativa (adm/)

| Arquivo | Alteracao |
|---------|-----------|
| `adm/app/Services/Classificacao/ClassificacaoProcessorService.php` | Adicionada logica de configuracao dinamica com **backward compatibility**. Cargos sem configuracao continuam usando a regra fixa original. |
| `adm/app/Services/Classificacao/ClassificacaoPersistService.php` | Agora suporta insert **individual** quando ha scores dinamicos (para obter o ID da classificacao e inserir scores), mantendo o insertBatch para regra fixa. |
| `adm/app/Services/Classificacao/ClassificacaoService.php` | Coordena o fluxo de reprocessamento detectando automaticamente se o cargo possui desempate dinamico. |
| `adm/app/Controllers/Classificacoes.php` | Ajustado para montar titulos dinamicos da tabela quando o cargo possui configuracao de desempate. |
| `adm/app/Models/ClassificacaoModel.php` | Metodo `listarClassificacao` agora carrega automaticamente os scores dinamicos de cada candidato. |
| `adm/app/Views/pages/candidatos/Classificacoes_view.php` | Renderizacao dinamica de colunas conforme configuracao de desempate. |

### 3.2. Area do Cidadao (app/)

| Arquivo | Alteracao |
|---------|-----------|
| `app/Services/Transparencia/TransparenciaService.php` | Agora detecta se o cargo tem desempate dinamico e faz `LEFT JOIN` com `tb_classificacao_scores` para exibir as colunas dinamicas. Caso contrario, mantem o comportamento atual (colunas fixas + colunas_ocultas). |
| `app/Views/pages/Transparencia/partials/_tabela_candidatos_view.php` | Renderizacao dinamica de colunas na transparencia publica. Se houver desempate dinamico, exibe as colunas dos scores configurados. |

---

## 4. Logica de Desempate Implementada

### 4.1. Fluxo do Reprocessamento

```
Usuario (Admin) solicita reprocessamento
         |
         ▼
ClassificacaoService::reprocessar(edital, cargo)
         |
         ▼
ClassificacaoProcessorService::processar(edital, cargo)
         |
    1. Busca configuracao de desempate do cargo
    2. Se houver config (dinamico):
         |
         ├── Carrega dados em batch (experiencias, escolaridades, etc.)
         ├── Para cada candidato:
         │   ├── Calcula pontuacoes (PontuacaoCalculatorService)
         │   ├── Calcula scores dinamicos (CandidatoScoreBuilderService)
         │   └── Preenche $scores no DTO
         ├── Ordena via OrdenacaoDinamicaService
         └── Retorna array com '_scores'
    3. Se nao houver config (fixo):
         |
         ├── Para cada candidato:
         │   └── Calcula pontuacoes (regra fixa)
         ├── Ordena pela regra hard-coded
         └── Retorna array SEM '_scores'
         |
         ▼
ClassificacaoPersistService::salvar(edital, cargo, dados)
         |
    1. Limpa classificacao anterior (CASCADE limpa scores)
    2. Se houver '_scores' em algum candidato:
         └── Insert individual (um por candidato) + insert dos scores
    3. Se nao houver '_scores':
         └── Insert em lote (insertBatch) - performance
         |
         ▼
    Sucesso
```

### 4.2. Calculo dos Scores

O `CandidatoScoreBuilderService` calcula os scores da seguinte forma:

- **Totais agregados:** Soma todos os registros da categoria (experiencias, escolaridades, etc.)
- **Especificos:** Filtra pelo `fk_id_referencia` e aplica `ds_quantidade × ds_multiplicador`
- **CHECKBOX:** Ausencia de registro = score 0. Presenca = `1 × ds_multiplicador`
- **INPUT/SELECT:** Score = `ds_quantidade × ds_multiplicador`

### 4.3. Backward Compatibility

**Cargos sem configuracao em `tb_cargos_desempates_config` continuam usando a regra fixa:**

1. Maior pontuacao total
2. Maior pontuacao em experiencias
3. Maior pontuacao em doutorado
4. Maior pontuacao em mestrado
5. Maior pontuacao em pos-graduacao
6. Candidato mais velho

---

## 5. Testes Realizados

### 5.1. Backward Compatibility
- **Edital:** 26
- **Cargo:** 5 (Assistente Social)
- **Configuracao:** Nenhuma (usa regra fixa)
- **Resultado:** 119 candidatos processados com sucesso
- **Posicao 1:** UILIANE FABIENE PEREIRA (75 pts)
- **Status:** ✅ PASSOU

### 5.2. Desempate Dinamico
- **Edital:** 26
- **Cargo:** 5 (Assistente Social)
- **Configuracao:** PONTUACAO_TOTAL (DESC), PONTUACAO_EXPERIENCIAS (DESC)
- **Resultado:** 119 candidatos processados com sucesso
- **Posicao 1:** LUCIMEIRE ANA DOS SANTOS RIBEIRO (50 pts)
- **Scores persistidos:** 238 scores (2 por candidato)
- **Exemplo de score:** Chave `PONTUACAO_TOTAL`, Valor `50.00`, Tipo `PONTUACAO`
- **Status:** ✅ PASSOU

### 5.3. Verificacao de Sintaxe
- Todos os arquivos PHP passaram na verificacao `php -l`
- Sem erros de sintaxe

---

## 6. Proximos Passos Recomendados

### 6.1. Configuracao do Sistema

1. **Adicionar rotas** no arquivo `adm/app/Config/Routes.php`:
   ```php
   $routes->get('CargosDesempateConfig', 'CargosDesempateConfig::index');
   $routes->get('CargosDesempateConfig/listar/(:num)', 'CargosDesempateConfig::listar/$1');
   $routes->post('CargosDesempateConfig/salvar', 'CargosDesempateConfig::salvar');
   $routes->post('CargosDesempateConfig/reordenar', 'CargosDesempateConfig::reordenar');
   $routes->delete('CargosDesempateConfig/excluir/(:num)', 'CargosDesempateConfig::excluir/$1');
   $routes->get('CargosDesempateConfig/referencias/(:num)', 'CargosDesempateConfig::referencias/$1');
   ```

2. **Adicionar menu** no sidemenu da area administrativa para acesso rapido a tela de configuracao.

### 6.2. Testes em Producao

3. **Testar com cargo real** que precise de desempate especifico (ex: Agrimensor).
4. **Configurar os criterios** na tela admin para o cargo desejado.
5. **Reprocessar a classificacao** e verificar se a ordem obedece os criterios configurados.
6. **Verificar a transparencia publica** para confirmar que as colunas dinamicas aparecem corretamente.

### 6.3. Documentacao e Treinamento

7. **Documentar para a equipe de expedicao** como configurar os criterios na nova tela.
8. **Explicar os tipos de campo** (CHECKBOX, INPUT, SELECT) e como afetam o calculo de scores.
9. **Criar exemplos praticos** de configuracao para os principais cargos.

---

## 7. Consideracoes Importantes

### 7.1. Performance
- O `CandidatoScoreBuilderService` carrega todos os dados em **batch** antes do loop de candidatos.
- Nao ha queries dentro do loop principal.
- Quando nao ha desempate dinamico, o sistema usa `insertBatch` (mais rapido).

### 7.2. Integridade dos Dados
- A tabela `tb_classificacao_scores` possui `FOREIGN KEY` com `ON DELETE CASCADE`.
- Ao limpar a classificacao (`DELETE tb_classificacao`), os scores sao automaticamente removidos.
- A transacao garante consistencia entre `tb_classificacao` e `tb_classificacao_scores`.

### 7.3. Seguranca
- O controller `CargosDesempateConfig` verifica se o usuario esta logado.
- Todas as operacoes usam prepared statements (CodeIgniter Query Builder).

---

*Documento gerado em: 09/09/2026*  
*Sistema: Processos Seletivos - Prefeitura Municipal de Divinopolis*
