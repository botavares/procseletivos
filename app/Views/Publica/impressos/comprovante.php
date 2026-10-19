<?php
	ob_start();
?>
<!DOCTYPE html>
	<html lang="pt-BR">
		<head>
            <meta charset="UTF-8">
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
          <style type="text/css">

				
            	body{font-size:20px;padding:0;}
				
				#brasao img{margin-left:0%; margin-top:0%;}
			  	.red{color:#ff0000;}
			  	#ladoE,#ladoD{margin-top:0;}
				#ladoE{width:100%;position:relative; clear:left;margin-left:0px;z-index:99;}
				#ladoD{width:100%;position:relative; clear:right;z-index:99;}
				.cabecalho{width:100%; height:45px;}
				.rodape{height:45px;}
				
				
				#prefeitura,#brasao{float:left; position: relative;}
				#prefeitura{width:80%;float:left;}
				#prefeitura h1, #prefeitura h2, .condicionantes h1, .condicionantes h2{font-family: "Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Helvetica, "sans-serif"; text-align:center;margin:1px;}
				#prefeitura h1, .condicionantes h1{font-size: 14px;}
				#prefeitura h2, .condicionantes h2{font-size: 13px;}
				
				#tabelaEsquerda table{width:100%;border-collapse: collapse;}
				.nomeDocumento{
					font-size:14px;
					font-family:Helvetica;
				}
				.titTabela{
					font-family: Helvetica;
					font-size:12px;
					margin:5px;
					border-top:1px solid #000000;
					border-left:1px solid #000000;
					border-right:1px solid #000000;
					border-bottom:none;
				}
			  .destaque{
					font-family: Helvetica;
					font-size:18px;
					height: 22px;
					text-align: center;
					border-top:none;
					border-left:1px solid #000000;
					border-right:1px solid #000000;
					border-bottom:1px solid #000000;
					padding: 0 5px;
			  }
				.datTabela{
					font-family: Helvetica;
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
					font-family: Helvetica;
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
					font-family:Helvetica;
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
					font-family:Helvetica;
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
			  
			  	#qrCode{height:85px; float:left;}
				#qrCode img{}
				#qrCode{width:19%; margin-top:40px;}
			  
			  #aviso{border:thin solid;}
				#termoResponsabilidade{position:relative; width:100%;}
				#termoResponsabilidade #p1, #termoResponsabilidade #p2,#termoResponsabilidade ol li{margin:0;padding-left:35px; text-align: justify; font-family: "Helvetica Narrow"; font-size:13px; font-weight: 100;}
			  	#termoResponsabilidade #p2{width:100%;text-align:center;}
			  	ol li{text-align: justify; font-family: Helvetica; font-size:14px;}
			  .condicionantes ul li{
				  font-family: Helvetica;
				  font-size: 14px;
				  text-align: justify;
				  margin-bottom:5px;
			  }
            </style>
            
		</head>

	<?php $encoding = mb_internal_encoding(); ?>
	<div style="width:70%; position: absolute; left:15%; right: 0; top: 0; bottom: 0;z-index:-99;">
		<img src="<?php echo $fundo?>" style="width: 300mm; height: 225mm; margin: 260px auto; opacity:0.2;z-index:-99;"/>
	</div>
	<div id="ladoE" style="z-index:99;">
		<div class="cabecalho" style="margin-bottom:90px;">

			<div id="brasao">
			
				<img style ="width:80px;" src="<?php echo $brasao?>"/>
				
			</div>
			<div id="prefeitura">
			  <h1>PREFEITURA MUNICIPAL DE DIVINÓPOLIS</h1>
				<h2>SECRETARIA MUNICIPAL DE PLANEJAMENTO, GESTÃO, CIÊNCIA E TECNOLOGIA</h2>
				<h2>PROCESSO SELETIVO SIMPLIFICADO</h2>
				<h2><?php echo mb_strtoupper($nomeCargo, $encoding)?></h2>
				<h3 style="text-align: center; margin-left: 0;"><?php echo mb_strtoupper($edital, $encoding) ?> </h3>
			</div>

		</div>
				
		<div id="tabelaEsquerda">
			<table>
				<tr><td colspan="8" class="sepV"></td></tr>
				<tr >
					<th colspan="4" class="titTabela">PROTOCOLO</th>
					<th class="sepH"></th>
					<th colspan="4" class="titTabela">NOME</th>
				</tr>
				<tr >
					<td colspan="4" class="datTabela negrito red"><strong><?php echo $protocolo?></strong></td>
					<td class="sepH"></td>
					<td colspan="4" class="datTabela"><?php echo mb_strtoupper($dadosPessoais->ds_nome, $encoding)?></td>
				</tr>
			</table>
			
			
			<table>
			
			<tr><td colspan="8" class="sepV"></td> </tr>
			<tr>
				<th colspan="5" class="titTabela">NASCIMENTO</th>
				<th class="sepH"></th>
				<th colspan="3" class="titTabela">CARGO PLEITEADO</th>
			</tr>
			<tr >
				<td colspan="5"  class="datTabela"><?php echo $nascimento?></td>
				<td class="sepH"></td>
				<td colspan="3"  class="datTabela"><?php   echo mb_strtoupper($nomeCargo, $encoding)?></td>
			</tr>
			</table>
			
			
			
			
			
			
			<table>
				<tr><td colspan="8" class="sepV"></td> </tr>
				<tr >
					<th colspan="8"  class="titTabela"></th>
				</tr>
				<tr>
					<td colspan="8" class="datTabela">O declarante alega que <?php echo $deficiencia;?></td>
				</tr>
			</table>
			
			<table>
				<tr><td colspan="8" class="sepV"></td> </tr>
				<tr >
					<th colspan="8"  class="titTabela"></th>
				</tr>
				<tr>
					<td colspan="8" class="datTabela"><strong>Declaro, sob as penas da Lei, que são verdadeiras e completas as informações prestadas neste documento.</strong></td>
				</tr>
			</table>
			
			<div class="rodape">
				<div id="link">
					<p><?php echo "Cadastro efetuado no dia ".$dataCadastro." - ".date("H:i",strtotime($horaCadastro))?> </p>
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
					<h2 style="text-align:center; font-size:18px; margin:0;"></h2><BR>
						<h2 style="text-align:center;text-decoration: underline;font-size:27px; margin:0;"></h2><br>
					<h2 style="text-align:center; font-size:25px; margin:0;"></h2>
					
					<?php 
					$data = $dia." de ".$mes. " de ".date("Y");
					?>
					<h2>DIVINÓPOLIS, <?php echo mb_strtoupper($data,$encoding)?></h2>
				</div>
				
			</div>
		</div>
		<!--

				
					

		-->
	
	
	</div>
</body>
</html>