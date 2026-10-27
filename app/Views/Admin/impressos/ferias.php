<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title><?=$titulo_documento?></title>
<style>
    @page {
        margin: 2.5cm 2cm 2.5cm 2cm;
    }
    body::before {
        content: "";
        position: fixed;
        top: 30%;
        left: 10%;
        width: 150mm;
        height: 127mm;
        background: url('<?=$fundo?>') no-repeat center center;
        background-size: contain;
        opacity: 0.15;
        z-index: -1;
    }

    body {
        font-family: "Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Arial, "sans-serif";
        font-size: 12pt;
        line-height: 1.5;
        /*background-image: url('<?=$fundo?>');*/
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        
    }
    .brasao {
        width: 90px;
        display: block;
        margin: 0 auto 10px auto;
    }
    .titulo {
        text-align: center;
        font-size: 14pt;
        font-weight: bold;
        text-transform: uppercase;
    }
    .subtitulo {
        text-align: center;
        font-size: 11pt;
        margin-bottom: 25px;
    }
    .secao {
        margin-top: 20px;
        text-align: justify;
    }
    .clausula {
        margin-top: 12px;
        text-align: justify;
    }
    .assinaturas {
        margin-top: 50px;
        text-align: center;
    }
    .assinaturas table {
        width: 100%;
        border: none;
        margin-top: 20px;
    }
    .assinaturas td {
        width: 33%;
        vertical-align: top;
        text-align: center;
        padding: 10px;
    }
    .assinatura-linha {
        border-top: 1px solid #000;
        width: 90%;
        margin: 0 auto;
        padding-top: 5px;
    }
    .qrcode {
        text-align: center;
        font-size: 9pt;
        margin-top: 40px;
    }
</style>
</head>
<body>
<?php $encoding = mb_internal_encoding(); ?>
    <div style="text-align: center;  margin-bottom: 20px; border-bottom: 1px solid #000">
        <div style="display: inline-block; vertical-align: middle;horizontal-align: left; width: 90px;">
            <img src="<?=$brasao?>" alt="Brasão" style="height:85px">
        </div>
        <div style="display: inline-block; vertical-align: middle; width: 75%; border-top: 12px solid #f90;">
            <h2 style="margin: 0; font-size: 11px;text-align: left; font-family: ">SECRETARIA MUNICIPAL DE PLANEJAMENTO, GESTÃO, CIÊNCIA E TECNOLOGIA - SEPLAG</h2>
            <h2 style="margin: 0; font-size: 11px;text-align: left;">DIRETORIA DE RECURSOS HUMANOS</h2>
            <h2 style="margin: 0; font-size: 9px;text-align: left;">GERÊNCIA DE RECURSOS HUMANOS</h2>
            <p style="margin: 0; font-size: 9px; text-align: left;">Avenida Paraná, nº 2.601, sala 307 – Bairro São José – Divinópolis, Minas Gerais – CEP: 35.501-170</p>
            <p style="margin: 0; font-size: 9px; text-align: left;">(37) 3229-8154 – prefeituradivinopolis.estagio@gmail.com</p>
        </div>
    </div>
    <div class="titulo"><?=$titulo_documento?></div>
    
    <!--
	<div style="width:55%; position: absolute; left:11%; right: 0; top: 0; bottom: 0;z-index:-99;">
		<img src="<?php echo $fundo?>" style="width: 150mm; height: 127mm; margin: 250px auto; opacity:0.2;z-index:-99;"/>
	</div>-->
    <!-- Identificação das partes -->
    

    <!-- Cláusulas -->
    <div class="clausula">
        Declaro para os devidos fins que eu, <?=$nome_estagiario?>, estagiário (a) desta Prefeitura de Divinópolis, gozei <?=$dias_gozados?> dias de recessos.
    </div>

    <!-- Assinaturas -->
    <div class="assinaturas">
        <p><?=$local_assinatura?>, <?=$data_assinatura?></p>

        <table style="margin-top: 40px;">
            <tr>
                <td>
                    <p style="text-align: center; font-size: 8px;">Assinado digitalmente</p>                       
                    <b><?=$nome_estagiario?></b><br>
                    Estagiário(a)
                </td>
            </tr>
        </table>

        <table style="margin-top: 40px;">
            <tr>
                <td>
                    <p style="text-align: center; font-size: 8px;">Assinado digitalmente</p>
                    <b><?=$nome_secretario?></b><br>
                </td>
            </tr>
        </table>

    </div>


</body>
</html>
