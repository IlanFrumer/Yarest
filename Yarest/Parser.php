<?php

namespace Yarest;

/**
 * Yarest Parser class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class Parser
{
    private $config;
    private $request;

    public function __construct($config, $request)
    {
        $this->config  = $config;
        $this->request = $request;
    }

    public function filterMethods(array $methods, array $elements)
    {

        $http_method = $this->request['method'];
        $alias       = $this->config['alias'];
        $regex       = $this->config['regex'];

        $number_of_parameters = count($elements);
        
        $catched_errors  = array();
        $allowed_methods = array();
        $matched_methods = array();

        foreach ($methods as $method) {
            
            # filter by alias
            preg_match("/^[a-z]+/", $method->name, $matched);
            if (!empty($matched) && array_key_exists($matched[0], $alias)) {

                # filter by number of parameters
                if ($method->getnumberofparameters() == $number_of_parameters) {
                    
                    $verb = $alias[$matched[0]];

                    # check if matched with the request http method
                    if ($verb == $http_method) {
                        
                        $allowed_methods[$verb] = true;
                        

                        # validate method parameters preconditions
                        list($errors, $valid) = $this->validateParameters($method, $elements);

                        if (!empty($errors)) {

                            $error = array();
                            $error['class']  = $method->class;
                            $error['method'] = $method->name;
                            $error['parameters'] = $errors;
                            $catched_errors[] = $error;

                        } elseif ($valid == true) {
                            $matched_methods[] = $method;
                        }
                       

                    } else {

                        $allowed_methods[$verb] = false;
                    }

                }
            }
        }
       
        return array($catched_errors, $allowed_methods, $matched_methods);
        
    }

    /**
     * [validateParameters description]
     * @param  ReflectionMethod $method   [description]
     * @param  array            $elements [description]
     * @return boolean|array   [description]
     */
    public function validateParameters(\ReflectionMethod $method, array $elements)
    {
        $regex = $this->config['regex'];

        $valid = true;
        $errors = array();

        foreach ($method->getParameters() as $key => $parameter) {

            $input  = $elements[$key];

            if ($parameter->isOptional()) {

                $default = $parameter->getDefaultValue();
                $result = $this->expression($default, $input);

                if (is_string($result)) {
                    $errors[$parameter->name] = $result;
                    $valid = false;
                } elseif ($result == false) {
                    $valid = false;
                }
            }
        }

        return array($errors, $valid);
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
        $object['auth']  = array();

        $pattern = '/(^\/\*\*)|(^\s*\**[ \/]?)|\s(?=@)|\s\*\//m';

        $comment  = preg_replace($pattern, '', $comment);
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

                list($param, $values) = Helpers\Collection::splitBySpaces($comment, 2);

                $param = substr($param, 1);
                
                if (empty($param)) {
                    continue;
                }
                
                if ($param == 'var') {

                    $v = array();

                    $v['expression'] = null;

                    $values = preg_replace_callback('/"([^"]*)"/', function ($a) use (&$v) {
                        $v['expression'] = isset($a[1]) ? $a[1] : null;
                    }, $values);

                    $v['default'] = null;

                    $values = preg_replace_callback('/\[([a-z0-9]+)\]/i', function ($a) use (&$v) {
                        $v['default'] = isset($a[1]) ? $a[1] : null;
                    }, $values);
                    
                    list($v['name'],$v['desc']) = Helpers\Collection::splitBySpaces($values, 2);

                    $values = $v;

                } elseif ($param == 'return') {
                    $values = preg_split('/\s+/', $values, 3);
                    $map = array('name','type','desc');
                    $values = Helpers\Collection::mapAssoc($map, $values);
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

    public function checkCommentVars(array &$vars)
    {
        $body  = $this->request['body'];
        $errors = array();
        $invalid_input = array();

        foreach ($vars as &$var) {
                        
            $name       = $var['name'];
            $default    = $var['default'];
            $expression = $var['expression'];
        
            if (array_key_exists($name, $body)) {
                if (!empty($expression)) {

                    $input = $body[$name];
                    $validate = $this->expression($expression, $input);

                    if ($expression != $var['expression']) {
                        $var['regex'] = $expression;
                    }

                    if (is_string($validate)) {
                        $errors[$name] = $validate;
                    } elseif ($validate == false) {

                        $invalid = array();
                        $invalid = $var;
                        $invalid['input'] = $input;
                        $invalid['message'] = "$name: wrong input";

                        $invalid_input[] = $invalid;
                    }
                }
            } elseif (!is_null($default)) {
                $body[$name] = $default;
            } else {
                $invalid = array();
                $invalid['field'] = $var;
                $invalid['message'] = "$name: not optional";
                $invalid_input[] = $invalid;
            }
        }

        return array($errors, $invalid_input, $body);
    }

    /**
     * [expression description]
     * @param  string $subject [description]
     * @param  string $element [description]
     * @return boolean|string   if the expression is valid returns the evaluation against the element 
     *                          else returns a string with an error
     */
    public function expression(&$subject, $element)
    {
        $subject = strtolower($subject);
        $element = strtolower($element);

        #regex
        if (preg_match("/^\/.+\/$/", $subject, $matches)) {

            $regex = $matches[0];

            # try regex :name
            if (preg_match("/^\/:([a-z_]+)\/$/i", $subject, $matches)) {

                $regexvar = $matches[1];

                if (array_key_exists($regexvar, $this->config['regex'])) {
                    $subject = $regex = $this->config['regex'][$regexvar];
                } else {
                    return "invalid regex variable $regex";
                }
                
            }
            return (bool) preg_match($regex, $element);
        }

        # vertical bar

        if (preg_match('/^(\w+)(\|\w+)*$/', $subject)) {
         
            preg_match_all("/\w+/", $subject, $matches);
            return in_array($element, $matches[0]);
        }


        # arithmetics
        
        $unsigned_number = is_numeric($element) && (int) $element >= 0;

        preg_match('/^>(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element > $matches[1];
        }

        preg_match('/^<(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element < $matches[1];
        }

        preg_match('/^>=(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element >= $matches[1];
        }
        
        preg_match('/^<=(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element <= $matches[1];
        }

        preg_match('/^(\d+)\.\.(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $matches[1] <= $element && $matches[2] >= $element;
        }

        preg_match('/^(\d+)\.\.\.(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $matches[1] <= $element && $matches[2] > $element;
        }

        preg_match('/^%(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element % $matches[1] == 0;
        }

        return "invalid expression: $subject";
    }
}
