<?php

namespace Yarest;

/**
 * Yarest api documenting class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Docs
{
    /**
     * [$methods description]
     * @var array
     */
    public $methods = array();

    /**
     * [__construct description]
     * @param [type] $absolute_path
     * @param [type] $namespace
     * @param [type] $aliases
     */
    public function __construct($absolute_path, $namespace, $aliases)
    {

        // request -> virtualHost
        // request -> server name
        // config  -> aliases
        $classes = $this->traverseClasses($absolute_path, $namespace);
        
        foreach ($classes as $class) {
            $this->getMethods($class, $aliases);
        }

        // $this->server        = $app->request->server;
        // $this->aliases       = $app->config['alias'];
        // $this->path          = $path;
        // $this->namespace     = $namespace;
        // $this->root_stack    = $app->request->root_stack;
        // $this->pattern_stack = $pattern_stack;
    }

    /**
     * Find all classes of a given namespace
     * @param  string $path      an absoulute path to where the namespace is located
     * @param  string $namespace the namespace to traverse
     * @return array Returns a list of the namespace classes
     */
    private function traverseClasses($absolute_path, $namespace)
    {
        $dir  = $absolute_path . $namespace;

        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::SELF_FIRST);

        $classes = array();

        foreach ($iterator as $path) {
            if (! $path->isDir()) {
                $class = preg_replace('/^' . preg_quote($absolute_path, '/') . '/', '', $path->__toString());
                $class = preg_replace('/\.php$/', '', $class);
                $class = str_replace('/', '\\', $class);
                $classes[] = $class;
            }
        }

        return $classes;
    }

    /**
     * Find all Public owned valid Methods of a class
     * Adds the list of methods to the Docs instance variable $methods
     * @param  string $class
     * @param  array  $aliases a list of valid method names
     */
    private function getMethods($class, array $aliases)
    {
        if (!class_exists($class)) {
            return;
        }

        $ref = new \ReflectionClass($class);
        $class_methods = $ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        $class_methods = array_values(array_filter($class_methods, function ($method) use ($class, $aliases) {
            return $method->class == $class && array_key_exists($method->name, $aliases);
        }));

        $this->methods = array_merge($this->methods, $class_methods);
    }

    // public function methodToURIStack($method)
    // {
        
    //     $collections = Helpers::stripURI($method->class, $this->namespace);
        
    //     $collections = Helpers::uriToStack($collections, '\\');
        
    //     $elements = array_map(function ($parameter) {
    //         return ":".$parameter->name;
    //     }, $method->getParameters());

    //     return Helpers::mergeZip($collections, $elements);
    // }

    // public function generate()
    // {

    //     $this->traverseClasses();
    //     $this->getMethods();

    //     $docs = array();
 
    //     $docs['server']  = $this->server;

    //     foreach ($this->methods as $method) {
    //         $end_point = array();
    //         $end_point['method']  = $this->aliases[$method->name];

    //         # reverse the router stack

    //         $uri_stack = array_merge($this->root_stack, $this->pattern_stack, $this->methodToURIStack($method));
    //         $end_point['uri']        = Helpers::stackToURI($uri_stack)."/";
            
    //         /* Debug
    //         $end_point['comment']    = $method->getDocComment();
    //         $end_point['namespace']  = $this->namespace;
    //         $end_point['class']      = $method->class;
    //         $end_point['name']       = $method->name;
    //         $end_point['startLine']  = $method->getStartLine();
    //         $end_point['endLine']    = $method->getEndLine();
    //         $end_point['fileName']    = $method->getFileName();
    //         */
            
    //         foreach ($method->getParameters() as $parameter) {
    //             // $end_point['parameters'][] = $parameter;
    //         }
    //         $docs['methods'][] = $end_point;
    //     }

    //     return $docs;
    // }
    
    /**
     * [__string description]
     * @todo implementation
     * @return [type]
     */
    public function __string()
    {
    }
}
