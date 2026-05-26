<?php
namespace App\Exceptions;

class ValidationException extends DomainException
{
    protected int $statusCode = 422;
}
