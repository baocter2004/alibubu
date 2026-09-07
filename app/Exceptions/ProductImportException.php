<?php

namespace App\Exceptions;

use RuntimeException;

class ProductImportException extends RuntimeException
{
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Product import validation failed.');
    }
}
