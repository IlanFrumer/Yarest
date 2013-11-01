<?php

namespace Yarest\Helpers;

/**
 * Yarest Pattern helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Pattern
{

    /**
     * [match description]
     * @param  array  $endpoint
     * @param  array  $pattern
     * @return array|false
     */
    public static function match(array $endpoint, array $pattern)
    {
        while ($p = array_shift($pattern)) {
            $r = array_shift($endpoint);
            if ($p != $r) {
                return false;
            }
        }

        return $endpoint;
    }
}
