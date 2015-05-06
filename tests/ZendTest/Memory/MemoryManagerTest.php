<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Memory;

use Zend\Cache\StorageFactory as CacheFactory;
use Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter;
use Zend\Memory;

/**
 * @group      Zend_Memory
 */
class MemoryManagerTest extends \PHPUnit_Framework_TestCase
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
     * tests the Memory ManagerInterface creation
     */
    public function testCreation()
    {
        /** Without caching */
        $memoryManager = new Memory\MemoryManager();
        $this->assertInstanceOf('Zend\Memory\MemoryManager', $memoryManager);
        unset($memoryManager);

        /** Caching using 'File' backend */
        $memoryManager = new Memory\MemoryManager($this->_cache);
        $this->assertInstanceOf('Zend\Memory\MemoryManager', $memoryManager);
        unset($memoryManager);
    }

    /**
     * tests the Memory ManagerInterface settings
     */
    public function testSettings()
    {
        $memoryManager = new Memory\MemoryManager($this->_cache);

        // MemoryLimit
        $memoryManager->setMemoryLimit(2*1024*1024 /* 2Mb */);
        $this->assertEquals($memoryManager->getMemoryLimit(), 2*1024*1024);

        // MinSize
        $this->assertEquals($memoryManager->getMinSize(), 16*1024); // check for default value (16K)
        $memoryManager->setMinSize(4*1024 /* 4Kb */);
        $this->assertEquals($memoryManager->getMinSize(), 4*1024);
    }

    /**
     * tests the memory Objects creation
     */
    public function testCreate()
    {
        $memoryManager = new Memory\MemoryManager($this->_cache);

        $memObject1 = $memoryManager->create('Value of object 1');
        $this->assertInstanceOf('Zend\Memory\Container\AccessController', $memObject1);
        $this->assertEquals($memObject1->getRef(), 'Value of object 1');

        $memObject2 = $memoryManager->create();
        $this->assertInstanceOf('Zend\Memory\Container\AccessController', $memObject2);
        $this->assertEquals($memObject2->getRef(), '');

        $memObject3 = $memoryManager->createLocked('Value of object 3');
        $this->assertInstanceOf('Zend\Memory\Container\Locked', $memObject3);
        $this->assertEquals($memObject3->getRef(), 'Value of object 3');

        $memObject4 = $memoryManager->createLocked();
        $this->assertInstanceOf('Zend\Memory\Container\Locked', $memObject4);
        $this->assertEquals($memObject4->getRef(), '');
    }

    /**
     * tests the processing of data
     */
    public function testProcessing()
    {
        $memoryManager = new Memory\MemoryManager($this->_cache);

        $memoryManager->setMinSize(256);
        $memoryManager->setMemoryLimit(1024*32);

        $memObjects = array();
        for ($count = 0; $count < 64; $count++) {
            $memObject = $memoryManager->create(str_repeat((string)($count % 10), 1024) /* 1K */);
            $memObjects[] = $memObject;
        }

        for ($count = 0; $count < 64; $count += 2) {
            $this->assertEquals($memObjects[$count]->value[16], (string)($count % 10));
        }

        for ($count = 63; $count > 0; $count -= 2) {
            $memObjects[$count]->value[16] = '_';
        }

        for ($count = 1; $count < 64; $count += 2) {
            $this->assertEquals($memObjects[$count]->value[16], '_');
        }
    }

    public function testNotEnoughSpaceThrowException()
    {
        $memoryManager = new Memory\MemoryManager($this->_cache);

        $memoryManager->setMinSize(128);
        $memoryManager->setMemoryLimit(1024);

        $memObjects = array();
        for ($count = 0; $count < 8; $count++) {
            $memObject = $memoryManager->create(str_repeat((string)($count % 10), 128) /* 1K */);
            $memObjects[] = $memObject;
        }

        $this->setExpectedException('Zend\Memory\Exception\RuntimeException');
        $memoryManager->create('a');
    }
}
