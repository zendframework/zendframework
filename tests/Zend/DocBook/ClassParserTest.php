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
use Zend\DocBook\ClassParser;
use Zend\Code\Reflection\ClassReflection;

/**
 * @category   Zend
 * @package    Zend_DocBook
 * @subpackage UnitTests
 */
class ClassParserTest extends TestCase
{
    /** @var ClassReflection */
    public $class;
    /** @var ClassParser */
    public $parser;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->class  = new ClassReflection(new TestAsset\ParsedClass());
        $this->parser = new ClassParser($this->class);
    }

    public function testIdShouldBeNormalizedNamespacedClass()
    {
        $id = $this->parser->getId();
        $this->assertEquals('zend-test.doc-book.test-asset.parsed-class', $id);
    }

    public function testRetrievingMethodsShouldReturnClassMethodObjects()
    {
        $methods = $this->parser->getMethods();

        $this->assertEquals(count($this->class->getMethods(\ReflectionMethod::IS_PUBLIC)), count($methods));
        foreach ($methods as $method) {
            $this->assertInstanceOf('Zend\DocBook\ClassMethod', $method);
        }
    }
}
