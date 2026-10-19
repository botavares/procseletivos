<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title><?=$titulo_documento?></title>
<style>
    @page {
        margin: 1.0cm 2cm 2.0cm 2cm;
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
        font-size: 10pt;
        line-height: 1.5;
        /*background-image: url('<?=$fundo?>');*/
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
        
    }
    .brasao {
        width: 70px;
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
    <div style="text-align: center;  margin-bottom: 40px;">
        <div style="display: inline-block; vertical-align: middle;horizontal-align: left; width: 90px;">
            <img src="<?=$brasao?>" alt="Brasão" style="height:80px;">
        </div>
        <div style="display: inline-block; vertical-align: middle; width: 75%; border-top: 15px solid #f90;">
            <h2 style="margin: 0; font-size: 10px;text-align: left; font-family: ">SECRETARIA MUNICIPAL DE PLANEJAMENTO, GESTÃO, CIÊNCIA E TECNOLOGIA - SEPLAG</h2>
            <h2 style="margin: 0; font-size: 10px;text-align: left;">DIRETORIA DE RECURSOS HUMANOS</h2>
            <p style="margin: 0; font-size: 9px; text-align: left;">Avenida Paraná, nº 2.601, sala 307 – Bairro São José – Divinópolis, Minas Gerais – CEP: 35.501-170</p>
            <p style="margin: 0; font-size: 9px; text-align: left;">(37) 3229-8154 – prefeituradivinopolis.estagio@gmail.com</p>
        </div>
    </div>
    
    <div class="titulo"><?=$titulo_documento?></div>
    <div class="subtitulo"><?=$subtitulo_documento?></div>
    <!--
	<div style="width:55%; position: absolute; left:11%; right: 0; top: 0; bottom: 0;z-index:-99;">
		<img src="<?php echo $fundo?>" style="width: 150mm; height: 127mm; margin: 250px auto; opacity:0.2;z-index:-99;"/>
	</div>-->
    <!-- Identificação das partes -->
    <div class="secao">
        Pelo presente instrumento particular, de um lado, a <b><?= $concedente_nome?></b>, inscrita no CNPJ sob o nº <b><?=$concedente_cnpj?></b>,
        com sede à <b><?=$concedente_endereco?></b>, neste ato representada por <b><?=$representante_nome?></b>,
        <b><?=$representante_cargo?></b>, doravante denominada <b>CONCEDENTE</b>;
        de outro lado, o(a) estudante <b><?=$estagiario?></b>, inscrito(a) no CPF sob o nº <b><?=$cpf?></b>,
        residente à <b><?=$rua?>, nº <?=$numero?>, <?=$bairro?>, <?=$cidade?>/<?=$uf?>, CEP <?=$cep?></b>,
        nascido(a) em <b><?=$nascimento?></b>, doravante denominado(a) <b>ESTAGIÁRIO(A)</b>;
        e, por fim, a <b><?=$faculdade?></b>, inscrita no CNPJ sob o nº <b><?=$instituicao_cnpj?></b>,
        com sede à <b><?=$instituicao_endereco?></b>, representada por <b><?=$coordenador?></b>,
        coordenador(a) do curso de <b><?= mb_strtoupper($curso, $encoding)?></b>, doravante denominada <b>INSTITUIÇÃO DE ENSINO</b>;
        têm, entre si, justo e acordado o presente <b>TERMO DE COMPROMISSO DE ESTÁGIO</b>, que se regerá pelas seguintes cláusulas:
    </div>

    <!-- Cláusulas -->
    <div class="clausula">
        <b>CLÁUSULA PRIMEIRA – DO OBJETO</b><br>
        O presente Termo tem por objeto a realização de estágio curricular <b>(obrigatório/não obrigatório)</b> pelo(a) ESTAGIÁRIO(A),
        no âmbito da <b><?=$setor_estagio?></b> da <b><?=$concedente_nome?></b>, sob a supervisão de <b><?=$supervisor_nome?></b>, 
        <b><?=$supervisor_cargo?>.</b>
        
    </div>

    <div class="clausula">
        <b>CLÁUSULA SEGUNDA – DA DURAÇÃO</b><br>
        O estágio terá início em <b><?=$dataInicio?></b> e término previsto para <b><?=$dataFim?></b>,
        podendo ser prorrogado mediante termo aditivo. A jornada será de <b><?=$carga_horaria_diaria?></b> horas diárias
        e <b><?=$carga_horaria_semanal?></b> semanais, de <b><?=$dias_semana?></b>, no turno da <b><?=$turno?></b>.
    </div>

    <div class="clausula">
        <b>CLÁUSULA TERCEIRA – DA BOLSA E AUXÍLIO-TRANSPORTE</b><br>
        Pela participação neste estágio, o(a) ESTAGIÁRIO(A) receberá uma bolsa-auxílio no valor de <b><?=$valor_bolsa?></b> 
        e auxílio-transporte em que o valor será calculado de acordo com a quantidade de conduções e valor do vale transporte em vigor,
        pagos pela CONCEDENTE até o 5º dia útil de cada mês.
    </div>

    <div class="clausula">
        <b>CLÁUSULA QUARTA – DO SEGURO</b><br>
        O(a) ESTAGIÁRIO(A) estará coberto(a) por apólice de seguro contra acidentes pessoais,
        contratada junto à <b><?=$seguro_empresa?></b>, sob o nº <b><?=$seguro_numero?></b>, com cobertura de <b><?=$seguro_valor?></b>.
    </div>

    <div class="clausula">
        <b>CLÁUSULA QUINTA – DO PLANO DE ATIVIDADES</b><br>
        As atividades a serem desenvolvidas pelo(a) ESTAGIÁRIO(A) estão descritas no <b>Plano de Atividades</b>, 
        anexo a este Termo, o qual passa a integrar o presente instrumento.
    </div>

    <div class="clausula">
        <b>CLÁUSULA SEXTA – DO RECESSO</b><br>
        Será concedido ao(à) ESTAGIÁRIO(A) um recesso de <b><?=$recesso?></b>, preferencialmente durante as férias escolares.
    </div>

    <div class="clausula">
        <b>CLÁUSULA SÉTIMA – DA RESCISÃO</b><br>
        O presente Termo poderá ser rescindido a qualquer tempo por qualquer das partes, mediante comunicação prévia mínima de 5 (cinco) dias úteis,
        ou automaticamente, no caso de descumprimento de cláusulas, encerramento do curso ou conclusão da carga horária prevista.
    </div>

    <div class="clausula">
        <b>CLÁUSULA OITAVA – DAS OBRIGAÇÕES DAS PARTES</b><br>
        A CONCEDENTE compromete-se a proporcionar ao(à) ESTAGIÁRIO(A) condições adequadas para o aprendizado prático e supervisão.
        O(A) ESTAGIÁRIO(A) deverá cumprir as normas internas e manter sigilo das informações da CONCEDENTE.
        A INSTITUIÇÃO DE ENSINO acompanhará e avaliará o desempenho do(a) ESTAGIÁRIO(A).
    </div>

    <div class="clausula">
        <b>CLÁUSULA NONA – DA VIGÊNCIA E DISPOSIÇÕES FINAIS</b><br>
        Este Termo entra em vigor na data de sua assinatura, ficando as partes cientes de que o estágio não cria vínculo empregatício de qualquer natureza.
        As partes elegem o foro da Comarca de Divinópolis/MG para dirimir eventuais controvérsias.
    </div>

    <!-- Assinaturas -->
    <div class="assinaturas">
        <p><?=$local_assinatura?>, <?=$data_assinatura?></p>

        <table>
            <tr>
                <td>
                    <div class="assinatura-linha"></div>
                    <b><?=$estagiario?></b><br>
                    Estagiário(a)
                </td>
                <td>
                    <div class="assinatura-linha"></div>
                    <b><?=$representante_nome?></b><br>
                    <?=$representante_cargo?><br>
                    <?=$concedente_nome?>
                </td>
                <td>
                    <div class="assinatura-linha"></div>
                    <b><?=$coordenador?></b><br>
                    Coordenador(a) do Curso<br>
                    <?=$faculdade?>
                </td>
            </tr>
        </table>

        <br><br>
        <table>
            <tr>
                <td>
                    <div class="assinatura-linha"></div>
                    <b><?=$testemunha_1?></b><br>
                    Testemunha 1
                </td>
                <td>
                    <div class="assinatura-linha"></div>
                    <b><?=$testemunha_2?></b><br>
                    Testemunha 2
                </td>
            </tr>
        </table>
    </div>

    <!-- QR Code e Autenticação -->
    <div class="qrcode">
        <?=$qrCode?><br>
        Código de autenticação: <b><?=$autenticador?></b><br>
        Verifique a autenticidade em:<br>
        <i>https://divinopolis.mg.gov.br/autentica/<?=$autenticador?></i>
    </div>

</body>
</html>
