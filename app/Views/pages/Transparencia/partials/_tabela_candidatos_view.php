<?php
$candidatos = $dados ?? [];
$paginacao  = $paginacao ?? [];
$filtros    = $filtros ?? [];
$usaDesempateDinamico = $usa_desempate_dinamico ?? false;
$colunasDinamicas = $colunas_dinamicas ?? [];
$colunasOcultas = $colunas_ocultas ?? [];
$criteriosAdicionais = $criterios_adicionais ?? [];

$paginaAtual = $paginacao['paginaAtual'] ?? 1;
$totalPaginas = $paginacao['totalPaginas'] ?? 1;
$inicio = $paginacao['inicio'] ?? 1;
$fim = $paginacao['fim'] ?? 1;

// Mapeamento das colunas fixas que podem ser ocultas
$mapaColunasFixas = [
    'experiencias'    => ['label' => 'Pts Experiência',  'campo' => 'nr_total_experiencias'],
    'graduacao'       => ['label' => 'Pts Graduação',    'campo' => 'nr_total_graduacao'],
    'posgraduacao'    => ['label' => 'Pts Pós Graduação','campo' => 'nr_total_posgraduacao'],
    'mestrado'        => ['label' => 'Pts Mestrado',     'campo' => 'nr_total_mestrado'],
    'doutorado'       => ['label' => 'Pts Doutorado',    'campo' => 'nr_total_doutorado'],
    'aperfeicoamentos'=> ['label' => 'Pts Cursos',       'campo' => 'nr_total_aperfeicoamentos'],
];

// Calcula colspan dinamicamente
$colspan = 4; // Class + Nome + Cargo + Edital
if ($usaDesempateDinamico && !empty($colunasDinamicas)) {
    $colspan += count($colunasDinamicas);
} else {
    foreach ($mapaColunasFixas as $chave => $cfg) {
        if (!in_array($chave, $colunasOcultas, true)) {
            $colspan++;
        }
    }
    $colspan += count($criteriosAdicionais);
}
$colspan += 3; // Nascimento + Pontuação + Situação
?>

<div data-ajax-fragment>
    <div class="table-header">
        <div class="top-bar">
            <div class="table-title">Classificação dos Candidatos</div>
        </div>
    </div>
    <table class="br-table" data-search="data-search" data-selection="data-selection" data-collapse="data-collapse" data-random="data-random">
        <caption>Título da Tabela</caption>
        <thead>
        <tr>
            <th>Class</th>
            <th width="40%">Nome</th>
            <th width="20%">Cargo</th>
            <th>Edital</th>

            <!-- Critérios dinâmicos de desempate -->
            <?php if ($usaDesempateDinamico && !empty($colunasDinamicas)): ?>
                <?php foreach ($colunasDinamicas as $col): ?>
                    <th><?= esc($col['label']) ?></th>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Regra fixa: mostra subtotais -->
                <?php foreach ($mapaColunasFixas as $chave => $cfg): ?>
                    <?php if (!in_array($chave, $colunasOcultas, true)): ?>
                        <th><?= esc($cfg['label']) ?></th>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- Critérios adicionais -->
                <?php foreach ($criteriosAdicionais as $crit): ?>
                    <th><?= esc($crit['nome']) ?></th>
                <?php endforeach; ?>
            <?php endif; ?>

            <th>Nascimento</th>
            <th>Pontuação</th>
            <th>Situação</th>
        </tr>
    </thead>
    <tbody>

    <?php if (empty($candidatos)): ?>
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

    <!-- Critérios dinâmicos de desempate -->
    <?php if ($usaDesempateDinamico && !empty($colunasDinamicas)): ?>
        <?php foreach ($colunasDinamicas as $col): ?>
            <td><?= esc($c->{$col['alias']} ?? 0) ?></td>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Regra fixa: mostra subtotais -->
        <?php foreach ($mapaColunasFixas as $chave => $cfg): ?>
            <?php if (!in_array($chave, $colunasOcultas, true)): ?>
                <td><?= esc($c->{$cfg['campo']}) ?></td>
            <?php endif; ?>
        <?php endforeach; ?>
        <!-- Critérios adicionais -->
        <?php foreach ($criteriosAdicionais as $crit): ?>
            <td><?= esc($c->{$crit['alias']} ?? 0) ?></td>
        <?php endforeach; ?>
    <?php endif; ?>

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
