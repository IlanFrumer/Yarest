<?php

namespace Yarest;

/**
 * @link(test headers, http://stackoverflow.com/questions/9745080/test-php-headers-with-phpunit#answer-10815902)
 */

class AppTest extends \PHPUnit_Framework_TestCase
{
    
    public function testApp2()
    {
        $y = new App();
        
        $router = $y->route('/api/*','Api');
        $this->assertInstanceOf("\Yarest\Router", $router);
    }
}
