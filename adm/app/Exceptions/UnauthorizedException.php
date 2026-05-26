<?php
namespace App\Exceptions;

class UnauthorizedException extends DomainException
{
    protected int $statusCode = 403;
}
