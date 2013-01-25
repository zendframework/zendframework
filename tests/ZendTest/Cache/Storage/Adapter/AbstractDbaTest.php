<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
abstract class AbstractDbaTest extends CommonAdapterTest
{

    protected $handler;
    protected $temporaryDbaFile;

    public function setUp()
    {
        if (!extension_loaded('dba')) {
            try {
                new Cache\Storage\Adapter\Dba();
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped("Missing ext/dba");
            }
        }

        if (!in_array($this->handler, dba_handlers())) {
            try {
                new Cache\Storage\Adapter\DbaOptions(array('handler' => $this->handler));
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped("Missing ext/dba handler '{$this->handler}'");
            }
        }

        $this->temporaryDbaFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('zfcache_dba_') . '.' . $this->handler;
        $this->_options = new Cache\Storage\Adapter\DbaOptions(array(
            'pathname' => $this->temporaryDbaFile,
            'handler'  => $this->handler,
        ));

        $this->_storage = new Cache\Storage\Adapter\Dba();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        $this->_storage = null;

        if (file_exists($this->temporaryDbaFile)) {
            unlink($this->temporaryDbaFile);
        }

        parent::tearDown();
    }
}
