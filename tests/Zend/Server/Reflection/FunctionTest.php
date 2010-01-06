<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

require_once 'Zend/Server/Reflection/Function.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

/**
 * Test case for Zend_Server_Reflection_Function
 *
 * @category   Zend
 * @package    Zend_Server
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Server
 */
class Zend_Server_Reflection_FunctionTest extends PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r = new Zend_Server_Reflection_Function($function);
        $this->assertTrue($r instanceof Zend_Server_Reflection_Function);
        $this->assertTrue($r instanceof Zend_Server_Reflection_Function_Abstract);
        $params = $r->getParameters();
        try {
            $r = new Zend_Server_Reflection_Function($params[0]);
            $this->fail('Should not be able to construct with non-function');
        } catch (Exception $e) {
            // do nothing
        }

        $r = new Zend_Server_Reflection_Function($function, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());

        $argv = array('string1', 'string2');
        $r = new Zend_Server_Reflection_Function($function, 'namespace', $argv);
        $this->assertTrue(is_array($r->getInvokeArguments()));
        $this->assertTrue($argv === $r->getInvokeArguments());

        $prototypes = $r->getPrototypes();
        $this->assertTrue(is_array($prototypes));
        $this->assertTrue(0 < count($prototypes));
    }

    public function test__getSet()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r = new Zend_Server_Reflection_Function($function);

        $r->system = true;
        $this->assertTrue($r->system);
    }


    public function testNamespace()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r = new Zend_Server_Reflection_Function($function, 'namespace');
        $this->assertEquals('namespace', $r->getNamespace());
        $r->setNamespace('framework');
        $this->assertEquals('framework', $r->getNamespace());
    }

    public function testDescription()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r = new Zend_Server_Reflection_Function($function);
        $this->assertContains('function for reflection', $r->getDescription());
        $r->setDescription('Testing setting descriptions');
        $this->assertEquals('Testing setting descriptions', $r->getDescription());
    }

    public function testGetPrototypes()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r = new Zend_Server_Reflection_Function($function);

        $prototypes = $r->getPrototypes();
        $this->assertTrue(is_array($prototypes));
        $this->assertTrue(0 < count($prototypes));
        $this->assertEquals(8, count($prototypes));

        foreach ($prototypes as $p) {
            $this->assertTrue($p instanceof Zend_Server_Reflection_Prototype);
        }
    }

    public function testGetPrototypes2()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function2');
        $r = new Zend_Server_Reflection_Function($function);

        $prototypes = $r->getPrototypes();
        $this->assertTrue(is_array($prototypes));
        $this->assertTrue(0 < count($prototypes));
        $this->assertEquals(1, count($prototypes));

        foreach ($prototypes as $p) {
            $this->assertTrue($p instanceof Zend_Server_Reflection_Prototype);
        }
    }


    public function testGetInvokeArguments()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r = new Zend_Server_Reflection_Function($function);
        $args = $r->getInvokeArguments();
        $this->assertTrue(is_array($args));
        $this->assertEquals(0, count($args));

        $argv = array('string1', 'string2');
        $r = new Zend_Server_Reflection_Function($function, null, $argv);
        $args = $r->getInvokeArguments();
        $this->assertTrue(is_array($args));
        $this->assertEquals(2, count($args));
        $this->assertTrue($argv === $args);
    }

    public function test__wakeup()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function');
        $r = new Zend_Server_Reflection_Function($function);
        $s = serialize($r);
        $u = unserialize($s);
        $this->assertTrue($u instanceof Zend_Server_Reflection_Function);
        $this->assertEquals('', $u->getNamespace());
    }

    public function testMultipleWhitespaceBetweenDoctagsAndTypes()
    {
        $function = new ReflectionFunction('Zend_Server_Reflection_FunctionTest_function3');
        $r = new Zend_Server_Reflection_Function($function);

        $prototypes = $r->getPrototypes();
        $this->assertTrue(is_array($prototypes));
        $this->assertTrue(0 < count($prototypes));
        $this->assertEquals(1, count($prototypes));

        $proto = $prototypes[0];
        $params = $proto->getParameters();
        $this->assertTrue(is_array($params));
        $this->assertEquals(1, count($params));
        $this->assertEquals('string', $params[0]->getType());
    }
}

/**
 * Zend_Server_Reflection_FunctionTest_function
 *
 * Test function for reflection unit tests
 *
 * @param string $var1
 * @param string|array $var2
 * @param array $var3
 * @return null|array
 */
function Zend_Server_Reflection_FunctionTest_function($var1, $var2, $var3 = null)
{
}

/**
 * Zend_Server_Reflection_FunctionTest_function2
 *
 * Test function for reflection unit tests; test what happens when no return
 * value or params specified in docblock.
 */
function Zend_Server_Reflection_FunctionTest_function2()
{
}

/**
 * Zend_Server_Reflection_FunctionTest_function3
 *
 * @param  string $var1
 * @return void
 */
function Zend_Server_Reflection_FunctionTest_function3($var1)
{
}
