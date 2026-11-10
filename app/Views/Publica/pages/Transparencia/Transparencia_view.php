   <nav class="br-breadcrumb" aria-label="Breadcrumbs">
        <ol class="crumb-list" role="list">
            <li class="crumb home"><a class="br-button circle" href="<?php echo base_url("Home")?>"><span class="sr-only">Página inicial</span><i class="fas fa-home"></i></a></li>
            <li class="crumb" data-active="active"><i class="icon fas fa-chevron-right"></i><span tabindex="0" aria-current="page">Lista de Cadastrados (Página Atual)</span>
        </ol>
    </nav>
<?php $candidatos = $dados ?? [];
 $pag = $paginacao ?? [];
  $currentPage = $pag['paginaAtual'] ?? 1;
  $totalPages = $pag['totalPaginas'] ?? 1;
  $start = $pag['inicio'] ?? 1;
  $end = $pag['fim'] ?? 1;
  $filtros = $filtros ?? [];
  $queryBase = [ 'search' => $filtros['search'] ?? '',
  'per_page' => $filtros['per_page'] ?? ($pag['porPagina'] ?? 10), ];
   ?>

<div class="br-input">
  <label>Selecione o processo seletivo</label>
  <select id="filtro-processo">
    <option value="">Processos Seletivos</option>
    <?php foreach ($processos as $processo): ?>
      <option value="<?= esc($processo->pk_id_processo) ?>">
        <?= esc(formatarNumeroEdital(($processo->nm_numero_processo ?? '') . ($processo->nm_ano_processo ?? ''))) ?>
        <?= !empty($processo->ds_nome_secretaria) ? ' - ' . esc($processo->ds_nome_secretaria) : '' ?>
      </option>
    <?php endforeach ?>
  </select>
</div>
<div class="br-input">
  <label>Selecione o Cargo</label>
  <select id="filtro-cargo">
    <option value="">Selecione um Cargo</option>
  </select>
</div>
<div class="br-input">
  <label for="busca-transparencia">Buscar candidato</label>
  <input id="busca-transparencia"
         type="text"
         placeholder="Digite o nome do candidato">
</div>
<div id="tabela-container">
  <div class="text-center mt-3"><p>Selecione o processo seletivo e o cargo</p></div>
</div>


<script>
function carregarTabela(page = 1) {
    const processo = document.getElementById('filtro-processo').value;
    const cargo = document.getElementById('filtro-cargo').value;
    const busca = document.getElementById('busca-transparencia')?.value || '';
    const perPage = 10;
    const container = document.getElementById('tabela-container');

    // So carrega dados se processo e cargo estiverem selecionados
    if (!processo || !cargo) {
        container.innerHTML = '<div class="text-center mt-3"><p>Selecione o processo seletivo e o cargo</p></div>';
        return;
    }

    const url = new URL('<?= site_url("transparencia/carregarTabela") ?>');

    url.searchParams.set('page', page);
    url.searchParams.set('processo', processo);
    url.searchParams.set('cargo', cargo);
    url.searchParams.set('search', busca);
    url.searchParams.set('per_page', perPage);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            container.innerHTML = html;
            bindPaginacaoAjax();
        });
}

function carregarCargosPorProcesso(processoId) {
    const url = new URL('<?= site_url("transparencia/carregarCargosPorProcesso") ?>');
    url.searchParams.set('processo', processoId);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(cargos => {
            const selectCargo = document.getElementById('filtro-cargo');
            selectCargo.innerHTML = '';

            if (!cargos || cargos.length === 0) {
                selectCargo.innerHTML = '<option value="">Nenhum cargo disponível</option>';
                return;
            }

            // Adiciona opção "Todos"
            const optTodos = document.createElement('option');
            optTodos.value = '';
            optTodos.textContent = 'Todos';
            selectCargo.appendChild(optTodos);

            cargos.forEach(cargo => {
                const opt = document.createElement('option');
                opt.value = cargo.pk_id_cargo;
                opt.textContent = cargo.ds_nome_cargo;
                selectCargo.appendChild(opt);
            });
        });
}

// filtro processo seletivo
document.getElementById('filtro-processo')
  ?.addEventListener('change', function() {
      const processoId = this.value;
      const selectCargo = document.getElementById('filtro-cargo');

      if (processoId) {
          // Carrega os cargos do processo selecionado
          carregarCargosPorProcesso(processoId);
      } else {
          // Volta ao estado inicial
          selectCargo.innerHTML = '<option value="">Selecione um processo seletivo</option>';
      }

      // Recarrega a tabela (sem cargo)
      carregarTabela(1);
  });

// filtro cargo
document.getElementById('filtro-cargo')
  ?.addEventListener('change', () => carregarTabela(1));

// busca por nome com debounce
let timer;

document.getElementById('busca-transparencia')
  ?.addEventListener('input', () => {
      clearTimeout(timer);
      timer = setTimeout(() => carregarTabela(1), 400);
  });

function bindPaginacaoAjax() {
    document.querySelectorAll('.br-pagination a')
        .forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();

                const page = new URL(link.href).searchParams.get('page') || 1;

                carregarTabela(page);
            });
        });
}
</script>


