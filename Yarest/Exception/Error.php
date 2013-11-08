<?php

namespace Yarest\Exception;

class Error extends YarestException
{
    protected $status = "500";

    public function __construct($errstr, $errno, $errfile, $errline)
    {
        if (error_reporting()) {
            $errtype = self::errorType($errno);

            $error = array();

            $error['message'] = $errstr;
            $error['type']    = $errtype;
            $error['file']    = $errfile;
            $error['line']    = $errline;

            $this->errors = $error;
        }
    }

    private static function errorType($type)
    {
        switch($type)
        {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_CORE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_CORE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
            default:
                return "";
        }
    }
}
