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
                    
                        <div class="small fw-light text-center"><h3 class="branco bold font26">PESQUISE AQUI:</h3></div>
                            <div class="boxBusca">
                                <div class="input-group">
                                    <input class="form-control border-end-0 border rounded-pill" type="search" value="" id="busca-telefones-input" placeholder="Digite aqui o nome da unidade ou setor">
                                    <span class="input-group-append">
                                        <button class="btn btn-outline-secondary bg-white border-bottom-0 border rounded-pill icone-search">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                                <div class=" chanfrado" >
                                    
                                </div>
                            </div>
                        </div>
                    
                    
                </div>
            </div>
        </section>
        <!-- ========================= FIM DE AREA DE BUSCA ========================= -->
       
        <!-- ========================= INÍCIO SEÇÃO DE SERVIÇOS ========================= -->
        <section  id="features"  class="feature-section pt-100" >
                <div class="container" id="destaques">
                    <div class="row" >
                        <div class="col-xl-6 col-lg-7 col-md-9 mx-auto">
                            <div class="section-title text-center mb-55">
                                <!--<span class="wow fadeInDown" data-wow-delay=".2s"><a href="#">Serviços</a></span>-->
                                <h2 class="wow fadeInUp" data-wow-delay=".4s" >Telefones Municipais</h2>
                                    <p class="wow fadeInUp" data-wow-delay=".6s"></p>
                            </div>
                        </div>
                    </div>
                    <div id="resultados" class="row">
                    </div>
                </div>
      
            
        </section>
       
        <!-- ========================= FIM DA SEÇÃO DE SERVIÇOS ========================= -->



        <!-- ========================= TELEFONES EM ORDEM ALFABÉTICA========================= -->
        <section  id="mais-acessados"  class="feature-section pt-100">
            <div style="width:100%;" class="container">
                <div class="row">
                
                    <div class="col-xl-6 col-lg-7 col-md-9 mx-auto">
                        <div class="section-title text-center mb-55">
                            <span class="wow fadeInDown" data-wow-delay=".2s"><a href="#">Telefones</a></span>
                            <p class="wow fadeInUp" data-wow-delay=".6s">Abaixo, os nossos telefones por setores e ordem alfabética. </p>
                        </div>
                    </div>
                </div>

                	<?php
						foreach($setores as $setor){
					?>
						 <div class="tab-setor col-lg-4 border text-center chanfrado-topo " >
                         	<?php echo $setor->ds_nome_setor?>
                         </div>    
						
						<?php
							foreach($unidades as $unidade){
								if($unidade->fk_id_setor == $setor->pk_id_setor){
						?>   
						<div  class="box-telefones border chanfrado mrg-bottom-10 pdd-bottom-0 row">
							<a style="text-decoration: none; display: block; width:100%;" href="#">

								<div class="box-content-style feature-content col-lg-12 row" >
										<div id="nome-unidade"class="col-lg-4 border-right">
											<h3 style="font-size:1.1em" class="text-left bold">
												<?php echo $unidade->ds_nome_unidade?>
											</h3>
										</div>
										<div id="telefones-unidade" class="col-lg-4 border-right text-center">
											<?php
											 $countSetor = 0;
											foreach($telefones as $telefone){
												if($unidade->pk_id_unidade == $telefone->fk_id_unidade){
											?>
											<span style="color:red; font-size:1em;" class="bold red">
											<?php
													if(strlen($telefone->ds_telefone) == 10){
														$telefoneMask =  mask($telefone->ds_telefone, '(##) ####-####');
													}else if(strlen($telefone->ds_telefone) == 11){
														$telefoneMask =  mask($telefone->ds_telefone, '(##) #####-####');
													}else{
														$telefoneMask = $telefone->ds_telefone;
													}
											?>
												<?php 
													if($countSetor == 0){
														echo $telefoneMask;
													}else{
														echo "  /  ".$telefoneMask;
													}

												?>
												</span>
											<?php
												 $countSetor++;
												}else{
													$countSetor = 0;
												}

												}
											?>
										</div>
										<div id="endereco-setor" class="col-lg-4">
											<p class="text-left">
												<!--
												<span style="color:#000; font-size: 14px" class="bold black">
													<?php echo " "?>
												</span>
											-->
												<p style="color:#000;">
													<?php $numero = ($unidade->ds_numero_unidade == "") ? "" : $unidade->ds_numero_unidade?>
													<?php echo $unidade->ds_rua_unidade.", ".$numero." Bairro: ".$unidade->ds_bairro_unidade." - CEP: ".$unidade->ds_cep_unidade?>
												</p>
											</p>
										</div>
									</div>


							</a>
							
							<?php
												if($unidade->ds_hora_inicio != ""){
											?>
											 <span style="margin-bottom:0px;" class="bold mrg-top-20">
												<?php echo "Horário de Funcionamento: De ".date("H:i", strtotime($unidade->ds_hora_inicio))." as ".date("H:i", strtotime($unidade->ds_hora_encerra))?>
											</span>

											<?php
												}
							   ?>
							
						</div>

						<?php
								}
							}
						}
						?>
					</div>
               
            
            
        </section>
                    -->
        <!-- ========================= FIM DA SEÇÃO DE MAIS ACESSADOS ========================= -->