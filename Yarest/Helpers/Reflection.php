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
     * @param  string $class
     * @return array of \ReflectionMethod
     */
    public static function getOwnPublicMethods($class)
    {

        $class_ref = new \ReflectionClass($class);

        $methods = $class_ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = array_filter($methods, function ($method) use ($class_ref) {
            return $method->class == $class_ref->name;
        });

        return $methods;
    }
}
