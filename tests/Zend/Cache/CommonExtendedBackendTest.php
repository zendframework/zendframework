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

require_once 'PHPUnit/Util/Filter.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @see Zend_Cache_CommonBackendTest
 */
require_once 'CommonBackendTest.php';

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_CommonExtendedBackendTest extends Zend_Cache_CommonBackendTest {

    private $_capabilities;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name);
    }

    public function setUp($notag = false)
    {
        parent::setUp($notag);
        $this->_capabilities = $this->_instance->getCapabilities();
    }

    public function testGetFillingPercentage()
    {
        $res = $this->_instance->getFillingPercentage();
        $this->assertTrue(is_integer($res));
        $this->assertTrue($res >= 0);
        $this->assertTrue($res <= 100);
    }

    public function testGetFillingPercentageOnEmptyBackend()
    {
        $this->_instance->setDirectives(array('logging' => false)); // ???
        $this->_instance->clean(Zend_Cache::CLEANING_MODE_ALL);
        $res = $this->_instance->getFillingPercentage();
        $this->_instance->setDirectives(array('logging' => true)); // ???
        $this->assertTrue(is_integer($res));
        $this->assertTrue($res >= 0);
        $this->assertTrue($res <= 100);
    }

    public function testGetIds()
    {
        if (!($this->_capabilities['get_list'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIds();
        $this->assertTrue(count($res) == 3);
        $this->assertTrue(in_array('bar', $res));
        $this->assertTrue(in_array('bar2', $res));
        $this->assertTrue(in_array('bar3', $res));
    }

    public function testGetTags()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getTags();
        $this->assertTrue(count($res) == 4);
        $this->assertTrue(in_array('tag1', $res));
        $this->assertTrue(in_array('tag2', $res));
        $this->assertTrue(in_array('tag3', $res));
        $this->assertTrue(in_array('tag4', $res));
    }

    public function testGetIdsMatchingTags()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIdsMatchingTags(array('tag3'));
        $this->assertTrue(count($res) == 3);
        $this->assertTrue(in_array('bar', $res));
        $this->assertTrue(in_array('bar2', $res));
        $this->assertTrue(in_array('bar3', $res));
    }

    public function testGetIdsMatchingTags2()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIdsMatchingTags(array('tag2'));
        $this->assertTrue(count($res) == 1);
        $this->assertTrue(in_array('bar3', $res));
    }

    public function testGetIdsMatchingTags3()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIdsMatchingTags(array('tag9999'));
        $this->assertTrue(count($res) == 0);
    }


    public function testGetIdsMatchingTags4()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIdsMatchingTags(array('tag3', 'tag4'));
        $this->assertTrue(count($res) == 1);
        $this->assertTrue(in_array('bar', $res));
    }

    public function testGetIdsNotMatchingTags()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIdsNotMatchingTags(array('tag3'));
        $this->assertTrue(count($res) == 0);
    }

    public function testGetIdsNotMatchingTags2()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIdsNotMatchingTags(array('tag1'));
        $this->assertTrue(count($res) == 2);
        $this->assertTrue(in_array('bar', $res));
        $this->assertTrue(in_array('bar3', $res));
    }

    public function testGetIdsNotMatchingTags3()
    {
        if (!($this->_capabilities['tags'])) {
            # unsupported by this backend
            return;
        }
        $res = $this->_instance->getIdsNotMatchingTags(array('tag1', 'tag4'));
        $this->assertTrue(count($res) == 1);
        $this->assertTrue(in_array('bar3', $res));
    }

    public function testGetMetadatas($notag = false)
    {
        $res = $this->_instance->getMetadatas('bar');
        $this->assertTrue(isset($res['tags']));
        $this->assertTrue(isset($res['mtime']));
        $this->assertTrue(isset($res['expire']));
        if ($notag) {
            $this->assertTrue(count($res['tags']) == 0);
        } else {
            $this->assertTrue(count($res['tags']) == 2);
            $this->assertTrue(in_array('tag3', $res['tags']));
            $this->assertTrue(in_array('tag4', $res['tags']));
        }
        $this->assertTrue($res['expire'] > time());
        $this->assertTrue($res['mtime'] <= time());
    }

    public function testTouch()
    {
        $res = $this->_instance->getMetadatas('bar');
        $bool = $this->_instance->touch('bar', 30);
        $this->assertTrue($bool);
        $res2 = $this->_instance->getMetadatas('bar');
        $this->assertTrue(($res2['expire'] - $res['expire']) == 30);
        $this->assertTrue(($res2['mtime'] >= $res['mtime']));
    }

    public function testGetCapabilities()
    {
        $res = $this->_instance->getCapabilities();
        $this->assertTrue(isset($res['tags']));
        $this->assertTrue(isset($res['automatic_cleaning']));
        $this->assertTrue(isset($res['expired_read']));
        $this->assertTrue(isset($res['priority']));
        $this->assertTrue(isset($res['infinite_lifetime']));
        $this->assertTrue(isset($res['get_list']));
    }

}


