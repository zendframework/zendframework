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

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_TwoLevelsBackendTest extends Zend_Cache_TestCommonExtendedBackend 
{

    protected $_instance;
    private $_cache_dir;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_TwoLevels', $data, $dataName);
    }

    public function setUp($notag = false)
    {
        if (!constant('TESTS_ZEND_CACHE_APC_ENABLED')) {
            $this->markTestSkipped('Zend_Cache APC tests not enabled');
        }
        @mkdir($this->getTmpDir());
        $this->_cache_dir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $slowBackend = 'File';
        $fastBackend = 'Apc';
        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $fastBackendOptions = array(
        );
        $this->_instance = new Zend_Cache_Backend_TwoLevels(array(
            'fast_backend' => $fastBackend,
            'slow_backend' => $slowBackend,
            'fast_backend_options' => $fastBackendOptions,
            'slow_backend_options' => $slowBackendOptions
        ));
        parent::setUp($notag);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        $slowBackend = 'File';
        $fastBackend = 'Apc';
        $slowBackendOptions = array(
            'cache_dir' => $this->_cache_dir
        );
        $fastBackendOptions = array(
        );
        $test = new Zend_Cache_Backend_TwoLevels(array(
            'fast_backend' => $fastBackend,
            'slow_backend' => $slowBackend,
            'fast_backend_options' => $fastBackendOptions,
            'slow_backend_options' => $slowBackendOptions
        ));
    }

}


