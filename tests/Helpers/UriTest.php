<?php

namespace Yarest\Helpers;

class UriTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticMethodToSlash()
    {
        $this->assertEquals(Uri::toSlash('/users'), '/users');
        $this->assertEquals(Uri::toSlash('\\users\\'), '/users/');
        $this->assertEquals(Uri::toSlash('\\'), '/');
    }

    public function testStaticMethodStripAsterisk()
    {
        $this->assertEquals(Uri::stripAsterisk('/api/**'), '/api/');
        $this->assertEquals(Uri::stripAsterisk('/api/v1/*'), '/api/v1/');
        $this->assertEquals(Uri::stripAsterisk('/api/v2*'), '/api/v2');
    }

    public function testStaticMethodStripURI()
    {
        $uri  = '/users';
        $root = '/';

        $this->assertEquals(Uri::stripURI($uri, $root), '/users');

        $uri  = '/www/users';
        $root = '/www';

        $this->assertEquals(Uri::stripURI($uri, $root), '/users');
    }

    public function testStaticMethodNamespaceToStack()
    {
        $namespace  = '\\Yarest\\Tests';
        $this->assertEquals(Uri::namespaceToStack($namespace), array('Yarest','Tests'));

        $namespace  = 'Yarest\\Tests';
        $this->assertEquals(Uri::namespaceToStack($namespace), array('Yarest','Tests'));

        $namespace  = 'Yarest';
        $this->assertEquals(Uri::namespaceToStack($namespace), array('Yarest'));

        $namespace  = '\\Yarest\\';
        $this->assertEquals(Uri::namespaceToStack($namespace), array('Yarest'));
    }

    public function testStaticMethodURIToStack()
    {
        $uri  = '/Yarest/Tests';
        $this->assertEquals(Uri::uriToStack($uri), array('Yarest','Tests'));

        $uri  = '/Yarest/Tests';
        $this->assertEquals(Uri::uriToStack($uri), array('Yarest','Tests'));

        $uri  = 'Yarest';
        $this->assertEquals(Uri::uriToStack($uri), array('Yarest'));

        $uri  = '/Yarest/';
        $this->assertEquals(Uri::uriToStack($uri), array('Yarest'));
    }

    public function testStaticMethodStackToNamespace()
    {
        $stack = array('Yarest','Tests');
        $this->assertEquals(Uri::stackToNamespace($stack), 'Yarest\\Tests');

        $stack = array('Yarest');
        $this->assertEquals(Uri::stackToNamespace($stack), 'Yarest');
    }

    public function testStaticMethodStackToURI()
    {
        $stack = array('Yarest','Tests');
        $this->assertEquals(Uri::stackToURI($stack), 'Yarest/Tests');
        
        $stack = array('Yarest');
        $this->assertEquals(Uri::stackToURI($stack), 'Yarest');
    }

    public function testStaticMethodDivideStack()
    {

        $stack     = array('sToRe','gooGLE','likes');
        $namespace = array('Api');
        $elements  = array();

        Uri::divideStack($stack, $namespace, $elements);

        $this->assertEquals($namespace, array('Api','Store','Likes'));
        $this->assertEquals($elements, array('Google'));

    }
}
