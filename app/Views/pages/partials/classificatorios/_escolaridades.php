    <legend class="font-18 bold mrg-0">Escolaridade</legend>
    <hr>
    <div class="mrg-bottom-30">
        <fieldset class="experiencia">
            <div class="br-input mb-4 mt-4col-sm-10 col-lg-12">
                <label class="text-normal">Aponte sua escolaridade:</label>

                <?php foreach($escolaridadesClassificatorias as $escolaridade):

                    $campoEscolaridade = "escolaridade".$escolaridade->fk_id_escolaridade;
                    $idEscolaridade = $escolaridade->fk_id_escolaridade;


                    // quantidade já cadastrada
                    $quantidade = $dadosEscolaridadeIndexado[$idEscolaridade] ?? 0;
                    if($quantidade > 0){
                        $checked = 'checked';
                    }else{
                        $checked = '';
                    }
                ?>

                    <!-- CHECKBOX -->
                    <?php if($escolaridade->ds_tipo_campo == "CHECK"): ?>
                        <div class="br-checkbox mt-3">
                            <input
                                type="checkbox"
                                id="checkbox-escolaridade<?= $idEscolaridade ?>"
                                name= "<?= $campoEscolaridade ?>"
                                value="1"
                                <?= $checked ?>
                            >
                            <label for="checkbox-escolaridade<?= $idEscolaridade ?>">
                                <?= $escolaridade->ds_nome_escolaridade ?>
                            </label>
                        </div>
                    <?php endif; ?>

                    <!-- INPUT NUMÉRICO -->
                    <?php if($escolaridade->ds_tipo_campo == "INPUT"): ?>
                        <div class="mt-3">
                            <label for="input-escolaridade<?= $idEscolaridade ?>" class="text-normal">
                                <?= $escolaridade->ds_nome_escolaridade ?>
                            </label>
                            <input
                                type="number"
                                min="0"
                                id="input-escolaridade<?= $idEscolaridade ?>"
                                name="escolaridade<?= $idEscolaridade ?>"
                                value="<?= old("escolaridade".$idEscolaridade, $quantidade) ?>"
                                class="form-control mt-2 col-sm-12 col-lg-4"
                            >
                        </div>
                        <span class="br-message info pdd-10"><div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>Será observada a pontuação máxima para cada escolaridade de acordo com o edital.</span>
                    <?php endif; ?>

                <?php endforeach; ?>

            </div>
        </fieldset>
    </div>
