<?php

namespace Mock;

class TestFilter extends \Yarest\Resource
{

    public function getEmailRegex($email = "/:email/")
    {
        
    }

    public function getPhoneRegex($phone = "/:israel_phone/")
    {
        
    }

    public function getInlineRegex($test = "/^test/")
    {
        
    }

    public function postTest($id, $name)
    {
        
    }

    public function getTest($id, $name, $email = "/:email/")
    {
        
    }

    public function getWrong($name = "/david|paul|simon")
    {
        
    }

    public function getArithmetics($num1 = "<=10", $num2 = ">10", $num3 = "1..20", $num4 = "%5")
    {
        
    }
}
