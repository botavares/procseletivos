# Relatório de Conflitos — Sprint 8 (Unificação de Estrutura)

Gerado em: 2026-10-21 13:11:11

## BaseController

- Status: **different**
- Tamanho app: 1684 bytes | adm: 1747 bytes
- Linhas divergentes: 3

## Models Comuns (app/Models vs adm/app/Models)

| Arquivo | Status | Tamanho App | Tamanho Adm | Linhas Dif |
|---------|--------|-------------|-------------|------------|
| AperfeicoamentosModel.php | different | 1588 | 1491 | 52 |
| CandidatosModel.php | different | 1523 | 8197 | 44 |
| CargosAperfeicoamentosModel.php | different | 869 | 1417 | 32 |
| CargosCriteriosAdicionaisModel.php | different | 873 | 1761 | 33 |
| CargosDesempateConfigModel.php | different | 1573 | 1047 | 57 |
| CargosEscolaridadesModel.php | different | 872 | 1690 | 33 |
| CargosExperienciasModel.php | different | 867 | 1612 | 33 |
| CargosModel.php | different | 1677 | 1521 | 55 |
| ClassificacaoScoreModel.php | different | 2125 | 1651 | 75 |
| CriteriosAdicionaisModel.php | different | 1682 | 502 | 53 |
| DadosContratosModel.php | different | 6791 | 12952 | 123 |
| EditaisCandidatosModel.php | different | 895 | 1227 | 35 |
| EditaisCargosModel.php | different | 1994 | 526 | 57 |
| EditaisModel.php | different | 3462 | 2747 | 104 |
| EscolaridadesModel.php | different | 1803 | 534 | 53 |
| ExperienciasModel.php | different | 1686 | 495 | 47 |
| ProtocolosModel.php | different | 1002 | 1977 | 31 |
| SetoresModel.php | different | 240 | 215 | 9 |
| UsuarioModel.php | different | 1700 | 2585 | 49 |
| VerificadorModel.php | different | 239 | 367 | 9 |

## Services Comuns (raiz app/Services vs adm/app/Services)

| Arquivo | Status | Tamanho App | Tamanho Adm | Linhas Dif |
|---------|--------|-------------|-------------|------------|
| AperfeicoamentoService.php | different | 3557 | 1721 | 96 |
| AbstractEmailService.php | identical | 890 | 890 | 0 |
| AbstractFormService.php | different | 795 | 809 | 16 |
| AbstractGridService.php | identical | 1113 | 1113 | 0 |
| AbstractUploadService.php | identical | 618 | 618 | 0 |
| CandidatoScoreBuilderService.php | different | 13280 | 19285 | 310 |
| DesempateConfigService.php | different | 1317 | 1381 | 35 |
| DesempateConfigDTO.php | different | 1943 | 1985 | 37 |
| ResultadoClassificacaoDTO.php | different | 1280 | 1700 | 39 |
| OrdenacaoDinamicaService.php | different | 1790 | 4061 | 58 |
| CriterioAdicionalService.php | different | 3627 | 1730 | 95 |
| EscolaridadeService.php | different | 3429 | 1675 | 95 |
| ExperienciaService.php | different | 3127 | 1667 | 87 |

## Helpers Comuns

- **MyHelper_helper.php**: different
- **Principal_helper.php**: different
- **WKPDF.php**: identical

## Configs Comuns

| Arquivo | Status | Tamanho App | Tamanho Adm | Linhas Dif |
|---------|--------|-------------|-------------|------------|
| Config/services.php | different | 13981 | 17153 | 400 |
| Config/routes.php | different | 3071 | 19289 | 75 |

## Resumo

- **Models comuns:** 20 (idênticos: 0, diferentes: 20)
- **Services comuns (raiz):** 13 (idênticos: 3, diferentes: 10)
- **BaseController:** different
- **Helpers comuns:** 3
