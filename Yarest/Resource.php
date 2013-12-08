<?php

namespace Yarest;

/**
 * Yarest Resource class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

/**
 * Annotations:
 *
 *
 * Alias:
 *
 *
 * Arguments:
 */

abstract class Resource extends \Pimple
{
    public $config;
    public $request;
    public $response;

    public $elements = array();
    public $data;
    public $vars;

    public $prefix;
    public $current;

    final public function __construct ()
    {
        
    }

    final public function halt ($status, $message = null)
    {
        throw new Exception\Halt($status, $message);
    }

    final public function render($file)
    {

        $dir = pathinfo($file, PATHINFO_DIRNAME);

        $long_path = $this->request['path'] . $this->config['template.folder'] . "/" . $dir;

        if (!$path = realpath($long_path)) {
            throw new Exception\FolderNotFound(array("path" => $long_path));
        }

        $filename = pathinfo($file, PATHINFO_BASENAME);

        $extensions = array();

        while ($extension = pathinfo($filename, PATHINFO_EXTENSION)) {
            $extensions[] = $extension;
            $filename = pathinfo($filename, PATHINFO_FILENAME);
        }

        if (empty($extensions)) {
            $filename = $filename . "." . $this->config['template.extension'];
        } else {
            $filename = $filename . "." . $extensions[0];
        }

        $filepath = $path . "/" . $filename;

        if (file_exists($filepath)) {
            return fopen($filepath, "r");
        } else {
            throw new Exception\FileNotFound(array("path" => $path, "file" => $file));
        }

    }

    final public function offset(array $array, $offset = 0)
    {
        return array_map(function ($item) use ($offset) {
            $arr = preg_split("/\s+/", $item);
            return isset($arr[$offset]) ? $arr[$offset] : null;
        }, $array);
    }

    final public function toComma(array $array)
    {
        return empty($array) ? "*" : implode(',', $array);
    }

    final public function toSet(array $array)
    {
        $set = array();
        $params = array();

        foreach ($array as $key => $value) {
             $set[] = "$key = ?";
             $params[] = $value;
        }

        $set = implode(",", $set);

        return array($set, $params);
    }

    final public function found($object, $message = null)
    {
        if ($object) {
            return $object;
        } else {
            $this->response->setStatus(404, $message);
        }
    }

    final public function qmarks($times, $plus = 0)
    {
        if (is_array($times)) {
            $times = count($times);
        }

        $times+= $plus;
        
        $qmarks = [];
        
        for ($i=0; $i < $times; $i++) {
            $qmarks[] = '?';
        }

        return implode(',', $qmarks);
    }

    final public function arrayKV(array $array)
    {
        return array(array_keys($array), array_values($array));
    }
}
