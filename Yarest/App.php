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
    /**
     * [$response description]
     * @var \Yarest\Response
     */
    public $response;

    /**
     * [$request description]
     * @var \Yarest\Request
     */
    public $request;

    /**
     * [$config description]
     * @var \Yarest\Config
     */
    public $config;

    /**
     * [$routers description]
     * @var array
     */
    private $routers = array();

    /**
     * Creating config, requsest, response instances which would be passed to the routers.
     * 
     * @param array $config pass user configuration to override defaults
     */
    public function __construct($config = array())
    {
        $this->response = new Response();
        $this->request  = new Request();
        $this->config   = new Config($config);
    }

    /**
     * Mapping a route to a namepace.
     * 
     * Every part of thr uri pattern after an asterisk is completly being ignored.
     * 
     * @param string $pattern   A valid URI to match the request
     * @param string|array $namespace The root namespace
     * @param string|array $folder    OPTIONAL folder relative to the root folder
     */
    public function route($pattern, $namespace, $folder = array())
    {

        $route  = new Route($pattern, $namespace, $folder);

        $router = new Router($this, $route);
        
        $this->routers[] = $router;

        return $router;
    }

    /**
     * Iterating all routers until matched
     */
    public function run()
    {

        while ($router = array_shift($this->routers)) {

            $router->run();

        }
        return $this;
    }

    /**
     * Responding to the client
     */
    public function headers()
    {
        $headers = $this->response->getHeaders();

        foreach ($headers as $header) {
            header($header);            
        }
        return $this;
    }
    public function body()
    {
        echo $this->response->getBody();
        return $this;
    }
}
