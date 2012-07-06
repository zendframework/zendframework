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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;
use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

        $this->temporaryDbaFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('zfcache_dba_');
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
