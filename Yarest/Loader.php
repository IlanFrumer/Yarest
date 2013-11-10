<?php

namespace Yarest;

/**
 * Yarest Loader class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Loader
{

    private $path;
    private $namespace;
    private $folder;

    public function __construct($path, $namespace, $folder)
    {
        $this->path      = $path;
        $this->namespace = $namespace;
        $this->folder    = $folder;
    }

    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'), false, true);
    }

    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    public function loadClass($class)
    {
        $class = Helpers\Uri::namespaceToArray($class);
        $class = Helpers\Uri::matchPattern($class, $this->namespace);

        if ($class) {

            $find = array_merge($this->folder, $this->namespace, $class);
            $file = $this->path . Helpers\Uri::arrayToUri($find) . ".php";

            if (file_exists($file)) {
                include_once $file;
            }

            return true;
        }
    }


    /**
     * [loadNamespace description]
     *
     * @param  string $path
     * @param  string $namespace
     * @param  string $folder
     * @return ClassLoader
     */
    public static function loadNamespace($path, array $namespace, array $folder)
    {
        $loader = new self($path, $namespace, $folder);
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
