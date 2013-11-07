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

    private $class;
    private $config;
    private $request;

    private $methods = array();

    public $invoker = null;
    public $allowed_http_methods = array();

    public $errors = array();
    public $variables = array();

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
        $http_method = $this->request['method'];

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

                        if (!empty($errors)) {
                            $this->errors['arguments']['invalid_syntax'] = $errors;
                        } elseif (! empty($invalid)) {
                            $this->errors['arguments']['invalid_input'] = $invalid;
                        
                        } elseif (is_null($this->invoker)) {
                            
                            $docComment = new DocComment($method->getDocComment());

                            list($errors, $invalid) = $this->validateInput($docComment['var']);

                            if (!empty($errors)) {
                                $this->errors['variables']['invalid_syntax'] = $errors;
                            } elseif (! empty($invalid)) {
                                $this->errors['variables']['invalid_input']  = $invalid;
                            }

                            $this->invoker = new Invoke($method, $docComment, $elements, $this->variables);
                        }
                       
                    } else {

                        $this->allowed_http_methods[$verb] = false;
                    }

                }
            }
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

    private function validateInput(array $vars)
    {
        $body  = $this->request['body'];
        $errors = array();
        $invalid_input = array();

        $expressions = new Expressions($this->config);

        foreach ($vars as $var) {
                        
            $name       = $var['name'];
            $default    = $var['default'];
            $expression = $var['expression'];

            if (array_key_exists($name, $body)) {

                $this->variables[$name] = $body[$name];

                $expressions->add($name, $expression, $this->variables[$name]);

            } elseif (!is_null($default)) {
                $this->variables[$name] = $default;
            } else {

                $expressions->add($name, $expression);
            }
        }

        return $expressions->check();
    }
}
