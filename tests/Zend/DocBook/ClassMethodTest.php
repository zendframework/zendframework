<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_DocBook
 */

namespace ZendTest\DocBook;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\DocBook\ClassMethod;
use Zend\Code\Reflection\ClassReflection;

/**
 * @category   Zend
 * @package    Zend_DocBook
 * @subpackage UnitTests
 */
class ClassMethodTest extends TestCase
{
    /**
     * @var ClassReflection
     */
    protected $class;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->class = new ClassReflection(new TestAsset\ParsedClass());
    }

    public function testCorrectlyDetectsMethodName()
    {
        $r      = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertEquals('action1', $method->getName());
    }

    public function testIdShouldBeNormalizedMethodName()
    {
        $r      = $this->class->getMethod('camelCasedMethod');
        $method = new ClassMethod($r);

        $this->assertEquals('zend-test.doc-book.test-asset.parsed-class.methods.camel-cased-method', $method->getId());
    }

    public function testCorrectlyDetectsMethodShortDescription()
    {
        $r      = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertContains('short action1 method description', $method->getShortDescription());
        $this->assertNotContains('Long description for action1', $method->getShortDescription());
        $this->assertNotRegExp('/\*\s*/', $method->getShortDescription());
    }

    public function testCorrectlyDetectsMethodLongDescription()
    {
        $r      = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertContains('Long description for action1', $method->getLongDescription());
        $this->assertNotContains('short action1 method description', $method->getLongDescription());
        $this->assertNotRegExp('/\*\s*/', $method->getLongDescription());
    }

    public function testCorrectlyDeterminesNonObjectReturnType()
    {
        $r      = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertEquals('float', $method->getReturnType());
    }

    /**
     * @group prototype
     */
    public function testCorrectlyBuildsNonObjectArgumentPrototype()
    {
        $r      = $this->class->getMethod('action1');
        $method = new ClassMethod($r);

        $this->assertEquals('string $arg1, bool $arg2, null|array $arg3', $method->getPrototype());
    }

    public function testCorrectlyDeterminesReturnTypeClass()
    {
        $r      = $this->class->getMethod('action2');
        $method = new ClassMethod($r);

        $this->assertEquals('ZendTest\DocBook\TestAsset\ParsedClass', $method->getReturnType());
    }

    /**
     * @group prototype
     */
    public function testCorrectlyBuildsArgumentPrototypeContainingClassNames()
    {
        $r      = $this->class->getMethod('action2');
        $method = new ClassMethod($r);

        $this->assertEquals('null|Zend\Loader\PluginClassLoader $loader', $method->getPrototype());
    }
}
