<?php

namespace App\Exceptions\Admin;

class ConvocacaoInvalidaException extends DomainException
{
    protected int $statusCode = 422;

    public function __construct(string $message = 'Configuração de convocação inválida.')
    {
        parent::__construct($message);
    }
}
