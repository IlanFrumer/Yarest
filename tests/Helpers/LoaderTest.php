<?php

namespace Yarest\Helpers;

class LoaderTest extends \PHPUnit_Framework_TestCase
{

    public function testLoadNamespace()
    {
        $this->assertFalse(class_exists('Mock\\Main'));
        
        $loader = Loader::loadNamespace(TEST_ROOT, "Mock", "");

        $prefixes = $loader->getPrefixes();

        $this->assertArrayHasKey('Mock', $prefixes);
        $this->assertEquals(TEST_ROOT, $prefixes['Mock'][0]);

        $this->assertTrue(class_exists('Mock\\Main'));
        
        $loader->unregister();
        
        $this->assertFalse(class_exists('Mock\\Members'));
    }


    public function testCheckValidClass()
    {

        $loader = Loader::loadNamespace(TEST_ROOT, "Mock", "");

        $abstract = "\\Yarest\\Resource";
        
        $class = "Mock\\Members";
        $this->assertTrue(Loader::checkValidClass($class, $abstract));
        
        $class = "Mock\\Members\\Followers";
        $this->assertTrue(Loader::checkValidClass($class, $abstract));

        $class = "Mock\\Invalid";
        $this->assertFalse(Loader::checkValidClass($class, $abstract));

        $class = "Mock\\Notexist";
        $this->assertFalse(Loader::checkValidClass($class, $abstract));

        $loader->unregister();
    }
}
