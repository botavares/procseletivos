<legend class="font-18 bold mrg-0">Critérios Adicionais</legend>
    <hr>
    <div class="mrg-bottom-30">
        <fieldset class="aperfeicoamento">
            <div class="br-input mb-4 col-sm-10 col-lg-12">
                <label class="text-normal"></label>
                <?php foreach($criteriosAdicionaisClassificatorios as $criteriosAdicionais):
                    $idCriterio =  $criteriosAdicionais->fk_id_criterio;
                    // quantidade (caso futuramente tenha INPUT)

                    $quantidade = $dadosCriterioAdicionalIndexado[$idCriterio] ?? 0;

                    if($quantidade > 0){
                        $checked = 'checked';
                    }else{
                        $checked = '';
                    }
                ?>
                <!-- CHECKBOX -->
                <?php if($criteriosAdicionais->ds_tipo_campo == "CHECK"): ?>
                        <div class="br-checkbox mt-3">
                            <input
                                type="checkbox"
                                id="criterio<?= $idCriterio ?>"
                                name="criterio<?= $idCriterio ?>"
                                value="1"
                                <?= $checked ?>
                            >
                            <label for="criterio<?= $idCriterio ?>">
                                <?= $criteriosAdicionais->ds_nome_criterio ?>
                            </label>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </fieldset>
    </div>
