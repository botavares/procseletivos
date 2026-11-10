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
<div class="home-filtros" id="form-filtros-editais" autocomplete="off">
    <div class="home-filtros__fld">
        <label for="filtro-situacao">Situação</label>
        <select id="filtro-situacao" name="situacao">
            <?php $situacaoAtual = $filtros['situacao'] ?? 'Todas'; ?>
            <option value="Todas" <?= $situacaoAtual === 'Todas' ? 'selected' : '' ?>>Todas</option>
            <option value="Vigentes" <?= $situacaoAtual === 'Vigentes' ? 'selected' : '' ?>>Vigentes</option>
            <option value="Encerrados" <?= $situacaoAtual === 'Encerrados' ? 'selected' : '' ?>>Encerrados</option>
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
            <?php $cargoAtual = $filtros['cargo'] ?? 'Todos'; ?>
            <option value="Todos" <?= $cargoAtual === 'Todos' ? 'selected' : '' ?>>Todos</option>
            <?php foreach ($cargos as $cargo) : ?>
                <option value="<?= esc($cargo->ds_nome_cargo) ?>"
                    <?= $cargoAtual === $cargo->ds_nome_cargo ? 'selected' : '' ?>>
                    <?= esc($cargo->ds_nome_cargo) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!------------------------
Lista de editais (atualizada via AJAX)
-------------------------->
<div id="lista-editais-container">
    <?= view('Publica/pages/Editais/_listaEditais_view', ['editais' => $editais]) ?>
</div>

<script>
/* Atualiza a lista de editais via AJAX sem recarregar a página nem alterar a URL */
document.addEventListener('DOMContentLoaded', function () {
    var filtros   = document.getElementById('form-filtros-editais');
    var container = document.getElementById('lista-editais-container');

    if (!filtros) { console.error('Editais: container de filtros não encontrado'); return; }
    if (!container) { console.error('Editais: container de lista não encontrado'); return; }

    function carregarEditais() {
        var situacao   = document.getElementById('filtro-situacao').value;
        var secretaria = document.getElementById('filtro-secretaria').value;
        var cargo      = document.getElementById('filtro-cargo').value;

        var params = new URLSearchParams();
        params.append('situacao', situacao);
        params.append('secretaria', secretaria);
        params.append('cargo', cargo);

        var url = '<?= base_url('Editais/filtrar') ?>?' + params.toString();
        console.log('Editais: carregando', url);

        container.innerHTML = '<div class="home-vazio">Carregando...</div>';

        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (response) {
            console.log('Editais: resposta HTTP', response.status);
            if (!response.ok) throw new Error('Erro ao buscar editais: ' + response.status);
            return response.text();
        })
        .then(function (html) {
            console.log('Editais: HTML recebido');
            container.innerHTML = html;
        })
        .catch(function (error) {
            container.innerHTML = '<div class="home-vazio" style="color:#A00000">Não foi possível carregar os editais. Tente novamente.</div>';
            console.error('Editais: erro no fetch', error);
        });
    }

    var selects = filtros.querySelectorAll('select');
    console.log('Editais: selects encontrados', selects.length);

    selects.forEach(function (select) {
        select.addEventListener('change', function () {
            console.log('Editais: change em', select.id, 'valor', select.value);
            carregarEditais();
        });
    });
});
</script>

</div>
