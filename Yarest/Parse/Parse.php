<?php

namespace Yarest\Parse;

/**
 * Yarest Parse class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class Parse
{

    private $config;
    private $request;
    public $vars = array();

    public function __construct($config, $request)
    {
        $this->config  = $config;
        $this->request = $request;
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

    public function matchMethod($class, array $elements)
    {
        $alias = $this->config['route.alias'];
        $http_method = $this->request['method'];

        $methods = $this->getOwnPublicMethods($class);

        $number_of_parameters = count($elements);

        $invalid_syntax   = array();
        $invalid_elements = array();
        $allowed_http_methods = array();
        
        foreach ($methods as $method) {
            
            # filter by alias
            preg_match("/^[a-z]+/", $method->name, $matched);
            if (!empty($matched) && array_key_exists($matched[0], $alias)) {

                # filter by number of parameters
                if ($method->getnumberofparameters() == $number_of_parameters) {
                    
                    $verb = $alias[$matched[0]];

                    # check if matched with the request http method
                    if ($verb == $http_method) {
                        
                        $allowed_http_methods[$verb] = true;
                        
                        # validate method parameters preconditions
                        list($s_errors, $p_errors) = $this->validateParameters($method, $elements);

                        if (!empty($s_errors)) {
                            
                            $invalid_syntax[] = $s_errors;

                        } elseif (! empty($p_errors)) {
                            
                            $invalid_elements[] = $p_errors;

                        } else {

                            return $method;
                        }
                       
                    } else {
                        ## if the request http method is not matched
                        #  than allow method list should be passed in the header
                        $allowed_http_methods[$verb] = false;
                    }

                }
            }
        }

        if (!empty($invalid_syntax)) {
            throw new \Yarest\Exception\InvalidSyntax($invalid_syntax);
        }

        if (!empty($invalid_elements)) {
            throw new \Yarest\Exception\InvalidElements($invalid_elements);
        }

        if (!empty($allowed_http_methods)) {
            $allowed = array_keys($allowed_http_methods);
            throw new \Yarest\Exception\MethodNotAllowed($allowed);
        }

        return false;
    }

    /**
     * [validateParameters description]
     * @param  ReflectionMethod $method   [description]
     * @param  array            $elements [description]
     * @return boolean|array   [description]
     */
    private function validateParameters(\ReflectionMethod $method, array $elements)
    {
        $expressions = new Expressions($this->config);

        foreach ($method->getParameters() as $key => $parameter) {

            $input  = $elements[$key];

            if ($parameter->isOptional()) {
                $expression = $parameter->getDefaultValue();
                $expressions->add($parameter->name, $expression, $input);
            }
        }

        return $expressions->check();
    }

    public function getComment(\ReflectionMethod $method)
    {
        return new DocComment($method->getDocComment());
    }

    public function validateMethod(DocComment $comment)
    {
        list($s_errors, $i_errors) = $this->validateInput($comment);

        if (!empty($s_errors)) {
            throw new \Yarest\Exception\InvalidSyntax($s_errors);
        } elseif (! empty($i_errors)) {
            throw new \Yarest\Exception\InvalidInput($i_errors);
        }
    }

    private function validateVar($var, $expressions)
    {
        $body  = $this->request['body'];
        $name       = $var['name'];
        $default    = $var['default'];
        $expression = $var['expression'];

        $value = null;

        if (array_key_exists($name, $body)) {

            $value = $body[$name];

            if (!is_null($expression)) {
                $expressions->add($name, $expression, $var['value']);
            }

        } elseif (!is_null($default)) {

            $value = $default;

        } elseif (!is_null($expression)) {
            
            $expressions->add($name, $expression);
            
        } else {

            $expressions->add($name);
        }

        return array($name, $value);
    }

    private function validateInput(DocComment $comment)
    {
        $errors = array();
        $invalid_input = array();

        $expressions = new Expressions($this->config);
        
        foreach ($comment->groups as $group => $true) {
            if (isset($comment[$group]['var'])) {

                foreach ($comment[$group]['var'] as $var) {
                    
                    list($name, $value) = $this->validateVar($var, $expressions);
                    $this->vars[$group][$name] = $value;

                }
            }
        }

        foreach ($comment['var'] as $var) {
            list($name, $value) = $this->validateVar($var, $expressions);
            $this->vars[$name] = $value;
        }

        return $expressions->check();
    }
}
