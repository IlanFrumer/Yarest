<?php

namespace Yarest;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testRouteConstruct()
    {
        $route = new Route('/api/**', '\Api', '/src');


        $this->assertEquals(array('api'), $route->pattern);
        $this->assertEquals('Api', $route->namespace);
        $this->assertEquals('src', $route->folder);

        $route = new Route('/api/docs/**/extra', '\Api\v1', 'src/app/');

        $this->assertEquals(array('api','docs'), $route->pattern);
        $this->assertEquals('Api\V1', $route->namespace);
        $this->assertEquals('src/app', $route->folder);

    }
}
