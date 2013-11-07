<?php

namespace Yarest\Parse;

/**
 * Yarest Invoke class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

class Invoke
{

    private $method;
    private $docComment;
    private $elements;
    private $variables;
    private $resource;

    /**
     * [__construct description]
     * @param ReflectionMethod $method     [description]
     * @param DocComment       $docComment [description]
     * @param array            $elements   [description]
     * @param array            $variables  [description]
     */
    public function __construct(\ReflectionMethod $method, DocComment $docComment, array $elements, array $variables)
    {
        $this->method     = $method;
        $this->docComment = $docComment;
        $this->elements   = $elements;
        $this->variables  = $variables;
    }

    public function createResource()
    {
        $class = $this->method->getDeclaringClass()->name;
        $this->resource = new $class();

        $this->resource->variables = $this->variables;
        $this->resource->comment   = $this->docComment;

    
        $fields = \Yarest\Helpers\Collection::arrayColumn($this->docComment['return'], 'name');
    
        $this->resource->fields = empty($fields) ? "*" : implode(',', $fields);

        return $this->resource;
    }

    public function invoke()
    {
        return $this->method->invokeArgs($this->resource, $this->elements);
    }
}
