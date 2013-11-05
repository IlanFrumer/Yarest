<?php

namespace Yarest\Helpers;

class ReflectionTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticGetOwnPublicMethods()
    {
        $loader = Loader::loadNamespace(TEST_ROOT, "Mock", "");

        $methods = Reflection::getOwnPublicMethods('Mock\TestReflection');
        $names = array_map(function ($method) {
            return $method->name;
        }, $methods);

        $this->assertCount(5, $methods);

        $m = array('first', 'second', 'third', 'fourth', 'fifth');
        $this->assertEquals($m, $names);

        foreach ($methods as $method) {
            $this->assertEquals('Mock\TestReflection', $method->class);
            $this->assertTrue($method->isPublic());
        }

        $loader->unregister();
    }
}
