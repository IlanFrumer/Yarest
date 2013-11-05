<?php

namespace Yarest\Helpers;

class CollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticMergeZip()
    {

        $arr1 = array(1, 3);
        $arr2 = array(2, 4);
        $this->assertEquals(Collection::mergeZip($arr1, $arr2), array(1, 2, 3, 4));

        $arr1 = array(1, 3, 5);
        $arr2 = array(2, 4);
        $this->assertEquals(Collection::mergeZip($arr1, $arr2), array(1, 2, 3, 4, 5));

        $arr1 = array(1, 3);
        $arr2 = array(2, 4, 6);
        $this->assertEquals(Collection::mergeZip($arr1, $arr2), array(1, 2, 3, 4, 6));

        $arr1 = array(1, 3);
        $arr2 = array();
        $this->assertEquals(Collection::mergeZip($arr1, $arr2), array(1, 3));

        $arr1 = array();
        $arr2 = array(2, 4);
        $this->assertEquals(Collection::mergeZip($arr1, $arr2), array(2, 4));

    }

    public function testStaticMapAssoc()
    {
        $map = array('a','b','c');
        $array = array(1, 2, 3);
        $this->assertEquals(array("a"=>1,"b"=>2,"c"=>3), Collection::mapAssoc($map, $array));

        $map = array('a','b','c');
        $array = array(1, 2, 3, 4, 5);
        $this->assertEquals(array("a"=>1,"b"=>2,"c"=>3), Collection::mapAssoc($map, $array));
    }

    public function testStaticArrayColumn()
    {
        $a1 = array("a"=>1,"b"=>2,"c"=>3);
        $a2 = array("a"=>4,"b"=>5,"c"=>6);
        $a3 = array("a"=>7,"b"=>8,"c"=>9);
        $array =  array($a1, $a2, $a3);

        $this->assertEquals(array(1, 4, 7), Collection::arrayColumn($array, "a"));
        $this->assertEquals(array(2, 5, 8), Collection::arrayColumn($array, "b"));
        $this->assertEquals(array(3, 6, 9), Collection::arrayColumn($array, "c"));

        $a1 = array("a","b","c");
        $a2 = array("d","e","f");
        $a3 = array("g","h","i");
        $array =  array($a1, $a2, $a3);

        $this->assertEquals(array("a", "d", "g"), Collection::arrayColumn($array, 0));
        $this->assertEquals(array("b", "e", "h"), Collection::arrayColumn($array, 1));
        $this->assertEquals(array("c", "f", "i"), Collection::arrayColumn($array, 2));
    }

    public function testStaticSplitBySpaces()
    {
        $subject = "hello world !";

        $a = Collection::splitBySpaces($subject);
        $this->assertEquals(array("hello","world","!"), $a);

        $b = Collection::splitBySpaces($subject, 1);
        $this->assertEquals(array($subject), $b);

        $c = Collection::splitBySpaces($subject, 2);
        $this->assertEquals(array("hello","world !"), $c);

        $d = Collection::splitBySpaces($subject, 4);
        $this->assertEquals(array("hello","world","!",null), $d);

    }
}
