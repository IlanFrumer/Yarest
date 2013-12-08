<?php

namespace Yarest;

/**
 * Yarest ReadOnlyArray abstract class
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
abstract class ReadOnlyArray implements \ArrayAccess
{
    /**
     * [$values description]
     * @var array
     */
    protected $values = array();

    public function offsetSet($offset, $value)
    {
        throw new Exception\ReadOnlyException();
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetUnset($offset)
    {
        throw new Exception\ReadOnlyException();
    }

    public function offsetGet($offset)
    {
        return isset($this->values[$offset]) ? $this->values[$offset] : null;
    }
}
