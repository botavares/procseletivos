    <legend class="font-18 bold mrg-0">Cursos de Aperfeiçamento</legend>
    <hr>
    <div class="mrg-bottom-30">
        <fieldset class="aperfeicoamento">
            <div class="br-input mb-4 col-sm-10 col-lg-12">
                <label class="text-normal">
                    Aponte seu(s) curso(s) de aperfeiçamento(s):
                </label>

                <?php foreach($aperfeicoamentoClassificatorios as $aperfeicoamento):

                    $idAperfeicoamento = $aperfeicoamento["fk_id_curso"];

                    // quantidade (caso futuramente tenha INPUT)
                    $quantidade = $dadosAperfeicoamentoIndexado[$idAperfeicoamento] ?? 0;

                    if($quantidade > 0){
                        $checked = 'checked';
                    }else{
                        $checked = '';
                    }
                ?>

                    <!-- CHECKBOX -->
                    <?php if($aperfeicoamento["ds_tipo_campo"] == "CHECK"): ?>
                        <div class="br-checkbox mt-3">
                            <input
                                type="checkbox"
                                id="aperfeicoamento<?= $idAperfeicoamento ?>"
                                name="aperfeicoamento<?= $idAperfeicoamento ?>"
                                value="1"
                                <?= $checked ?>
                            >
                            <label for="aperfeicoamento<?= $idAperfeicoamento ?>">
                                <?= $aperfeicoamento["ds_nome_curso"] ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <!-- CASO FUTURAMENTE TENHA INPUT -->
                    <?php if($aperfeicoamento["ds_tipo_campo"] == "INPUT"): ?>
                        <div class=" br-input mt-3">
                            <label for="aperfeicoamento<?= $idAperfeicoamento ?>" class="text-normal">
                                <?= $aperfeicoamento["ds_nome_curso"] ?>
                            </label>
                            <input
                                type="number"
                                min="0"
                                id="aperfeicoamento<?= $idAperfeicoamento ?>"
                                name="aperfeicoamento<?= $idAperfeicoamento ?>"
                                value="<?= old("aperfeicoamento".$idAperfeicoamento, $quantidade) ?>"
                                class="mt-2 col-sm-12 col-lg-4"
                            >
                        </div>
                        <span class="br-message info pdd-10"><div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>Informe o total de cursos de aperfeiçoamentos que você possui de acordo com as áreas citadas acima</span>
                    <?php endif; ?>

                <?php endforeach; ?>

            </div>
        </fieldset>
    </div>
</div>
