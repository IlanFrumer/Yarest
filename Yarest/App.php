<?php

namespace Yarest;

/**
 * Yarest main class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class App
{
    public $config;
    public $request;
    public $response;
    public $injector = null;

    public $routers = array();

    /**
     * Creating config, requsest, response instances which would be passed to the routers.
     * 
     * @param array $config pass user configuration to override defaults
     */
    
    public function __construct(array $config = array())
    {
        ob_start();

        $this->request  = new Request();
        $this->response = new Response();
        $this->config   = new Config($config);
    }

    /**
     * Passing the callback which the resource should be injected.
     * 
     * @param callable $callback User defined callback.
     */
    public function inject(callable $callback)
    {
        $this->injector = $callback;
    }

    /**
     * Mapping a route to a namepace.
     * Every part of thr uri pattern after an asterisk is completly being ignored.
     * 
     * Internally, every path is transformed into an array representation
     * 
     * @param string $pattern   A valid URI to match the request
     * @param string|array $namespace The root namespace
     * @param string|array $folder    OPTIONAL folder relative to the root folder
     * 
     */
    public function router($pattern, $namespace, $folder = array())
    {
        
        $pattern   = Helpers::stripAsterisk($pattern);
        $pattern   = Helpers::uriToStack($pattern);

        if (is_string($namespace)) {
            $namespace = Helpers::namespaceToStack($namespace);
        }

        if (is_string($folder)) {
            $folder    = Helpers::uriToStack($folder);
        }
        
        $router = new Router($this, $pattern, $namespace, $folder);

        $this->routers[] = $router;

        return $router;
    }

    /**
     * Iterating all routers until matched
     */

    public function run()
    {
        while ($router = array_shift($this->routers)) {
            if ($router->run()) {
                return;
            }
        }
    }

    /**
     * Responding to the clients
     */
    public function __destruct()
    {
        echo $this->response;
    }
}
