<?php

namespace Yarest\Helpers;

/**
 * Yarest Arguments helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Arguments
{
    /**
     * Check if an argument is callable and if not than it throws \InvalidArgumentException
     * @param  object $callable
     */
    public static function checkCallable($callable)
    {
        if (!is_object($callable) || !method_exists($callable, '__invoke')) {
            throw new \InvalidArgumentException('Service definition is not a Closure or invokable object.');
        }
        return true;
    }
}
