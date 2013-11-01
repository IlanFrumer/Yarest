<?php

namespace Yarest\Helpers;

/**
 * Yarest Uri helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Uri
{
    /**
     * [toSlash description]
     * @param  [type] $string
     * @return [type]
     */
    public static function toSlash($string)
    {
        return str_replace('\\', '/', $string);
    }

    /**
     * [stripAsterisk description]
     * @param  [type] $string
     * @return [type]
     */
    public static function stripAsterisk($string)
    {
        $pos = strpos($string, '*');
        if ($pos !== false) {
            return substr($string, 0, $pos);
        } else {
            return $string;
        }
    }

    /**
     * [substrURI description]
     * @param  [type] $uri
     * @param  [type] $root
     * @return [type]
     */
    public static function substrURI($uri, $root)
    {
        if ($root == '/') {
            return $uri;
        } else {
            return preg_replace('/^' . preg_quote($root, '/') . '/', '', $uri);
        }
    }

    /**
     * [namespaceToArray description]
     * @param  string|array $namespace
     * @return array
     */
    public static function namespaceToArray($namespace)
    {
        if (is_string($namespace)) {
            return array_values(array_filter(explode('\\', $namespace)));
        } elseif (is_array($namespace)) {
            return $namespace;
        } else {
            throw new \InvalidArgumentException("Expected namespace to be an array or a string", 1);        
        }
    }

    /**
     * [uriToArray description]
     * @param  string|array $uri
     * @return array
     */
    public static function uriToArray($uri)
    {
        if (is_string($uri)) {
            return array_values(array_filter(explode('/', $uri)));
        } elseif (is_array($uri)) {
            return $uri;
        } else {
            throw new \InvalidArgumentException("Expected uri to be an array or a string", 1);        
        }
    }

    /**
     * [arrayToNamespace description]
     * @param  array  $array
     * @return [type]
     */
    public static function arrayToNamespace(array $array)
    {
        $array = array_map(function ($el) {
            return ucfirst(strtolower($el));
        }, $array);
        return implode('\\', $array);
    }

    /**
     * [arrayToURI description]
     * @param  array  $array
     * @return [type]
     */
    public static function arrayToURI(array $array)
    {
        return implode('/', $array);
    }

    /**
     * [uriToNamespace description]
     * @param  [type] $array
     * @param  [type] $namespace
     * @param  [type] $elements
     * @return [type]
     */
    public static function uriToNamespace($array, &$namespace, &$elements)
    {
        foreach ($array as $k => $v) {
            
            $v = ucfirst(strtolower($v));

            if ($k % 2 == 0) {
                $namespace[] = $v;
            } else {
                $elements[]  = $v;
            }
        }
    }
}
