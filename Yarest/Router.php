<?php

namespace Yarest;

/**
 * Yarest router class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Router
{
    /**
     * [$config description]
     * @var Config
     */
    private $config;

    /**
     * [$requsest description]
     * @var Request
     */
    private $requsest;

    /**
     * [$response description]
     * @var Response
     */
    private $response;

    /**
     * [$response description]
     * @var Route
     */
    private $route;

    /**
     * [$callbacks description]
     * @var array
     */
    private $callbacks = array();


    /**
     * [__construct description]
     * @param Config   $config
     * @param Request  $requsest
     * @param Response $response
     * @param Route    $route
     */
    public function __construct(Config $config, Request $requsest, Response $response, Route $route)
    {
        $this->config    = $config;
        $this->requsest  = $requsest;
        $this->response  = $response;
        $this->route     = $route;
    }

    /**
     * Creates a class and Invokes the matched method
     */
    
    // private function invokeMethod()
    // {
    //     $this->app->response->setStatus('200');

    //     $resource = new $this->class_name();

    //     $docComments = $this->matchedMethod->getDocComment();

    //     ## injecting stuff

    //     $resource->request  = $this->app->request;
    //     $resource->response = $this->app->response;

        

    //         $resource['docs'] = $resource->share(function () {
                
    //             $absolute_path = $this->app->request->pathUri;
    //             $namespace     = Helpers::stackToNamespace($this->namespace);
    //             $alias         = $this->app->config['alias'];

    //             $docs = new Docs($this->app->request->pathUri, $namespace, $alias);
                
    //             return $docs->generateAllMethods();
    //         });

    //         $resource['doc'] = $resource->share(function () {
                    
    //             return Docs::generateMethod($this->matchedMethod);

    //         });
    //     };
        
    //     $resource->auth
    //     $resource->fields
    //     $resource->fields
    //     $resource->fields

        
       
    //     try {

    //         # invoke user defined before method
            
    //         if (array_key_exists('before', $this->callbacks)) {
    //             call_user_func_array($this->callbacks['before'], array($resource));
    //         }

    //         # invoke class matched method
            
    //         $body = $this->matchedMethod->invokeArgs($resource, $this->elements);

    //         # invoke user defined after method
            
    //         if (array_key_exists('after', $this->callbacks)) {
    //             call_user_func_array($this->callbacks['after'], array($resource));
    //         }

    //     } catch (\Exception $error) {

    //         $this->app->response->setStatus('500');
            
    //         if (array_key_exists('error', $this->callbacks)) {
    //             call_user_func_array($this->callbacks['error'], array($error));
    //         }
            
    //         echo "error";
    //     }

    //     if (isset($body)) {
    //         $this->app->response->setBody($body);
    //     }
    // }


    /**
     * phase 0: Match the router pattern with the end point
     * phase 1: Match the appropriate class (autoloading)
     * phase 2: Match end point appropriate class methods
     * phase 3: If found invokes matched method
     *          else sets response to 405 with the allowed method list
     * 
     * If router fails before phase 3 than it returns false
     * Notice: On phase 3, the router returns true even if no matched method was invoked
     *         but there are other methods that responses to other HTTP methods
     *
     * @return boolean If Methods found
     */
    public function run()
    {
        # Phase 1: match route with request end point

        $endpoint = $this->route->matchPattern($this->request['endpoint']);

        if (!$endpoint) {
            return false;
        }

        # Phase 2: auto resolve target class and prepare elements

        $this->route->resolveClass($endpoint, $this->config['base_class']);

        ## Phase 3: register namespace

        $loader = Helpers\Loader::loadNamespace($this->request['path'], $this->route->namespace, $this->route->folder);

        ## Phase 4: check if class is valid , if not unregister

        if (!Helpers\Loader::checkValidClass($this->route->class, '\Yarest\Resource')) {
            $loader->unregister();
            return false;
        }

        ## Phase 5: looks for valid methods in the class (owned , public , alias), if not unregister

        if (!$this->route->findMethods($this->config['alias'], $this->request['method'])) {
            $loader->unregister();
            return false;
        }


        if (! $this->route->matchedMethod) {

            $this->response->setStatus('405');
            $this->response->setAllowed($this->route->allowedMethods);

        } else {

            $this->invokeMethod();
        }

        return true;
    }

    /**
     * [before description]
     * @param  object $callable
     * @return \Yarest\Router the same instance for method chaining
     */
    public function before($callable)
    {
        Helpers\Arguments::checkCallable($callable);
        $this->callbacks['before'] = $callable;
        return $this;
    }

    /**
     * [after description]
     * @param  object $callable
     * @return \Yarest\Router the same instance for method chaining
     */
    public function after($callable)
    {
        Helpers\Arguments::checkCallable($callable);
        $this->callbacks['after'] = $callable;
        return $this;
    }

    /**
     * [error description].
     * 
     * Allow Multiple errors with differnt Exception class
     * @param  object $callable
     * @return \Yarest\Router the same instance for method chaining
     */
    public function error($callable)
    {
        Helpers\Arguments::checkCallable($callable);
        $this->callbacks['error'] = $callable;
        return $this;
    }
}
