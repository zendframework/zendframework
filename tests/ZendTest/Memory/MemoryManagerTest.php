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
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Memory;
use Zend\Cache\Cache;
use Zend\Memory;
use Zend\Memory\Container;


/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Memory
 */
class MemoryManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Cache object
     *
     * @var \Zend\Cache\Frontend
     */
    private $_cache = null;

    public function setUp()
    {
        $this->_cache = Cache::factory('Core', 'File',
                 array('lifetime' => 1, 'automatic_serialization' => true),
                 array('cache_dir' => __DIR__ . '/_files/'));
    }


    public function tearDown()
    {
        $this->_cache->clean(Cache::CLEANING_MODE_ALL);
        $this->_cache = null;
    }

    /**
     * tests the Memory Manager creation
     */
    public function testCreation()
    {
        /** Without caching */
        $memoryManager = new Memory\MemoryManager();
        $this->assertTrue($memoryManager instanceof Memory\MemoryManager);
        unset($memoryManager);

        /** Caching using 'File' backend */
        $memoryManager = new Memory\MemoryManager($this->_cache);
        $this->assertTrue($memoryManager instanceof Memory\MemoryManager);
        unset($memoryManager);
    }

    /**
     * tests the Memory Manager settings
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
        $this->assertTrue($memObject1 instanceof Container\AccessController);
        $this->assertEquals($memObject1->getRef(), 'Value of object 1');

        $memObject2 = $memoryManager->create();
        $this->assertTrue($memObject2 instanceof Container\AccessController);
        $this->assertEquals($memObject2->getRef(), '');

        $memObject3 = $memoryManager->createLocked('Value of object 3');
        $this->assertTrue($memObject3 instanceof Container\Locked);
        $this->assertEquals($memObject3->getRef(), 'Value of object 3');

        $memObject4 = $memoryManager->createLocked();
        $this->assertTrue($memObject4 instanceof Container\Locked);
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
            if (version_compare(PHP_VERSION, '5.2') < 0) {
                $value = $memObjects[$count]->getRef();
                $this->assertEquals($value[16], (string)($count % 10));
            } else {
                $this->assertEquals($memObjects[$count]->value[16], (string)($count % 10));
            }
        }

        for ($count = 63; $count > 0; $count -= 2) {
            if (version_compare(PHP_VERSION, '5.2') < 0) {
                $value = &$memObjects[$count]->getRef();
                $value[16] = '_';
            } else {
                $memObjects[$count]->value[16] = '_';
            }
        }

        for ($count = 1; $count < 64; $count += 2) {
            if (version_compare(PHP_VERSION, '5.2') < 0) {
                $value = $memObjects[$count]->getRef();
                $this->assertEquals($value[16], '_');
            } else {
                $this->assertEquals($memObjects[$count]->value[16], '_');
            }
        }
    }
}
