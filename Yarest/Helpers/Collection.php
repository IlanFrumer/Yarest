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

    public static function mapAssoc(array $map, array $array)
    {
        $mapped = array();
        foreach ($map as $key => $value) {
            $mapped[$value] = isset($array[$key]) ? $array[$key] : null;
        }
        return $mapped;
    }

    /**
     * [splitBySpaces description]
     * @param  string $subject [description]
     * @param  int    $count   [description]
     * @return array[description]
     */
    public static function splitBySpaces($subject, $count)
    {
        # http://stackoverflow.com/questions/6576313/how-to-avoid-undefined-offset
        return array_pad(preg_split('/(\s+)/', $subject, $count), $count, null);
    }

    public static function arrayColumn(array $input, $columnKey, $indexKey = null)
    {
        $result = array();
    
        if (null === $indexKey) {
            if (null === $columnKey) {
                // trigger_error('What are you doing? Use array_values() instead!', E_USER_NOTICE);
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
    
        return $result;
    }
}
