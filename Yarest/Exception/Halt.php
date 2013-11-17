<?php

namespace Yarest\Exception;

class Halt extends YarestException
{
    public function __construct($status, $message)
    {
        $this->status = $status;

        if (!is_null($message)) {
            $this->message = $message;
        }
    }
}
