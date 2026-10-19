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
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item black"><a href="<?php echo base_url(); ?>"><i class="fas fa-home"></i></a></li>
    <li class="breadcrumb-item active" aria-current="page">Todos Serviços</li>
  </ol>
</nav>
        <section  id="features"  class="feature-section pt-100" >
                <div class="container" id="destaques">
                    <div class="row" >
                        <div class="col-xl-6 col-lg-7 col-md-9 mx-auto">
                            <div class="section-title text-center mb-55">
                                <!--<span class="wow fadeInDown" data-wow-delay=".2s"><a href="#">Serviços</a></span>-->
                                <h2 class="wow fadeInUp" data-wow-delay=".4s" >Apresentação dos serviços</h2>
                                
                                    <p class="wow fadeInUp" data-wow-delay=".6s">Serviços disponíveis <?php echo date('d/m/Y'); ?></p>
									<p class="wow fadeInUp" data-wow-delay=".6s"><small>Apresentamos <?php echo $contagemServicos?> serviços em ordem alfabética</small></p>
                            </div>
                        </div>
                    </div>
                    <a href="<?php echo base_url(); ?>" class="btn btn-primary btn-lg wow fadeInUp mrg-bottom-15" data-wow-delay=".8s">Voltar</a>
                    <div class="row">
                    <?php
                    foreach($servicos as $valueTodosServicos){
                        foreach($categorias as $valueCategorias){
                        
                            if($valueCategorias->pk_id_categoria == $valueTodosServicos->fk_id_categoria){
                                
                           
                    ?>    
                        <div class="col-lg-3 col-md-6 pdd-top-0">
                            
                            <a href="<?php echo base_url('Microsservicos/redirServicos/'.$valueTodosServicos->pk_id_servico)?>">
                                <div class="feature-box box-style">
                                    <h3 style="color: <?php echo $valueCategorias->ds_cor?>" class="text-center font-14 bold mrg-top-0"><?php echo $valueCategorias->ds_nome_categoria?></h3>
                                    <div class="box-content-style feature-content">
                                        <h4 class="text-center"><?php echo $valueTodosServicos->ds_nome_servico?></h4>
                                        <p><?php echo $valueTodosServicos->ds_descricao_servico?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php
                            }
                        }
                    }
                    ?>
                 <!-- exemplo de estrutura livre
                    <div class="col-lg-3 col-md-6">
                        <div class="feature-box box-style">
                            
                            <div class="box-content-style feature-content">
                                <h4>Lorem lorem</h4>
                                <p>Lorem ipsum dolor sit amet, adipscing elitr, sed diam nonumy eirmod tempor ividunt
                                    labor dolore magna.</p>
                            </div>
                        </div>
                    </div>
                    -->
                </div>
            </div>
            
        </section>
       
        <!-- ========================= FIM DA SEÇÃO DE SERVIÇOS ========================= -->