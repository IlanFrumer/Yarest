<?php

namespace Mock;

class TestReflection extends \Yarest\Resource
{
    /**
     * short description
     *
     * long description
     */
    public function first()
    {
    }

    /**
     * short description
     * more short
     *
     * long description
     */
    public function second()
    {
    }

    /**
     * @var a
     * @var b [0]
     * @var c "/:number/"
     * @var d "/:number/" [0] descripion !
     */
    public function third()
    {
    }

    /**
     * @return  a b c d e
     */
    public function fourth()
    {
    }

    /**
     * @auth member
     * 
     * @boom a
     * @boom b
     * @boom c
     * 
     * @
     * @ ignored
     */
    public function fifth()
    {
    }

    private function hidden()
    {
    }
}
