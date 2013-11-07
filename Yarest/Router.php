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
     * [invoke description]
     * @param  ParseInvoke $invoker [description]
     * @return [type]               [description]
     */
    private function invoke(Parse\Invoke $invoker)
    {

        $resource = $invoker->createResource();

        $resource->config   = $this->app->config;
        $resource->request  = $this->app->request;
        $resource->response = $this->app->response;

        $server   = $this->app->request['server'];
        $protocol = $this->app->request['protocol'];
        $pattern  = $this->route->pattern_string;

        $resource->prefix = "$protocol://$server/$pattern";

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

            if (array_key_exists('inject', $this->route->callbacks)) {
                call_user_func_array($this->route->callbacks['inject'], array($resource));
            }

            # invoke class matched method
            
            $body = $invoker->invoke();

            if (isset($body)) {
                $this->app->response->setBody($body);
            }

            # invoke user defined after method
            
            if (array_key_exists('after', $this->route->callbacks)) {
                call_user_func_array($this->route->callbacks['after'], array($resource));
            }

        } catch (Exception\Halt $error) {

            // pass
            
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
        
        $parse = new Parse\Parse($class, $this->app->config, $this->app->request);

        $parse->filterMethods($elements);


        if (isset($parse->errors['arguments']['invalid_syntax'])) {

            $this->app->response->setStatus(500);
            $this->app->response->setBody($parse->errors);

        }

        if (!is_null($parse->invoker)) {

            if (isset($parse->errors['variables']['invalid_syntax'])) {

                $this->app->response->setStatus(500);
                $this->app->response->setBody($parse->errors);

            } elseif (isset($parse->errors['variables']['invalid_input'])) {

                $this->app->response->setStatus(412);
                $this->app->response->setBody($parse->errors);

            } else {
                $this->invoke($parse->invoker);
            }

        } elseif (isset($parse->errors['arguments']['invalid_input'])) {

                $this->app->response->setStatus(400);
                $this->app->response->setBody($parse->errors);

        } elseif (!empty($parse->allowed_http_methods)) {

                $this->app->response->setStatus(405);
                $this->app->response->setAllowed(array_keys($parse->allowed_http_methods));
        }

        $loader->unregister();

        return true;

        ####################################
    }
}
