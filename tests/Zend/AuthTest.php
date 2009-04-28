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
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Test helper
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * Start output buffering to prevent problems using session
 */
ob_start();


/**
 * @see Zend_Auth
 */
require_once 'Zend/Auth.php';


/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_AuthTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the Singleton pattern is implemented properly
     *
     * @return void
     */
    public function testSingleton()
    {
        $this->assertTrue(Zend_Auth::getInstance() instanceof Zend_Auth);
        $this->assertEquals(Zend_Auth::getInstance(), Zend_Auth::getInstance());
    }

    /**
     * Ensures that getStorage() returns Zend_Auth_Storage_Session
     *
     * @return void
     */
    public function testGetStorage()
    {
        $this->assertTrue(Zend_Auth::getInstance()->getStorage() instanceof Zend_Auth_Storage_Session);
    }

    /**
     * Ensures expected behavior for successful authentication
     *
     * @return void
     */
    public function testAuthenticate()
    {
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate(new Zend_AuthTest_Success_Adapter());
        $this->assertTrue($result instanceof Zend_Auth_Result);
        $this->assertTrue($auth->hasIdentity());
        $this->assertEquals('someIdentity', $auth->getIdentity());
    }

    /**
     * Ensures expected behavior for clearIdentity()
     *
     * @return void
     */
    public function testClearIdentity()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->assertFalse($auth->hasIdentity());
        $this->assertEquals(null, $auth->getIdentity());
    }
}


class Zend_AuthTest_Success_Adapter implements Zend_Auth_Adapter_Interface
{
    public function authenticate()
    {
        return new Zend_Auth_Result(true, 'someIdentity');
    }
}
