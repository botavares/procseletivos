<?php
// Placeholder para breadcrumbs: Início > Editais > Detalhes do edital
// O controller deve passar: $edital (objeto), $cargos (array), $resultados, $convocacoes, $contratos

// Helper: acessa propriedade em objeto ou chave em array de forma segura
function _val($item, string $key, $default = null)
{
    if (is_object($item)) {
        return property_exists($item, $key) ? $item->$key : $default;
    }
    if (is_array($item)) {
        return $item[$key] ?? $default;
    }
    return $default;
}

$editalNum = $edital->ds_numero_edital ?? $idEdital ?? 'N/D';
$situacao = $edital->ds_status ?? 0;

// Mapear status para tag
$tagClass = 'home-tag--encerrado';
$tagLabel = 'Encerrado';
if ($situacao == 1) { $tagClass = 'home-tag--aberto'; $tagLabel = 'Inscrições abertas'; }
elseif ($situacao == 2) { $tagClass = 'home-tag--vigente'; $tagLabel = 'Vigente'; }

$secretaria = $edital->ds_nome_secretaria ?? 'SEPLAG';
$objetivo = $edital->ds_objetivo ?? 'Objetivo não informado.';
$dataInicial = isset($edital->ds_data_inicial) ? date('d/m/Y', strtotime($edital->ds_data_inicial)) : '—';
$dataTermino = isset($edital->ds_data_termino) ? date('d/m/Y', strtotime($edital->ds_data_termino)) : '—';
?>

<div class="home-novo">

<!-- Breadcrumb -->
<nav aria-label="Breadcrumb" class="home-crumb">
    <a href="<?= base_url('/') ?>">Início</a> <span>›</span>
    <a href="<?= base_url('Editais') ?>">Editais (Processos Seletivos)</a> <span>›</span>
    <span aria-current="page">PSS <?= esc($secretaria) ?> nº <?= esc($editalNum) ?></span>
</nav>

<!-- Header do processo -->
<div class="home-phead">
    <div class="home-phead__secr"><?= esc($secretaria) ?> — SECRETARIA MUNICIPAL DE PLANEJAMENTO, GESTÃO, CIÊNCIA E TECNOLOGIA</div>
    <div class="home-phead__lin">
        <h1>Processo Seletivo Simplificado nº <?= esc($editalNum) ?></h1>
        <span class="home-tag <?= $tagClass ?>"><?= esc($tagLabel) ?></span>
    </div>
    <div class="home-phead__obj"><?= esc($objetivo) ?></div>
</div>

<!-- Faixa de destaque -->
<div class="home-faixa">
    <div class="home-faixa__tx">
        <?php if ($situacao == 1) : ?>
            <b>Inscrições abertas até <?= esc($dataTermino) ?></b>
            Inscreva-se com seu login gov.br. As inscrições seguem até o prazo final.
        <?php elseif ($situacao == 2) : ?>
            <b>Processo vigente — convocações em andamento</b>
            Acompanhe as convocações e a situação dos contratados.
        <?php else : ?>
            <b>Processo encerrado</b>
            Os documentos permanecem publicados para consulta.
        <?php endif; ?>
    </div>
    <?php if ($situacao == 1) : ?>
        <a href="<?= base_url('Cadastros') ?>" class="home-btn">Inscrever-se</a>
    <?php else : ?>
        <a href="#conv" class="home-btn home-btn--outline">Ver convocações</a>
    <?php endif; ?>
</div>

<!-- Abas (tabs) -->
<div class="home-abas" role="tablist">
    <button class="home-aba home-aba--on" data-tab="edital" role="tab" aria-selected="true">
        <span class="home-aba__nm">Edital e cargos</span>
        <span class="home-aba__st"><?= count($cargos ?? []) ?> cargo(s) · <?= array_sum(array_column($cargos ?? [], 'vg')) ?> vaga(s)</span>
    </button>
    <button class="home-aba" data-tab="resultados" role="tab" aria-selected="false">
        <span class="home-aba__nm">Resultados</span>
        <span class="home-aba__st"><?= !empty($resultados ?? []) ? 'Publicado' : 'Ainda não publicado' ?></span>
    </button>
    <button class="home-aba" data-tab="conv" role="tab" aria-selected="false">
        <span class="home-aba__nm">Convocações</span>
        <span class="home-aba__st"><?= !empty($convocacoes ?? []) ? count($convocacoes) . ' chamamento(s)' : 'Ainda não publicado' ?></span>
    </button>
    <button class="home-aba" data-tab="contratos" role="tab" aria-selected="false">
        <span class="home-aba__nm">Contratos</span>
        <span class="home-aba__st"><?= !empty($contratos ?? []) ? count($contratos) . ' contratado(s)' : 'Ainda não publicado' ?></span>
    </button>
</div>

