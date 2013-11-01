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
    private $methods = array();

    /**
     * [__construct description]
     * @param [type] $absolute_path
     * @param [type] $namespace
     * @param [type] $aliases
     */
    public function __construct($absolute_path, $namespace, $aliases)
    {

        $classes = self::traverseClasses($absolute_path, $namespace);
        
        foreach ($classes as $class) {
            $this->traverseMethods($class, $aliases);
        }

    }

    /**
     * Find all classes of a given namespace
     * @param  string $path      an absoulute path to where the namespace is located
     * @param  string $namespace the namespace to traverse
     * @return array Returns a list of the namespace classes
     */
    private static function traverseClasses($absolute_path, $namespace)
    {
        $dir  = $absolute_path . $namespace;

        $directory_iterator = new \RecursiveDirectoryIterator($dir);
        $iterator = new \RecursiveIteratorIterator($directory_iterator, \RecursiveIteratorIterator::SELF_FIRST);

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
    private function traverseMethods($class, array $aliases)
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

    /**
     * [generateAllMethods description]
     * @return [type] [description]
     */
    public function generateAllMethods()
    {
        $docs = array();

        foreach ($this->methods as $method) {
            $docs['methods'][] = self::generateMethod($method);
        }

        return $docs;
    }

    /**
     * [generateMethod description]
     * @param  ReflectionMethod $method [description]
     * @return [type]                   [description]
     */
    public static function generateMethod(\ReflectionMethod $method)
    {
        $doc = array();

        $doc['name']  = $method->name;
        $doc['class'] = $method->class;
        $doc['comments']  = $method->getDocComment();
        $doc['filename']  = $method->getFileName();

        foreach ($method->getParameters() as $parameter) {
            
            $p = array();

            $p['name'] = $parameter->name;
            $p['default'] = $parameter->getDefaultValue();
            $p['position'] = $parameter->getPosition();
            $p['byValue'] = $parameter->canBePassedByValue();

            $doc['parameters'][] = $p;

        }

        return $doc;
    }

    // public function methodToURIStack($method)
    // {
        
    //     $collections = Helpers::substrURI($method->class, $this->namespace);
        
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
            

    //         $docs['methods'][] = $end_point;
    //     }

    //     return $docs;
    // }
    
    /**
     * [__string description]
     * @return [type]
     */
    public function __string()
    {
        // TODO: implementation
    }
}
