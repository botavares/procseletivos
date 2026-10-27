<?php

if (! function_exists('ordinal_por_extenso')) {

    /**
     * Converte um número ordinal para o formato por extenso em português.
     *
     * @param int    $numero  Número ordinal (1, 2, 3, ...)
     * @param string $genero  'm' para masculino, 'f' para feminino
     * @return string
     */
    function ordinal_por_extenso(int $numero, string $genero = 'm'): string
    {
        if ($numero <= 0) {
            return '';
        }

        $ordinais = [
            1  => ['primeiro', 'primeira'],
            2  => ['segundo', 'segunda'],
            3  => ['terceiro', 'terceira'],
            4  => ['quarto', 'quarta'],
            5  => ['quinto', 'quinta'],
            6  => ['sexto', 'sexta'],
            7  => ['sétimo', 'sétima'],
            8  => ['oitavo', 'oitava'],
            9  => ['nono', 'nona'],
            10 => ['décimo', 'décima'],
            11 => ['décimo primeiro', 'décima primeira'],
            12 => ['décimo segundo', 'décima segunda'],
            13 => ['décimo terceiro', 'décima terceira'],
            14 => ['décimo quarto', 'décima quarta'],
            15 => ['décimo quinto', 'décima quinta'],
            16 => ['décimo sexto', 'décima sexta'],
            17 => ['décimo sétimo', 'décima sétima'],
            18 => ['décimo oitavo', 'décima oitava'],
            19 => ['décimo nono', 'décima nona'],
            20 => ['vigésimo', 'vigésima'],
            30 => ['trigésimo', 'trigésima'],
            40 => ['quadragésimo', 'quadragésima'],
            50 => ['quinquagésimo', 'quinquagésima'],
            60 => ['sexagésimo', 'sexagésima'],
            70 => ['septuagésimo', 'septuagésima'],
            80 => ['octogésimo', 'octogésima'],
            90 => ['nonagésimo', 'nonagésima'],
            100 => ['centésimo', 'centésima'],
        ];

        $indiceGenero = ($genero === 'f') ? 1 : 0;

        // Caso direto (até 100 exato)
        if (isset($ordinais[$numero])) {
            return $ordinais[$numero][$indiceGenero];
        }

        // Decomposição (ex: 23 => 20 + 3)
        if ($numero < 100) {
            $dezena = intdiv($numero, 10) * 10;
            $unidade = $numero % 10;

            if (isset($ordinais[$dezena], $ordinais[$unidade])) {
                return $ordinais[$dezena][$indiceGenero] . ' ' .
                       $ordinais[$unidade][$indiceGenero];
            }
        }

        // Para valores acima de 100 não tratados
        return (string) $numero;
    }
}
