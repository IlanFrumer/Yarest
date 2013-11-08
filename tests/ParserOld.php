<?php

namespace Yarest;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    private $loader;

    public function setUp()
    {
        $this->loader = Helpers\Loader::loadNamespace(TEST_ROOT, "Mock", "");
    }

    public function tearDown()
    {
        $this->loader->unregister();
    }

    public static function getParser()
    {
        $_SERVER['REQUEST_URI']    = '/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SERVER_NAME']    = 'localhost';
        $_SERVER['PHP_SELF']       = '/index.php';
        $_SERVER['DOCUMENT_ROOT']  = TEST_ROOT;

        $app = new App();
        return $app->parser;
    }

    public function testFilterMethods()
    {

        $methods = Helpers\Reflection::getOwnPublicMethods('Mock\TestFilter');
        $parser = self::getParser();

        # 1 regex match
        $elements = array("ilan@gmail.com");
        list($errors, $allowed_methods, $matched_method) = $parser->filterMethods($methods, $elements);

        $this->assertEmpty($errors);
        $this->assertEquals(array("GET" => true), $allowed_methods);
        $this->assertInstanceOf("ReflectionMethod", $matched_method);
        $this->assertEquals("getEmailRegex", $matched_method->name);

        # 2 regex match
        $elements = array("04-9922222");
        list($errors, $allowed_methods, $matched_method) = $parser->filterMethods($methods, $elements);

        $this->assertEmpty($errors);
        $this->assertEquals(array("GET" => true), $allowed_methods);
        $this->assertInstanceOf("ReflectionMethod", $matched_method);
        $this->assertEquals("getPhoneRegex", $matched_method->name);

        # 3 regex match
        $elements = array("testme");
        list($errors, $allowed_methods, $matched_method) = $parser->filterMethods($methods, $elements);

        $this->assertEmpty($errors);
        $this->assertEquals(array("GET" => true), $allowed_methods);
        $this->assertInstanceOf("ReflectionMethod", $matched_method);
        $this->assertEquals("getInlineRegex", $matched_method->name);

        # 4 405 method wrong
        $elements = array("1", "2");
        list($errors, $allowed_methods, $matched_method) = $parser->filterMethods($methods, $elements);

        $this->assertEmpty($errors);
        $this->assertEquals(array("POST" => false), $allowed_methods);
        $this->assertNull($matched_method);

        # 5 regex error
        $elements = array("1");
        list($errors, $allowed_methods, $matched_method) = $parser->filterMethods($methods, $elements);

        $this->assertEquals('Mock\TestFilter', $errors[0]['class']);
        $this->assertEquals('getWrong', $errors[0]['method']);
        $this->assertEquals(array("name" => 'invalid expression: /david|paul|simon'), $errors[0]['parameters']);
        $this->assertEquals(array("GET" => true), $allowed_methods);

        # 6 regex not matched
        $elements = array("1", "2", "ilangmail.com");
        list($errors, $allowed_methods, $matched_method) = $parser->filterMethods($methods, $elements);

        $this->assertEmpty($errors);
        $this->assertEquals(array("GET" => true), $allowed_methods);
        $this->assertNull($matched_method);
    }

    public function testValidateParameters()
    {

        $parser = self::getParser();

        $getEmailRegex  = new \ReflectionMethod('\Mock\TestFilter', 'getEmailRegex');
        $getArithmetics = new \ReflectionMethod('\Mock\TestFilter', 'getArithmetics');
        $getWrong       = new \ReflectionMethod('\Mock\TestFilter', 'getWrong');

        list($errors, $valid) = $parser->validateParameters($getEmailRegex, array("ilan@gmail.com"));
        $this->assertEmpty($errors);
        $this->assertTrue($valid);

        list($errors, $valid) = $parser->validateParameters($getEmailRegex, array("ilangmail.com"));
        $this->assertEmpty($errors);
        $this->assertFalse($valid);

        list($errors, $valid) = $parser->validateParameters($getArithmetics, array('10','20','15','25'));
        $this->assertTrue($valid);

        list($errors, $valid) = $parser->validateParameters($getArithmetics, array('11','20','15','25'));
        $this->assertFalse($valid);

        list($errors, $valid) = $parser->validateParameters($getArithmetics, array('10','10','15','25'));
        $this->assertFalse($valid);

        list($errors, $valid) = $parser->validateParameters($getArithmetics, array('10','20','25','25'));
        $this->assertFalse($valid);

        list($errors, $valid) = $parser->validateParameters($getArithmetics, array('10','20','15','24'));
        $this->assertFalse($valid);

        list($errors, $valid) = $parser->validateParameters($getWrong, array('david'));
        $this->assertEquals(array('name' => "invalid expression: /david|paul|simon"), $errors);
        $this->assertFalse($valid);
        
    }

    public function testParseComment()
    {
        $parser = self::getParser();

        $first  = new \ReflectionMethod('\Mock\TestReflection', 'first');
        $second  = new \ReflectionMethod('\Mock\TestReflection', 'second');
        $third  = new \ReflectionMethod('\Mock\TestReflection', 'third');
        $fourth  = new \ReflectionMethod('\Mock\TestReflection', 'fourth');
        $fifth  = new \ReflectionMethod('\Mock\TestReflection', 'fifth');


        # short & long description
        $first = new \ReflectionMethod('\Mock\TestReflection', 'first');
        $result = $parser->parseComment($first);

        $this->assertEquals('short description', $result['short']);
        $this->assertEquals('<p>long description</p>'.PHP_EOL, $result['long']);

        # short description multiline
        $result = $parser->parseComment($second);

        $this->assertEquals('short description more short', $result['short']);
        $this->assertEquals('<p>long description</p>'.PHP_EOL, $result['long']);

        # variables
        $result = $parser->parseComment($third);

        $this->assertEquals('', $result['short']);
        $this->assertEquals(PHP_EOL, $result['long']);

        $a = array('name' => 'a', 'expression' => null, 'default' => null, 'desc' => null);
        $b = array('name' => 'b', 'expression' => null , 'default' => '0' , 'desc' => null);
        $c = array('name' => 'c', 'expression' => '/:number/', 'default' => null, 'desc' => null);
        $d = array('name' => 'd', 'expression' => '/:number/', 'default' => '0' , 'desc' => 'descripion !');
        $this->assertEquals(array($a, $b, $c, $d), $result['var']);

        # return
        $result = $parser->parseComment($fourth);

        $return = array("name"=>'a',"type"=>'b',"desc"=>'c d e');
        $this->assertEquals(array($return), $result['return']);

        # return
        $result = $parser->parseComment($fifth);

        $auth   = array('member');
        $boom   = array('a','b','c');
        $this->assertEquals($auth, $result['auth']);
        $this->assertEquals($boom, $result['boom']);
    }


    public function testCheckCommentVars()
    {

    }

    public function testExpression()
    {
        $parser = self::getParser();

        # 1 expression true
        $subject = "/:email/";
        $element = "ilan@gmail.com";
        
        $valid = $parser->expression($subject, $element);
        $this->assertEquals("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $subject);
        $this->assertTrue($valid);

        #2 regex variable error
        $subject = "/:none/";
        $valid = $parser->expression($subject, $element);
        $this->assertEquals("invalid regex variable /:none/", $valid);

        #3 invalid expression
        $subject = "/:email";

        $valid = $parser->expression($subject, $element);
        $this->assertEquals("invalid expression: /:email", $valid);


        #4 expression false
        $subject = "/:email/";
        $element = "ilangmail.com";

        $valid = $parser->expression($subject, $element);
        $this->assertEquals("/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/", $subject);
        $this->assertFalse($valid);


        # 5 vertical bar
        $subject = "1|2|3";
        $elementT = "1";
        $elementF = "4";
        
        $valid = $parser->expression($subject, $elementT);
        $this->assertTrue($valid);
        $valid = $parser->expression($subject, $elementF);
        $this->assertFalse($valid);

        # 6 arithmetics
         
        $array = array();
        $array[]= array("subject" => ">10"   , "true" => "11", "false" => "10");
        $array[]= array("subject" => "<10"   , "true" => "9" , "false" => "11");
        $array[]= array("subject" => ">=10"  , "true" => "10", "false" => "9");
        $array[]= array("subject" => "<=10"  , "true" => "10", "false" => "11");
        $array[]= array("subject" => "1..10" , "true" => "5" , "false" => "11");
        $array[]= array("subject" => "1...10", "true" => "5" , "false" => "10");
        $array[]= array("subject" => "%10"   , "true" => "20", "false" => "21");

        foreach ($array as $test) {

            $valid = $parser->expression($test['subject'], $test['true']);
            $this->assertTrue($valid);
            $valid = $parser->expression($test['subject'], $test['false']);
            $this->assertFalse($valid);
        }
    }
}
