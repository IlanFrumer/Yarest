<?php

namespace Yarest\Helpers;

/**
 * Yarest Regex helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Regex
{

    /**
     * [isRegex description]
     * @param  string $pattern [description]
     * @return boolean          [description]
     */
    public static function isRegex($subject)
    {
        return preg_match("^\/.+\/$", $subject);
    }

    /**
     * [isVertical description]
     * @param  string $pattern [description]
     * @return boolean          [description]
     */
    public static function isVertical($subject)
    {
        return preg_match('/^\w+(|\w+)*$/', $subject);
    }

    /**
     * [isArithmetic description]
     * @param  string $pattern [description]
     * @return boolean|array   [description]
     */
    public static function isArithmetic($subject, $element)
    {
        if (!is_numeric($element)) {
            return array('element is not a number');
        }

        preg_match('/^>(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $matches[1] > $element;
        }

        preg_match('/^<(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $matches[1] < $element;
        }

        preg_match('/^>=(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $matches[1] >= $element;
        }
        
        preg_match('/^<=(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $matches[1] <= $element;
        }

        preg_match('/^(\d+)\.\.(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $matches[1] < $element && $matches[2] >= $element;
        }

        preg_match('/^(\d+)\.\.\.(\d+)$/', $subject, $matches);
        if (!empty($matches)) {
            return $matches[1] < $element && $matches[2] > $element;
        }

        return array('not matched');
    }
}
