<?php

namespace Yarest\Helpers;

class PatternTest extends \PHPUnit_Framework_TestCase
{

    public function testMatch()
    {
        $endpoint = array('api','docs');
        $pattern  = array('api');

        $this->assertEquals(array('docs'), Pattern::match($endpoint, $pattern));

        $endpoint = array('api');
        $pattern  = array('api');

        $this->assertEquals(array(), Pattern::match($endpoint, $pattern));

        $endpoint = array('images');
        $pattern  = array('api');

        $this->assertFalse(Pattern::match($endpoint, $pattern));
    }
}
