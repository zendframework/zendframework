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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Memory_AccessControllerTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Memory */
require_once 'Zend/Memory.php';

/**
 * @category   Zend
 * @package    Zend_Memory
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Memory_Container_AccessControllerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Memory manager, used for tests
     *
     * @var Zend_Memory_Manager
     */
    private $_memoryManager = null;

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $tmpDir = sys_get_temp_dir() . '/zend_memory';
        $this->_removeCacheDir($tmpDir);
        mkdir($tmpDir);
        $this->cacheDir = $tmpDir;
    }

    protected function _removeCacheDir($dir) 
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            $this->_removeCacheDir($dir . '/' . $item);
        }

        return rmdir($dir);
    }

    /**
     * Retrieve memory manager
     *
     */
    private function _getMemoryManager()
    {
        if ($this->_memoryManager === null) {
            $backendOptions = array('cache_dir' => $this->cacheDir); // Directory where to put the cache files
            $this->_memoryManager = Zend_Memory::factory('File', $backendOptions);
        }

        return $this->_memoryManager;
    }



    /**
     * tests the Movable memory container object creation
     */
    public function testCreation()
    {
        $memoryManager  = $this->_getMemoryManager();
        $memObject      = $memoryManager->create('012345678');

        $this->assertTrue($memObject instanceof Zend_Memory_AccessController);
    }


    /**
     * tests the value access methods
     */
    public function testValueAccess()
    {
        $memoryManager  = $this->_getMemoryManager();
        $memObject      = $memoryManager->create('0123456789');

        // getRef() method
        $this->assertEquals($memObject->getRef(), '0123456789');

        $valueRef = &$memObject->getRef();
        $valueRef[3] = '_';
        $this->assertEquals($memObject->getRef(), '012_456789');

        if (version_compare(PHP_VERSION, '5.2') < 0) {
            // Skip next tests for PHP versions before 5.2
            return;
        }

        // value property
        $this->assertEquals((string)$memObject->value, '012_456789');

        $memObject->value[7] = '_';
        $this->assertEquals((string)$memObject->value, '012_456_89');

        $memObject->value = 'another value';
        $this->assertTrue($memObject->value instanceof Zend_Memory_Value);
        $this->assertEquals((string)$memObject->value, 'another value');
    }


    /**
     * tests lock()/unlock()/isLocked() functions
     */
    public function testLock()
    {
        $memoryManager  = $this->_getMemoryManager();
        $memObject      = $memoryManager->create('012345678');

        $this->assertFalse((boolean)$memObject->isLocked());

        $memObject->lock();
        $this->assertTrue((boolean)$memObject->isLocked());

        $memObject->unlock();
        $this->assertFalse((boolean)$memObject->isLocked());
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Memory_AccessControllerTest::main') {
    Zend_Memory_AccessControllerTest::main();
}