<!-- Painéis -->
<div class="home-painel home-painel--on" data-panel="edital">
    <!-- Prazos -->
    <div class="home-prazos">
        <div class="home-prazo-c">
            <div class="home-prazo-c__r">Inscrições</div>
            <div class="home-prazo-c__v"><?= esc($dataInicial) ?> a <?= esc($dataTermino) ?></div>
            <div class="home-prazo-c__x">
                <?php if ($situacao == 1) : ?>Inscrições abertas<?php elseif ($situacao == 2) : ?>Encerradas<?php else : ?>Encerradas<?php endif; ?>
            </div>
        </div>
        <div class="home-prazo-c">
            <div class="home-prazo-c__r">Resultado final</div>
            <div class="home-prazo-c__v">—</div>
            <div class="home-prazo-c__x">A definir</div>
        </div>
        <div class="home-prazo-c">
            <div class="home-prazo-c__r">Validade do processo</div>
            <div class="home-prazo-c__v">12 meses</div>
            <div class="home-prazo-c__x">A contar da homologação</div>
        </div>
    </div>

    <!-- Edital e retificações -->
    <h4 class="home-sub">Edital e retificações</h4>
    <div class="home-box" style="padding:4px 18px">
        <div class="home-doc">
            <div>
                <div class="home-doc__d">
                    <b>Edital nº <?= esc($editalNum) ?></b> — versão original
                    <span class="home-mini home-mini--vig">VIGENTE</span>
                </div>
                <div class="home-doc__dt">Publicado no Diário Oficial dos Municípios · <a href="#">ver publicação ↗</a></div>
            </div>
            <a href="#" class="home-btn home-btn--outline">Ver edital (PDF) ↗</a>
        </div>
    </div>

    <!-- Cargos ofertados -->
    <h4 class="home-sub" style="margin-top:24px">Cargos ofertados</h4>
    <div class="home-tbl-sc">
        <table class="home-tbl">
            <thead>
                <tr>
                    <th>Cargo</th>
                    <th>Vagas</th>
                    <th>PcD</th>
                    <th>Carga horária</th>
                    <th>Remuneração</th>
                    <th>Requisito</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cargos)) : ?>
                    <?php foreach ($cargos as $cargo) : ?>
                        <?php $c = (array)$cargo; ?>
                        <tr>
                            <td data-r="Cargo"><b><?= esc($c['ds_nome_cargo'] ?? $c['n'] ?? 'Não informado') ?></b></td>
                            <td data-r="Vagas"><?= esc($c['vg'] ?? '—') ?></td>
                            <td data-r="PcD"><?= ($c['pcd'] ?? 0) ? 'Sim' : '—' ?></td>
                            <td data-r="Carga horária"><?= esc($c['ch'] ?? '—') ?></td>
                            <td data-r="Remuneração"><?= esc($c['rem'] ?? '—') ?></td>
                            <td data-r="Requisito"><?= esc($c['req'] ?? '—') ?></td>
                            <td data-r=""><a href="<?= site_url('transparencia') ?>" class="home-lnk">Ver classificação →</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="7" class="home-vazio" style="border:0">Nenhum cargo vinculado a este edital.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="home-nota">"Ver classificação" abre a Lista Pública de Candidatos filtrada por este processo e por este cargo.</div>
</div>

<div class="home-painel" data-panel="resultados">
    <?php if (!empty($resultados)) : ?>
        <h4 class="home-sub">Resultados publicados</h4>
        <div class="home-box" style="padding:4px 18px">
            <?php foreach ($resultados as $r) : ?>
                <div class="home-doc">
                    <div>
                        <div class="home-doc__d">
                            <b><?= esc(_val($r, 't', 'Resultado')) ?></b>
                            <span class="home-mini home-mini--vig">VIGENTE</span>
                        </div>
                        <div class="home-doc__dt">
                            <?= esc(_val($r, 'tp', '')) ?> · publicado em <?= esc(_val($r, 'dt', '')) ?>
                            · <a href="#">ver publicação ↗</a>
                        </div>
                    </div>
                    <span class="home-btn home-btn--outline">Abrir (PDF) ↗</span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <div class="home-vazio">
            Esta etapa do processo ainda não aconteceu.<br>Quando houver resultado publicado, ele aparece aqui.
        </div>
    <?php endif; ?>
</div>

