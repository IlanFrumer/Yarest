<?php

namespace Yarest\Exception;

class FileNotFound extends YarestException
{
    protected $status = "404";
    protected $message = "File Not Found";
}
