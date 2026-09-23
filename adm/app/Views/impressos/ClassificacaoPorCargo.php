<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Classificação por Cargo</title>

<style>
    @page {
        margin: 1.5cm 2cm 2.5cm 2cm;
    }

    body {
        font-family: "DejaVu Sans", Helvetica, Arial, sans-serif;
        font-size: 9.5pt;
        line-height: 1.4;
        color: #333;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        page-break-inside: auto;
        margin-top: 10px;
    }

    thead {
        display: table-header-group;
    }

    tr {
        page-break-inside: avoid;
    }

    th, td {
        border: 1px solid #444;
        padding: 5px 4px;
        text-align: center;
        vertical-align: middle;
    }

    th {
        background-color: #2c3e50;
        color: #fff;
        font-size: 8.5pt;
        font-weight: bold;
    }

    td {
        font-size: 8.5pt;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .titulo {
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        margin-bottom: 4px;
        color: #2c3e50;
    }

    .subtitulo {
        text-align: center;
        font-size: 10pt;
        margin-bottom: 12px;
        color: #555;
    }

    .header-box {
        text-align: center;
        margin-bottom: 25px;
        border-bottom: 3px solid #f90;
        padding-bottom: 10px;
    }

    .header-box img {
        height: 70px;
        margin-bottom: 6px;
    }

    .header-text {
        font-size: 9pt;
        color: #444;
    }

    .header-text strong {
        display: block;
        font-size: 10pt;
        color: #2c3e50;
        margin-bottom: 2px;
    }

    .info-box {
        width: 100%;
        margin-bottom: 15px;
    }

    .info-box td {
        border: none;
        text-align: left;
        padding: 2px 0;
        font-size: 9.5pt;
    }

    .info-label {
        font-weight: bold;
        color: #2c3e50;
        width: 120px;
    }

    .posicao {
        font-weight: bold;
        font-size: 10pt;
    }

    .total {
        font-weight: bold;
        color: #c0392b;
    }

    .pcd-sim {
        font-weight: bold;
        color: #27ae60;
    }
</style>
</head>

<body>

<div class="header-box">
    <img src="<?= imageToBase64(FCPATH . 'external/img/logo/brasao.png') ?>" alt="Brasão">
    <div class="header-text">
        <strong>PREFEITURA MUNICIPAL DE DIVINÓPOLIS</strong>
        SECRETARIA MUNICIPAL DE PLANEJAMENTO, GESTÃO, CIÊNCIA E TECNOLOGIA — SEPLAG<br>
        DIRETORIA DE RECURSOS HUMANOS<br>
        <small>Avenida Paraná, nº 2.601, sala 307 – Bairro São José – Divinópolis, MG – CEP: 35.501-170</small>
    </div>
</div>

<div class="titulo">Classificação por Cargo</div>
<div class="subtitulo">
    Edital: <?= esc(substr_replace($nomeEdital, '/', -4, 0)) ?> &nbsp;|&nbsp;
    Cargo: <?= esc($nomeCargo) ?>
</div>

<table class="info-box">
    <tr>
        <td class="info-label">Data de emissão:</td>
        <td><?= esc($dataGeracao) ?></td>
        <td class="info-label" style="text-align:right;">Total de classificados:</td>
        <td style="text-align:right;"><?= count($classificacoes) ?></td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th width="6%">Pos.</th>
            <th width="26%">Candidato</th>

            <?php if (isset($usaDesempateDinamico) && $usaDesempateDinamico && !empty($configDesempate)): ?>
                <?php foreach ($configDesempate as $config): ?>
                    <th><?= esc($config->descricao ?: $config->tipoCriterio) ?></th>
                <?php endforeach; ?>
            <?php else: ?>
                <?php foreach ($colunasDinamicas as $col): ?>
                    <?php if ($col['tipo'] !== 'fixo'): ?>
                        <th><?= esc($col['label']) ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <th width="10%">Nascimento</th>
            <th width="8%">Total</th>
            <th width="6%">PCD</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($classificacoes)): ?>
            <?php foreach ($classificacoes as $c): ?>
                <tr>
                    <td class="posicao"><?= esc($c['ds_posicao']) ?></td>
                    <td align="left"><?= esc(nome_formatado($c['ds_nome_candidato'])) ?></td>

                    <?php if (isset($usaDesempateDinamico) && $usaDesempateDinamico && !empty($configDesempate)): ?>
                        <?php foreach ($configDesempate as $config): ?>
                            <?php
                                $chave = $config->chaveScore();
                                $scoreData = $c['_scores'][$chave] ?? null;
                                $valor = is_array($scoreData) ? ($scoreData['nr_valor'] ?? 0) : ($scoreData ?? 0);
                            ?>
                            <td><?= is_numeric($valor) ? number_format((float)$valor, 2, ',', '.') : esc($valor) ?></td>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php $candDados = $dadosDinamicos[$c['fk_id_candidato']] ?? []; ?>
                        <?php foreach ($colunasDinamicas as $col): ?>
                            <?php if ($col['tipo'] !== 'fixo'): ?>
                                <td>
                                    <?php
                                        $valor = $candDados[$col['chave']] ?? 0;
                                        echo is_numeric($valor) ? number_format((float)$valor, 2, ',', '.') : esc($valor);
                                    ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <td><?= date('d/m/Y', strtotime($c['dt_nascimento'])) ?></td>
                    <td class="total"><?= esc($c['nr_total_pontos']) ?></td>
                    <td>
                        <?php if (($c['ds_possui_pne'] ?? 0) == 1): ?>
                            <span class="pcd-sim">SIM</span>
                        <?php else: ?> NÃO
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <?php
                $colCount = 4;
                if (isset($usaDesempateDinamico) && $usaDesempateDinamico && !empty($configDesempate)) {
                    $colCount += count($configDesempate);
                } elseif (!empty($colunasDinamicas)) {
                    foreach ($colunasDinamicas as $col) {
                        if ($col['tipo'] !== 'fixo') $colCount++;
                    }
                }
            ?>
            <tr>
                <td colspan="<?= $colCount ?>">Nenhuma classificação encontrada.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
