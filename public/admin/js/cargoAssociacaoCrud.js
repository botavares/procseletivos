/**
 * Inicializa o comportamento de CRUD de associações em formulários de cargo.
 * Espera a seguinte marcação/data-attributes no formulário/container:
 *
 * data-iniciar="associacao-cargo"        (obrigatório no formulário/container)
 * data-table="#idTabela"                 (obrigatório)
 * data-btn-alterar=".btn-alterar-xxx"    (obrigatório)
 * data-btn-retirar=".btn-retirar-xxx"    (obrigatório)
 * data-url-buscar="Controller/buscarAssociacao" (obrigatório)
 * data-campo-pk="#pk_id_..."
 * data-campo-item="#fk_id_..."
 * data-campo-min="#ds_pontuacao_minima"
 * data-campo-max="#ds_pontuacao_maxima"
 * data-campo-tipo="input[name='ds_tipo_campo']"
 * data-modal-retirar="#modalRetirarXxx"
 * data-modal-pk="#retirar_pk_id"         (padrão)
 * data-modal-nome="#retirar_nome_xxx"
 * data-nome-entidade="critério"          (padrão: associação)
 * data-valor-input="INPUT"               (padrão)
 * data-valor-check="CHECK"               (padrão)
 * data-campo-acao="#acao"                (padrão)
 * data-acao-create="create"              (padrão)
 * data-acao-update="update"              (padrão)
 * data-btn-limpar="#btnLimpar"           (padrão)
 */
(function ($) {
    'use strict';

    function inicializarAssociacaoCargo(container) {
        var $container = $(container || 'body');

        var formSelector = $container.data('form') || ($container.is('form') ? null : 'form');
        var $form = formSelector ? $container.closest(formSelector).length
            ? $container.closest(formSelector).first()
            : $(formSelector).first()
            : $container;

        var tableSelector = $container.data('table');
        var btnLimparSelector = $container.data('btn-limpar') || '#btnLimpar';
        var btnAlterarSelector = $container.data('btn-alterar');
        var btnRetirarSelector = $container.data('btn-retirar');
        var campoPkSelector = $container.data('campo-pk');
        var campoItemSelector = $container.data('campo-item');
        var campoMinSelector = $container.data('campo-min');
        var campoMaxSelector = $container.data('campo-max');
        var campoTipoSelector = $container.data('campo-tipo');
        var modalRetirarSelector = $container.data('modal-retirar');
        var modalPkSelector = $container.data('modal-pk') || '#retirar_pk_id';
        var modalNomeSelector = $container.data('modal-nome');
        var urlBuscar = $container.data('url-buscar');
        var nomeEntidade = $container.data('nome-entidade') || 'associação';
        var valorInput = $container.data('valor-input') || 'INPUT';
        var valorCheck = $container.data('valor-check') || 'CHECK';
        var campoAcaoSelector = $container.data('campo-acao') || '#acao';
        var acaoCreate = $container.data('acao-create') || 'create';
        var acaoUpdate = $container.data('acao-update') || 'update';

        if (!tableSelector || !btnAlterarSelector || !btnRetirarSelector || !urlBuscar) {
            console.warn('inicializarAssociacaoCargo: data-attributes obrigatórios ausentes');
            return;
        }

        var $table = $(tableSelector).first();

        // DataTable
        $table.DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true
        });

        // Modal de retirada
        if (modalRetirarSelector) {
            $(document).on('click', btnRetirarSelector, function () {
                var id = $(this).data('id');
                var nome = $(this).data('nome');

                $(modalPkSelector).val(id);
                if (modalNomeSelector) {
                    $(modalNomeSelector).text(nome);
                }
                $(modalRetirarSelector).modal('show');
            });
        }

        // Carregar dados no formulário ao clicar em alterar
        $(document).on('click', btnAlterarSelector, function () {
            var id = $(this).data('id');

            $.ajax({
                url: urlBuscar + '/' + id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data && !data.erro) {
                        if (campoPkSelector) {
                            var nomeCampoPk = $form.find(campoPkSelector).attr('name');
                            if (nomeCampoPk && data[nomeCampoPk] !== undefined) {
                                $form.find(campoPkSelector).val(data[nomeCampoPk]);
                            }
                        }

                        $form.find(campoAcaoSelector).val(acaoUpdate);

                        if (campoItemSelector) {
                            var nomeCampoItem = $form.find(campoItemSelector).attr('name');
                            $form.find(campoItemSelector).val(data[nomeCampoItem]).trigger('change');
                        }
                        if (campoMinSelector) {
                            var nomeCampoMin = $form.find(campoMinSelector).attr('name');
                            $form.find(campoMinSelector).val(data[nomeCampoMin]);
                        }
                        if (campoMaxSelector) {
                            var nomeCampoMax = $form.find(campoMaxSelector).attr('name');
                            $form.find(campoMaxSelector).val(data[nomeCampoMax]);
                        }

                        if (campoTipoSelector) {
                            var $radios = $form.find(campoTipoSelector);
                            $radios.prop('checked', false);

                            if (data.ds_tipo_campo === valorInput) {
                                $radios.filter('[value="' + valorInput + '"]').prop('checked', true);
                            } else if (data.ds_tipo_campo === valorCheck) {
                                $radios.filter('[value="' + valorCheck + '"]').prop('checked', true);
                            }
                        }

                        $('html, body').animate({
                            scrollTop: $form.offset().top - 100
                        }, 500);
                    } else {
                        alert('Erro ao buscar dados da(o) ' + nomeEntidade + '.');
                    }
                },
                error: function () {
                    alert('Erro ao buscar dados da(o) ' + nomeEntidade + '.');
                }
            });
        });

        // Botão Limpar
        $(document).on('click', btnLimparSelector, function () {
            $form[0].reset();
            if (campoPkSelector) {
                $form.find(campoPkSelector).val('');
            }
            $form.find(campoAcaoSelector).val(acaoCreate);
        });
    }

    $(document).ready(function () {
        $('[data-iniciar="associacao-cargo"]').each(function () {
            inicializarAssociacaoCargo(this);
        });
    });
})(jQuery);
