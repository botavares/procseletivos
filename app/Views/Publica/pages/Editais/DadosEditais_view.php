<div class="home-novo">

<!------------------------
Breadcrumb
-------------------------->
<nav aria-label="Breadcrumb" class="home-crumb">
    <a href="<?= base_url('/') ?>">Início</a> <span>›</span>
    <span aria-current="page">Editais (Processos Seletivos)</span>
</nav>

<!------------------------
Título e callout
-------------------------->
<h1>Editais (Processos Seletivos)</h1>

<div class="home-callout" style="margin-top:12px">
    Cada edital corresponde a um processo seletivo para contratação temporária. Abrindo o processo,
    você encontra tudo o que dele decorreu: retificações, resultados, convocações e contratos.
    A convocação dos classificados é comunicada pela Gerência de Recursos Humanos por e-mail,
    telefone e WhatsApp — acompanhe também a <b>Lista Pública de Candidatos</b> para consultar
    a sua situação.
</div>

<!------------------------
Filtros
-------------------------->
<form method="get" action="<?= base_url('Editais') ?>" class="home-filtros" id="form-filtros-editais">
    <div class="home-filtros__fld">
        <label for="filtro-situacao">Situação</label>
        <select id="filtro-situacao" name="situacao">
            <option value="Vigentes" <?= ($filtros['situacao'] ?? 'Vigentes') === 'Vigentes' ? 'selected' : '' ?>>Vigentes</option>
            <option value="Encerrados" <?= ($filtros['situacao'] ?? '') === 'Encerrados' ? 'selected' : '' ?>>Encerrados</option>
            <option value="Todas" <?= ($filtros['situacao'] ?? '') === 'Todas' ? 'selected' : '' ?>>Todas</option>
        </select>
    </div>

    <div class="home-filtros__fld">
        <label for="filtro-secretaria">Secretaria</label>
        <select id="filtro-secretaria" name="secretaria">
            <option value="">Todas</option>
            <?php foreach ($secretarias as $secretaria) : ?>
                <option value="<?= esc($secretaria->pk_id_secretaria) ?>"
                    <?= ($filtros['secretaria'] ?? '') === (string) $secretaria->pk_id_secretaria ? 'selected' : '' ?>>
                    <?= esc($secretaria->ds_nome_secretaria) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="home-filtros__fld">
        <label for="filtro-cargo">Cargo</label>
        <select id="filtro-cargo" name="cargo">
            <option value="Todos">Todos</option>
            <?php foreach ($cargos as $cargo) : ?>
                <option value="<?= esc($cargo->ds_nome_cargo) ?>"
                    <?= ($filtros['cargo'] ?? 'Todos') === $cargo->ds_nome_cargo ? 'selected' : '' ?>>
                    <?= esc($cargo->ds_nome_cargo) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</form>

<!------------------------
Lista de editais
-------------------------->
<?php if (!empty($editais)) : ?>
    <?php foreach ($editais as $edital) : ?>
        <div class="home-edital">
            <div class="home-edital__top">
                <div>
                    <div class="home-edital__sec">
                        <?= esc($edital['secretaria']->sigla ?? $edital['secretaria']->nome ?? 'Secretaria não informada') ?>
                    </div>
                    <h4>Processo Seletivo Simplificado nº <?= esc($edital['numero_formatado']) ?></h4>
                    <div class="home-edital__obj"><?= esc($edital['objeto']) ?></div>

                    <?php if (!empty($edital['cargos'])) : ?>
                        <div class="home-edital__cargos">
                            Cargos:
                            <?php
                            $listaCargos = [];
                            foreach ($edital['cargos'] as $cargo) {
                                $vagas = (int) ($cargo->nm_vagas ?? 0);
                                $listaCargos[] = esc($cargo->ds_nome_cargo ?? 'Não informado') . ' (' . $vagas . ' ' . ($vagas === 1 ? 'vaga' : 'vagas') . ')';
                            }
                            echo implode(' · ', $listaCargos);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($edital['situacao_id'] === 5) : ?>
                        <div class="home-edital__prazo">
                            <b>Validade encerrada</b> — os documentos permanecem publicados para consulta.
                        </div>
                    <?php else : ?>
                        <div class="home-edital__prazo">
                            <b>Inscrições de <?= esc($edital['data_inicio']) ?> a <?= esc($edital['data_fim']) ?></b>
                            — <?= $edital['inscricao_aberta'] ? 'Inscrições abertas' : 'Encerradas' ?>
                        </div>
                    <?php endif; ?>
                </div>

                <span class="home-tag <?= $edital['tag_class'] ?>"><?= esc($edital['situacao_label']) ?></span>
            </div>

            <div class="home-hero__acoes">
                <?php if ($edital['inscricao_aberta']) : ?>
                    <a href="<?= base_url('Cadastros') ?>" class="home-btn">Inscrever-se</a>
                <?php elseif ($edital['situacao_id'] !== 5) : ?>
                    <span class="home-btn home-btn--disabled">Inscrições encerradas em <?= esc($edital['data_fim']) ?></span>
                <?php endif; ?>

                <?php if (!empty($edital['editais'])) : ?>
                    <a href="<?= site_url('ProcessosSeletivos/obterEdital/' . pasta_tipo_documento($edital['editais'][0]['tipo_id']) . '/' . $edital['editais'][0]['arquivo']) ?>"
                       class="home-btn home-btn--outline"
                       target="_blank">Ver edital (PDF) ↗</a>
                <?php endif; ?>

                <a href="<?= base_url('ProcessosSeletivos/detalhes/' . $edital['id']) ?>" class="home-btn home-btn--outline">Ver processo completo →</a>
            </div>

            <?php if (!empty($edital['retificacoes'])) : ?>
                <?php foreach ($edital['retificacoes'] as $ret) : ?>
                    <div class="home-edital__ret">
                        Retificação:
                        <a href="<?= site_url('ProcessosSeletivos/obterEdital/' . pasta_tipo_documento($ret['tipo_id']) . '/' . $ret['arquivo']) ?>"
                           target="_blank">
                            <?= esc($ret['tipo']) ?> nº <?= esc($ret['numero_formatado']) ?> (PDF) ↗
                        </a>
                        <?php if ($ret['vigente']) : ?>
                            <span class="home-edital__mini home-mini--vig">VIGENTE</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="home-vazio">
        Nenhum edital encontrado para esta combinação de secretaria, cargo e situação.
    </div>
<?php endif; ?>

<script>
/* Submete o formulário de filtros automaticamente ao alterar qualquer select */
document.addEventListener('change', function (e) {
    var target = e.target;
    if (target && target.closest('#form-filtros-editais')) {
        document.getElementById('form-filtros-editais').submit();
    }
});
</script>

</div>
