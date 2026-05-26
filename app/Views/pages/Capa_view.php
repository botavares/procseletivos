
        <div class="preloader">
            <div class="loader">
                <div class="ytp-spinner">
                    <div class="ytp-spinner-container">
                        <div class="ytp-spinner-rotator">
                            <div class="ytp-spinner-left">
                                <div class="ytp-spinner-circle"></div>
                            </div>
                            <div class="ytp-spinner-right">
                                <div class="ytp-spinner-circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM PRELOADER -->

        
             <?php
                if($edital != null && $status == "naovinculado"){
            ?>
            <div class="content" aria-label="" role="alert">
                <div class="br-message success mrg-top-10 pdd-5">
                    <div class="icon"><i class="fas fa-check-circle fa-lg" aria-hidden="true"></i></div>
                    <span class="message-title">BOAS NOTÍCIAS!</span>
                    <span class="message-body"> Existe um edital direcionado ao seu curso!. Clique no botão abaixo para vincular seu cadastro ao edital!.</span>
                 <div>
                <a  class="br-button success voltar mrg-top-10 text-center" href="<?php echo base_url("Cadastros/vincularCandidatoEdital/".$params['id']."/".$edital)?>"><i class="fas fa-link"></i> Vincular ao Edital</a>
            </div>
            <?php
            }else{?>
            <div class="content" aria-label="" role="alert">
                <div class="br-message info mrg-top-10 pdd-5">
                    <div class="icon"><i class="fas fa-info-circle fa-lg" aria-hidden="true"></i></div>
                    <div class="content" aria-label="" role="alert">
                        <span class="message-title">Olá!</span>
                        <span class="message-body"> Seus dados pessoais estarão protegidos nos termos da Lei nº <a style="color:blue" href="https://www.planalto.gov.br/ccivil_03/_ato2015-2018/2017/lei/l13460.htm">13.460/2017</a>.</span>
                    </div>
                </div>
            <?php
            }
            ?>
        </div> 
        
        <div class="row mrg-top-15">
            <div class="col-md-6 offset-md-3 pdd0">
                <div class="border-bottom mrg-bottom-10">
                    <span class="bold font-24">Cadastros </span>
                </div>

                <div class="row">
                    
                        <div class="col-sm-12 d-flex">
                            <div class="br-card col-sm-12">
                                <div class="card-header">
                                    <img src="<?php echo base_url("external/img/cracha.png")?>" alt="Cadastros" class="icones" />
                                </div>
                                <div class="card-content text-center bold font-24 font-blue">
                                    <a href="<?php echo base_url("Cadastros")?>" class="col-md-12">
                                        <?php echo $labelCaption?>
                                    </a>
                                </div>
                                <div class="card-footer text-center border-top pdd-top-12">
                                    Aqui você irá cadastrar seus dados pessoais e classificatórios para participar dos processos seletivos.
                                </div>
                            </div>
                        </div>
                    

                        <div class="col-sm-12 d-flex">
                            <div class="br-card col-sm-12">
                                <div class="card-header">
                                    <img src="<?php echo base_url("external/img/editais.png")?>" alt="Editais disponíveis" class="icones" />
                                </div>
                                <div class="card-content text-center bold font-24 font-blue">
                                    <a href="<?php echo base_url("Editais")?>" class="col-md-12">
                                      Editais
                                    </a>
                                </div>
                                <div class="card-footer text-center border-top pdd-top-20">
                                    Aqui você tem acesso aos editais vigentes e os encerrados.
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        
            <div class="col-md-6 offset-md-3">
                <div class="border-bottom mrg-bottom-10">
                    <span class="bold font-24">Serviços</span>
                </div>
                <div class="row">
                    <div class="col-sm-12 d-flex">
                        <div class="br-card col-sm-12">
                            <div class="card-header">
                                <img src="<?php echo base_url("external/img/lista-publica.png")?>" alt="Lista-Publica" class="icones" />
                            </div>
                            <div class="card-content text-center bold font-24 font-blue">
                                <a href="<?php echo site_url("transparencia")?>" class="col-md-12">
                                    Lista Pública de Cadastrados
                                </a>
                            </div>
                            <div class="card-footer text-center border-top pdd-top-20">
                                Acesse a lista dos cadastrados e sua situação cadastral.
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-12 d-flex">
                            <div class="br-card col-sm-12">
                                <div class="card-header">
                                    <img src="<?php echo base_url("external/img/ouvidoria.png")?>" alt="Ouvidoria" class="icones" />
                                </div>
                                <div class="card-content text-center bold font-24 font-blue">
                                    <a href="<?php echo base_url('PerguntasFrequentes')?>" class="col-md-12">
                                        Perguntas Frequentes
                                    </a>
                                </div>
                                <div class="card-footer text-center border-top pdd-top-20">
                                    Encontre aqui respostas para as perguntas mais frequentes .
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    
</main>
             