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
class Config implements \arrayaccess
{
    
    private $container = array();

    /**
     * [setDefaults description]
     */
    private function setDefaults()
    {
        $this['application.debug'] = true;

        $this['template.folder'] = "view";

        $this['template.extension'] = "html";

        $this['route.base'] = 'Root';

        $this['route.alias'] = array(

            "all"     => "GET",
            "find"    => "GET",
            "get"     => "GET",
            "read"    => "GET",
            "post"    => "POST",
            "create"  => "POST",
            "patch"   => "PATCH",
            "options" => "OPTIONS",
            "put"     => "PUT",
            "update"  => "PUT",
            "delete"  => "DELETE",
            "remove"  => "DELETE",
            "trash"   => "DELETE"
        );

        $regex = array();

        $regex['id']['pattern'] = '/^\d+$/';
        $regex['id']['error']   = 'id error';
        
        $regex['email']['pattern'] = '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/';
        $regex['email']['error']   = 'email error';

        $regex['israel.phone']['pattern'] = '/^0\d([\d]{0,1})([-]{0,1})\d{7}$/';
        $regex['israel.phone']['error']   = 'israel.phone error';
        
        $regex['name']['pattern'] = '/^[^\d\W]\w*/';
        $regex['name']['error']   = 'name error';

        $regex['number']['pattern'] = '/^\d+$/';
        $regex['number']['error']   = 'number error';

        $regex['password']['pattern'] = '/^.{4,20}$/';
        $regex['password']['error']   = 'password error';

        $this['application.regex'] = $regex;
    }

    /**
     * @param array $user_config User configuration to override defaults
     */
    public function __construct(array $user_config = array())
    {
        $this->setDefaults();

        foreach ($user_config as $key => $value) {
            if (is_array($value)) {
                $this[$key] = array_merge($this[$key], $value);
            } else {
                $this[$key] = $value;
            }
        }
    }

    private function validate($offset, $value)
    {

        switch ($offset) {
            case 'application.debug':
                if (is_bool($value)) {
                    return $value;
                }
                break;

            case 'template.folder':
                if (is_string($value)) {
                    $array = Helpers\Uri::uriToArray($value);
                    return Helpers\Uri::arrayToUri($array);
                }
                break;

            case 'template.extension':
                if (is_string($value) && preg_match("/^[a-z]+$/i", $value)) {
                    return strtolower($value);
                }
                break;
            case 'route.base':
                if (is_string($value) && preg_match("/^[a-z]+$/i", $value)) {
                    return ucfirst($value);
                }
                break;

            case 'route.alias':
                if (is_array($value)) {
                    $new = array();
                    foreach ($value as $k => $v) {
                        
                        if (preg_match("/^[a-z][a-z_]+$/i", $k) && preg_match("/^[a-z]+$/i", $v)) {
                            $new[lcfirst($k)]  = strtoupper($v);
                        } else {
                            break 2;
                        }

                    }
                    return $new;
                }
                break;

            case 'application.regex':
                if (is_array($value)) {
                    foreach ($value as $regex) {
                        if (! is_array($regex) || ! array_key_exists("pattern", $regex) || false === @preg_match($regex['pattern'], "")) {
                            break 2;
                        }

                    }
                    return $value;
                }
                break;

            default:
                return $value;
        }

        throw new \Exception("$offset wrong type or value", 1);
    }

    public function offsetSet($offset, $value)
    {

        $value = $this->validate($offset, $value);
        
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    public function __toString()
    {
        return json_encode($this->container);
    }
}
