<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title><?=$titulo_documento?></title>
<style>
    @page {
        margin: 2.0cm 2cm 2.0cm 2cm;
    }
    body::before {
        content: "";
        position: fixed;
        top: 20%;
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
        font-size: 7pt;
        line-height: 1.5;
        
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        
    }
    .brasao {
        width: 90px;
        display: block;
        margin: 0 auto 5px auto;
    }
    .titulo {
        text-align: center;
        font-size: 10pt;
        font-weight: bold;
        text-transform: uppercase;
    }
    .subtitulo {
        text-align: center;
        font-size: 9pt;
        margin-bottom: 10px;
    }
    .titulo-clausula{
        text-align: center;
        font-size: 9pt;
        font-weight: bold;
        text-transform: uppercase;}
    .texto{
        text-indent: 30px;
    }
    .secao {
        margin-top: 15px;
        text-align: justify;
    }
    .clausula {
        text-align: justify;
        
    }
    .assinaturas {
        margin-top: 15px;
        text-align: center;
    }
    .assinaturas table {
        width: 100%;
        border: none;
       
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
        font-size: 10pt;
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
    <div class="subtitulo">Termo de Compromisso de Estagio nº.:<?=$numero_termo?></div>
    
 
    
    <div class="clausula">
        <p class="texto">Termo Aditivo ao Termo de Compromisso de Estágio Extracurricular <b><?=$numero_aditivo?></b> que entre si celebram o Município de Divinópolis e a(o) estagiário(a) <b><?=$nome_estagiario?></b> especifica o MUNICÍPIO DE DIVINÓPOLIS, com sede de seu governo, na Prefeitura situada à Av. Paraná, 2601, São José e o (a) estagiária (a) <b><?=$nome_estagiario?></b> aditivo: </p>
    </div>
    <!-- Cláusulas -->
    <div class="clausula">
        <?php $clausula = 1; ?>

        <h5 class="titulo-clausula"><b><u>CLÁUSULA <?= mb_strtoupper(ordinal_por_extenso($clausula,'f'), $encoding) ?> – ALTERAÇÃO DA VIGÊNCIA DO TERMO DE COMPROMISSO DE ESTÁGIO</b></u></h5>
        <p class="texto">Conforme acordado entre o estagiário e a Gerência de Recursos Humanos fica prorrogado o Termo de Compromisso de Estágio – TCE, alterando a data de término para o dia <b><?=$data_prorrogacao?></b>.</p>
        <?php $clausula++; ?>
    </div>
    
    
    <?php
    if($alterar_carga_horaria == true ){ ?>
        <div class="clausula">
            <h5 class="titulo-clausula"><b><u>CLÁUSULA <?= mb_strtoupper(ordinal_por_extenso($clausula,'f'), $encoding) ?> – MUDANÇA DE CARGA HORÁRIA</b></u></h5>
            <p class="texto">O Estágio Extracurricular a que se refere o presente Termo de Compromisso mudará de <?=$carga_horaria_antiga?> (<?=$extenso_carga_horaria_antiga?>) horas diárias para <?=$carga_horaria_nova?> (<?=$extenso_carga_horaria_nova?>) horas diárias, a partir de <?=$data_aditivo?>, alterando também a remuneração de R$ <?=$remuneracao_antiga?> (<?=$extenso_remuneracao_antiga?>) para R$ <?=$remuneracao_nova?> (<?=$extenso_remuneracao_nova?>).</p>
            <?php $clausula++; ?>
        </div>
    <?php }?>


    <?php
    if($alterar_lotacao == true ){ ?>
        <div class="clausula">
            <h5 class="titulo-clausula"><b><u>CLÁUSULA <?= mb_strtoupper(ordinal_por_extenso($clausula,'f'), $encoding) ?> – MUDANÇA DE LOTAÇÃO</b></u></h5>
            <p class="texto">A partir de <?=$data_aditivo?>, o estagiário passa a exercer suas atividades no (a) <?=$lotacao_nova?>.</p>
            <?php $clausula++; ?>
        </div>
    <?php }?>

    <?php if($alterar_supervisor == true ){ ?>
        <div class="clausula">
            <h5 class="titulo-clausula"><b><u>CLÁUSULA <?= mb_strtoupper(ordinal_por_extenso($clausula,'f'), $encoding) ?> – MUDANÇA DE SUPERVISOR</b></u></h5>
            <p class="texto">A partir de <?=$data_aditivo?>, o supervisor do estágio extracurricular a que se refere o presente termo aditivo passará a ser o (a) senhor (a) <?=$supervisor_novo?>.</p>
            <?php $clausula++; ?>
        </div>
    <?php }?>


    <div class="clausula">
        <h5 class="titulo-clausula"><b><u>CLÁUSULA <?= mb_strtoupper(ordinal_por_extenso($clausula,'f'), $encoding) ?> </b></h5>
        <p class="texto">O estagiário terá direito ao abono administrativo de até <b>3 (três) dias</b> de atestado, durante a vigência de seu termo de estágio, que deve ser apresentado por meio de sistema administrativo disponibilizado pela Prefeitura Municipal de Divinópolis.</p>
        <?php $clausula++; ?>
    </div>

    <div class="clausula">
        <h5 class="titulo-clausula"><b><u>CLÁUSULA <?= mb_strtoupper(ordinal_por_extenso($clausula,'f'), $encoding) ?></u> </b></h5>
        <p class="texto">Ficam ratificadas as demais cláusulas e condições deste termo de compromisso ora aditado, não especificamente alteradas neste instrumento. E, por estarem justas e de acordo, assinam digitalmente as partes o presente instrumento.</p>

        <?php $clausula++; ?>
    </div>

    <!-- Assinaturas -->

<div class="assinaturas">
    <p><?=$local_assinatura?>, <?=$data_assinatura?></p>

    <table style="margin-top: 0; width: 100%; border: none;">
        <tr>
            <!-- Assinatura do estagiário -->
            <td style="text-align: center;">
                <div class="assinatura-linha"></div>
                <p style="font-size: 8px;">Assinado digitalmente</p>                       
                <b><?=$nome_estagiario?></b><br>
                Estagiário(a)
                
            </td>

            <!-- Assinatura do diretor -->
            <td style="text-align: center;">
                <div class="assinatura-linha"></div>
                <p style="font-size: 8px;">Assinado digitalmente</p>
                <b><?=$nome_secretario?></b><br>
                Diretor(a) de Recursos Humanos
                
            </td>
        </tr>
    </table>
</div>



</body>
</html>
