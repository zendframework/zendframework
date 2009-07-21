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
    define('PHPUnit_MAIN_METHOD', 'Zend_Memory_MemoryTest::main');
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
class Zend_Memory_MemoryTest extends PHPUnit_Framework_TestCase
{
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
     * tests the Memory Manager creation
     *
     */
    public function testCreation()
    {
        /** 'None' backend */
        $memoryManager = Zend_Memory::factory('None');
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
        unset($memoryManager);

        /** 'File' backend */
        $backendOptions = array('cache_dir' => $this->cacheDir); // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
        unset($memoryManager);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Memory_MemoryTest::main') {
    Zend_Memory_MemoryTest::main();
}
