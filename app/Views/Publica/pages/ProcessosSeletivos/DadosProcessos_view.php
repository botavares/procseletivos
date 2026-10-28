<div class="home-novo">

<!------------------------
Breadcrumb 
-------------------------->
<nav aria-label="Breadcrumb" class="home-crumb">
    <a href="<?= base_url('/') ?>">Início</a> <span>›</span>
    <a href="<?= base_url('Editais') ?>">Editais (Processos Seletivos)</a> <span>›</span>
    <span aria-current="page">PSS <?= esc($secretaria->sigla ?? '') ?> nº <?= esc($processoNum) ?></span>
    
</nav>

<!------------------------
Header do processo 
-------------------------->
<div class="home-phead">
    <div class="home-phead__secr"><?= esc($secretaria->nome ?? ($secretaria->sigla ?? 'Secretaria não informada')) ?></div>
    <div class="home-phead__lin">
        <h1>Processo Seletivo Simplificado nº <?= esc($processoNum) ?></h1>
        <span class="home-tag <?= $tagClass ?>"><?= esc($tagLabel) ?></span>
    </div>
    <div class="home-phead__obj"><?= esc($objetivo) ?></div>
</div>

<!----------------------
Faixa de destaque 
---------------------->
<div class="home-faixa">
    <div class="home-faixa-destaque">
        <?php if ($situacaoId == 1) : ?>
            <b>Inscrições abertas até <?= esc($dataTermino) ?></b>
            Inscreva-se com seu login gov.br. As inscrições seguem até o prazo final.
        <?php elseif ($situacaoId == 4) : ?>
            <b>Processo vigente — convocações em andamento</b>
            Acompanhe as convocações e a situação dos contratados.
        <?php elseif ($situacaoId == 2) : ?>
            <b>Inscrições encerradas</b>
            Aguarde a publicação do resultado final e das próximas etapas.
        <?php else : ?>
            <b>Processo encerrado</b>
            Os documentos permanecem publicados para consulta.
        <?php endif; ?>
    </div>
    <?php if ($situacaoId == 1) : ?>
        <a href="<?= base_url('Cadastros') ?>" class="home-btn">Inscrever-se</a>
    <?php else : ?>
        <a href="#conv" class="home-btn home-btn--outline">Ver convocações</a>
    <?php endif; ?>
</div>

<!-------------------]
     Abas (tabs) 
-------------------->
<div class="home-abas" role="tablist">
    <button class="home-aba home-aba--on" data-tab="processo" role="tab" aria-selected="true">
        <span class="home-aba__nm">Processo e cargos</span>
        <span class="home-aba__st"><?= count($cargos ?? []) ?> cargo(s) · <?= array_sum(array_column($cargos ?? [], 'nm_vagas')) ?> vaga(s)</span>
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

<!-------------------
Painéis 
---------------------->
<div class="datas-processos datas-processos--on" data-panel="processo" style="margin-top:24px">
    <!-- Prazos -->
    <div class="home-prazos">
        <div class="home-prazo-c">
            <div class="home-prazo-c__r">Inscrições</div>
            <div class="home-prazo-c__v"><?= esc($dataInicial) ?> a <?= esc($dataTermino) ?></div>
            <div class="home-prazo-c__x"><?= $situacaoId == 1 ? 'Inscrições abertas' : 'Encerradas' ?></div>
        </div>
        <div class="home-prazo-c">
            <div class="home-prazo-c__r">Resultado final (homologação)</div>
            <div class="home-prazo-c__v"><?= esc($dataHomologacao ?? '') ?></div>
            <div class="home-prazo-c__x"><?= esc($dataHomologacao ?? '') ? 'Publicado' : 'Aguardando publicação' ?></div>
        </div>
        <div class="home-prazo-c">
            <div class="home-prazo-c__r">Validade do processo</div>
            <div class="home-prazo-c__v"><?= esc($validade) ?></div>
            <div class="home-prazo-c__x">A contar da homologação</div>
        </div>
    </div>

    <!-- Edital e retificações -->
    <h4 class="home-sub">Edital e retificações</h4>
    <div class="home-box" style="padding:4px 18px">
        <div class="home-doc">
            <div>
                <div class="home-doc__d">
                    <b>Edital nº <?= esc($processoNum) ?></b> — versão original
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
                        <tr>
                            <td data-r="Cargo"><b><?= esc($cargo->ds_nome_cargo ?? 'Não informado') ?></b></td>
                            <td data-r="Vagas"><?= esc($cargo->nm_vagas ?? '—') ?></td>
                            <td data-r="PcD"><?= esc($cargo->nm_vagas_pcd ?? '—') ?></td>
                            <td data-r="Carga horária"><?= esc(trim(($cargo->nm_carga_horaria ?? '') . ' ' . ($cargo->ds_tipo_carga_horaria ?? '')) ?: '—') ?></td>
                            <td data-r="Remuneração"><?= esc($cargo->ds_remuneracao ?? '—') ?></td>
                            <td data-r="Requisito"><?= esc($cargo->ds_requisito_provimento ?? '—') ?></td>
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

