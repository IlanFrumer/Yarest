<?php

namespace Yarest\Helpers;

/**
 * Yarest Collection helpers class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Collection
{
    /**
     * [mergeZip description]
     * @param  array  $arr1
     * @param  array  $arr2
     * @return [type]
     */
    public static function mergeZip(array $arr1, array $arr2)
    {

        if (empty($arr1)) {
            return $arr2;
        } elseif (empty($arr2)) {
            return $arr1;
        }

        $array = array();

        while ($val = array_shift($arr1)) {
            $array[] = $val;

            if ($val = array_shift($arr2)) {
                $array[] = $val;
            }
        }

        return array_merge($array, $arr2);
    }
}
