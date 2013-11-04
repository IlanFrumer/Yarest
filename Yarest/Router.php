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
    public function __call($method, $args)
    {
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
     * @param  ReflectionMethod $method    [description]
     * @param  array  $elements [description]
     */
    private function invoke(\ReflectionMethod $method, array $elements)
    {

        $class = $method->getDeclaringClass()->name;
        $resource = new $class();

        // parse comment
        $info = $this->app->parser->parseComment($method);

        // validate input
        list($errors, $invalid_input, $body) = $this->app->parser->checkCommentVars($info['var']);

        // developer bad expressions
        if (!empty($errors)) {
            $output = array();
            $output['class']  = $method->class;
            $output['method'] = $method->name;
            $output['errors'] = $errors;
            throw new Exception\InvalidExpression(array("InvalidCommentExpressions" => $output));
        }

        // invalid input
        if (!empty($invalid_input)) {
            $this->app->response->setStatus('412');
            $this->app->response->setBody(array("InvalidInput" => $invalid_input));
            return;
        }

        $resource->body = $body;
        $resource->info = $info;
        $resource->response = $this->app->response;
        
        // $resource['docs'] = $resource->share(function () {
            
        //     $absolute_path = $this->app->request->pathUri;
        //     $namespace     = Helpers::stackToNamespace($this->namespace);
        //     $alias         = $this->app->config['alias'];

        //     $docs = new Docs($this->app->request->pathUri, $namespace, $alias);
            
        //     return $docs->generateAllMethods();
        // });

        // $resource['doc'] = $resource->share(function () {
                
        //     return Docs::generateMethod($method);

        // });
        
        try {

            $this->app->response->setStatus('200');

            # invoke user defined before method

            if (array_key_exists('before', $this->route->callbacks)) {
                call_user_func_array($this->route->callbacks['before'], array($resource));
            }

            # invoke class matched method
            
            $body = $method->invokeArgs($resource, $elements);

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
            } else {

                // pass it up if there is no user defined error handler
                throw $error;
            }
        }

    }



    /**
     *
     * @return boolean If methods found
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
        
        list($errors, $allowed_methods, $matched_method) = $this->app->parser->filterMethods($methods, $elements);

        if (!empty($errors)) {
            throw new Exception\InvalidExpression(array("InvalidExpressions" => $errors));
        }

        if (empty($allowed_methods)) {

            $loader->unregister();
            return false;

        }

        ### from now on returns true
    
        if (is_null($matched_method)) {

            $http_method = $this->app->request['method'];

            if (array_key_exists($http_method, $allowed_methods) && $allowed_methods[$http_method]) {
                $this->app->response->setStatus('400');
            } else {
                $this->app->response->setStatus('405');
                $this->app->response->setAllowed(array_keys($allowed_methods));
            }

        } else {
            $this->invoke($matched_method, $elements);
        }

        return true;
    }
}
