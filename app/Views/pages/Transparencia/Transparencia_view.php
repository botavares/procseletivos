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
  <label>Selecione o Cargo</label>
  <select id="filtro-cargo">
    <option value="">Todos</option>
    <?php foreach ($cargos as $cargo): ?>
      <option value="<?= esc($cargo->pk_id_cargo) ?>">
        <?= esc($cargo->ds_nome_cargo) ?>
      </option>
    <?php endforeach ?>
  </select>
</div>
<div class="br-input">
  <label for="busca-transparencia">Buscar candidato</label>
  <input id="busca-transparencia" 
         type="text" 
         placeholder="Digite o nome do candidato">
</div>
<div id="tabela-container" class="br-table" data-search="data-search" data-selection="data-selection" data-collapse="data-collapse" data-random="data-random">
  
</div>
    


<script>
document.addEventListener('DOMContentLoaded', () => carregarTabela(1));
</script>

<script>
function carregarTabela(page = 1) {

    const cargo = document.getElementById('filtro-cargo').value;
    const busca = document.getElementById('busca-transparencia')?.value || '';
    const perPage = 10;

    const url = new URL('<?= site_url("transparencia/carregarTabela") ?>');

    url.searchParams.set('page', page);
    url.searchParams.set('cargo', cargo);
    url.searchParams.set('search', busca);
    url.searchParams.set('per_page', perPage);

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => {
            document.getElementById('tabela-container').innerHTML = html;
            bindPaginacaoAjax();
        });
}

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





