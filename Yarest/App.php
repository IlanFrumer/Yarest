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

        return $route;
    }

    /**
     * Iterating all routers until matched or until error is raised
     */
    public function run()
    {
        set_error_handler(array('\Yarest\App', 'handleErrors'));

        try {

            while ($router = array_shift($this->routers)) {
                if ($router->run()) {
                    break;
                }
            }

        } catch (Exception\YarestException $e) {
            
            $e->setResponse($this->response);
        
        } catch (\Exception $e) {

            $this->response->setBody($e->getMessage());
            $this->response->setStatus(500);
        }

        restore_error_handler();
        return $this;
    }

    public static function handleErrors($errno, $errstr = '', $errfile = '', $errline = '')
    {
        throw new Exception\Error($errstr, $errno, $errfile, $errline);
    }

    /**
     * Responding to the client with headers
     */
    public function headers()
    {
        $headers = $this->response->getHeaders();

        foreach ($headers as $header) {
            header($header);
        }
        return $this;
    }

    /**
     * Responding to the client with body
     */
    public function body()
    {
        $body = $this->response->getBody();
        
        if (is_resource($body)) {

            fpassthru($body);

        } else {

            echo $body;
        }

        return $this;
    }
}
