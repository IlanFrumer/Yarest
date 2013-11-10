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

    public $comment;
    public $variables;
    
    public $fields;
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
}
