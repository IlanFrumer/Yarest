<?php

namespace Yarest;

/**
 * Yarest Resource class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

/**
 * Annotations:
 *
 *
 * Alias:
 *
 *
 * Arguments:
 */

abstract class Resource extends \Pimple
{
    public $config;
    public $request;
    public $response;

    public $comment;
    public $variables;
    
    public $fields;
    public $prefix;
    public $current;

    final public function __construct ()
    {
        
    }

    final public function halt ($status, $message = null)
    {
        throw new Exception\Halt($status, $message);
    }
}
