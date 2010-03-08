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
 * Zend_Cache
 */

/**
 * Common tests for backends
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_ApcBackendTest extends Zend_Cache_CommonExtendedBackendTest {

    protected $_instance;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct('Zend_Cache_Backend_Apc', $data, $dataName);
    }

    public function setUp($notag = true)
    {
        $this->_instance = new Zend_Cache_Backend_Apc(array());
        parent::setUp($notag);
    }

    public function tearDown()
    {
        parent::tearDown();
        unset($this->_instance);
    }

    public function testConstructorCorrectCall()
    {
        $test = new Zend_Cache_Backend_Apc();
    }

    public function testCleanModeOld() {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('old');
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testCleanModeMatchingTags() {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('matchingTag', array('tag1'));
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testCleanModeNotMatchingTags() {
        $this->_instance->setDirectives(array('logging' => false));
        $this->_instance->clean('notMatchingTag', array('tag1'));
        // do nothing, just to see if an error occured
        $this->_instance->setDirectives(array('logging' => true));
    }

    // Because of limitations of this backend...
    public function testGetWithAnExpiredCacheId() {}
    public function testCleanModeMatchingTags2() {}
    public function testCleanModeNotMatchingTags2() {}
    public function testCleanModeNotMatchingTags3() {}
    public function testGetIdsMatchingTags() {}
    public function testGetIdsMatchingTags2() {}
    public function testGetIdsMatchingTags3() {}
    public function testGetIdsMatchingTags4() {}
    public function testGetIdsNotMatchingTags() {}
    public function testGetIdsNotMatchingTags2() {}
    public function testGetIdsNotMatchingTags3() {}
    public function testGetTags() {}

    public function testSaveCorrectCall()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveCorrectCall();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testSaveWithNullLifeTime()
    {
        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveWithNullLifeTime();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testSaveWithSpecificLifeTime()
    {

        $this->_instance->setDirectives(array('logging' => false));
        parent::testSaveWithSpecificLifeTime();
        $this->_instance->setDirectives(array('logging' => true));
    }

    public function testGetMetadatas($notag = true)
    {
        parent::testGetMetadatas($notag);
    }

}


