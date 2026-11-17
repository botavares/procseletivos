/**
 * Filtro PCD em DataTable com alternância entre botões de exportação normal/PCD.
 *
 * Marcação esperada no container (ex: .card):
 *   data-iniciar="filtro-pcd-exportacao"
 *   data-url-exportacao="Caminho/Exportar"            (obrigatório)
 *   data-url-exportacao-pcd="Caminho/Exportar?pcd=1"  (obrigatório)
 *   data-tabela="#tabela-paginada"                    (padrão: #tabela-paginada)
 *   data-toggle-pcd="#togglePcd"                      (padrão: #togglePcd)
 *   data-btn-exportar="#btnExportarPlanilha"          (padrão: #btnExportarPlanilha)
 *   data-btn-exportar-pcd="#btnExportarPlanilhaPcd"    (padrão: #btnExportarPlanilhaPcd)
 *   data-coluna-pcd-texto="PCD"                       (padrão: PCD)
 */
(function ($) {
    'use strict';

    function inicializarFiltroPcdExportacao(container) {
        var $container = $(container || 'body');

        var urlExportacao = $container.data('url-exportacao');
        var urlExportacaoPcd = $container.data('url-exportacao-pcd');
        var tabelaSelector = $container.data('tabela') || '#tabela-paginada';
        var toggleSelector = $container.data('toggle-pcd') || '#togglePcd';
        var btnExportarSelector = $container.data('btn-exportar') || '#btnExportarPlanilha';
        var btnExportarPcdSelector = $container.data('btn-exportar-pcd') || '#btnExportarPlanilhaPcd';
        var colunaPcdTexto = $container.data('coluna-pcd-texto') || 'PCD';

        if (!urlExportacao || !urlExportacaoPcd) {
            console.warn('inicializarFiltroPcdExportacao: data-url-exportacao e data-url-exportacao-pcd são obrigatórios');
            return;
        }

        var $tabela = $(tabelaSelector).first();
        var $toggle = $(toggleSelector).first();
        var $btnExportar = $(btnExportarSelector).first();
        var $btnExportarPcd = $(btnExportarPcdSelector).first();

        if (!$tabela.length || !$toggle.length) {
            console.warn('inicializarFiltroPcdExportacao: tabela ou toggle não encontrados');
            return;
        }

        // Exportação
        $btnExportar.on('click', function () {
            window.location.href = urlExportacao;
        });
        $btnExportarPcd.on('click', function () {
            window.location.href = urlExportacaoPcd;
        });

        var pcdColIndex = -1;
        var tabelaEl = $tabela[0];
        if (tabelaEl.rows.length > 0) {
            var headerCells = tabelaEl.rows[0].cells;
            for (var i = 0; i < headerCells.length; i++) {
                if ((headerCells[i].textContent || '').trim() === colunaPcdTexto) {
                    pcdColIndex = i;
                    break;
                }
            }
        }

        if (pcdColIndex >= 0) {
            $.fn.dataTable.ext.search.push(function (settings, data) {
                if (!$toggle.is(':checked')) return true;
                var valor = (data[pcdColIndex] || '').trim();
                return valor === 'SIM';
            });
        }

        $toggle.on('change', function () {
            var mostrarApenasPcd = this.checked;
            $tabela.DataTable().draw();

            $btnExportar.toggle(!mostrarApenasPcd);
            $btnExportarPcd.toggle(mostrarApenasPcd);
        });
    }

    $(document).ready(function () {
        $('[data-iniciar="filtro-pcd-exportacao"]').each(function () {
            inicializarFiltroPcdExportacao(this);
        });
    });
})(jQuery);
