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

    public $routers = array();

    /**
     * Creating config, requsest, response instances which would be passed to the routers.
     * 
     * @param array $config pass user configuration to override defaults
     */
    
    public function __construct($config = array())
    {
        
        # Response must be first because it registers an error handler
        $this->response = new Response();

        $this->request  = new Request();
                
        $this->config   = new Config($config);
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
        $router  = array();
        $invoker = new Invoker();
        
        $pattern = Helpers::stripAsterisk($pattern);

        $router['pattern']   = Helpers::uriToStack($pattern);
        $router['namespace'] = Helpers::namespaceToStack($namespace);
        $router['folder']    = Helpers::uriToStack($folder);

        $router['invoker']   = $invoker;
        
        ###

        // $injector = function (Resource $resource) use ($namespace) {
            
        //     $namespace_string = Helpers::stackToNamespace($namespace);

        //     $resource['docs'] = $resource->share(function () use ($namespace_string) {

        //         $absolute_path = $this->request['pathUri'];
        //         $alias         = $this->config['alias'];
        //         $docs = new Docs($absolute_path, $namespace_string, $alias);
        //         return $docs->generateAllMethods();
        //     });

               
        // };

        ####
        
        // $router = new Router($pattern, $namespace, $folder);

        $this->routers[] = $router;

        return $invoker;
    }

    /**
     * Iterating all routers until matched
     */
    public function run()
    {

        while ($router = array_shift($this->routers)) {

            $endpoint = Dispatch::matchPattern($this->request, $router);

            if (!$endpoint) {
                continue;
            }

            $loader = Dispatch::loadNamespace($root_path, $router);

            $another  = Dispatch::matchClass($this->config, $endpoint, $router);


            $router['invoker']->invoke($resurce);
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
