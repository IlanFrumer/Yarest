<?php

namespace Yarest;

class HelpersTest extends \PHPUnit_Framework_TestCase
{
    public function testStaticMethodToSlash()
    {
        $this->assertEquals(Helpers::toSlash('/users'), '/users');
        $this->assertEquals(Helpers::toSlash('\\users\\'), '/users/');
        $this->assertEquals(Helpers::toSlash('\\'), '/');
    }

    public function testStaticMethodStripAsterisk()
    {
        $this->assertEquals(Helpers::stripAsterisk('/api/**'), '/api/');
        $this->assertEquals(Helpers::stripAsterisk('/api/v1/*'), '/api/v1/');
        $this->assertEquals(Helpers::stripAsterisk('/api/v2*'), '/api/v2');
    }

    public function testStaticMethodMergeZip()
    {

        $arr1 = array(1, 3);
        $arr2 = array(2, 4);
        $this->assertEquals(Helpers::mergeZip($arr1, $arr2), array(1, 2, 3, 4));

        $arr1 = array(1, 3, 5);
        $arr2 = array(2, 4);
        $this->assertEquals(Helpers::mergeZip($arr1, $arr2), array(1, 2, 3, 4, 5));

        $arr1 = array(1, 3);
        $arr2 = array(2, 4, 6);
        $this->assertEquals(Helpers::mergeZip($arr1, $arr2), array(1, 2, 3, 4, 6));

        $arr1 = array(1, 3);
        $arr2 = array();
        $this->assertEquals(Helpers::mergeZip($arr1, $arr2), array(1, 3));

        $arr1 = array();
        $arr2 = array(2, 4);
        $this->assertEquals(Helpers::mergeZip($arr1, $arr2), array(2, 4));

    }

    public function testStaticMethodStripURI()
    {
        $uri  = '/users';
        $root = '/';

        $this->assertEquals(Helpers::stripURI($uri, $root), '/users');

        $uri  = '/www/users';
        $root = '/www';

        $this->assertEquals(Helpers::stripURI($uri, $root), '/users');
    }

    public function testStaticMethodNamespaceToStack()
    {
        $namespace  = '\\Yarest\\Tests';
        $this->assertEquals(Helpers::namespaceToStack($namespace), array('Yarest','Tests'));

        $namespace  = 'Yarest\\Tests';
        $this->assertEquals(Helpers::namespaceToStack($namespace), array('Yarest','Tests'));

        $namespace  = 'Yarest';
        $this->assertEquals(Helpers::namespaceToStack($namespace), array('Yarest'));

        $namespace  = '\\Yarest\\';
        $this->assertEquals(Helpers::namespaceToStack($namespace), array('Yarest'));
    }

    public function testStaticMethodURIToStack()
    {
        $uri  = '/Yarest/Tests';
        $this->assertEquals(Helpers::uriToStack($uri), array('Yarest','Tests'));

        $uri  = '/Yarest/Tests';
        $this->assertEquals(Helpers::uriToStack($uri), array('Yarest','Tests'));

        $uri  = 'Yarest';
        $this->assertEquals(Helpers::uriToStack($uri), array('Yarest'));

        $uri  = '/Yarest/';
        $this->assertEquals(Helpers::uriToStack($uri), array('Yarest'));
    }

    public function testStaticMethodStackToNamespace()
    {
        $stack = array('Yarest','Tests');
        $this->assertEquals(Helpers::stackToNamespace($stack), 'Yarest\\Tests');

        $stack = array('Yarest');
        $this->assertEquals(Helpers::stackToNamespace($stack), 'Yarest');
    }

    public function testStaticMethodStackToURI()
    {
        $stack = array('Yarest','Tests');
        $this->assertEquals(Helpers::stackToURI($stack), 'Yarest/Tests');
        
        $stack = array('Yarest');
        $this->assertEquals(Helpers::stackToURI($stack), 'Yarest');
    }

    public function testStaticMethodGetConfig()
    {
        $alias    = Helpers::getConfig('alias');
        $defaults = Helpers::getConfig('defaults');

        $this->assertArrayHasKey('root_class', $defaults);
        $this->assertArrayHasKey('get', $alias);
        $this->assertArrayHasKey('post', $alias);
        $this->assertArrayHasKey('delete', $alias);
        $this->assertArrayHasKey('put', $alias);

    }

    public function testStaticMethodDivideStack()
    {

        $stack     = array('sToRe','gooGLE','likes');
        $namespace = array('Api');
        $elements  = array();

        Helpers::divideStack($stack, $namespace, $elements);

        $this->assertEquals($namespace, array('Api','Store','Likes'));
        $this->assertEquals($elements, array('Google'));

    }
}
