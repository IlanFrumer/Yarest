<?php

namespace Yarest\Helpers;

class ArgumentsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStaticMethodCheckCallable()
    {
        $callable = function(){};

        $this->assertTrue(Arguments::checkCallable($callable));

        Arguments::checkCallable(123);
    }
}
