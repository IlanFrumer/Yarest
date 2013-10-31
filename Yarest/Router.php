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
     * [$pattern description]
     * @var [type]
     */
    private $pattern;

    /**
     * [$namespace description]
     * @var [type]
     */
    private $namespace;

    /**
     * [$folder description]
     * @var [type]
     */
    private $folder;

    /**
     * [$endPoint description]
     * @var array
     */
    private $endPoint  = array();

    /**
     * [$class_name description]
     * @var [type]
     */
    private $class_name;

    /**
     * [$elements description]
     * @var array
     */
    private $elements  = array();

    /**
     * [$matchedMethod description]
     * @var [type]
     */
    private $matchedMethod  = null;

    /**
     * [$allowedMethods description]
     * @var array
     */
    private $allowedMethods = array();

    /**
     * [$callbacks description]
     * @var array
     */
    private $callbacks = array();

    /**
     * [__construct description]
     * @param App    $app       The instance of Yarest application
     * @param array  $pattern   Router pattern
     * @param array  $namespace Namespace of the classes
     * @param array  $folder    A Directory relative to the root folder
     */
    public function __construct(App $app, array $pattern, array $namespace, array $folder)
    {
        $this->app       = $app;
        $this->namespace = $namespace;
        $this->pattern   = $pattern;
        $this->folder    = $folder;
    }

    /**
     * Matches the current router with the given pattern and creates a router stack
     * @return boolean if Matched
     */
    private function matchPattern()
    {

        $requestEndPoint = $this->app->request->endPoint;

        $pattern = $this->pattern;

        while ($p = array_shift($pattern)) {
            $r = array_shift($requestEndPoint);
            if ($p != $r) {
                return false;
            }
        }

        $this->endPoint = $requestEndPoint;

        return true;
    }

    /**
     * Resolves the class and method elements from the end point stack
     * 
     * @return boolean if class exists and extends Pimple
     */
    private function findClass()
    {

        $class = $this->namespace;

        if (empty($this->endPoint)) {
    
            $root  = Helpers::namespaceToStack($this->app->config['root_class']);
            $class = array_merge($class, $root);

        } else {

            Helpers::divideStack($this->endPoint, $class, $this->elements);

        }

        $this->class_name = Helpers::stackToNamespace($class);

        return class_exists($this->class_name) && is_subclass_of($this->class_name, '\Yarest\Pimple');
    }

    /**
     * Matches all available methods with the right number of arguments and with the http method request
     * 
     * @return boolean if matched methods with the right number of arguments
     */
    private function matchMethods()
    {
        $ref = new \ReflectionClass($this->class_name);

        $methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = array_filter($methods, function ($method) {
            return $method->class == $this->class_name;
        });

        $numberofparameters = count($this->elements);

        $aliases = $this->app->config['alias'];

        $allowedMethods = array();

        foreach ($methods as $method) {
            if (array_key_exists($method->name, $aliases)) {
                if ($method->getnumberofparameters() == $numberofparameters) {
                    
                    $verb = $aliases[$method->name];

                    $allowedMethods[] = $verb;

                    if ($verb == $this->app->request->method) {
                        $this->matchedMethod = $method;
                        return true;
                    }
                }
            }
        }
        $this->allowedMethods = array_unique($allowedMethods);

        return !empty($this->allowedMethods);
    }

    /**
     * Creates a class and Invokes the matched method
     */
    
    private function invokeMethod()
    {
        $this->app->response->setStatus('200');

        $resource = new $this->class_name();

        $docComments = $this->matchedMethod->getDocComment();

        ## injecting stuff

        $resource->request  = $this->app->request;
        $resource->response = $this->app->response;

        $resource['docs'] = $resource->share(function () {
            
            $absolute_path = $this->app->request->pathUri;
            $namespace     = Helpers::stackToNamespace($this->namespace);
            $aliases       = $this->app->config['alias'];

            return new Docs($this->app->request->pathUri, $namespace, $aliases);
        });

        /*
        $resource->auth
        $resource->fields
        $resource->fields
        $resource->fields

        */

        if (is_callable($this->app->injector)) {
            call_user_func_array($this->app->injector, array($resource));
        }

        $body = $this->matchedMethod->invokeArgs($resource, $this->elements);
        
        if ($body) {
            $this->app->response->setBody($body);
        }
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
    
    /**
     * [before description]
     * @param  callable $callback
     * @return [type]
     */
    public function before(callable $callback)
    {
        $this->callbacks['before'] = $callback;
    }

    /**
     * [after description]
     * @param  callable $callback
     * @return [type]
     */
    public function after(callable $callback)
    {
        $this->callbacks['after'] = $callback;
    }

    /**
     * [error description]
     * @param  callable $callback
     * @return [type]
     */
    public function error(callable $callback)
    {
        $this->callbacks['error'] = $callback;
    }
    
    /**
     * [run description]
     * @return [type]
     */
    public function run()
    {
        # Phase 0:

        if (!$this->matchPattern()) {
            return false;
        }


        ## Phase 1:

        $loader = new ClassLoader();
        $namespaceUri    = Helpers::stackToNamespace($this->namespace);
        $namespaceFolder = $this->app->request->pathUri . Helpers::stackToURI($this->folder);
        $loader->add($namespaceUri, $namespaceFolder);
        $loader->register();

        if (!$this->findClass()) {
            $loader->unregister();
            return false;
        }

        ## Phase 2:

        if (!$this->matchMethods()) {
            return false;
        }

        ## Phase 3:
        
        if (is_null($this->matchedMethod)) {

            $this->app->response->setStatus('405');
            $this->app->response->setAllowed($this->allowedMethods);

        } else {
            
            $this->invokeMethod();
        }

        return true;
    }
}
