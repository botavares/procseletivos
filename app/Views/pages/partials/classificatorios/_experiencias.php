<?php
foreach($experienciasClassificatorias as $valueExperiencia){
    
    $idExperiencia = $valueExperiencia->fk_id_experiencia;
    $nomeCampo = "quantidadeExperiencia".$idExperiencia;
    //pegar a array salva ou o zero se não existir
    $quantidadeBanco = $experienciasSalvas[$idExperiencia] ?? 0;
    $valorSelecionado = old($nomeCampo, $quantidadeBanco);

    
    if($valueExperiencia->ds_tipo_campo == 'SELECT'){
        $totalDeAnos = ($valueExperiencia->ds_pontuacao_maxima/$valueExperiencia->ds_pontuacao_minima);
?>
        <legend class="font-18 bold mrg-0" >Experiência <?= $valueExperiencia->ds_nome_experiencia?></legend>
        <hr>
        <div class=" mrg-bottom-30">
            <fieldset class="experiencia">
                <div class="col-sm-10 col-lg-12">
                    <label for="select-experiencia" class="text-normal"></label>
                    <div class="select-container mt-3">
                        <i class="fas fa-search"></i>
                        <select id="<?= $nomeCampo?>" class="select-experiencia form-control" name="<?= $nomeCampo?>" required>
                            <option value="0" <?php echo set_select('quantidadeExperiencia','0')?>>Não possui <?php echo $valueExperiencia->ds_tipo_experiencia?></option>
                                <?php
                                    for($i=1;$i<=$totalDeAnos;$i++){
                                        if($i == $totalDeAnos){
                                            $textoOption = $i." ou mais ". $valueExperiencia->ds_tipo_experiencia;
                                        }else{
                                            $textoOption = $i." ".$valueExperiencia->ds_tipo_experiencia;
                                        }
                                    ?>
                            <option value="<?= $i ?>" <?= (string)$valorSelecionado === (string)$i ? 'selected' : '' ?>><?php echo $textoOption?></option>
                                    <?php
                                    }
                                ?>
                        </select>
                        <i class="fas fa-angle-down"></i>
                    </div>
                </div>
            </fieldset>
        </div>
    <?php 
    }
}
?>
