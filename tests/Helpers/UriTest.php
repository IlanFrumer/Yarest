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
        $this->assertEquals(Uri::stripAsterisk('/api/**/asd/sad/asd/asdas'), '/api/');
        $this->assertEquals(Uri::stripAsterisk('/api/v1/'), '/api/v1/');
        $this->assertEquals(Uri::stripAsterisk('/api/v2*'), '/api/v2');
    }

    public function testStaticMethodSubstrURI()
    {
        $uri  = '/users';
        $root = '/';

        $this->assertEquals(Uri::substrURI($uri, $root), '/users');

        $uri  = '/www/users';
        $root = '/www';

        $this->assertEquals(Uri::substrURI($uri, $root), '/users');
    }

    public function testStaticMethodNamespaceToArray()
    {
        $namespace  = '\\Yarest\\Tests';
        $this->assertEquals(Uri::namespaceToArray($namespace), array('Yarest','Tests'));

        $namespace  = 'Yarest\\Tests';
        $this->assertEquals(Uri::namespaceToArray($namespace), array('Yarest','Tests'));

        $namespace  = array('Yarest','Tests');
        $this->assertEquals(Uri::namespaceToArray($namespace), array('Yarest','Tests'));

        $namespace  = 'Yarest';
        $this->assertEquals(Uri::namespaceToArray($namespace), array('Yarest'));

        $namespace  = '\\Yarest\\';
        $this->assertEquals(Uri::namespaceToArray($namespace), array('Yarest'));
    }

     /**
     * @expectedException \InvalidArgumentException
     */
    public function testStaticMethodNamespaceToArrayFail()
    {
        Uri::namespaceToArray(null);
    }


    public function testStaticMethodURIToArray()
    {
        $uri  = '/Yarest/Tests';
        $this->assertEquals(Uri::uriToArray($uri), array('Yarest','Tests'));

        $uri  = '/Yarest/Tests';
        $this->assertEquals(Uri::uriToArray($uri), array('Yarest','Tests'));

        $uri  = array('Yarest','Tests');
        $this->assertEquals(Uri::uriToArray($uri), array('Yarest','Tests'));

        $uri  = 'Yarest';
        $this->assertEquals(Uri::uriToArray($uri), array('Yarest'));

        $uri  = '/Yarest/';
        $this->assertEquals(Uri::uriToArray($uri), array('Yarest'));
    }

     /**
     * @expectedException \InvalidArgumentException
     */
    public function testStaticMethodURIToArrayFail()
    {
        Uri::uriToArray(null);
    }

    public function testStaticMethodArrayToNamespace()
    {
        $array = array('Yarest','Tests');
        $this->assertEquals(Uri::arrayToNamespace($array), 'Yarest\\Tests');

        $array = array('Yarest');
        $this->assertEquals(Uri::arrayToNamespace($array), 'Yarest');
    }

    public function testStaticMethodArrayToURI()
    {
        $array = array('Yarest','Tests');
        $this->assertEquals(Uri::arrayToURI($array), 'Yarest/Tests');
        
        $array = array('Yarest');
        $this->assertEquals(Uri::arrayToURI($array), 'Yarest');
    }

    public function testStaticMethodUriToNamespace()
    {

        $array = Uri::uriToArray('/sToRe/gooGLE/likes');

        $namespace = array('Api');
        $elements  = array();

        Uri::uriToNamespace($array, $namespace, $elements);

        $this->assertEquals($namespace, array('Api','Store','Likes'));
        $this->assertEquals($elements, array('Google'));

    }
}
