<?php

namespace Yarest\Helpers;

/**
 * Yarest Reflection helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Reflection
{
    /**
     * [getOwnPublicMethods description]
     * @param  \ReflectionClass $class
     * @return array of \ReflectionMethod
     */
    public static function getOwnPublicMethods(\ReflectionClass $class)
    {
        
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = array_filter($methods, function ($method) use ($class) {
            return $method->class == $class->name;
        });

        return $methods;
    }
}
