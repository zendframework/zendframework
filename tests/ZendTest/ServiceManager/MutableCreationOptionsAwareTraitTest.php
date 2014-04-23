<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\ServiceManager;

use PHPUnit_Framework_TestCase as TestCase;

class MutableCreationOptionsAwareTraitTest extends TestCase
{
    protected $stub;

    public function setUp()
    {
        $this->stub = $this->getObjectForTrait('\Zend\ServiceManager\MutableCreationOptionsAwareTrait');
    }

    public function tearDown()
    {
        unset($this->stub);
    }

    public function testCreationOptionsInitiallyIsArray()
    {
        $this->assertAttributeEquals(Array(), 'creationOptions', $this->stub);
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
        $creationOptions = array('foo' => 'bar');
        $this->stub->setCreationOptions($creationOptions);
        $this->assertEquals($creationOptions, $this->stub->getCreationOptions());
    }
}
