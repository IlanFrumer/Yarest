<?php

namespace Yarest;

class ConfigTest extends \PHPUnit_Framework_TestCase
{

    public function wrongalias()
    {
        return array(array(123), array(0 => "get"),array("give" => "get"), array("Give" => "GET"));
    }

    /**
     * @expectedException \Yarest\Exception\WrongConfigException
     * @dataProvider wrongalias
     */
    public function testStaticValidateAliasWrong($alias)
    {
        Config::validateAlias($alias);
    }

    public function testStaticValidateAlias()
    {
        $alias = array();
        $this->assertTrue(Config::validateAlias($alias));

        $alias = array("give" => "GET" , "let" => "POST" , "do" => "DO");
        $this->assertTrue(Config::validateAlias($alias));
    }

    public function testConfigDefaults()
    {
        $config = new Config();
        $this->assertEquals('Root', $config['base']);
    }

    public function testConfigOverride()
    {
        $user_config = array();

        $user_config['base_class'] = 'Main';

        $config = new Config($user_config);

        $this->assertEquals('Main', $config['base_class']);
    }

    public function testConfigGet()
    {
        $config = new Config();
        $base_class = $config['base'];
    }

    public function testConfigIsset()
    {
        $config = new Config();
        $this->assertFalse(isset($config['Foo']));
        $this->assertTrue(isset($config['base']));
    }

    /**
     * @expectedException \Yarest\Exception\ReadOnlyException
     */
    public function testConfigSet()
    {
        $config = new Config();
        
        $config['base_class'] = 'other';
    }

    /**
     * @expectedException \Yarest\Exception\ReadOnlyException
     */
    public function testConfigUnset()
    {
        $config = new Config();
        
        unset($config['base_class']);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConfigNotArray()
    {
        $config = new Config("123");
    }
}
