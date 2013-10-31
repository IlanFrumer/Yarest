<?php

namespace Yarest;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigDefaults()
    {
        $config = new Config();
        $this->assertEquals('Root', $config['root_class']);
    }

    public function testConfigOverride()
    {
        $user_config = array();

        $user_config['root_class'] = 'Main';

        $config = new Config($user_config);

        $this->assertEquals('Main', $config['root_class']);
    }

    public function testConfigNotAssoc()
    {
        $user_config = array(0 => "value");

        $config = new Config($user_config);

        $this->assertEquals(null, $config[0]);
    }

    public function testConfigArrayMerge()
    {
        $user_config = array();

        $user_config['alias'] = array('recover' => 'RECOVER');

        $config = new Config($user_config);

        $this->assertArrayHasKey('recover', $config['alias']);
        $this->assertArrayHasKey('get', $config['alias']);

        $this->assertContains('RECOVER', $config['alias']);
        $this->assertContains('GET', $config['alias']);
    }

    public function testConfigGet()
    {
        $config = new Config();
        $root_class = $config['root_class'];
    }

    public function testConfigIsset()
    {
        $config = new Config();
        $this->assertFalse(isset($config['Foo']));
        $this->assertTrue(isset($config['root_class']));
    }

    /**
     * @expectedException \Yarest\Exception\ReadOnlyException
     */
    public function testConfigSet()
    {
        $config = new Config();
        
        $config['root_class'] = 'other';
    }

    /**
     * @expectedException \Yarest\Exception\ReadOnlyException
     */
    public function testConfigUnset()
    {
        $config = new Config();
        
        unset($config['root_class']);
    }
    /**
     * @expectedException \Yarest\Exception\InvalidArgumentException
     */
    public function testConfigNotArray()
    {
        $config = new Config("123");
    }
}
