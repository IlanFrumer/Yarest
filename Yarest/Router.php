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

    

    public function inject(Resource $resource)
    {
        $fields = Helpers\Collection::arrayColumn($resource->comment['return'], 'name');
        $resource->fields = empty($fields) ? "*" : implode(',', $fields);
        
        $host     = $this->app->request['host'];
        $protocol = $this->app->request['protocol'];
        $pattern  = Helpers\Uri::arrayToURI($this->route->pattern);
        $uri      = Helpers\Uri::arrayToURI($this->app->request['uri']);

        $resource->prefix  = "$protocol://$host/$pattern";
        $resource->current = "$protocol://$host/$uri";

        // $resource['docs'] = $resource->share(function () {
            
        //     $absolute_path = $this->app->request->pathUri;
        //     $namespace     = Helpers::stackToNamespace($this->namespace);
        //     $alias         = $this->app->config['route.alias'];

        //     $docs = new Docs($this->app->request->pathUri, $namespace, $alias);
            
        //     return $docs->generateAllMethods();
        // });

        // $resource['doc'] = $resource->share(function () {
                
        //     return Docs::generateMethod($method);

        // });

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
            $derived_uri = array($this->app->config['route.base']);
        }

        $namespace = $this->route->namespace;

        list($class, $elements) = Helpers\Uri::uriToClassAndElements($derived_uri, $namespace);

        ####################################

        $path      = $this->app->request['path'];
        $namespace = $this->route->namespace;
        $folder    = $this->route->folder;

        $loader = Loader::loadNamespace($path, $namespace, $folder);

        if (!Loader::checkValidClass($class, '\Yarest\Resource')) {
            $loader->unregister();
            return false;
        }


        ####################################
        
        $parse = new Parse\Parse($this->app->config, $this->app->request);

        $method = $parse->matchMethod($class, $elements);

        if (!$method) {
            return false;
            $loader->unregister();
        }

        ####################################

        $resource = new $class();
        $loader->unregister();

        $resource->config  = $this->app->config;
        $resource->request = $this->app->request;
        $resource->comment = $parse->getComment($method);

        $this->route->run('before', array($resource));

        $parse->validateMethod($resource->comment);

        ####################################

        $resource->response  = $this->app->response;
        $resource->variables = $parse->variables;

        $this->inject($resource);
        
        $this->route->run('inject', array($resource));

        ####################################

        $this->route->run(function ($resource) use ($method, $elements) {
            
            $resource->response->setStatus(200);
            
            $body = $method->invokeArgs($resource, $elements);
            
            if (!is_null($body)) {
                $resource->response->setBody($body);
            }

        }, array($resource));

        ####################################
        
        $this->route->run('after', array($resource));

        ####################################
        return true;
    }
}
