<?php
/**
 * Variáveis esperadas:
 * @var string $nome
 * @var string $setor
 * @var string $dataFim (YYYY-MM-DD ou já formatada)
 * @var int    $diasTrabalhados
 * @var int    $diasFerias
 * @var int    $feriasTiradas
 */

$dataFormatada = $dataFim;
if (strpos($dataFim, '-') !== false) {
    [$ano, $mes, $dia] = explode('-', $dataFim);
    $dataFormatada = "{$dia}/{$mes}/{$ano}";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Comunicação de Encerramento de Contrato</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; color:#333; line-height:1.6">

    <p>Prezados,</p>

    <p>
        Comunicamos que o(a) estagiário(a)
        <strong><?= esc($nome) ?></strong>,
        vinculado(a) ao setor
        <strong><?= esc($setor) ?></strong>,
        encontra-se com o contrato de estágio previsto para encerramento em
        <strong><?= esc($dataFormatada) ?></strong>.
    </p>

    <p>
        Até o momento, o(a) estagiário(a) contabiliza:
    </p>

    <ul>
        <li><strong><?= (int) $diasTrabalhados ?></strong> dias trabalhados;</li>
    </ul>

    <p>
        Caso haja interesse na renovação do contrato ou necessidade de esclarecimentos
        adicionais, solicitamos que o setor entre em contato com a equipe responsável
        dentro do prazo adequado.
    </p>

    <p>
        Permanecemos à disposição.
    </p>

    <p style="margin-top:30px">
        Atenciosamente,<br>
        <strong>Setor de Estágios</strong>
    </p>

</body>
</html>
