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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Tool\Framework\Client;
use Zend\Tool\Framework\Client\Storage;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_Client
 */
class StorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend\Tool\Framework\Client\Storage
     */
    protected $storage = null;

    public function setup()
    {
        $this->storage = new Storage();
    }

    public function getStorageDirectory()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'_files'. DIRECTORY_SEPARATOR .'storagedirectory';
    }

    public function testNoAdapterStorageIsNotEnabled()
    {
        $this->assertFalse($this->storage->isEnabled());
    }

    public function testPassingArrayToConstructor()
    {
        $directory = new Storage\Directory($this->getStorageDirectory());
        $storage = new Storage(array('adapter' => $directory));
        $this->assertTrue($storage->isEnabled());
    }
}
