<?php
namespace App\Exceptions\Admin;

class ContratoNaoEncontradoException extends DomainException
{
    protected int $statusCode = 404;
}
