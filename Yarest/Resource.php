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
    public $response;
    public $request;
    public $config;
    public $fields;
    public $comment;
    public $variables;
    public $prefix;

    final public function __construct (array $values = array())
    {
        $this->values = $values;
    }

    final public function halt ($status, $message = null)
    {
        $this->response->setStatus($status, $message);
        throw new Exception\Halt();
    }
}