<div class="home-painel" data-panel="conv">
    <?php if (!empty($convocacoes)) : ?>    
        <h4 class="home-sub">Chamamentos publicados</h4>
        <?php foreach ($convocacoes as $c) : ?>
            <div class="home-box home-conv">
                <div class="home-conv__top">
                    <div>
                        <h4>Edital de Convocação nº <?= esc(_val($c, 'n', '')) ?></h4>
                        <div class="home-conv__dt">Publicado em <?= esc(_val($c, 'dt', '')) ?> · <a href="#">ver publicação ↗</a></div>
                    </div>
                    <span class="home-btn home-btn--outline">Abrir edital ↗</span>
                </div>
                <?php if (!empty(_val($c, 'lista', []))) : ?>
                    <div class="home-tbl-sc">
                        <table class="home-tbl">
                            <thead><tr><th>Class.</th><th>Nome</th><th>Cargo</th><th>Condição</th></tr></thead>
                            <tbody>
                                <?php foreach ((_val($c, 'lista', [])) as $x) : ?>
                                    <tr>
                                        <td data-r="Class."><?= esc(_val($x, 'c', '')) ?></td>
                                        <td data-r="Nome"><?= esc(_val($x, 'nm', '')) ?></td>
                                        <td data-r="Cargo"><?= esc(_val($x, 'cg', '')) ?></td>
                                        <td data-r="Condição">
                                            <span class="home-cond <?= _val($x, 'cd', '') === 'TITULAR' ? 'home-cond--tit' : 'home-cond--sup' ?>">
                                                <?= esc(_val($x, 'cd', '')) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
                                    
        <div class="home-callout">
            <b>Não encontrou seu nome?</b> As convocações seguem uma ordem de prioridade definida em lei: primeiro são chamados os aprovados do concurso público em cadastro de reserva; somente depois entram os classificados deste processo seletivo. Cada convocação chama o número exato de pessoas necessário, respeitando a classificação.
        </div>
    <?php else : ?>
        <div class="home-vazio">
            Esta etapa do processo ainda não aconteceu.<br>Quando houver convocação publicada, ela aparece aqui.
        </div>
    <?php endif; ?>
</div>

<div class="home-painel" data-panel="contratos">
    <?php if (!empty($contratos)) : ?>  
        <h4 class="home-sub">Contratados neste processo seletivo</h4>
        <div class="home-nota" style="margin:0 0 14px">Relação nominal dos contratados por prazo determinado. O instrumento contratual não é publicado: cada registro remete ao extrato publicado no Diário Oficial.</div>
        <div class="home-tbl-sc">
            <table class="home-tbl">
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>Nome</th>
                        <th>Cargo</th>
                        <th>Lotação</th>
                        <th>Vigência</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($contratos as $c) : ?>
                        <tr>
                            <td data-r="Contrato">
                                <b><?= esc(_val($c, 'n', '')) ?></b><br><a href="#" style="font-size:10.5px">extrato ↗</a>
                            </td>
                            <td data-r="Nome"><?= esc(_val($c, 'nm', '')) ?></td>
                            <td data-r="Cargo"><?= esc(_val($c, 'cg', '')) ?></td>
                            <td data-r="Lotação"><?= esc(_val($c, 'lt', '')) ?></td>
                            <td data-r="Vigência"><?= esc(_val($c, 'vg', '')) ?></td>
                            <td data-r="Situação">
                                <span class="home-tag home-tag--<?= strtolower(_val($c, 'sit', 'vigente')) ?>"><?= esc(_val($c, 'sit', '')) ?></span>
                            </td>
                        </tr>
                        <?php if (!empty(_val($c, 'hist', []))) : ?>
                            <?php foreach ((_val($c, 'hist', [])) as $h) : ?>
                                <tr>
                                    <td colspan="6" style="padding:0 12px 12px">
                                        <div class="home-hist">
                                            <b><?= esc(_val($h, 't', '')) ?></b> — <?= esc(_val($h, 'x', '')) ?> · <a href="#">extrato ↗</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="home-nota">
            Não são publicados CPF, endereço, estado civil ou qualquer outro dado de qualificação civil dos contratados. A rescisão publica o fato e a data, não o motivo.
        </div>
    <?php else : ?>
        <div class="home-vazio">
            Esta etapa do processo ainda não aconteceu.<br>Quando houver contrato publicado, ele aparece aqui.
        </div>
    <?php endif; ?>
</div>

<!-- Contato -->
<div class="home-contato">
    <b>Gerência de Recursos Humanos — SEPLAG</b>
    Avenida Paraná, 2.601, sala 307 — São José · (37) 3229-6528 (WhatsApp) · grh.semad@divinopolis.mg.gov.br · Atendimento de segunda a sexta, das 8h às 18h
</div>

<script>
/* Tabs simples (sem dependência de framework) */
document.addEventListener('click', function(e) {
    var tab = e.target.closest('.home-aba');
    if (!tab) return;
    
    var tablist = tab.closest('.home-abas');
    var paneId = tab.dataset.tab;
    
    tablist.querySelectorAll('.home-aba').forEach(function(t) {
        t.classList.remove('home-aba--on');
        t.setAttribute('aria-selected', 'false');
    });
    tab.classList.add('home-aba--on');
    tab.setAttribute('aria-selected', 'true');
    
    var container = tablist.parentElement;
    container.querySelectorAll('.home-painel').forEach(function(p) {
        p.classList.remove('home-painel--on');
    });
    var pane = container.querySelector('.home-painel[data-panel="' + paneId + '"]');
    if (pane) pane.classList.add('home-painel--on');
});
</script>

</div>
