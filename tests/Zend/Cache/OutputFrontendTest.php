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

use Zend\Cache,
    Zend\Cache\Backend\TestBackend;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_OutputFrontendTest extends \PHPUnit_Framework_TestCase {

    private $_instance;

    public function setUp()
    {
        if (!$this->_instance) {
            $this->_instance = new Cache\Frontend\Output(array());
            $this->_backend = new TestBackend();
            $this->_instance->setBackend($this->_backend);
        }
    }

    public function tearDown()
    {
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        $test = new Cache\Frontend\Output(array('lifetime' => 3600, 'caching' => true));
    }

    public function testStartEndCorrectCall1()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('123'))) {
            echo('foobar');
            $this->_instance->end();
        }
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foo', $data);
    }

    public function testStartEndCorrectCall2()
    {
        ob_start();
        ob_implicit_flush(false);
        if (!($this->_instance->start('false'))) {
            echo('foobar');
            $this->_instance->end();
        }
        $data = ob_get_contents();
        ob_end_clean();
        ob_implicit_flush(true);
        $this->assertEquals('foobar', $data);
    }
}