<div class="datas-processos" data-panel="resultados">
    <?php if (!empty($resultados)) : ?>
        <h4 class="home-sub">Resultados publicados</h4>
        <div class="home-box" style="padding:4px 18px">
            <?php foreach ($resultados as $r) : ?>
                <div class="home-doc">
                    <div>
                        <div class="home-doc__d">
                            <b><?= esc($r['t'] ?? 'Resultado') ?></b>
                            <span class="home-mini home-mini--vig">VIGENTE</span>
                        </div>
                        <div class="home-doc__dt">
                            <?= esc($r['tp'] ?? '') ?> · publicado em <?= esc($r['dt'] ?? '') ?>
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

<div class="datas-processos" data-panel="conv">
    <?php if (!empty($convocacoes)) : ?>
        <h4 class="home-sub">Chamamentos publicados</h4>
        <?php foreach ($convocacoes as $c) : ?>
            <div class="home-box home-conv">
                <div class="home-conv__top">
                    <div>
                        <h4>Edital de Convocação nº <?= esc($c['n'] ?? '') ?></h4>
                        <div class="home-conv__dt">Publicado em <?= esc($c['dt'] ?? '') ?> · <a href="#">ver publicação ↗</a></div>
                    </div>
                    <span class="home-btn home-btn--outline">Abrir edital ↗</span>
                </div>
                <?php if (!empty($c['lista'] ?? [])) : ?>
                    <div class="home-tbl-sc">
                        <table class="home-tbl">
                            <thead><tr><th>Class.</th><th>Nome</th><th>Cargo</th><th>Condição</th></tr></thead>
                            <tbody>
                                <?php foreach ($c['lista'] as $x) : ?>
                                    <tr>
                                        <td data-r="Class."><?= esc($x['c'] ?? '') ?></td>
                                        <td data-r="Nome"><?= esc($x['nm'] ?? '') ?></td>
                                        <td data-r="Cargo"><?= esc($x['cg'] ?? '') ?></td>
                                        <td data-r="Condição">
                                            <span class="home-cond <?= ($x['cd'] ?? '') === 'TITULAR' ? 'home-cond--tit' : 'home-cond--sup' ?>">
                                                <?= esc($x['cd'] ?? '') ?>
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

<div class="datas-processos" data-panel="contratos">
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
                                <b><?= esc($c['n'] ?? '') ?></b><br><a href="#" style="font-size:10.5px">extrato ↗</a>
                            </td>
                            <td data-r="Nome"><?= esc($c['nm'] ?? '') ?></td>
                            <td data-r="Cargo"><?= esc($c['cg'] ?? '') ?></td>
                            <td data-r="Lotação"><?= esc($c['lt'] ?? '') ?></td>
                            <td data-r="Vigência"><?= esc($c['vg'] ?? '') ?></td>
                            <td data-r="Situação">
                                <span class="home-tag home-tag--<?= strtolower($c['sit'] ?? 'vigente') ?>"><?= esc($c['sit'] ?? '') ?></span>
                            </td>
                        </tr>
                        <?php if (!empty($c['hist'] ?? [])) : ?>
                            <?php foreach ($c['hist'] as $h) : ?>
                                <tr>
                                    <td colspan="6" style="padding:0 12px 12px">
                                        <div class="home-hist">
                                            <b><?= esc($h['t'] ?? '') ?></b> — <?= esc($h['x'] ?? '') ?> · <a href="#">extrato ↗</a>
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
    <b><?= esc($secretaria->responsavel_processo) ?></b>
    <?= '·'.esc($secretaria->rua).', '.esc($secretaria->numero).', '.esc($secretaria->complemento). ' — '.esc($secretaria->bairro).' · '.esc($secretaria->telefone).' · '.esc($secretaria->email).' · '.esc($secretaria->atendimento) ?> 
    
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
    container.querySelectorAll('.datas-processos').forEach(function(p) {
        p.classList.remove('datas-processos--on');
    });
    var pane = container.querySelector('.datas-processos[data-panel="' + paneId + '"]');
    if (pane) pane.classList.add('datas-processos--on');
});
</script>

</div>
