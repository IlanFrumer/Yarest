<?php

namespace Yarest\Parse;

/**
 * Yarest Expressions class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class Expressions
{

    private $config;

    private $checks = array();

    private $invalid = array();
    private $errors  = array();

    public function __construct(\Yarest\Config $config)
    {
        $this->config     = $config;
    }

    public function add($expression, $element)
    {
        $expression = strtolower($expression);
        $element    = strtolower($element);
        $this->checks[] = array("expression" => $expression, "element" => $element);
    }

    public function check()
    {
        foreach ($this->checks as $check) {
            if (! $this->_check($check)) {
                $this->invalid = $check;
            }
        }
        return array($this->errors, $this->$invalid);
    }

    private function _check(&$check)
    {
        $expression = $check['expression'];
        $element    = $check['element'];
        #regex
        if (preg_match("/^\/.+\/$/", $expression, $matches)) {

            $regex = $matches[0];

            # try regex :name
            if (preg_match("/^\/:([a-z_]+)\/$/i", $expression, $matches)) {

                $regexvar = $matches[1];

                if (array_key_exists($regexvar, $this->config['regex'])) {
                    $check['regex'] = $regex = $this->config['regex'][$regexvar];
                } else {
                    $this->errors[] = "invalid regex variable $regex";
                    return true;
                }
                
            }
            return preg_match($regex, $element);
        }

        # vertical bar

        if (preg_match('/^(\w+)(\|\w+)*$/', $expression)) {
         
            preg_match_all("/\w+/", $expression, $matches);
            return in_array($element, $matches[0]);
        }


        # arithmetics
        
        $unsigned_number = is_numeric($element) && (int) $element >= 0;

        preg_match('/^>(\d+)$/', $expression, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element > $matches[1];
        }

        preg_match('/^<(\d+)$/', $expression, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element < $matches[1];
        }

        preg_match('/^>=(\d+)$/', $expression, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element >= $matches[1];
        }
        
        preg_match('/^<=(\d+)$/', $expression, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element <= $matches[1];
        }

        preg_match('/^(\d+)\.\.(\d+)$/', $expression, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $matches[1] <= $element && $matches[2] >= $element;
        }

        preg_match('/^(\d+)\.\.\.(\d+)$/', $expression, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $matches[1] <= $element && $matches[2] > $element;
        }

        preg_match('/^%(\d+)$/', $expression, $matches);
        if (!empty($matches)) {
            return $unsigned_number && $element % $matches[1] == 0;
        }

        $this->errors[] = "invalid expression: $expression";

        return true;
    }
}