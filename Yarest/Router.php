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
     * [$app description]
     * @var [type]
     */
    private $app;

    /**
     * [$response description]
     * @var Route
     */
    private $route;

    /**
     * [$response description]
     * @var ReflectionMethod
     */
    private $matched_method = null;


    /**
     * [__construct description]
     * @param App   $app
     * @param Route $route
     */
    public function __construct(App $app, Route $route)
    {
        $this->app   = $app;
        $this->route = $route;
    }

    /**
     * [__call description]
     * @param  [type] $method [description]
     * @param  [type] $args   [description]
     * @return [type]         [description]
     */
    public function __call($method, $args) {
        switch ($method) {
        case 'before':
        case 'after':
        case 'error':
        case 'notFound':
            if (count($args) == 1) {
                Helpers\Arguments::checkCallable($args[0]);
                $this->route->callbacks[$method] = $args[0];
            } else {
                throw new \InvalidArgumentException('Method $method expects only 1 argument');
            }
            return $this;
        default:
            throw new \BadMethodCallException("Bad method: $method", 1);        
        }    
    }

    /**
     * Creates a class and Invokes the matched method
     * @param  [type] $class    [description]
     * @param  array  $elements [description]
     */
    private function invoke($class, array $elements)
    {

        $resource = new $class();

        ## injecting stuff

        $info = Helpers\Reflection::parseComment($this->matched_method);

        $data = new \stdclass();

        $data->regex = $this->app->config['regex'];
        $data->body  = $this->app->request['body'];

        $info['var'] = Helpers\Parse::variables($info['var'], $data);


        if( !empty($data->invalid_input)) {
            $this->app->response->setStatus('412');
            $this->app->response->setBody($data->invalid_input);
            return;
        }

        if( !empty($data->invalid_regex)) {

            $this->app->response->setStatus('500');
            $this->app->response->setBody($data->invalid_regex);
            return;
        }

        $resource->body = $data->body;
        $resource->info = $info;
        $resource->response = $this->app->response;        
        
        $resource['docs'] = $resource->share(function () {
            
            $absolute_path = $this->app->request->pathUri;
            $namespace     = Helpers::stackToNamespace($this->namespace);
            $alias         = $this->app->config['alias'];

            $docs = new Docs($this->app->request->pathUri, $namespace, $alias);
            
            return $docs->generateAllMethods();
        });

        $resource['doc'] = $resource->share(function () {
                
            return Docs::generateMethod($this->matched_method);

        });
        
        try {

            $this->app->response->setStatus('200');

            # invoke user defined before method

            if (array_key_exists('before', $this->route->callbacks)) {
                call_user_func_array($this->route->callbacks['before'], array($resource));
            }

            # invoke class matched method
            
            $body = $this->matched_method->invokeArgs($resource, $elements);

            if (isset($body)) {
                $this->app->response->setBody($body);
            }

            # invoke user defined after method
            
            if (array_key_exists('after', $this->route->callbacks)) {
                call_user_func_array($this->route->callbacks['after'], array($resource));
            }

        } catch (\Exception $error) {

            $this->app->response->setStatus('500');
            
            if (array_key_exists('error', $this->route->callbacks)) {
                call_user_func_array($this->route->callbacks['error'], array($error));
            }
            
            echo "error";
        }

    }

    /**
     * [findMethods description]
     * @param  array  $methods  [description]
     * @param  array  $elements [description]
     * @return array|ReflectionMethod
     */
    public function findMethods(array $methods, array $elements)
    {

        $http_method = $this->app->request['method'];
        $alias  = $this->app->config['alias'];
        $regex  = $this->app->config['regex'];

        $numberofparameters = count($elements);
        
        $allowedMethods = array();

        foreach ($methods as $method) {
            
            preg_match("/^[a-z]+/", $method->name , $matched);

            if( !empty($matched) && array_key_exists($matched[0], $alias)) {    

                if ($method->getnumberofparameters() == $numberofparameters) {
                    
                    $verb = $alias[$matched[0]];

                    $allowedMethods[$verb] = false;

                    if ($verb == $http_method) {
                        
                        $allowedMethods[$verb] = true;

                        if(is_null($this->matched_method)) { 
                            if(Helpers\reflection::validateParameters($method, $elements, $regex)) {
                                $this->matched_method = $method;
                            }
                        }

                    } else {
                        $allowedMethods[$verb] = false;
                    }

                }
            }
        }

        return $allowedMethods;
    }

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
        
        ####################################

        $uri    = $this->app->request['uri'];

        $pattern   = $this->route->pattern;

        $derived_uri = Helpers\Uri::matchPattern($uri, $pattern);

        if ($derived_uri === false) {
            return false;
        }

        ####################################

        if (empty($derived_uri)) {
            $derived_uri = array($this->app->config['base']);
        }

        $namespace = $this->route->namespace;

        list($class, $elements) = Helpers\Uri::uriToClassAndElements($derived_uri, $namespace);

        ####################################

        $path      = $this->app->request['path'];
        $namespace = $this->route->namespace;
        $folder    = $this->route->folder;

        $loader = Helpers\Loader::loadNamespace($path, $namespace, $folder);

        if (!Helpers\Loader::checkValidClass($class, '\Yarest\Resource')) {
            $loader->unregister();
            return false;
        }
        
        $methods = Helpers\Reflection::getOwnPublicMethods($class);

        ####################################
        
        $allowedMethods = $this->findMethods($methods, $elements);

        if (empty($allowedMethods)) {

            $loader->unregister();
            return false;

        }

        ### from now on the router run method returns true
    
        if(is_null($this->matched_method)) {

            $http_method = $this->app->request['method'];

            if(array_key_exists($http_method, $allowedMethods) && $allowedMethods[$http_method]) {
                $this->app->response->setStatus('400');
            } else {
                $this->app->response->setStatus('405');                
                $this->app->response->setAllowed(array_keys($allowedMethods));
            }

        } else {            
            $this->invoke($class, $elements);
        }

        return true;
    }

}
