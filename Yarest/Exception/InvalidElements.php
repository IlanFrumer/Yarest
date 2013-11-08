<?php

namespace Yarest\Exception;

class InvalidElements extends YarestException
{
    protected $status = "400";
    protected $message = "Invalid URI";
}
