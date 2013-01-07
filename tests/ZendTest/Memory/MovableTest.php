<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Memory
 */

namespace ZendTest\Memory;

use Zend\Memory;
use Zend\Memory\Container;

/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @group      Zend_Memory
 */
class MovableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager = new DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        $this->assertTrue($memObject instanceof Container\Movable);
    }

    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager = new DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        // value property
        $this->assertEquals((string)$memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string)$memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertTrue($memObject->value instanceof \Zend\Memory\Value);
        $this->assertEquals((string)$memObject->value, 'another value');
    }

    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memoryManager = new DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        $this->assertFalse((bool) $memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((bool) $memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse((bool) $memObject->isLocked());
    }

    /**
     * tests the touch() method
     */
    public function testTouch()
    {
        $memoryManager = new DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        $this->assertFalse($memoryManager->processUpdatePassed);

        $memObject->touch();

        $this->assertTrue($memoryManager->processUpdatePassed);
        $this->assertTrue($memoryManager->processedObject === $memObject);
        $this->assertEquals($memoryManager->processedId, 10);
    }

    /**
     * tests the value update tracing
     */
    public function testValueUpdateTracing()
    {
        $memoryManager = new DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');

        // startTrace() method is usually invoked by memory manager, when it need to be notified
        // about value update
        $memObject->startTrace();

        $this->assertFalse($memoryManager->processUpdatePassed);

        $memObject->value[6] = '_';

        $this->assertTrue($memoryManager->processUpdatePassed);
        $this->assertTrue($memoryManager->processedObject === $memObject);
        $this->assertEquals($memoryManager->processedId, 10);
    }

    public function testInvalidGetThrowException()
    {
        $memoryManager = new DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');
        $this->setExpectedException('Zend\Memory\Exception\InvalidArgumentException');
        $value = $memObject->unknowProperty;
    }

    public function testInvalidSetThrowException()
    {
        $memoryManager = new DummyMemoryManager();
        $memObject = new Container\Movable($memoryManager, 10, '0123456789');
        $this->setExpectedException('Zend\Memory\Exception\InvalidArgumentException');
        $memObject->unknowProperty = 5;
    }
}

/**
 * Memory manager helper
 *
 */
class DummyMemoryManager extends Memory\MemoryManager
{
    /** @var bool */
    public $processUpdatePassed = false;

    /** @var integer */
    public $processedId;

    /** @var Zend_Memory_Container_Movable */
    public $processedObject;

    /**
     * Empty constructor
     */
    public function __construct()
    {
        // Do nothing
    }

    /**
     * DummyMemoryManager value update callback method
     */
    public function processUpdate(Container\Movable $container, $id)
    {
        $this->processUpdatePassed = true;
        $this->processedId         = $id;
        $this->processedObject     = $container;
    }
}
