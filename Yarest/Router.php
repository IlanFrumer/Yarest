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
     * Creates a class and Invokes the matched method
     * @param  array  $methods  array of ReflectionMethod object
     * @param  array  $elements [description]
     */
    private function invoke(array $methods, array $elements)
    {

        $class = $methods[0]->getDeclaringClass()->name;
        $resource = new $class();

        $all_invalid_input = array();

        foreach ($methods as $method) {
            
            // parse comment
            $comment = $this->app->parser->parseComment($method);

            // validate input
            list($errors, $invalid_input, $body) = $this->app->parser->checkCommentVars($comment['var']);

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

                $all_invalid_input[] = $invalid_input;

            } else {

                break;
            }

        }

        if (! empty($all_invalid_input)) {
            $this->app->response->setStatus('412');
            $this->app->response->setBody(array("InvalidInputs" => $all_invalid_input));
            return;
        }

        $resource->body     = $body;
        $resource->comment  = $comment;
        $resource->request  = $this->app->request;
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
     * @return boolean If no more routers should be dispatched
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

        ####################################
        
        $parse = new \Parse\Reflection($class, $this->app->config, $this->app->request);

        $parse->filterMethods($elements);

        return true;
        $methods = Helpers\Reflection::getOwnPublicMethods($class);
        
        list($errors, $allowed_methods, $matched_methods) = $this->app->parser->filterMethods($methods, $elements);

        if (!empty($errors)) {
            throw new Exception\InvalidExpression(array("InvalidExpressions" => $errors));
        }


        if (empty($allowed_methods)) {

            $loader->unregister();
            return false;

        }

        ### from now on returns true
    
        if (empty($matched_methods)) {

            $http_method = $this->app->request['method'];

            if (array_key_exists($http_method, $allowed_methods) && $allowed_methods[$http_method]) {
                $this->app->response->setStatus('400');
            } else {
                $this->app->response->setStatus('405');
                $this->app->response->setAllowed(array_keys($allowed_methods));
            }

        } else {
            $this->invoke($matched_methods, $elements);
        }

        return true;
    }
}
