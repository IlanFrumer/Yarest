<?php

namespace Yarest;

/**
 * Yarest helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Helpers
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
     * [mergeZip description]
     * @param  array  $arr1
     * @param  array  $arr2
     * @return [type]
     */
    public static function mergeZip(array $arr1, array $arr2)
    {

        if (empty($arr1)) {
            return $arr2;
        } else if (empty($arr2)) {
            return $arr1;
        }

        $array = array();

        while ($val = array_shift($arr1)) {
            $array[] = $val;

            if ($val = array_shift($arr2)) {
                $array[] = $val;
            }
        }

        return array_merge($array, $arr2);
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
     * @param  [type] $uri
     * @return [type]
     */
    public static function namespaceToStack($uri)
    {
        return array_values(array_filter(explode('\\', $uri)));
    }

    public static function uriToStack($uri)
    {
        return array_values(array_filter(explode('/', $uri)));
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
     * [getConfig description]
     * @param  [type] $file
     * @return [type]
     */
    public static function getConfig($file)
    {
        $path = __DIR__."/config/$file.json";

        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        } else {
            throw new \Exception("Config file: $file.json not found", 1);
        }
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

    /**
     * [parseComment description]
     * @param  [type] $comment
     * @return [type]
     */
    public static function parseComment($comment)
    {
        $object = array();

        $pattern = '/(^\/\*\*)|(^\s*\**[ \/]?)|\s(?=@)|\s\*\//m';
        $comment = preg_replace($pattern, '', $comment);
        $comments = preg_split("/(\r?\n)/", $comment);
        $comments = array_filter($comments);

        foreach ($comments as $comment) {
            
            $comment = preg_split('/\s+/', $comment);
            $param   = array_shift($comment);
            $object[$param][] = $comment;

        }

        return $object;
    }
}
