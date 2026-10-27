<?php use App\Enums\Admin\SituacaoProcessosEnum; ?>
<div class="home-novo">

    <!-- ACONTECENDO AGORA -->
    <div class="home-secao-titulo" style="margin-top:6px">
        <h3>Acontecendo agora</h3>
        <span class="linha"></span>
    </div>

    <?php if (!empty($processosSeletivos)) : ?>
        <?php foreach ($processosSeletivos as $processo) : ?>
            <?php
            $situacaoId = (int) ($processo->nm_id_situacao ?? 0);
            $enum = SituacaoProcessosEnum::tryFrom($situacaoId);
            $tagClass = match ($situacaoId) {
                1 => 'home-tag--aberto',
                2 => 'home-tag--encerrado',
                3 => 'home-tag--vigente',
                6 => 'home-tag--aberto',
                default => 'home-tag--encerrado',
            };
            $tagLabel = $enum?->label() ?? 'Status não encontrado';
            ?>
            <div class="home-hero">
                <div class="home-hero__top">
                    <div>
                        <h4><?= esc($processo->ds_nome_secretaria ?? 'SEPLAG') ?> · Processo Seletivo Simplificado nº <?= esc($processo->nm_numero_processo) ?>/<?= esc($processo->nm_ano_processo) ?></h4>
                        <div class="home-hero__meta">
                            <p style="margin:0 0 6px 0;font-size:13px;color:var(--home-texto2);line-height:1.6"><?= esc($processo->ds_cargos ?? 'Não informado') ?></p>
                            <b>Inscrições de <?= esc(date('d/m/Y', strtotime($processo->dt_inicio_inscricao))) ?> a <?= esc(date('d/m/Y', strtotime($processo->dt_fim_inscricao))) ?></b>
                        </div>
                    </div>
                    <span class="home-tag <?= $tagClass ?>"><?= esc($tagLabel) ?></span>
                </div>
                <div class="home-hero__acoes">
                    <a href="<?= base_url("Cadastros") ?>" class="home-btn">Inscrever-se</a>
                    <a href="<?= base_url("ProcessosSeletivos/detalhes/{$processo->pk_id_processo}") ?>" class="home-btn home-btn--outline">Ver processo completo →</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <div class="home-vazio">
            No momento não há processo seletivo com inscrições abertas.<br>
            Consulte em Editais os processos vigentes e os encerrados.
        </div>
    <?php endif; ?>

    <div class="home-hero__aviso">
        Seus dados pessoais estarão protegidos nos termos da
        <a href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2017/lei/l13460.htm" target="_blank">Lei nº 13.460/2017</a>.
    </div>

    <!-- POR ONDE COMEÇAR -->
    <div class="home-secao-titulo">
        <h3>Por onde começar</h3>
        <span class="linha"></span>
    </div>

    <div class="home-portais">

        <!-- Card 1: Editais -->
        <div class="home-portal">
            <h5>Editais<br>(Processos Seletivos)</h5>
            <p>Todos os processos seletivos, vigentes e encerrados. Cada edital reúne suas retificações, resultados, convocações e contratos.</p>
            <a href="<?= base_url("Editais") ?>" class="home-btn home-btn--outline">Acessar</a>
        </div>

        <!-- Card 2: Lista Pública -->
        <div class="home-portal">
            <h5>Lista Pública<br>de Cadastrados</h5>
            <p>Consulte a classificação e a situação de cada candidato, atualizada pela Gerência de Recursos Humanos.</p>
            <a href="<?= site_url("transparencia") ?>" class="home-btn home-btn--outline">Acessar</a>
        </div>

        <!-- Card 3: Perguntas Frequentes -->
        <div class="home-portal">
            <h5>Perguntas<br>Frequentes</h5>
            <p>Respostas às dúvidas mais comuns sobre inscrição, classificação, convocação e contratação.</p>
            <a href="<?= base_url("PerguntasFrequentes") ?>" class="home-btn home-btn--outline">Acessar</a>
        </div>
    </div>

</div>
