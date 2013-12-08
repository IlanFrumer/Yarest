<?php

namespace Yarest;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testRouteMatchPattern()
    {
        // $route = new Route('/images/**','images','');

        // $this->assertFalse($route->matchPattern(array('api','docs')));

        // $this->assertEquals(array('1.jpg'), $route->matchPattern(array('images','1.jpg')));
        // $this->assertEquals(array(), $route->matchPattern(array('images')));
    }

    // public function testRouteResolveClass()
    // {
    //   $base_class = "Root";
    //   $route = new Route('/**','api','');
      
    //   # 1
    //   $endpoint = array('members','123','products');
    //   $route->resolveClass($endpoint, $base_class);

    //   $this->assertEquals('Api\Members\Products',$route->class);
    //   $this->assertEquals(array('123'),$route->elements);

    //   # 2
    //   $endpoint = array();
    //   $route->resolveClass($endpoint, $base_class);

    //   $this->assertEquals('Api\Root',$route->class);
    //   $this->assertEquals(array(),$route->elements);

    //   # 3
    //   $endpoint = array('mEmBeRs','123','PRODUCTS','324','lIKE');
    //   $route->resolveClass($endpoint, $base_class);

    //   $this->assertEquals('Api\Members\Products\Like',$route->class);
    //   $this->assertEquals(array('123','324'),$route->elements);
    // }

    // public function testRouteFindMethods()
    // {
    //   $route  = new Route('/**','Mock','');
    //   $config = new Config();
    //   $loader = Helpers\Loader::loadNamespace(TEST_ROOT , "Mock", "");

    //   $base_class = "Main";
    //   $alias = $config['alias'];

    //   # 1
    //   $endpoint = Helpers\Uri::uriToArray('/');
    //   $route->resolveClass($endpoint, $base_class);

    //   $this->assertTrue($route->findMethods($alias, 'GET'));
    //   $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

    //   $this->assertTrue($route->findMethods($alias, 'POST'));
    //   $this->assertContains('GET',$route->allowedMethods);
    //   $this->assertNull($route->matchedMethod);

    //   # 2
    //   $endpoint = Helpers\Uri::uriToArray('/Members/');
    //   $route->resolveClass($endpoint, $base_class);

    //   $this->assertTrue($route->findMethods($alias, 'GET'));
    //   $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

    //   $this->assertTrue($route->findMethods($alias, 'POST'));
    //   $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

    //   $this->assertTrue($route->findMethods($alias, 'PUT'));
    //   $this->assertNull($route->matchedMethod);
    //   $this->assertContains('GET',$route->allowedMethods);
    //   $this->assertContains('POST',$route->allowedMethods);

    //   # 3
    //   $endpoint = Helpers\Uri::uriToArray('/Members/123');
    //   $route->resolveClass($endpoint, $base_class);

    //   $this->assertTrue($route->findMethods($alias, 'GET'));
    //   $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

    //   $this->assertTrue($route->findMethods($alias, 'PUT'));
    //   $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

    //   $this->assertTrue($route->findMethods($alias, 'DELETE'));
    //   $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

    //   $this->assertTrue($route->findMethods($alias, 'POST'));
    //   $this->assertNull($route->matchedMethod);
    //   $this->assertContains('GET',$route->allowedMethods);
    //   $this->assertContains('PUT',$route->allowedMethods);
    //   $this->assertContains('DELETE',$route->allowedMethods);

    //   # 4
    //   $endpoint = Helpers\Uri::uriToArray('/Members/123/Followers');
    //   $route->resolveClass($endpoint, $base_class);

    //   $this->assertTrue($route->findMethods($alias, 'GET'));
    //   $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

    //   $this->assertTrue($route->findMethods($alias, 'POST'));
    //   $this->assertContains('GET',$route->allowedMethods);
    //   $this->assertNull($route->matchedMethod);

    //   $loader->unregister();
    // }
}
