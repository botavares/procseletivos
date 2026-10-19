<body>
        <!--[if lte IE 9]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
        <![endif]-->

        <!-- ========================= INICIO DO PRE-LOADER ========================= -->
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


        
       
        <!-- ========================= ÁREA DE BUSCA ========================= -->
        <section id="areabusca" class="feature-section pt-100">
            <div id="ferramenta-busca" class="row">
                <div class="col-md-5 mx-auto">
                    
                        <div class="small fw-light text-center"><h3 class="branco bold font22">ENCONTRE SEU SERVIÇO</h3></div>
                            <div class="boxBusca">
                                <div class="input-group">
                                    <input class="form-control border-end-0 border rounded-pill" type="search" value="" id="busca-servicos-servidores-input">
                                    <span class="input-group-append">
                                        <button class="btn btn-outline-secondary bg-white border-bottom-0 border rounded-pill icone-search">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                                <div style="position:relative;z-index:100;"class="resultadoBusca chanfrado" >
                                    <ul id="resultados"></ul>
                                </div>
                            </div>
                        </div>
                    
                    
                </div>
            </div>
        </section>




        <!-- ========================= INÍCIO DE MAIS ACESSADOS ========================= -->

        <section  id="mais-acessados"  class="feature-section pt-100">
            <div class="container">
                <div class="row">
                
                    <div class="col-xl-6 col-lg-7 col-md-9 mx-auto">
                        <div class="section-title text-center mb-55">
                            <span class="wow fadeInDown"  data-wow-delay=".2s"><a href="#">Servicos para Servidores Municipais de Divinópolis</a></span>
                            <p class="wow fadeInUp" data-wow-delay=".6s">Abaixo, os serviços para os servidores municipais (em ordem alfabética). </p>
                        </div>
                    </div>
                </div>

                <div class="row pdd-bootom-0">
                    <?php
                        foreach($maisAcessados as $maisAcessado){
                    ?>   
                    <div class="col-lg-3 col-md-6">
                        <a href="<?php echo base_url('Microsservicos/redirServicos/'.$maisAcessado->pk_id_servico)?>" target="_blank">
                            <div class="feature-box box-style">
                            <h3 style="color: <?php echo $maisAcessado->ds_cor?>" class="text-center font-14 bold mrg-top-0"><?php echo $maisAcessado->ds_nome_categoria?></h3>
                                <div class="box-content-style feature-content">
                                    <h4 class="text-center"><?php echo $maisAcessado->ds_nome_servico?></h4>
                                    <p><?php echo $maisAcessado->ds_descricao_servico?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                   <?php
                        }
                        ?>
                    
                </div>
               
            </div>
            
        </section>
        <!-- ========================= FIM DA SEÇÃO DE MAIS ACESSADOS ========================= -->