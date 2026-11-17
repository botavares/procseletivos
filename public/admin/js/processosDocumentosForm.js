/**
 * Inicializa o formulário de documentos de processos seletivos no painel admin.
 * Recebe os dados necessários via data-attributes do form #formDocumento.
 */
(function () {
    'use strict';

    const form = document.getElementById('formDocumento');
    if (!form) {
        return;
    }

    const tipoSelect = document.getElementById('nm_tipo');
    const arquivoInput = document.getElementById('ds_arquivo');
    const grupoVersaoVigente = document.getElementById('grupoVersaoVigente');
    const versaoVigenteSelect = document.getElementById('nm_versao_vigente');
    const grupoRetificar = document.getElementById('grupoDocumentoRetificar');
    const retificarSelect = document.getElementById('nm_documento_retificado');
    const processoSelect = document.getElementById('fk_processo_referencia');
    const numeroDocInput = document.getElementById('nm_numero_documento');
    const anoDocInput = document.getElementById('nm_ano_documento');
    const avisoSemEdital = document.getElementById('avisoSemEdital');
    const grupoFkIdCargo = document.getElementById('grupoFkIdCargo');
    const cargoSelect = document.getElementById('fk_id_cargo');
    const avisoSemCargo = document.getElementById('avisoSemCargo');
    const grupoArquivo = arquivoInput ? arquivoInput.closest('.form-group') : null;

    if (!tipoSelect || !processoSelect) {
        return;
    }

    // Dados injetados pela view via data-attributes
    const processos = JSON.parse(form.dataset.processos || '[]');
    const documentosProcesso = JSON.parse(form.dataset.documentosProcesso || '[]');
    const cargosProcesso = JSON.parse(form.dataset.cargosProcesso || '[]');
    const retificarInicial = parseInt(form.dataset.retificarInicial || '0', 10);
    const tipoEditalConvocacao = String(form.dataset.tipoEditalConvocacao || '0');
    const cargoSelecionado = parseInt(form.dataset.cargoSelecionado || '0', 10);
    const proximosNumerosConvocacao = JSON.parse(form.dataset.proximosNumerosConvocacao || '{}');
    const acao = form.dataset.acao || 'create';

    // Tipos que exigem arquivo obrigatório: 1 (Edital Abertura) e 2 (Retificação)
    const tiposComArquivoObrigatorio = ['1', '2'];
    // Versão vigente só para edital de abertura
    const tiposComVersaoVigente = ['1'];

    function buscarProcesso(id) {
        return processos.find(function (p) { return p.id === id; }) || null;
    }

    function buscarEditaisDoProcesso() {
        return documentosProcesso.filter(function (d) { return d.tipo === 1; });
    }

    function buscarCargosDoProcesso(processoId) {
        return cargosProcesso.filter(function (c) { return c.processoId === processoId; });
    }

    function atualizarSelectCargo() {
        if (!cargoSelect || !processoSelect) {
            return;
        }

        cargoSelect.innerHTML = '<option value="">Selecione...</option>';
        const processoId = parseInt(processoSelect.value, 10);
        const cargos = buscarCargosDoProcesso(processoId);

        if (cargos.length === 0) {
            avisoSemCargo.style.display = '';
            cargoSelect.value = '';
            cargoSelect.removeAttribute('required');
            return;
        }

        avisoSemCargo.style.display = 'none';
        cargos.forEach(function (cargo) {
            const option = document.createElement('option');
            option.value = cargo.cargoId;
            option.textContent = cargo.nomeCargo;
            cargoSelect.appendChild(option);
        });

        if (cargoSelecionado > 0) {
            cargoSelect.value = String(cargoSelecionado);
        }
    }

    function atualizarSelectRetificar() {
        retificarSelect.innerHTML = '<option value="0">Selecione...</option>';
        const editais = buscarEditaisDoProcesso();

        if (editais.length === 0) {
            avisoSemEdital.style.display = '';
            retificarSelect.value = '0';
            return;
        }

        avisoSemEdital.style.display = 'none';
        editais.forEach(function (edital) {
            const option = document.createElement('option');
            option.value = edital.id;
            option.textContent = edital.label + ' ' + edital.numero + '/' + edital.ano;
            retificarSelect.appendChild(option);
        });

        const idsEditais = editais.map(function (e) { return e.id; });
        if (retificarInicial > 0 && idsEditais.indexOf(retificarInicial) !== -1) {
            retificarSelect.value = String(retificarInicial);
        } else {
            retificarSelect.value = String(editais[editais.length - 1].id);
        }
    }

    function definirReadOnlyCamposDoc(readOnly) {
        numeroDocInput.readOnly = readOnly;
        anoDocInput.readOnly = readOnly;
        if (readOnly) {
            numeroDocInput.classList.add('bg-light');
            anoDocInput.classList.add('bg-light');
        } else {
            numeroDocInput.classList.remove('bg-light');
            anoDocInput.classList.remove('bg-light');
        }
    }

    function preencherCamposDoProcesso() {
        const proc = buscarProcesso(parseInt(processoSelect.value, 10));
        if (proc) {
            numeroDocInput.value = proc.numero;
            anoDocInput.value = proc.ano;
        }
    }

    function preencherCamposConvocacao() {
        if (acao !== 'create') {
            return;
        }

        const processoId = parseInt(processoSelect.value, 10);
        if (!processoId || isNaN(processoId)) {
            return;
        }

        const dados = proximosNumerosConvocacao[processoId];
        if (!dados) {
            return;
        }

        if (dados.numero !== undefined) {
            numeroDocInput.value = dados.numero;
        }
        if (dados.ano !== undefined) {
            anoDocInput.value = dados.ano;
        }
        definirReadOnlyCamposDoc(true);
    }

    function limparCamposDoc() {
        numeroDocInput.value = '';
        anoDocInput.value = '';
        definirReadOnlyCamposDoc(false);
    }

    function ajustarFormulario() {
        const tipo = tipoSelect.value;

        if (grupoArquivo) {
            if (tiposComArquivoObrigatorio.indexOf(tipo) !== -1) {
                grupoArquivo.style.display = '';
                arquivoInput.setAttribute('required', 'required');
                grupoArquivo.querySelector('label').innerHTML = 'Arquivo (PDF) <span class="text-danger">*</span>';
            } else {
                grupoArquivo.style.display = 'none';
                arquivoInput.removeAttribute('required');
                arquivoInput.value = '';
                grupoArquivo.querySelector('label').innerHTML = 'Arquivo (PDF)';
            }
        }

        if (grupoVersaoVigente && versaoVigenteSelect) {
            if (tiposComVersaoVigente.indexOf(tipo) !== -1) {
                grupoVersaoVigente.style.display = '';
                versaoVigenteSelect.setAttribute('required', 'required');
            } else {
                grupoVersaoVigente.style.display = 'none';
                versaoVigenteSelect.removeAttribute('required');
                versaoVigenteSelect.value = '0';
            }
        }

        if (grupoRetificar && retificarSelect) {
            if (tipo === '2') {
                grupoRetificar.style.display = '';
                retificarSelect.setAttribute('required', 'required');
                atualizarSelectRetificar();
            } else {
                grupoRetificar.style.display = 'none';
                retificarSelect.removeAttribute('required');
                retificarSelect.value = '0';
                if (avisoSemEdital) {
                    avisoSemEdital.style.display = 'none';
                }
            }
        }

        if (grupoFkIdCargo && cargoSelect) {
            if (tipo === tipoEditalConvocacao) {
                grupoFkIdCargo.style.display = '';
                cargoSelect.setAttribute('required', 'required');
                atualizarSelectCargo();
            } else {
                grupoFkIdCargo.style.display = 'none';
                cargoSelect.removeAttribute('required');
                cargoSelect.value = '';
                if (avisoSemCargo) {
                    avisoSemCargo.style.display = 'none';
                }
            }
        }

        if (tipo === '1' || tipo === '2') {
            preencherCamposDoProcesso();
            definirReadOnlyCamposDoc(true);
        } else if (tipo === tipoEditalConvocacao) {
            // mantém preenchimento da convocação caso já tenha sido feito
        } else {
            limparCamposDoc();
        }
    }

    processoSelect.addEventListener('change', function () {
        const tipo = tipoSelect.value;
        if (tipo === '1' || tipo === '2') {
            preencherCamposDoProcesso();
        }
        if (tipo === '2') {
            atualizarSelectRetificar();
        }
        if (tipo === tipoEditalConvocacao) {
            atualizarSelectCargo();
            preencherCamposConvocacao();
        }
    });

    tipoSelect.addEventListener('change', function () {
        ajustarFormulario();
        if (tipoSelect.value === tipoEditalConvocacao) {
            preencherCamposConvocacao();
        }
    });
    ajustarFormulario();
})();

// Modal de exclusão de documentos (mantido compatível com jQuery/AdminLTE)
$(function () {
    $('.deleteDocumento').on('click', function () {
        var id = $(this).data('id');
        $('#chavePrimariaDocumento').val(id);
        $('#modalDeleteDocumento').modal('show');
    });
});
