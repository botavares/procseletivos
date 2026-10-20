
<div class="home-novo">

    <!-- ACONTECENDO AGORA -->
    <div class="home-secao-titulo" style="margin-top:6px">
        <h3>Acontecendo agora</h3>
        <span class="linha"></span>
    </div>

    <?php if (!empty($editaisAbertos)) : ?>
        <?php
        $editaisAgrupados = [];
        $cargosPorEdital = [];
        foreach ($editaisAbertos as $edital) {
            $id = $edital->pk_id_edital;
            if (!isset($editaisAgrupados[$id])) {
                $editaisAgrupados[$id] = $edital;
                $cargosPorEdital[$id] = [];
            }
            $cargosPorEdital[$id][] = $edital->ds_nome_cargo ?? 'Cargo não informado';
        }
        ?>
        <?php foreach ($editaisAgrupados as $id => $edital) : ?>
            <div class="home-hero">
                <div class="home-hero__top">
                    <div>
                        <h4><?= esc($edital->ds_nome_secretaria ?? 'SEPLAG') ?> · Processo Seletivo Simplificado nº <?= esc($edital->ds_numero_edital) ?></h4>
                        <div class="home-hero__meta">
                            Cargos: <b><?= esc(implode(', ', $cargosPorEdital[$id])) ?></b><br>
                            <b>Inscrições de <?= esc(date('d/m/Y', strtotime($edital->ds_data_inicial))) ?> a <?= esc(date('d/m/Y', strtotime($edital->ds_data_termino))) ?></b>
                        </div>
                    </div>
                    <span class="home-tag home-tag--aberto">Inscrições abertas</span>
                </div>
                <div class="home-hero__acoes">
                    <a href="<?= base_url("Cadastros") ?>" class="home-btn">Inscrever-se</a>
                    <a href="<?= base_url("Editais/detalhes/{$edital->pk_id_edital}") ?>" class="home-btn home-btn--outline">Ver edital →</a>
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
