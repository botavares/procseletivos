<?php
$candidatos = $dados ?? [];
$paginacao  = $paginacao ?? [];
$filtros    = $filtros ?? [];
$colunasOcultas = $colunas_ocultas ?? [];

$paginaAtual = $paginacao['paginaAtual'] ?? 1;
$totalPaginas = $paginacao['totalPaginas'] ?? 1;
$inicio = $paginacao['inicio'] ?? 1;
$fim = $paginacao['fim'] ?? 1;

function colunaVisivel(string $nome, array $ocultas): bool {
    return !in_array($nome, $ocultas, true);
}
?>

<div data-ajax-fragment>
    <div class="table-header">
        <div class="top-bar">
            <div class="table-title">Classificação dos Candidatos</div>
        </div>
    </div>
    <table class="br-table">
        <caption>Título da Tabela</caption>
        <thead>
        <tr>
            <th>Class</th>
            <th width="30%">Nome</th>
            <th width="20%">Cargo</th>
            <th>Edital</th>
            <?php if (colunaVisivel('experiencias', $colunasOcultas)): ?><th>Pts Experiência</th><?php endif; ?>
            <?php if (colunaVisivel('doutorado', $colunasOcultas)): ?><th>Pts Doutorado</th><?php endif; ?>
            <?php if (colunaVisivel('mestrado', $colunasOcultas)): ?><th>Pts Mestrado</th><?php endif; ?>
            <?php if (colunaVisivel('posgraduacao', $colunasOcultas)): ?><th>Pts Pós Graduação</th><?php endif; ?>
            <?php if (colunaVisivel('graduacao', $colunasOcultas)): ?><th>Pts Graduação</th><?php endif; ?>
            <?php if (colunaVisivel('aperfeicoamentos', $colunasOcultas)): ?><th>Pts Cursos</th><?php endif; ?>
            <th>Nascimento</th>
            <th>Pontuação</th>
            <th>Situação</th>
        </tr>
    </thead>
    <tbody>

    <?php if (empty($candidatos)): ?>
        <?php
            $colspan = 9 - count($colunasOcultas);
        ?>
        <tr>
            <td colspan="<?= $colspan ?>">Nenhum registro encontrado</td>
        </tr>
    <?php else: ?>
        <?php foreach ($candidatos as $c): ?>

<tr>
    <td><?= esc($c->ds_posicao) ?></td>
    <td><?= esc($c->ds_nome_candidato) ?></td>
    <td><?= esc($c->ds_nome_cargo) ?></td>
    <td><?= esc($c->ds_nome_edital) ?></td>
    <?php if (colunaVisivel('experiencias', $colunasOcultas)): ?><td><?= esc($c->nr_total_experiencias) ?></td><?php endif; ?>
    <?php if (colunaVisivel('doutorado', $colunasOcultas)): ?><td><?= esc($c->nr_total_doutorado) ?></td><?php endif; ?>
    <?php if (colunaVisivel('mestrado', $colunasOcultas)): ?><td><?= esc($c->nr_total_mestrado) ?></td><?php endif; ?>
    <?php if (colunaVisivel('posgraduacao', $colunasOcultas)): ?><td><?= esc($c->nr_total_posgraduacao) ?></td><?php endif; ?>
    <?php if (colunaVisivel('graduacao', $colunasOcultas)): ?><td><?= esc($c->nr_total_graduacao) ?></td><?php endif; ?>
    <?php if (colunaVisivel('aperfeicoamentos', $colunasOcultas)): ?><td><?= esc($c->nr_total_aperfeicoamentos) ?></td><?php endif; ?>
    <td><?= date('d/m/Y', strtotime($c->dt_nascimento)) ?></td>
    <td><?= esc($c->nr_total_pontos) ?></td>
    <td><?= esc($c->ds_situacao) ?></td>
</tr>

<?php endforeach; ?>
    <?php endif; ?>

    </tbody>
</table>

<?php if ($totalPaginas > 1): ?>
<nav class="br-pagination" aria-label="Paginação">
<ul class="pagination-list">

<!-- PRIMEIRA -->
<li>
<?php if ($paginaAtual > 1): ?>
<a class="br-button circle" href="?page=1&curso=<?= esc($filtros['curso'] ?? '') ?>">
  &laquo;
</a>
<?php else: ?>
<button class="br-button circle" disabled>&laquo;</button>
<?php endif; ?>
</li>

<!-- ANTERIOR -->
<li>
<?php if ($paginaAtual > 1): ?>
<a class="br-button circle" href="?page=<?= $paginaAtual - 1 ?>&curso=<?= esc($filtros['curso'] ?? '') ?>">
  &lsaquo;
</a>
<?php else: ?>
<button class="br-button circle" disabled>&lsaquo;</button>
<?php endif; ?>
</li>

<!-- PÁGINAS NUMÉRICAS -->
<?php for ($i = $inicio; $i <= $fim; $i++): ?>
<li>
<a class="br-button <?= $i === $paginaAtual ? 'primary' : '' ?>"
   href="?page=<?= $i ?>&curso=<?= esc($filtros['curso'] ?? '') ?>">
   <?= $i ?>
</a>
</li>
<?php endfor; ?>

<!-- PRÓXIMA -->
<li>
<?php if ($paginaAtual < $totalPaginas): ?>
<a class="br-button circle" href="?page=<?= $paginaAtual + 1 ?>&curso=<?= esc($filtros['curso'] ?? '') ?>">
  &rsaquo;
</a>
<?php else: ?>
<button class="br-button circle" disabled>&rsaquo;</button>
<?php endif; ?>
</li>

<!-- ÚLTIMA -->
<li>
<?php if ($paginaAtual < $totalPaginas): ?>
<a class="br-button circle" href="?page=<?= $totalPaginas ?>&curso=<?= esc($filtros['curso'] ?? '') ?>">
  &raquo;
</a>
<?php else: ?>
<button class="br-button circle" disabled>&raquo;</button>
<?php endif; ?>
</li>

</ul>
</nav>
<?php endif; ?>

</div>
