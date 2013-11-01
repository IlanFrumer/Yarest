<?php

namespace Yarest;

/**
 * Yarest Route class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Route
{
    /**
     * [$pattern description]
     * @var array
     */
    
    public $pattern;
    /**
     * [$namespace description]
     * @var string
     */
    public $namespace;

    /**
     * [$folder description]
     * @var string
     */
    public $folder;

    /**
     * [$path description]
     * @var string
     */
    public $path;

    /**
     * [$endpoint description]
     * @var array
     */
    public $endpoint;

    /**
     * [$class description]
     * @var string
     */
    public $class;

    /**
     * [$elements description]
     * @var array
     */
    public $elements = array();

    /**
     * [$allowedMethods description]
     * @var array
     */
    public $allowedMethods = array();

    /**
     * [$matchedMethod description]
     * @var ReflectionMethod
     */
    public $matchedMethod = null;

    /**
     * [__construct description]
     * @param string|array $pattern
     * @param string|array $namespace
     * @param string|array $folder
     */
    public function __construct($pattern, $namespace, $folder)
    {
        $pattern   = Helpers\Uri::stripAsterisk($pattern);
        $pattern   = Helpers\Uri::uriToStack($pattern);

        $namespace = Helpers\Uri::namespaceToStack($namespace);
        $namespace = Helpers\Uri::stackToNamespace($namespace);

        $folder    = Helpers\Uri::uriToStack($folder);
        $folder    = Helpers\Uri::stackToURI($folder);

        $this->pattern   = $pattern;
        $this->namespace = $namespace;
        $this->folder    = $folder;
    }


    /**
     * [matchPattern description]
     * @param  array $endpoint
     */
    public function matchPattern(array $endpoint)
    {
        $this->endpoint = Helpers\Pattern::match($endpoint, $this->route->pattern);
    }

    /**
     * [resolveClass description]
     * @param  string $base_class
     */
    public function resolveClass($base_class)
    {
        if (empty($this->endpoint)) {

            // TODO: check namespace empty case
            // TODO: check base_class not valid
            // TODO: config validation method

            $this->class = $this->namespace . $base_class;

        } else {

            $class = array();

            Helpers::divideStack($this->route->endpoint, $class, $this->route->elements);

            $this->class = $this->namespace . Helpers\Uri::stackToNamespace($class);
        }
    }

    /**
     * [loadNamespace description]
     */
    public function loadNamespace()
    {
        $loader = new ClassLoader();
        $loader->add($this->namespace, $this->path);
        $loader->register();

        return $loader;
    }

    /**
     * [checkClass description]
     * @return boolean
     */
    public function checkClass()
    {
        return class_exists($this->class) && is_subclass_of($this->class, '\Yarest\Resource');
    }

    /**
     * [findMethods description]
     * @param  array $alias
     * @return boolean
     */
    public function findMethods($alias)
    {
        $ref = new \ReflectionClass($this->class);
        $methods = Helpers\Reflection::getOwnPublicMethods($ref);
        $numberofparameters = count($this->route->elements);

        foreach ($methods as $method) {
            if (array_key_exists($method->name, $this->config['alias'])) {
                if ($method->getnumberofparameters() == $numberofparameters) {
                    
                    $verb = $this->config['alias'][$method->name];

                    $this->allowedMethods[] = $verb;

                    if ($verb == $this->app->request->method) {
                        $this->matchedMethod = $method;
                        return true;
                    }
                }
            }
        }

        $this->allowedMethods = array_unique($this->allowedMethods);
        return !empty($this->allowedMethods);
    }
}
