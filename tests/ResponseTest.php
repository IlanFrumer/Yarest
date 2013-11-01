<?php

namespace Yarest;

class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testResponseSetStatus()
    {
        $response = new Response();
        
        $response->setStatus("400","Custom Error");
        $this->assertContains("HTTP/1.1: 400 Custom Error", $response->getHeaders());

        $response->setStatus("500");
        $this->assertContains("HTTP/1.1: 500", $response->getHeaders());

    }

    public function testResponseSetBody()
    {
        $response = new Response();
        
        $response->setBody("hello");
        $this->assertEquals("hello", $response->getBody());

        $array = array("a" => "b", "c" => "d");
        $response->setBody($array);        
        $this->assertEquals(json_encode($array), $response->getBody());
    }

    public function testResponseSetContentType()
    {
        $response = new Response();
        
        $response->setContentType("text/html");
        $this->assertContains("Content-type: text/html; charset=utf-8", $response->getHeaders());

        $response->setContentType("application/xml","windows-2555");
        $this->assertContains("Content-type: application/xml; charset=windows-2555", $response->getHeaders());
    }

    public function testResponseSetAllowed()
    {
        $response = new Response();
        
        $response->setAllowed();
        $this->assertContains("Allow: GET", $response->getHeaders());

        $response->setAllowed(array('GET','POST','PUT'));
        $this->assertContains("Allow: GET, POST, PUT", $response->getHeaders());
    }

}
