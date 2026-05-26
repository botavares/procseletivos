<?php
namespace App\Exceptions;

class ContratoNaoEncontradoException extends DomainException
{
    protected int $statusCode = 404;
}
