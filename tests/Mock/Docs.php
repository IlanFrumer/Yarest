<?php

namespace Mock;

class Docs extends \Yarest\Resource
{

    public function all()
    {
        return $this['docs'];
    }

    /**
     * @param $a NUMBER
     * @param $b EMAIL
     */
    public function get()
    {
        return $this['doc'];

    }
}
