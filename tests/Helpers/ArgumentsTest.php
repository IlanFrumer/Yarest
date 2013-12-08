<?php

namespace Yarest\Helpers;

class ArgumentsTest extends \PHPUnit_Framework_TestCase
{


    public function testStaticCheckCallable()
    {
        $callable = function () {

        };

        $this->assertTrue(Arguments::checkCallable($callable));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStaticCheckCallableError()
    {
        Arguments::checkCallable(123);
    }
}
