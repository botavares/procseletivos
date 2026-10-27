<?php
namespace App\Exceptions\Admin;

class UnauthorizedException extends DomainException
{
    protected int $statusCode = 403;
}
