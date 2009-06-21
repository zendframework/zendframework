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
        $backendOptions = array('cache_dir' => dirname(__FILE__) . '/_files/'); // Directory where to put the cache files
        $memoryManager = Zend_Memory::factory('File', $backendOptions);
        $this->assertTrue($memoryManager instanceof Zend_Memory_Manager);
        unset($memoryManager);
    }
}

