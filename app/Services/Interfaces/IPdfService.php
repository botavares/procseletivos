<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface IPdfService
{
    public function gerarPdf(array $dados, string $documento): string;
}
