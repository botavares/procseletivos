<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title><?= esc($titulo) ?></title>

<style>
    @page {
        margin: 1.5cm 2cm 2.5cm 2cm;
    }

    body {
        font-family: "DejaVu Sans", Helvetica, Arial, sans-serif;
        font-size: 10pt;
        line-height: 1.5;
    }

    /* ===== MARCA D’ÁGUA (FORMA CORRETA) ===== */
    .marca-agua {
        position: fixed;
        top: 30%;
        left: 10%;
        width: 150mm;
        height: 127mm;
        background-image: url('<?= $fundo ?>');
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        opacity: 0.12;
        z-index: -1000;
    }

    /* ===== TABELA ===== */
    table {
        width: 100%;
        border-collapse: collapse;
        page-break-inside: auto;
    }

    thead {
        display: table-header-group;
    }

    tr {
        page-break-inside: avoid;
    }

    th, td {
        border: 1px solid #000;
        padding: 6px;
    }

    th {
        background-color: #f0f0f0;
        font-size: 9.5pt;
    }

    /* ===== RODAPÉ ===== */
    footer {
       height:50px;
					position: fixed;
					bottom: 0;
					left: 0;
					width: 100%;
					text-align: center;
					font-size: 10px;
					color: gray;
					border-top: 1px solid #ddd;
					padding: 8px 0;
    }

    footer .pagina:after {
        content: counter(page) " de " counter(pages);
    }

    .titulo {
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .subtitulo {
        text-align: center;
        font-size: 11pt;
        margin-bottom: 20px;
    }
</style>
</head>

<body>

<div class="marca-agua"></div>

<footer>
    <!--
    <div>
        Secretaria Municipal de Planejamento, Gestão, Ciência e Tecnologia — SEPLAG
    </div>
    <div>
        Relatório emitido em <?= date('d/m/Y H:i') ?> —
        
    </div>
-->
</footer>

<div style="text-align: center; margin-bottom: 40px;">
    <div style="display: inline-block; vertical-align: middle; width: 90px;">
        <img src="<?= $brasao ?>" alt="Brasão" style="height:80px;">
    </div>
    <div style="display: inline-block; vertical-align: middle; width: 75%; border-top: 15px solid #f90;">
        <h2 style="margin: 0; font-size: 10px; text-align: left;">
            SECRETARIA MUNICIPAL DE PLANEJAMENTO, GESTÃO, CIÊNCIA E TECNOLOGIA - SEPLAG
        </h2>
        <h2 style="margin: 0; font-size: 10px; text-align: left;">
            DIRETORIA DE RECURSOS HUMANOS
        </h2>
        <p style="margin: 0; font-size: 9px; text-align: left;">
            Avenida Paraná, nº 2.601, sala 307 – Bairro São José – Divinópolis, MG – CEP: 35.501-170
        </p>
        <p style="margin: 0; font-size: 9px; text-align: left;">
            (37) 3229-8154 – prefeituradivinopolis.estagio@gmail.com
        </p>
    </div>
</div>

<div class="titulo"><?= esc($titulo) ?></div>
<div class="subtitulo">Curso: <?= esc($curso) ?></div>

<table>
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="50%">Nome do Candidato</th>
            <th width="15%">Período do Curso</th>
            <th width="20%">Nascimento</th>
            <th width="20%">Telefone Celular</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($candidatos)): ?>
            <?php $i = 1; foreach ($candidatos as $candidato): ?>
                <tr>
                    <td align="center"><?= $i++ ?></td>
                    <td><?= esc(nome_formatado($candidato->ds_nome)) ?></td>
                    <td align="center"><?= esc($candidato->ds_periodo) ?></td>
                    <td align="center"><?= date('d/m/Y', strtotime($candidato->ds_nascimento)) ?></td>
                    <td align="center"><?= esc($candidato->ds_celular ?? 'Não informado') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" align="center">Nenhum candidato encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<div style="height: 2.2cm;"></div>
</body>
</html>
