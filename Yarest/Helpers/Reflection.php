<?php

namespace Yarest\Helpers;

/**
 * Yarest Reflection helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Reflection
{
    /**
     * [getOwnPublicMethods description]
     * @param  string $class
     * @return array of \ReflectionMethod
     */
    public static function getOwnPublicMethods($class)
    {

        $class_ref = new \ReflectionClass($class);

        $methods = $class_ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = array_filter($methods, function ($method) use ($class_ref) {
            return $method->class == $class_ref->name;
        });

        return $methods;
    }

    public static function validateParameters(\ReflectionMethod $method, array $elements, array $regex)
    {
        foreach ($method->getParameters() as $key => $parameter) {

            $name   = $parameter->name;
            $input  = $elements[$key];

            if ($parameter->isOptional()) {

            //     $default = strtolower($parameter->getDefaultValue());

            //     if (\Yarest\Helpers\Regex::isRegex($default)) {

            //     } elseif (\Yarest\Helpers\Regex::isVertical($default)) {

            //     } elseif (\Yarest\Helpers\Regex::isArithmetic($default)) {

            //     }

            //     # explode
            //     $options = explode("|", strtolower($parameter->getDefaultValue()));
            //     if (!in_array(strtolower($input), $options)) {
            //         return false;
            //     }

            //     # math

            //     if (array_key_exists($name, $regex)) {
                    
            //         preg_match($regex[$name], $input, $matches);
            //         if (empty($matches)) {
            //             return false;
            //         }
            //     }
            }
        }
        return true;
    }

    /**
     * [parseComment description]
     * @param  ReflectionMethod $method [description]
     * @return [type]                   [description]
     */
    public static function parseComment(\ReflectionMethod $method)
    {

        $comment = $method->getDocComment();

        $object = array();

        // Descriptions
        $object['short'] = array();
        $object['long']  = array();
        $object['var']   = array();

        $pattern = '/(^\/\*\*)|(^\s*\**[ \/]?)|\s(?=@)|\s\*\//m';

        $comment = preg_replace($pattern, '', $comment);
        $comments = preg_split("/(\r?\n)/", $comment);
        $comments = array_map('trim', $comments);

        $mode = 0; //pre 0 | short 1 | long 2

        foreach ($comments as $comment) {
            
            if (strlen($comment) == 0) {
                if ($mode == 1) {
                    $mode++;
                }
                continue;
            }

            if ($comment[0] == "@") {
                $mode = 3;
                # http://stackoverflow.com/questions/6576313/how-to-avoid-undefined-offset
                list($param, $values) = array_pad(preg_split('/(\s+)/', $comment, 2), 2, null);

                $param = substr($param, 1);
                
                if (empty($param)) {
                    continue;
                }
                
                if ($param == 'var') {

                    $v = array();

                    $v['regex'] = null;

                    $values = preg_replace_callback('/(\/[a-z0-9|:]+\/)/i', function ($a) use (&$v) {
                        $v['regex'] = isset($a[0]) ? $a[0] : null;
                    }, $values);

                    $v['default'] = null;

                    $values = preg_replace_callback('/\[([a-z0-9]+)\]/i', function ($a) use (&$v) {
                        $v['default'] = isset($a[1]) ? $a[1] : null;
                    }, $values);
                    
                    # http://stackoverflow.com/questions/6576313/how-to-avoid-undefined-offset
                    list($v['name'],$v['desc']) = array_pad(preg_split('/\s+/', $values, 2), 2, null);

                    $values = $v;

                } elseif ($param == 'return') {
                    $values = preg_split('/\s+/', $values, 3);
                    $map = array('name','type','desc');
                    $values = \Yarest\Helpers\Collection::mapAssoc($map, $values);
                }

                $object[$param][] = $values;

            } elseif ($mode < 2) {
                $mode = 1;
                $object['short'][] = $comment;
            } elseif ($mode == 2) {
                $object['long'][] = $comment;
            }
        }

        $object['short'] = implode(" ", $object['short']);

        $object['long']  = implode("\r\n", $object['long']);

        $markdownParser = new \dflydev\markdown\MarkdownExtraParser();

        $object['long'] = $markdownParser->transformMarkdown($object['long']);

        return $object;
    }
}
