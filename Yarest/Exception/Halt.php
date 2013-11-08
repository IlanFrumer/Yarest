<?php

namespace Yarest\Exception;

class Halt extends YarestException
{
    public function __construct($status, $message)
    {
        $this->$status = $status;
        $this->$message = $message;
    }
}
