<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager;

use PHPUnit_Framework_TestCase as TestCase;

/**
 * @requires PHP 5.4.0
 */
class MutableCreationOptionsTraitTest extends TestCase
{
    protected $stub;

    public function setUp()
    {
        if (PHP_VERSION_ID < 50400) {
            $this->markTestSkipped('Requires PHP >=5.4.0');
        }
        $this->stub = $this->getObjectForTrait('Zend\ServiceManager\MutableCreationOptionsTrait');
    }

    public function tearDown()
    {
        unset($this->stub);
    }

    public function testCreationOptionsInitiallyIsArray()
    {
        $this->assertAttributeEquals(array(), 'creationOptions', $this->stub);
    }

    public function testTraitProvidesSetter()
    {
        $this->assertTrue(
            method_exists($this->stub, 'setCreationOptions')
        );
    }

    public function testTraitProvidesGetter()
    {
        $this->assertTrue(
            method_exists($this->stub, 'getCreationOptions')
        );
    }

    public function testTraitAcceptsCreationOptionsArray()
    {
        $creationOptions = array(
            'foo' => 'bar'
        );
        $this->stub->setCreationOptions($creationOptions);
        $this->assertEquals($creationOptions, $this->stub->getCreationOptions());
    }
}
