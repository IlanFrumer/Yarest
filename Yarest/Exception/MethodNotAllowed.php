<?php

namespace Yarest\Exception;

class MethodNotAllowed extends YarestException
{
    protected $status = "405";

    public function __construct(array $allowed)
    {
        $this->allowed = $allowed;
    }
}
