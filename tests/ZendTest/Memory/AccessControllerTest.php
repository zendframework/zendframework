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

use Zend\Cache\StorageFactory as CacheFactory;
use Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter;
use Zend\Memory;
use Zend\Memory\Container;

/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @group      Zend_Memory
 */
class AccessControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Cache object
     *
     * @var CacheAdapter
     */
    private $_cache = null;

    public function setUp()
    {
        $this->_cache = CacheFactory::adapterFactory('memory', array('memory_limit' => 0));
    }

    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager  = new Memory\MemoryManager($this->_cache);
        $memObject      = $memoryManager->create('012345678');

        $this->assertTrue($memObject instanceof \Zend\Memory\Container\AccessController);
    }

    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager  = new Memory\MemoryManager($this->_cache);
        $memObject      = $memoryManager->create('0123456789');

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
        $memoryManager  = new Memory\MemoryManager($this->_cache);
        $memObject      = $memoryManager->create('012345678');

        $this->assertFalse((bool) $memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((bool) $memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse((bool) $memObject->isLocked());
    }
}
