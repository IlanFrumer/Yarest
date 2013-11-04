<?php

namespace Yarest\Exception;

class InvalidExpression extends \Exception
{
    public $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }
}
