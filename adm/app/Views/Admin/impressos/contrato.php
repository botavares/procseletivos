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
    .lista-alfabetica {
        /* Usa letras minúsculas (a, b, c, d...) */
        list-style-type: lower-alpha; 
    }
    body {
        font-family: "Gill Sans", "Gill Sans MT", "Myriad Pro", "DejaVu Sans Condensed", Helvetica, Arial, "sans-serif";
        font-size: 9pt;
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
        font-size: 12pt;
        font-weight: bold;
        text-transform: uppercase;
    }
    .subtitulo {
        text-align: center;
        font-size: 11pt;
        margin-bottom: 25px;
    }
   
    .secao {
        margin-top: 5px;
        text-align: justify;
        padding: 0px;
    }
    .clausula {
        margin-top: 10px;
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
    
    <div class="titulo"><u><?=$titulo_documento?></u></div>
    <!--<div class="subtitulo"><?=$subtitulo_documento?></div>-->
    <!--
	<div style="width:55%; position: absolute; left:11%; right: 0; top: 0; bottom: 0;z-index:-99;">
		<img src="<?php echo $fundo?>" style="width: 150mm; height: 127mm; margin: 250px auto; opacity:0.2;z-index:-99;"/>
	</div>-->
    <div class="secao">
        <span style="text-indent: 50px;">
            <p>Termo de Compromisso de estágio sem vínculo empregatício, objetivando proporcionar formação e aperfeiçoamento técnico a estudantes, nos termos da Lei Federal n.º 11.788 de 25 de setembro de 2008, que dispõe sobre estágio de estudantes de ensino superior, de 2º grau e supletivo.</p>
        </span>
    </div>
    <!-- Identificação das partes -->
    <div class="secao">
        <h2 style="text-align: center; font-size: 16px;"><u>A - MUNICIPIO</u></h2>
        <span style="font-weight: bold; font-size: 14px;">Razão Social:</span> <?=mb_strtoupper($concedente_nome, $encoding)?><br>
        <span style="font-weight: bold; font-size: 14px;">Endereço:</span> <?=$concedente_endereco?><br>
        <span style="font-weight: bold; font-size: 14px;">Telefone:</span> <?=$concedente_telefone?>
        <span style="font-weight: bold; font-size: 14px;margin-left:40px;">CNPJ:</span> <?=$concedente_cnpj?>
        <span style="font-weight: bold; font-size: 14px;margin-left:40px;">INS:</span> <?="Isenta"?>
        <hr>
    </div>
    <div class="secao">
        <h2 style="text-align: center; font-size: 16px;"><u>B - ESTAGIÁRIO(A)</u></h2>
        <span style="font-weight: bold; font-size: 14px;">Nome:</span> <?=$estagiario?><br>
        <span style="font-weight: bold; font-size: 14px;">Endereço:</span> <?=$rua?>, nº <?=$numero?>, <?=$bairro?>, <?=$cidade?>/<?=$uf?>, CEP <?=$cep?><br>
        <span style="font-weight: bold; font-size: 14px;">Curso:</span> <?= mb_strtoupper($curso, $encoding)?>
        <span style="font-weight: bold; font-size: 14px;margin-left:40px;">Período/Modulo:</span> <?= mb_strtoupper($periodo, $encoding)?><br>
        <span style="font-weight: bold; font-size: 14px;">CPF:</span> <?=$cpf?>
        <span style="font-weight: bold; font-size: 14px;margin-left:40px;">Titulo de Eleitor:</span> <?=$titulo_eleitor?>
        <span style="font-weight: bold; font-size: 14px;margin-left:40px;">Telefone:</span> <?=$telefone?><br>
        <span style="font-weight: bold; font-size: 14px;">Email:</span> <?=$email?><br>
        <hr>
    </div>
    <div class="secao">
        <h2 style="text-align: center; font-size: 16px;"><u>C - INSTITUIÇÃO DE ENSINO</u></h2>
        <span style="font-weight: bold; font-size: 14px;">Nome:</span> <?=$faculdade?>
        <span style="font-weight: bold; font-size: 14px;margin-left:40px;">Convênio:</span><?=$convenio?><br>
        <span style="font-weight: bold; font-size: 14px;">Endereço:</span> <?=$endereco_instituicao?><br>
        <span style="font-weight: bold; font-size: 14px;">Telefone:</span> <?=$instituicao_telefone?>
        <span style="font-weight: bold; font-size: 14px;margin-left:40px;">CNPJ:</span> <?=$instituicao_cnpj?><br>
        <hr>
    </div>
    <div class="secao">
        <span style="text-indent: 50px;">
            <p>O <b>MUNICÍPIO</b>, o <b>ESTAGIÁRIO</b> e a <b>INSTITUIÇÃO DE ENSINO</b>, identificados respectivamente nos itens A, B e C deste Termo de Compromisso, têm entre si contratados o seguinte:</p>
        </span>
    </div>

    <!-- Cláusulas -->
    <div class="clausula">
        <b><u>CLÁUSULA PRIMEIRA</b><br>
        <span style="text-indent: 50px;">
            <p>Constitui objeto deste instrumento o estabelecimento de condições para a realização de estágio, <u>aos estudantes previamente selecionados</u>, nos termos da Lei Federal n° 11.788/08, como estratégia e complementação do processo de ensino e aprendizagem.</p>
        </span>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA SEGUNDA</u></b><br>
        <span style="text-indent: 50px;">
            <p>Ficam compromissadas as partes, as seguintes condições básicas de realização do estágio:<br></p>
        </span>
        <ol class="lista-alfabetica">
            <li>O ESTAGIÁRIO deverá cumprir uma jornada <?=$carga_horaria_diaria?> (<?=$carga_extenso?>) horas diárias, de 2ª a 6ª feira, e exercerá suas atividades na 
            <?=$nome_secretaria?>, no setor <?=$setor_estagio?>.</li>
            <li>O MUNICÍPIO concederá ao ESTAGIÁRIO, mensalmente, uma bolsa de complementação educacional no valor de <?= $valor_bolsa?> (<?=$bolsa_extenso?>), a ser entregue até o 5º dia útil do mês subsequente ao do estágio, bem como o vale transporte.</li>
            <li>As despesas orçamentárias decorrentes do estágio correrão por conta da dotação Manutenção das Despesas com Estagiários – 02.002.03.04.122.0002.2163 ficha: 215</li>
            <li>O MUNICÍPIO arcará com as despesas referentes ao Seguro contra Acidentes Pessoais, conforme Apólice de Seguros – <?=$seguro_numero?> da Seguradora <?=$seguro_empresa?>.</li>
            <li>O estágio oferecido <u>NÃO CONFIGURA VÍNCULO EMPREGATÍCIO</u>.</li>
            <li>O ESTAGIÁRIO deverá apresentar à instituição de ensino, relatório sobre o trabalho por ele desenvolvido durante o estágio, juntamente com a avaliação realizada pelo Município referente ao desempenho do estagiário.</li>
        </ol>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA TERCEIRA</u></b><br>
        <span style="text-indent: 50px;">
            <p>O Estágio Extra - Curricular Não Obrigatório terá a duração contada a partir do dia <?=$dataInicio?> até o dia <?=$dataFim?>, admitindo-se prorrogação mediante aditivo, com respeito ao tempo máximo previsto em lei.</p>
        </span>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA QUARTA</u></b><br>
        <span style="text-indent: 50px;">
            <p>A ADMINISTRAÇÃO designará o(a) Sr(ª). <?=$supervisor?>, como Supervisor (a), enquanto vigorar o presente TERMO DE COMPROMISSO.</p>
        </span>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA QUINTA</u></b><br>
        <span style="text-indent: 50px;">
            <p>O ESTAGIÁRIO se obriga a conhecer e cumprir normas internas do MUNICÍPIO, especialmente aquelas relativas à orientação geral do estágio, bem como observar a programação do estágio, elaborada de acordo com currículos e calendários escolares, assim como do órgão onde efetiva o estágio, bem como outras eventuais recomendações ou requisitos ajustados entre as partes, sob pena de rescisão e demais cominações legais.</p>
        </span>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA SEXTA</u></b><br>
        <span style="text-indent: 50px;">
            <p>O ESTAGIÁRIO compromete-se formalmente a manter sigilo sobre informações, dados ou trabalhos reservados do MUNICÍPIO aos quais tenha acesso.</p>
        </span>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA SÉTIMA</u></b><br>
        O presente TERMO DE COMPROMISSO ficará automaticamente rescindido nas seguintes hipóteses:<br>
        <ol class="lista-alfabetica">
            <li>Forem atribuídas ao estagiário, atividades incompatíveis com sua habilitação ou formação.</li>
            <li>Término do Estágio.</li>
            <li>Não comparecimento do aluno ao estágio por período superior a 5 dias sem justificativa.</li>
            <li>Se entre as partes não mais existir o interesse pelo estágio.</li>
            <li>A conclusão, abandono do curso, ou trancamento da matrícula. </li>
            <li>Transgressão de qualquer das normas que regem a disciplina administrativa do Município.</li>
        </ol><br>

    <p style="text-indent: 50px;">Parágrafo único: 
    A realização de exames escolares no horário do estágio devidamente comprovada pela instituição de ensino será considerada motivo justo para a falta do ESTAGIÁRIO.</p>

    </div>

    <div class="clausula">
        <b><u>CLÁUSULA OITAVA</b><br>
        <span style="text-indent: 50px;">
            <p>O MUNICÍPIO se compromete a avaliar através do Chefe Imediato, o desempenho do ESTAGIÁRIO.</p>
        </span>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA NONA</b><br>
        <span style="text-indent: 50px;">
            <p>As partes declaram e concordam que toda e qualquer atividade de tratamento de dados deve atender às finalidades e limites previstos neste TERMO DE COMPROMISSO e estar em conformidade com a legislação aplicável, principalmente a Lei 13.709/18 (“Lei Geral de Proteção de Dados” ou “LGPD”), bem como obrigam-se, desde já, a somente envolver pessoas efetivamente designadas para a prestação de serviços objeto do presente TERMO DE COMPROMISSO e a promover Tratamento de Dados Pessoais no limite indispensável à sua execução, sempre utilizando-se de ambiente seguro, observadas as melhores tecnologias disponíveis no mercado.</p>
        </span>
    </div>

    <div class="clausula">
        <b><u>CLÁUSULA DÉCIMA</b><br>
        <span style="text-indent: 50px;">
            <p>Fica convencionado e eleito o foro da Comarca de Divinópolis para dirimir eventuais conflitos oriundos deste termo de compromisso.</p>
        </span>
    </div>
    <div class="clausula">
        <span style="text-indent: 50px;">
            <p>Por estarem em comum acordo, os partícipes assinam digitalmente o presente instrumento.</p>
        </span>
    </div>
    <!-- Assinaturas -->
    <div class="assinaturas">
        <p><?=$local_assinatura?>, <?=$data_assinatura?></p>

        <table style="width: 60%; margin: 50px auto;">
            <tr>
                <td style="height: 50px;">
                    <div class="assinatura-linha"></div>
                    <span style="font-size: 9px;">(assinado digitalmente)</span><br>
                    <b><?=$diretor_recursos_humanos?></b><br>
                    Diretor(a) de Recursos Humanos<br>
                </td>
            </tr>
            <tr>
                <td style="height: 30px;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td>
                    <div class="assinatura-linha"></div>
                    <span style="font-size: 9px;">(assinado digitalmente)</span><br>
                    <b><?=$faculdade?></b><br>
                    <?=$representante_faculdade?><br>
                </td>
            </tr>
            <tr>
                <td style="height: 30px;">
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td>
                    <div class="assinatura-linha"></div>
                    <span style="font-size: 9px;">(assinado digitalmente)</span><br>
                    <b><?=$estagiario?></b><br>
                    Estagiário(a)<br>
                    
                </td>
            </tr>
        </table>
            
        
        
    </div>

   
</body>
</html>
