<?php
	ob_start();
?>
<!DOCTYPE html>
	<html>
		<head>
            <!--<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />-->
          <style type="text/css">

				
            	body{font-size:20px;padding:0;}
				
				#brasao img{margin-left:20%; width:80%;}
			  .red{color:#ff0000;}
			  	#ladoE,#ladoD{margin-top:0;}
				#ladoE{width:100%;position:relative; clear:left;margin-left:0px;z-index:99;}
				#ladoD{width:100%;position:relative; clear:right;z-index:99;}
				.cabecalho{width:100%; height:50px; margin-bottom:5%;}
				.rodape{height:45px;}
				h3{text-align:center; font-size:20px; font-family:Arial;margin:20px 0 20px 0;}
				
				#prefeitura,#brasao{height:35px; float:left; position: relative;}
				#brasao{width: 20%;}
				#prefeitura{width:80%;float:left;}
				#prefeitura h1, #prefeitura h2, .condicionantes h1, .condicionantes h2,h3{font-family: "Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Arial, "sans-serif"; text-align:center;margin:1px;}
				#prefeitura h1, .condicionantes h1{font-size: 18px;}
				#prefeitura h2, .condicionantes h2{font-size: 16px;}
				
				#tabelaEsquerda table{width:100%;border-collapse: collapse;}
				.nomeDocumento{
					font-size:14px;
					font-family:Arial;
				}
				.titTabela{
					font-family: Arial;
					font-size:12px;
					margin:5px;
					border-top:1px solid #000000;
					border-left:1px solid #000000;
					border-right:1px solid #000000;
					border-bottom:none;
				}
				.datTabela{
					font-family: Arial;
					font-size:12px;
					height: 22px;
					text-align: center;
					border-top:none;
					border-left:1px solid #000000;
					border-right:1px solid #000000;
					border-bottom:1px solid #000000;
					padding: 0 5px;
				}
			  .atividade{height:180px;}
			  .informacao{height:65px;}
				.textTabela{
					font-family: Arial;
					font-size:10px;
					text-indent: 2em;
					text-align: justify;
					border-top:none;
					border-left:1px solid #000000;
					border-right:1px solid #000000;
					border-bottom:1px solid #000000;
				}
			  	
				.sepH{width:5px;}
				.sepV{height:10px;}
				.assinaturas{
					margin-top:160px;
					/*border:1px solid #000000;*/
					font-size:10px;
					font-family:Arial;
					text-align:left;
					bottom:0;
					width:50%;
				}
			  .autenticador{font-size:16px; font-weight: bold; text-align:center;}
			  	.justificado{text-align:justify;}
				.negrito{font-weight: bold; font-size: 14px;}				  
				.rodape{
					margin-top:0px;
					font-size:10px;
					font-family:Arial;
					text-align:center;
					height:110px;
					
				}
				#qrCode{height:85px; float:left;}
				#qrCode img{}
				#qrCode{width:19%; margin-top:40px;}
				#link{width:100%;}
				#link p{
					width:100%;
					padding-top:05px;
					font-size: 10px;
					text-align:justify;
				}
			  #aviso{border:thin solid;}
				#termoResponsabilidade{position:relative; width:100%;}
				#termoResponsabilidade #p1, #termoResponsabilidade #p2,#termoResponsabilidade ol li{margin:0;padding-left:35px; text-align: justify; font-family: "Arial Narrow"; font-size:13px; font-weight: 100;}
			  	#termoResponsabilidade #p2{width:100%;text-align:center;}
			  	ol li{text-align: justify; font-family: Arial; font-size:14px;}
			  .condicionantes ul li{
				  font-family: Arial;
				  font-size: 14px;
				  text-align: justify;
				  margin-bottom:5px;
			  }
            </style>
            
		</head>

	<?php $encoding = mb_internal_encoding(); ?>
	<div style="width:55%; position: absolute; left:11%; right: 0; top: 0; bottom: 0;z-index:-99;">
		<img src="<?php echo $fundo?>" style="width: 150mm; height: 127mm; margin: 250px auto; opacity:0.2;z-index:-99;"/>
	</div>
	<div id="ladoE" style="z-index:99;">
		<div class="cabecalho">

			<div id="brasao">
			
				<img src="<?php echo $brasao?>"/>
				
			</div>
			<div id="prefeitura">
			  <h1>PREFEITURA MUNICIPAL DE DIVINÓPOLIS</h1>
				<h2>CONTROLADORIA GERAL DE DIVINÓPOLIS</h2>
			</div>

		</div>
		<?php $ano1 = date('Y')+1?>
		<h3>PROTOCOLO DE MANIFESTAÇÃO</h3>		
		<div id="tabelaEsquerda">
			<table>
				<tr><td colspan="8" class="sepV"></td></tr>
				<tr >
					
					<th colspan="8" class="titTabela">PROTOCOLO</th>
				
				</tr>
				<tr >
					<td colspan="8" class="datTabela negrito red"><strong><?php echo $protocolo?></strong></td>
				</tr>
			</table>
			
			
			<table>
			
			<tr><td colspan="8" class="sepV"></td> </tr>
			<tr>
				<th colspan="4" class="titTabela">SETOR</th>
				<th class="sepH"></th>
				<th colspan="4" class="titTabela">ASSUNTO</th>
			</tr>
			<tr >
				<td colspan="4"  class="datTabela"><strong><?php echo mb_strtoupper($setor, $encoding)?></strong></td>
				<td class="sepH"></td>
				<td colspan="4"  class="datTabela"><?php echo mb_strtoupper($assunto, $encoding)?></td>
			</tr>
			</table>
			
			
			
			
			<table>
				<tr><td colspan="8" class="sepV"></td> </tr>
				<tr >
					<th colspan="8"  class="titTabela">TEXTO MANIFESTAÇÃO</th>
				</tr>
				<tr>
					<td colspan="8" class="datTabela"><?php echo $textoManifestacao?></td>
				</tr>
			</table>
			
			<table>
			
			<tr><td colspan="8" class="sepV"></td> </tr>
			<tr>
				
				<th colspan="8" class="titTabela">DATA DO CADASTRO</th>
			</tr>
			<tr >

				<td colspan="8"  class="datTabela"><?php echo date("d/m/Y",strtotime($dataCadastro))?></td>
			</tr>
			</table>
			
			<div class="rodape">
				<div id="link">
					<p>ATENÇÃO. Por ser uma manifestação anônima, a única forma de ter acesso ao andamento da manifestação é utilizando o número do protocolo e a senha cadastrada. </p>
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
				
			</div>
		</div>
		<!--

				
					

		-->
	
	
	</div>
</body>
</html>