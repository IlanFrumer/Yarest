<?php

namespace Yarest;

/**
 * Yarest configuration class.
 * 
 * The only way to override defaults is to pass an array to \Yarest\App during instantiation
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Config implements \ArrayAccess
{
    /**
     * [$config description]
     * @var array
     */
    private $config = array();

    /**
     * [setDefaults description]
     */
    private function setDefaults()
    {
        $this->config['root_class'] = 'Root';

        $this->config['debug'] = true;

        $this->config['alias'] = array(

            "all"    => "GET",
            "find"   => "GET",
            "get"    => "GET",
            "read"   => "GET",
            "post"   => "POST",
            "create" => "POST",
            "put"    => "PUT",
            "update" => "PUT",
            "delete" => "DELETE",
            "remove" => "DELETE",
            "trash"  => "DELETE"
        );
    }

    /**
     * @param @param  array $user_config User configuration to override defaults
     */
    public function __construct($user_config = array())
    {
        $this->setDefaults();

        if (!is_array($user_config)) {
            throw new Exception\InvalidArgumentException('Config must be an array.');
        }

        foreach ($user_config as $key => $value) {
            if (is_integer($key)) {
                continue;
            }
            if (is_array($value)) {
                $this->config[$key] = array_merge($this->config[$key], $value);
            } else {
                $this->config[$key] = $value;
            }
        }
    }

    public function offsetSet($offset, $value)
    {
        throw new Exception\ReadOnlyException();
    }

    public function offsetExists($offset)
    {
        return isset($this->config[$offset]);
    }

    public function offsetUnset($offset)
    {
        throw new Exception\ReadOnlyException();
    }

    public function offsetGet($offset)
    {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }
}
