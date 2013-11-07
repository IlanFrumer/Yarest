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
}
