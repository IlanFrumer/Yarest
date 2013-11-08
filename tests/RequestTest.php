<?php

namespace Yarest;

class RequestTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_HOST']    = 'localhost';
        $_SERVER['PHP_SELF']       = '/index.php';
        $_SERVER['DOCUMENT_ROOT']  = TEST_ROOT;
    }

    public function testRequest()
    {
        $_SERVER['HTTP_HOST']    = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = new Request;
        $this->assertEquals($request['host'], 'localhost');
        $this->assertEquals($request['method'], 'GET');
        $this->assertEquals($request['path'], __DIR__ . "/");

        $request = new Request;
        $this->assertEquals($request['host'], 'localhost');
        $this->assertEquals($request['method'], 'GET');
        $this->assertEquals($request['path'], __DIR__ . "/");
    }

    public function testRequestURI()
    {
        ## 1
        $_SERVER['REQUEST_URI']    = '/';

        $request = new Request;
        
        $this->assertEquals($request['uri'], array());

        ## 2
        $_SERVER['REQUEST_URI']    = '/members/123';

        $request = new Request;

        $this->assertEquals($request['uri'], array('members','123'));

        ## 3
        $_SERVER['REQUEST_URI']    = '/members';

        $request = new Request;

        $this->assertEquals($request['uri'], array('members'));

    }

    public function testRequestVirtualHosts()
    {
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['PHP_SELF']       = '/index.php';
        $_SERVER['DOCUMENT_ROOT']  = __DIR__;

        $request = new Request;

        $this->assertEquals($request['virtual'], array());

    }

    public function testParseInput()
    {
        $request = new Request;
        $this->assertEmpty($request['body']);

        $_POST = array("a"=>"b");
        $request = new Request;
        $this->assertEquals($_POST, $request['body']);

        $_GET = array("c"=>"d");
        $request = new Request;
        $this->assertEquals($_GET, $request['body']);
    }
}
