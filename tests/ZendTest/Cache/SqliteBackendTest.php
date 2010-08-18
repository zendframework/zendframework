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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache;
use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class SqliteBackendTest extends TestCommonExtendedBackend 
{

    protected $_instance;
    private $_cache_dir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend\Cache\Backend\Sqlite', $data, $dataName);
    }

    public function setUp($notag = false)
    {
        @mkdir($this->getTmpDir());
        $this->_cache_dir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $this->_instance = new Cache\Backend\Sqlite(array(
            'cache_db_complete_path' => $this->_cache_dir . 'cache.db'
        ));
        parent::setUp($notag);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
        @unlink($this->_cache_dir . 'cache.db');
        $this->rmdir();
    }

    public function testConstructorCorrectCall()
    {
        $test = new Cache\Backend\Sqlite(array('cache_db_complete_path' => $this->_cache_dir . 'cache.db'));
    }

    public function testConstructorWithABadDBPath()
    {
        try {
            $test = new Cache\Backend\Sqlite(array('cache_db_complete_path' => '/foo/bar/lfjlqsdjfklsqd/cache.db'));
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Cache\Exception was expected but not thrown');
    }

    public function testCleanModeAllWithVacuum()
    {
        $this->_instance = new Cache\Backend\Sqlite(array(
            'cache_db_complete_path' => $this->_cache_dir . 'cache.db',
            'automatic_vacuum_factor' => 1
        ));
        parent::setUp();
        $this->assertTrue($this->_instance->clean('all'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->test('bar2'));
    }

    public function testRemoveCorrectCallWithVacuum()
    {
        $this->_instance = new Cache\Backend\Sqlite(array(
            'cache_db_complete_path' => $this->_cache_dir . 'cache.db',
            'automatic_vacuum_factor' => 1
        ));
        parent::setUp();
        $this->assertTrue($this->_instance->remove('bar'));
        $this->assertFalse($this->_instance->test('bar'));
        $this->assertFalse($this->_instance->remove('barbar'));
        $this->assertFalse($this->_instance->test('barbar'));
    }

}


