<?php

namespace Yarest\Exception;

class InvalidInput extends \Exception
{
    public $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }
}
