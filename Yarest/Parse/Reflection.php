<?php

namespace Yarest\Parse;

/**
 * Yarest Reflection class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class Reflection
{

    private $class;
    private $config;
    private $request;

    private $methods = array();

    public $allowed_http_methods = array();
    public $matched_method = null;

    public function __construct($class, $config, $request)
    {
        $this->config  = $config;
        $this->request = $request;

        $this->methods = $this->getOwnPublicMethods($class);
    }

    private function getOwnPublicMethods($class)
    {

        $class_ref = new \ReflectionClass($class);

        $methods = $class_ref->getMethods(\ReflectionMethod::IS_PUBLIC);

        $methods = array_filter($methods, function ($method) use ($class_ref) {
            return $method->class == $class_ref->name;
        });

        return $methods;
    }

    public function filterMethods(array $elements)
    {
        $alias = $this->config['alias'];
        $http_method = $this->request['http_method'];

        $number_of_parameters = count($elements);
        
        foreach ($this->methods as $method) {
            
            # filter by alias
            preg_match("/^[a-z]+/", $method->name, $matched);
            if (!empty($matched) && array_key_exists($matched[0], $alias)) {

                # filter by number of parameters
                if ($method->getnumberofparameters() == $number_of_parameters) {
                    
                    $verb = $alias[$matched[0]];

                    # check if matched with the request http method
                    if ($verb == $http_method) {
                        
                        $this->allowed_http_methods[$verb] = true;
                        
                        # validate method parameters preconditions
                        list($errors, $invalid) = $this->validateParameters($method, $elements);

                        exit();
                        //$DocComment = new DocComment($method->getDocComment());
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
        $valid = true;
        $errors = array();

        $expressions = new Expressions($this->config);

        foreach ($method->getParameters() as $key => $parameter) {

            $input  = $elements[$key];

            if ($parameter->isOptional()) {
                $expression = $parameter->getDefaultValue();
                $expressions->add($expression, $input);
            }
        }

        return $expressions->check();
    }

    public function validateCommentBlock(array &$vars)
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
}
