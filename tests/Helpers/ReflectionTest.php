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

    public function testValidateParameters()
    {

        // $regex = array("email" => '/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/',
        //                "israel_phone" => '/^0\d([\d]{0,1})([-]{0,1})\d{7}$/');

        // $loader = Loader::loadNamespace(TEST_ROOT, "Mock", "");

        // $email = new \ReflectionMethod('\Mock\TestFilter', 'email');

        // $this->assertTrue(Reflection::validateParameters($email, array('ilan@google.com'), $regex));
        // $this->assertFalse(Reflection::validateParameters($email, array('ilan.com'), $regex));

        // $phone = new \ReflectionMethod('\Mock\TestFilter', 'phone');

        // $this->assertTrue(Reflection::validateParameters($phone, array('049929876'), $regex));
        // $this->assertTrue(Reflection::validateParameters($phone, array('04-9929876'), $regex));
        // $this->assertTrue(Reflection::validateParameters($phone, array('077-1234567'), $regex));
        // $this->assertFalse(Reflection::validateParameters($phone, array('03-123412'), $regex));
        // $this->assertFalse(Reflection::validateParameters($phone, array('0890-123412'), $regex));


        // $one = new \ReflectionMethod('\Mock\TestFilter', 'onlyOne');

        // $this->assertTrue(Reflection::validateParameters($one, array('david'), $regex));
        // $this->assertTrue(Reflection::validateParameters($one, array('David'), $regex));
        // $this->assertTrue(Reflection::validateParameters($one, array('DAVID'), $regex));
        // $this->assertFalse(Reflection::validateParameters($one, array('simon'), $regex));
        
        // $some = new \ReflectionMethod('\Mock\TestFilter', 'onlySome');

        // $this->assertTrue(Reflection::validateParameters($some, array('david'), $regex));
        // $this->assertTrue(Reflection::validateParameters($some, array('paul'), $regex));
        // $this->assertTrue(Reflection::validateParameters($some, array('simon'), $regex));
        // $this->assertFalse(Reflection::validateParameters($some, array('bill'), $regex));


        // $loader->unregister();
    }

    public function testStaticParseComment()
    {
        $loader = Loader::loadNamespace(TEST_ROOT, "Mock", "");

        # short & long description
        $first = new \ReflectionMethod('\Mock\TestReflection', 'first');
        $result = Reflection::parseComment($first);

        $this->assertEquals('short description', $result['short']);
        $this->assertEquals('<p>long description</p>'.PHP_EOL, $result['long']);

        # short description multiline
        $second = new \ReflectionMethod('\Mock\TestReflection', 'second');
        $result = Reflection::parseComment($second);

        $this->assertEquals('short description more short', $result['short']);
        $this->assertEquals('<p>long description</p>'.PHP_EOL, $result['long']);

        # variables
        $third = new \ReflectionMethod('\Mock\TestReflection', 'third');
        $result = Reflection::parseComment($third);

        $this->assertEquals('', $result['short']);
        $this->assertEquals(PHP_EOL, $result['long']);

        $a = array('name' => 'a', 'regex' => null    , 'default' => null, 'desc' => null);
        $b = array('name' => 'b', 'regex' => null    , 'default' => '0' , 'desc' => null);
        $c = array('name' => 'c', 'regex' => '/:reg/', 'default' => null, 'desc' => null);
        $d = array('name' => 'd', 'regex' => '/:reg/', 'default' => '0' , 'desc' => 'descripion !');
        $this->assertEquals(array($a, $b, $c, $d), $result['var']);

        # return
        $fourth = new \ReflectionMethod('\Mock\TestReflection', 'fourth');
        $result = Reflection::parseComment($fourth);

        $return = array("name"=>'a',"type"=>'b',"desc"=>'c d e');
        $this->assertEquals(array($return), $result['return']);

        # return
        $fifth = new \ReflectionMethod('\Mock\TestReflection', 'fifth');
        $result = Reflection::parseComment($fifth);

        $auth   = array('member');
        $boom   = array('a','b','c');
        $this->assertEquals($auth, $result['auth']);
        $this->assertEquals($boom, $result['boom']);

        $loader->unregister();
    }
}
