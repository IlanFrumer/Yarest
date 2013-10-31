<?php

namespace Yarest;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testRequest()
    {
        $_SERVER['SERVER_NAME']    = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = new Request;
        $this->assertEquals($request['server'], 'localhost');
        $this->assertEquals($request['method'], 'GET');
        $this->assertEquals($request['pathUri'], __DIR__ . "/");

        $request = new Request;
        $this->assertEquals($request['server'], 'localhost');
        $this->assertEquals($request['method'], 'GET');
        $this->assertEquals($request['pathUri'], __DIR__ . "/");
    }

    public function testRequestEndPoints()
    {
        ## 1
        $_SERVER['REQUEST_URI']    = '/';

        $request = new Request;
        
        $this->assertEquals($request['endPoint'], array());

        ## 2
        $_SERVER['REQUEST_URI']    = '/members/123';

        $request = new Request;

        $this->assertEquals($request['endPoint'], array('members','123'));

        ## 3
        $_SERVER['REQUEST_URI']    = '/members';

        $request = new Request;

        $this->assertEquals($request['endPoint'], array('members'));

    }

    public function testRequestVirtualHosts()
    {
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['PHP_SELF']       = '/index.php';
        $_SERVER['DOCUMENT_ROOT']  = __DIR__;

        $request = new Request;

        $this->assertEquals($request['virtualHost'], array());

    }

    /**
     * @expectedException \Yarest\Exception\ServerMissingException
     */
    public function testRequestServerMissing()
    {
        $_SERVER = array();
        $config = new Request;
    }
}
