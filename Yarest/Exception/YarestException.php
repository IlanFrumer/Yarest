<?php

namespace Yarest\Exception;

abstract class YarestException extends \Exception
{
    protected $status  = "500";
    protected $message = null;
    protected $errors  = null;
    protected $allowed = null;

    public function __construct(array $errors = array())
    {
        $this->errors = $errors;
    }

    public function setResponse(\Yarest\Response $response)
    {
        $response->setStatus($this->status, $this->message);

        if ($this->errors) {
            $response->setBody($this->errors);
        }

        if ($this->allowed) {
            $response->setAllowed($this->allowed);
        }
    }
}
