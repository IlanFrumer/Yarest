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
class Config extends ReadOnlyArray
{
    /**
     * [setDefaults description]
     */
    private function setDefaults()
    {
        $this->values['root_class'] = 'Root';

        $this->values['debug'] = true;

        $this->values['alias'] = array(

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
                $this->values[$key] = array_merge($this->values[$key], $value);
            } else {
                $this->values[$key] = $value;
            }
        }
    }
}
