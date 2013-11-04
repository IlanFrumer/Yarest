<?php

namespace Yarest\Helpers;

class ParseTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticVariables()
    {
        # first
        $data = new \stdclass();
        $data->body  = array('start'=>1);
        $data->regex = array('number'=>'/^\d+$/');
        
        $var = array();
        $var['name'] = 'start';
        $var['regex'] = '/:number/';
        $var['default'] = null;

        $vars = array($var);
        $v = Parse::variables($vars, $data);

        $this->assertEquals('/^\d+$/', $v[0]['regex']);
        $this->assertEmpty($data->invalid_input);
        $this->assertEmpty($data->invalid_regex);

        # invalid input

        $data = new \stdclass();
        $data->body  = array('start'=>'asd');
        $data->regex = array('number'=>'/^\d+$/');
        
        $var = array();
        $var['name'] = 'start';
        $var['regex'] = '/:number/';
        $var['default'] = null;

        $vars = array($var);
        $v = Parse::variables($vars, $data);

        $this->assertEquals('/^\d+$/', $v[0]['regex']);
        $this->assertCount(1, $data->invalid_input);
        $this->assertEmpty($data->invalid_regex);

        # invalid regex

        $data = new \stdclass();
        $data->body  = array('start'=>12);
        $data->regex = array('number'=>'/^\d+$/');
        
        $var = array();
        $var['name'] = 'start';
        $var['regex'] = '/:none/';
        $var['default'] = null;

        $vars = array($var);
        $v = Parse::variables($vars, $data);

        $this->assertEquals(null, $v[0]['regex']);
        $this->assertCount(1, $data->invalid_regex);
        $this->assertEmpty($data->invalid_input);

        # defaults

        $data = new \stdclass();
        $data->body  = array();
        $data->regex = array('number'=>'/^\d+$/');
        
        $var = array();
        $var['name'] = 'start';
        $var['regex'] = '/:number/';
        $var['default'] = 10;

        $vars = array($var);
        $v = Parse::variables($vars, $data);

        $this->assertEquals('/^\d+$/', $v[0]['regex']);
        $this->assertEmpty($data->invalid_regex);
        $this->assertEmpty($data->invalid_input);
        $this->assertEquals(array('start'=>10), $data->body);
        
        # no defaults no body

        $data = new \stdclass();
        $data->body  = array();
        $data->regex = array('number'=>'/^\d+$/');
        
        $var = array();
        $var['name'] = 'start';
        $var['regex'] = '/:number/';
        $var['default'] = null;

        $vars = array($var);
        $v = Parse::variables($vars, $data);

        $this->assertEquals('/^\d+$/', $v[0]['regex']);
        $this->assertEmpty($data->invalid_regex);
        $this->assertCount(1, $data->invalid_input);
    }
}
