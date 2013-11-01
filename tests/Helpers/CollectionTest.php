<?php

namespace Yarest\Helpers;

class CollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testStaticMethodMergeZip()
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
}
