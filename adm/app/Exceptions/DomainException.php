<?php
namespace App\Exceptions;

abstract class DomainException extends \RuntimeException
{
    protected int $statusCode = 400;

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
