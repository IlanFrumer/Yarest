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
     * [fix description]
     * @param  [type] $string
     * @return [type]
     */
    private static function fix($string)
    {
        return ucfirst(strtolower($string));
    }

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
     * [stripURI description]
     * @param  [type] $uri
     * @param  [type] $root
     * @return [type]
     */
    public static function stripURI($uri, $root)
    {
        if ($root == '/') {
            return $uri;
        } else {
            return preg_replace('/^' . preg_quote($root, '/') . '/', '', $uri);
        }
    }

    /**
     * [namespaceToStack description]
     * @param  string|array $namespace
     * @return array
     */
    public static function namespaceToStack($namespace)
    {
        if (is_string($namespace)) {
            return array_values(array_filter(explode('\\', $namespace)));
        } else {
            return $namespace;
        }
    }

    /**
     * [uriToStack description]
     * @param  string|array $uri
     * @return array
     */
    public static function uriToStack($uri)
    {
        if (is_string($uri)) {
            return array_values(array_filter(explode('/', $uri)));
        } else {
            return $uri;
        }
    }

    /**
     * [stackToNamespace description]
     * @param  array  $stack
     * @return [type]
     */
    public static function stackToNamespace(array $stack)
    {
        return implode('\\', $stack);
    }

    /**
     * [stackToURI description]
     * @param  array  $stack
     * @return [type]
     */
    public static function stackToURI(array $stack)
    {
        return implode('/', $stack);
    }

    /**
     * [divideStack description]
     * @param  [type] $stack
     * @param  [type] $namespace
     * @param  [type] $elements
     * @return [type]
     */
    public static function divideStack($stack, &$namespace, &$elements)
    {
        foreach ($stack as $k => $v) {
            if ($k % 2 == 0) {
                $namespace[] = self::fix($v);
            } else {
                $elements[]  = self::fix($v);
            }
        }
    }
}
