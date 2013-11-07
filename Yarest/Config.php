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
        $this->values['base'] = 'Root';

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

        $regex = array();

        $regex['id'] = '/^\d+$/';
        
        $regex['email'] = '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/';

        $regex['israel_phone'] = '/^0\d([\d]{0,1})([-]{0,1})\d{7}$/';
        
        $regex['name'] = '/^[^\d\W]\w*/';

        $regex['number'] = '/^\d+$/';

        $regex['password'] = '/^.{4,20}$/';

        $this->values['regex'] = $regex;
    }

    private static function validate($type, $value)
    {
        switch ($type) {
            case 'alias':
                return self::validateAlias($value);
            case 'base_class':
                return is_string($value);
            case 'debug':
                return is_bool($value);
            default:
                return false;
        }
    }

    /**
     * @param @param  array $user_config User configuration to override defaults
     */
    public function __construct($user_config = array())
    {
        $this->setDefaults();

        if (!is_array($user_config)) {
            throw new \InvalidArgumentException('Config must be an array.');
        }

        foreach ($user_config as $key => $value) {

            if (!self::validate($key, $value)) {
                throw new Exception\WrongConfigException(json_encode(array($key => $value)));
            } else {

                if (is_array($value)) {
                    $this->values[$key] = array_merge($this->values[$key], $value);
                } else {
                    $this->values[$key] = $value;
                }
            }
        }
    }
}
