<?php

namespace Yarest\Helpers;

/**
 * Yarest Comments helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Parse
{

    /**
     * [variables description]
     * @param  array    $variables [description]
     * @param  stdclass $data      regex, body
     * @return [type]              [description]
     */
    public static function variables(array $variables, \stdclass &$data)
    {
        $data->invalid_input = array();
        $data->invalid_regex = array();
        return array_map(function ($var) use ($data) {
            $regex = preg_replace_callback("/^\/:([a-z]+)\/$/i", function ($a) use ($data) {
                if (array_key_exists($a[1], $data->regex)) {
                    return $data->regex[$a[1]];
                } else {
                    $data->invalid_regex[] = $a[0];
                    return null;
                }
            }, $var['regex']);

            $name = $var['name'];
            $default = $var['default'];

            if (array_key_exists($name, $data->body)) {
                if (!empty($regex)) {
                    $input = $data->body[$name];
                    preg_match($regex."i", $input, $matches);
                    if (empty($matches)) {
                        $invalid = array();
                        $invalid['field'] = $name;
                        $invalid['regex'] = $regex;
                        $invalid['input'] = $input;
                        $invalid['message'] = "wrong input ($input)";
                        $data->invalid_input[] = $invalid;
                    }
                }
            } elseif (!is_null($default)) {
                $data->body[$name] = $default;
            } else {
                $invalid = array();
                $invalid['field'] = $name;
                $invalid['regex'] = $regex;
                $invalid['message'] = "$name is not optional";
                $data->invalid_input[] = $invalid;
            }

            $var['regex'] = $regex;

            return $var;
        }, $variables);
    }
}
