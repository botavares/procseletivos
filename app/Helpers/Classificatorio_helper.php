<?php

/**
 * Helper para renderização de campos do formulário classificatório
 * Padrão Gov.br (Design System)
 */

if (!function_exists('renderCampoExperienciaSelect')) {
    /**
     * Renderiza campo SELECT de experiência profissional
     * 
     * @param object $experiencia Dados da experiência
     * @param mixed $valorSalvo Valor já salvo no banco (pode ser int, string ou null)
     * @return string HTML do campo
     */
    function renderCampoExperienciaSelect(object $experiencia, $valorSalvo = 0): string
    {
        $idExperiencia = $experiencia->fk_id_experiencia;
        $nomeCampo = "quantidadeExperiencia" . $idExperiencia;
        
        // Converte valor salvo para int de forma segura
        $valorSalvoInt = (int) ($valorSalvo ?? 0);
        $valorSelecionado = old($nomeCampo, $valorSalvoInt);
        
        $totalDeAnos = (int)($experiencia->ds_quantidade_maxima / $experiencia->ds_quantidade_minima);
        
        $html = '<div class="mrg-bottom-30">';
        $html .= '<fieldset class="experiencia">';
        $html .= '<legend class="font-18 bold mrg-0">Experiência ' . esc($experiencia->ds_nome_experiencia) . '</legend>';
        $html .= '<hr>';
        $html .= '<div class="col-sm-10 col-lg-12">';
        $html .= '<label for="' . $nomeCampo . '" class="text-normal">Informe sua experiência:</label>';
        $html .= '<div class="select-container mt-3">';
        $html .= '<i class="fas fa-search"></i>';
        $html .= '<select id="' . $nomeCampo . '" class="select-experiencia form-control" name="' . $nomeCampo . '" required>';
        $html .= '<option value="0" ' . ($valorSelecionado == 0 ? 'selected' : '') . '>Não possui ' . esc($experiencia->ds_tipo_experiencia) . '</option>';
        
        for ($i = 1; $i <= $totalDeAnos; $i++) {
            $textoOption = ($i == $totalDeAnos) 
                ? $i . " ou mais " . $experiencia->ds_tipo_experiencia 
                : $i . " " . $experiencia->ds_tipo_experiencia;
            
            // Comparação flexível (== em vez de ===)
            $selected = ($valorSelecionado == $i) ? 'selected' : '';
            $html .= '<option value="' . $i . '" ' . $selected . '>' . esc($textoOption) . '</option>';
        }
        
        $html .= '</select>';
        $html .= '<i class="fas fa-angle-down"></i>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</fieldset>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('renderCampoEscolaridadeCheckbox')) {
    /**
     * Renderiza checkbox de escolaridade
     * 
     * @param object $escolaridade Dados da escolaridade
     * @param array $idsSelecionados IDs já selecionados pelo candidato
     * @return string HTML do checkbox
     */
    function renderCampoEscolaridadeCheckbox(object $escolaridade, array $idsSelecionados): string
    {
        $idEscolaridade = $escolaridade->fk_id_escolaridade;
        $checked = in_array($idEscolaridade, $idsSelecionados) ? 'checked' : '';
        $nomeCampo = 'escolaridade' . $idEscolaridade;
        
        $html = '<div class="br-checkbox mt-3">';
        $html .= '<input type="checkbox" id="' . $nomeCampo . '" name="' . $nomeCampo . '" value="' . esc($escolaridade->ds_pontuacao_minima) . '" ' . $checked . '>';
        $html .= '<label for="' . $nomeCampo . '">' . esc($escolaridade->ds_nome_escolaridade) . '</label>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('renderCampoAperfeicoamentoCheckbox')) {
    /**
     * Renderiza checkbox de aperfeiçoamento
     * 
     * @param object|array $aperfeicoamento Dados do aperfeiçoamento
     * @param array $idsSelecionados IDs já selecionados pelo candidato
     * @return string HTML do checkbox
     */
    function renderCampoAperfeicoamentoCheckbox($aperfeicoamento, array $idsSelecionados): string
    {
        // Normaliza para objeto
        $ap = is_array($aperfeicoamento) ? (object)$aperfeicoamento : $aperfeicoamento;
        
        $idAperfeicoamento = $ap->fk_id_curso;
        $checked = in_array($idAperfeicoamento, $idsSelecionados) ? 'checked' : '';
        $nomeCampo = 'aperfeicoamento' . $idAperfeicoamento;
        $nomeCurso = $ap->ds_nome_curso ?? ($ap->ds_nome_escolaridade ?? '');
        $pontuacao = $ap->ds_pontuacao_minima ?? 0;
        
        $html = '<div class="br-checkbox mt-3">';
        $html .= '<input type="checkbox" id="' . $nomeCampo . '" name="' . $nomeCampo . '" value="' . esc($pontuacao) . '" ' . $checked . '>';
        $html .= '<label for="' . $nomeCampo . '">' . esc($nomeCurso) . '</label>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('renderSecaoExperiencias')) {
    /**
     * Renderiza seção completa de experiências profissionais
     * 
     * @param array $experiencias Lista de experiências
     * @param array $experienciasSalvas IDs e valores salvos
     * @return string HTML da seção
     */
    function renderSecaoExperiencias(array $experiencias, array $experienciasSalvas): string
    {
        if (empty($experiencias)) {
            return '';
        }
        
        $html = '';
        
        foreach ($experiencias as $experiencia) {
            // Só renderiza se for classificatória (não obrigatória) e tipo SELECT
            if ($experiencia->ds_obrigatorio !== '0') {
                continue;
            }
            
            if ($experiencia->ds_tipo_campo !== 'SELECT') {
                continue;
            }
            
            $idExperiencia = $experiencia->fk_id_experiencia;
            $quantidadeSalva = $experienciasSalvas[$idExperiencia] ?? 0;
            
            $html .= renderCampoExperienciaSelect($experiencia, $quantidadeSalva);
        }
        
        return $html;
    }
}

if (!function_exists('renderSecaoEscolaridades')) {
    /**
     * Renderiza seção completa de escolaridades classificatórias
     * 
     * @param array $escolaridades Lista de escolaridades
     * @param array $idsSelecionados IDs já selecionados
     * @return string HTML da seção
     */
    function renderSecaoEscolaridades(array $escolaridades, array $idsSelecionados): string
    {
        if (empty($escolaridades)) {
            return '';
        }
        
        $html = '<legend class="font-18 bold mrg-0">Escolaridade</legend>';
        $html .= '<hr>';
        $html .= '<div class="mrg-bottom-30">';
        $html .= '<fieldset class="escolaridade">';
        $html .= '<div class="col-sm-10 col-lg-12">';
        $html .= '<label class="text-normal">Aponte sua escolaridade:</label>';
        
        foreach ($escolaridades as $escolaridade) {
            if ($escolaridade->ds_tipo_campo !== 'CHECK') {
                continue;
            }
            
            $html .= renderCampoEscolaridadeCheckbox($escolaridade, $idsSelecionados);
        }
        
        $html .= '</div>';
        $html .= '</fieldset>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('renderSecaoAperfeicoamentos')) {
    /**
     * Renderiza seção completa de aperfeiçoamentos
     * 
     * @param array $aperfeicoamentos Lista de aperfeiçoamentos
     * @param array $idsSelecionados IDs já selecionados
     * @return string HTML da seção
     */
    function renderSecaoAperfeicoamentos(array $aperfeicoamentos, array $idsSelecionados): string
    {
        if (empty($aperfeicoamentos)) {
            return '';
        }
        
        $html = '<legend class="font-18 bold mrg-0">Aperfeiçoamento</legend>';
        $html .= '<hr>';
        $html .= '<div class="mrg-bottom-30">';
        $html .= '<fieldset class="aperfeicoamento">';
        $html .= '<div class="col-sm-10 col-lg-12">';
        $html .= '<label class="text-normal">Aponte seu(s) curso(s) de aperfeiçoamento(s):</label>';
        
        foreach ($aperfeicoamentos as $aperfeicoamento) {
            $ap = is_array($aperfeicoamento) ? (object)$aperfeicoamento : $aperfeicoamento;
            
            if (($ap->ds_tipo_campo ?? '') !== 'CHECK') {
                continue;
            }
            
            $html .= renderCampoAperfeicoamentoCheckbox($ap, $idsSelecionados);
        }
        
        $html .= '</div>';
        $html .= '</fieldset>';
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('renderBreadcrumbClassificatorio')) {
    /**
     * Renderiza breadcrumb do formulário classificatório
     * 
     * @param string $baseUrl URL base do projeto
     * @return string HTML do breadcrumb
     */
    function renderBreadcrumbClassificatorio(string $baseUrl): string
    {
        return '
        <nav class="br-breadcrumb" aria-label="Breadcrumbs">
            <ol class="crumb-list" role="list">
                <li class="crumb home">
                    <a class="br-button circle" href="' . $baseUrl . '/Home">
                        <span class="sr-only">Página inicial</span>
                        <i class="fas fa-home"></i>
                    </a>
                </li>
                <li class="crumb">
                    <i class="icon fas fa-chevron-right"></i>
                    <a href="' . $baseUrl . '/Cadastros">Candidato</a>
                </li>
                <li class="crumb" data-active="active">
                    <i class="icon fas fa-chevron-right"></i>
                    <span tabindex="0" aria-current="page">Dados Classificatórios (Página Atual)</span>
                </li>
            </ol>
        </nav>';
    }
}

if (!function_exists('renderMensagemInfo')) {
    /**
     * Renderiza mensagem informativa no padrão Gov.br
     * 
     * @param string $titulo Título da mensagem
     * @param string $conteudo Conteúdo HTML da mensagem
     * @return string HTML da mensagem
     */
    function renderMensagemInfo(string $titulo, string $conteudo): string
    {
        return '
        <div class="mrg-top-10 pdd-5 br-message info">
            <div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>
            <div class="content" role="alert">
                <span class="message-title">' . esc($titulo) . '</span>
                <span class="message-body">' . $conteudo . '</span>
            </div>
        </div>';
    }
}
