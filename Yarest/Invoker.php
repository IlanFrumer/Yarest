<?php

namespace Yarest;

/**
 * Yarest Invoke class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class Invoker
{

    private $callbacks = array();

    public function __construct()
    {
        
    }

    /**
     * [before description]
     * @param  object $callable
     * @return \Yarest\Invoker the same instance for method chaining
     */
    public function before($callable)
    {
        if (!is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }
        $this->callbacks['before'] = $callable;

        return $this;
    }

    /**
     * [after description]
     * @param  object $callable
     * @return \Yarest\Invoker the same instance for method chaining
     */
    public function after($callable)
    {
        if (!is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }

        $this->callbacks['after'] = $callable;
        return $this;
    }

    /**
     * [error description].
     * 
     * Allow Multiple errors with differnt Exception class
     * @param  object $callable
     * @return \Yarest\Invoker the same instance for method chaining
     */
    public function error($callable)
    {
        if (!is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }
        $this->callbacks['error'] = $callable;
        return $this;
    }

    public function invoke(Resource $resource)
    {

    }

}


