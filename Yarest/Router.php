<?php

namespace Yarest;

/**
 * Yarest router class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Dispatch
{
    /**
     * [matchPattern description]
     * 
     * @param  Request $request [description]
     * @param  array   $router [description]
     * @return array|false [description]
     */
    public static function matchPattern(Request $request, array $router)
    {
        $requestEndPoint = $request->endPoint;
        $pattern = $router['pattern'];

        while ($p = array_shift($pattern)) {
            $r = array_shift($requestEndPoint);
            if ($p != $r) {
                return false;
            }
        }

        return $requestEndPoint;
    }

    /**
     * [loadNamespace description]
     * @param  string $root_path [description]
     * @param  array  $namespace [description]
     * @param  array  $folder    [description]
     * @return ClassLoader       [description]
     */
    public static function loadNamespace(Request $request, array $router)
    {
        $loader = new ClassLoader();

        $namespaceUri = Helpers::stackToNamespace($router['namespace']);
        $folderUri    = Helpers::stackToURI($router['folder']);

        $loader->add($namespaceUri, $request['path'] . $folderUri);
        $loader->register();

        return $loader;
    }

    private static function getOwnMethods(\ReflectionClass $class)
    {
        
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = array_filter($methods, function ($method) use ($class) {
            return $method->class == $class->name;
        });

        return $methods;
    }


    /**
     * [matchMethod description]
     * @param  Config $config   [description]
     * @param  array  $endpoint [description]
     * @param  array  $router   [description]
     * 
     * @return \ReflectionMethod|array|false [description]
     */
    public static function matchMethod(Config $config, array $endpoint, array $router)
    {
        $class = $router['namespace'];

        $elements = array();

        if (empty($endpoint)) {
    
            $class = array_merge($class, $config['root_class']);

        } else {

            Helpers::divideStack($endpoint, $class, $elements);

        }

        $class_name = Helpers::stackToNamespace($class);

        if (!class_exists($class_name) || ! is_subclass_of($class_name, '\Yarest\Resource')) {
            return false;
        }

        $ref = new \ReflectionClass($class_name);

        $methods = self::getOwnMethods($ref);

        $numberofparameters = count($this->elements);

        $allowedMethods = array();

        foreach ($methods as $method) {
            if (array_key_exists($method->name, $config['alias'])) {
                if ($method->getnumberofparameters() == $numberofparameters) {
                    
                    $verb = $config['alias'][$method->name];

                    $allowedMethods[] = $verb;

                    if ($verb == $this->app->request->method) {
                        return $method;
                    }
                }
            }
        }

        return array_unique($allowedMethods);
    }
}
