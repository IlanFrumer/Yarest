<?php

namespace Yarest;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testRouteConstruct()
    {
      $route = new Route('/api/**','\Api','/src');


      $this->assertEquals(array('api'), $route->pattern);
      $this->assertEquals('Api', $route->namespace);
      $this->assertEquals('src', $route->folder);

      $route = new Route('/api/docs/**/extra','\Api\v1','src/app/');

      $this->assertEquals(array('api','docs'), $route->pattern);
      $this->assertEquals('Api\V1', $route->namespace);
      $this->assertEquals('src/app', $route->folder);

    }

    public function testRouteMatchPattern()
    {
      $route = new Route('/images/**','images','');
      
      $this->assertFalse($route->matchPattern(array('api','docs')));
      
      $this->assertEquals(array('1.jpg'), $route->matchPattern(array('images','1.jpg')));
      $this->assertEquals(array(), $route->matchPattern(array('images')));
    }

    public function testRouteResolveClass()
    {
      $base_class = "Root";
      $route = new Route('/**','api','');
      
      # 1
      $endpoint = array('members','123','products');
      $route->resolveClass($endpoint, $base_class);

      $this->assertEquals('Api\Members\Products',$route->class);
      $this->assertEquals(array('123'),$route->elements);

      # 2
      $endpoint = array();
      $route->resolveClass($endpoint, $base_class);

      $this->assertEquals('Api\Root',$route->class);
      $this->assertEquals(array(),$route->elements);

      # 3
      $endpoint = array('mEmBeRs','123','PRODUCTS','324','lIKE');
      $route->resolveClass($endpoint, $base_class);

      $this->assertEquals('Api\Members\Products\Like',$route->class);
      $this->assertEquals(array('123','324'),$route->elements);         
    }

    public function testRouteFindMethods()
    {
      $route  = new Route('/**','Mock','');
      $config = new Config();
      $loader = Helpers\Loader::loadNamespace(TEST_ROOT , "Mock", "");

      $base_class = "Main";
      $alias = $config['alias'];

      # 1
      $endpoint = Helpers\Uri::uriToArray('/');
      $route->resolveClass($endpoint, $base_class);

      $this->assertTrue($route->findMethods($alias, 'GET'));
      $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

      $this->assertTrue($route->findMethods($alias, 'POST'));
      $this->assertContains('GET',$route->allowedMethods);
      $this->assertNull($route->matchedMethod);

      # 2
      $endpoint = Helpers\Uri::uriToArray('/Members/123/Followers');
      $route->resolveClass($endpoint, $base_class);

      $this->assertTrue($route->findMethods($alias, 'GET'));
      $this->assertInstanceOf("ReflectionMethod", $route->matchedMethod);

      $this->assertTrue($route->findMethods($alias, 'POST'));
      $this->assertContains('GET',$route->allowedMethods);
      $this->assertNull($route->matchedMethod);

      $loader->unregister();
    }
}
