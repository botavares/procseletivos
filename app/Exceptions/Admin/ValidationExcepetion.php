<?php
namespace App\Exceptions\Admin;

class ValidationException extends DomainException
{
    protected int $statusCode = 422;
}
