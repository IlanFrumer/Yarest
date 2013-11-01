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
     * [$class description]
     * @var string
     */
    public $class;

    /**
     * [$elements description]
     * @var array
     */
    public $elements;

    /**
     * [$allowedMethods description]
     * @var array
     */
    public $allowedMethods;

    /**
     * [$matchedMethod description]
     * @var ReflectionMethod
     */
    public $matchedMethod;

    /**
     * [__construct description]
     * @param string|array $pattern
     * @param string|array $namespace
     * @param string|array $folder
     */
    public function __construct($pattern, $namespace, $folder)
    {
        $pattern   = Helpers\Uri::stripAsterisk($pattern);
        $pattern   = Helpers\Uri::uriToArray($pattern);

        $namespace = Helpers\Uri::namespaceToArray($namespace);
        $namespace = Helpers\Uri::arrayToNamespace($namespace);

        $folder    = Helpers\Uri::uriToArray($folder);
        $folder    = Helpers\Uri::arrayToURI($folder);

        $this->pattern   = $pattern;
        $this->namespace = $namespace;
        $this->folder    = $folder;
    }


    /**
     * [matchPattern description]
     * @param  array $endpoint
     * @return array|false [description]
     */
    public function matchPattern(array $endpoint)
    {
        return Helpers\Pattern::match($endpoint, $this->pattern);
    }

    /**
     * [resolveClass description]
     * @param  string $base_class
     */
    public function resolveClass($endpoint, $base_class)
    {
        $this->elements = array();

        if (empty($endpoint)) {

            // TODO: check namespace empty case
            // TODO: check base_class not valid
            // TODO: config validation method

            $this->class = $this->namespace . "\\" . $base_class;

        } else {

            $class = array();

            Helpers\Uri::uriToNamespace($endpoint, $class, $this->elements);

            $this->class = $this->namespace . "\\" . Helpers\Uri::arrayToNamespace($class);
        }
    }

    /**
     * [findMethods description]
     * @param  array $alias
     * @param  string $httpMethod
     * @return boolean
     */
    public function findMethods($alias, $httpMethod)
    {
        $ref = new \ReflectionClass($this->class);
        $methods = Helpers\Reflection::getOwnPublicMethods($ref);
        $numberofparameters = count($this->elements);

        $this->matchedMethod = null;
        $this->allowedMethods = array();

        foreach ($methods as $method) {
            if (array_key_exists($method->name, $alias)) {
                if ($method->getnumberofparameters() == $numberofparameters) {
                    
                    $verb = $alias[$method->name];

                    $this->allowedMethods[] = $verb;

                    if ($verb == $httpMethod) {
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
