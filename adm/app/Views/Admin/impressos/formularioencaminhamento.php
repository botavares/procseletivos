<html>
	<head>
		<meta charset="UTF-8">
		<style type="text/css">
			@page :first{
				margin-top:10px;
			}
			@page{
				margin-top:10px;
			}
				header{
					width:100%;
					height:100px;
					position:fixed;
					top: 0;
				}
				footer{
					height:50px;
					position: fixed;
					bottom: 0;
					left: 0;
					width: 100%;
					text-align: center;
					font-size: 10px;
					color: gray;
					border-top: 1px solid #ddd;
					padding: 5px 0;
				}

            	body{
					padding-top: 100px;
					padding-bottom: 50px;
					font-size:20px;
					font-family: "Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Arial, "sans-serif";
				}
				main{
					page-break-inside: auto;
				}
				main table{width:100%;border-collapse: collapse;}
			#brasao img{margin-left:20%; width:80%;float:left;}
			#brasao{width: 10%;}
			#prefeitura h1{font-size: 18px; text-align:center;margin:1px;}
			#prefeitura h2{font-size: 16px; text-align:center;margin:1px;}
			.titulo{
				margin-top: 60px;
			}
			main h1{
				text-align: center;
				font-size: 18px;
			}
			.dadosManifesto{
				font-size:16px;
				margin-top: 50px;
				padding:5px;
			}
			.dadosManifesto span{
				margin:5px 5px;
				font-weight: bold;
			}

			table{
				width: 100%;
				border-collapse: collapse;
			}
			tr:nth-child(odd) {
			background-color: #e2e2e2; /* Cinza claro para linhas ímpares */
			}

			tr:nth-child(even) {
			background-color: #ffffff; /* Branco para linhas pares */
			}

			td, th {
			padding: 8px; /* Espaçamento interno das células */
			text-align: left; /* Alinhamento do texto */
			}

			tr:hover {
			background-color: #e0e0e0; /* Cinza mais escuro ao passar o mouse */
			}
			
		</style>
		<title>Formulário de Encaminhamento</title>
	</head>
	<?php $encoding = mb_internal_encoding(); ?>
	<body>
		<div style="width:55%; position: absolute; left:11%; right: 0; top: 0; bottom: 0;z-index:-99;">
			<img src="<?php echo $fundo?>" style="width: 150mm; height: 127mm; margin: 250px auto; opacity:0.2;z-index:-99;"/>
		</div>
		<header>
			<div id="brasao">
				<img src="<?php echo $brasao?>"/>
			</div>
			<div id="prefeitura">
				<h1>PREFEITURA MUNICIPAL DE DIVINÓPOLIS</h1>
				<h2>CONTROLADORIA GERAL DE DIVINÓPOLIS</h2>
				<h1 class="titulo text-center"><?php echo $titulo?></h1>
			</div>
		</header>
		<footer>
			<div id="link">
				<span style="text-align: center;">Avenida Paraná, 2601 - Sala 316 - Bairro São José CEP: 35.501-170 - Divinópolis/MG</span>
				<span style="text-align: center;">E-mail: ouvidoria@divinopolis.mg.gov.br</span>
				<span style="text-align: center;">Telefone: (37) 3229-8110</span>
			</div>
			<?php 
				$dia = date("d");
				$meses = array('janeiro','fevereiro','março','abril','maio','junho','julho','agosto','setembro','outubro','novembro','dezembro');
				for($i=1;$i<13;$i++){
					$nmmes = date('m')-1;
					$mes = $meses[$nmmes];
				}
			?>
			<div id="aviso">
				<h2>DIVINÓPOLIS, <?php echo strtoupper($dia." de ".$mes. " de ".date("Y"))?></h2>
			</div>
		</footer>
		<?php $ano1 = date('Y')+1?>




			<main>
				<div class="dadosManifesto">
					<div style="margin-bottom:50px;">
						<span style="float: left;">PROTOCOLO Nº: <?php echo $protocolo?></span>
						<span style="float: right; ">Data: <?php echo date("d/m/Y", strtotime($data))?></span>
					</div>

					<div style="margin-bottom:20px;">
						<table class="border:none;">
							<tr>
								<td style="font-weight:bold;">Manifestação:</td>
								<td><?php echo $manifestante?></td>
							</tr>
							<?php if($manifestante != "ANÔNIMO"):?>
								<tr>
									<td style="font-weight:bold;">Nome:</td>
									<td><?php echo $nome?></td>
								</tr>
								<tr>	
									<td style="font-weight:bold;">Bairro:</td>
									<td><?php echo $bairro?></td>
								</tr>
								<tr>
									<td style="font-weight:bold;">Telefone:</td>
									<td><?php echo $telefone?></td>
								</tr>
								<tr>
									<td style="font-weight:bold;">Email:</td>
									<td><?php echo $email?></td>
								</tr>
							<?php endif; ?>
						</table>
					</div>
					
					<hr>
					<div style="margin-bottom:20px;">
						<table>
							<tr>
								<td style="font-weight:bold;">Categoria:</td>
								<td><?php echo $assunto?></td>
							</tr>
							<tr>
								<td style="font-weight:bold;">Canal:</td>
								<td><?php echo $canal?></td>
							</tr>
							<tr>
								<td style="font-weight:bold;">Área da Prefeitura:</td>
								<td><?php echo $secretaria?></td>
							</tr>
							<tr>
								<td style="font-weight:bold;">Setor direcionado a manifestação:</td>
								<td><?php echo $setor?></td>
							</tr>
						</table>
					</div>
					<div style="margin-bottom:20px; border-radius: 10px; border: 1px solid #ddd; padding: 10px">
						<span>Relato do Manifestante:</span><p style="text-align: justify; text-indent:25px;"><?php echo $relato?></p>
					</div>
					<div style="margin-bottom:20px; border-radius: 10px; border: 1px solid #ddd; padding: 10px">
						<span>Descrição da Manifestação:</span><p style="text-align: justify; text-indent:25px;"><?php echo $observacao?></p>
					</div>
					
				</div>
			</main>
	</body>
</html>