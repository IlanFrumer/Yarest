<?php

namespace Mock;

class TestFilter extends \Yarest\Resource
{

    public function emailRegex($email = "/:email/")
    {
        
    }

    public function phoneRegex($phone = "/:israel_phone/")
    {
        
    }

    public function inlineRegex($test = "/^test/")
    {
        
    }

    public function only($name = "david")
    {
        
    }

    public function some($name = "david|paul|simon")
    {
        
    }

    public function greater($number = ">10")
    {
        
    }

    public function less($number = "<100")
    {
        
    }
}
