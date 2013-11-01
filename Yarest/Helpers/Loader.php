<?php

namespace Yarest\Helpers;

/**
 * Yarest Loader helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Loader
{
    /**
     * [loadNamespace description]
     *
     * @param  string $path
     * @param  string $namespace
     * @param  string $folder
     * @return ClassLoader
     */
    public static function loadNamespace($path, $namespace, $folder)
    {
        $loader = new \Yarest\ClassLoader();
        $loader->add($namespace, $path . $folder);
        $loader->register();

        return $loader;
    }

    /**
     * [checkValidClass description]
     * @param  string $class
     * @param  string $abstract
     * @return boolean
     */
    public static function checkValidClass($class, $abstract)
    {
        return class_exists($class) && is_subclass_of($class, $abstract);
    }
}
