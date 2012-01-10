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
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\OpenId;

use Zend\OpenId,
    Zend\OpenId\Provider\User\Session;


/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_OpenId
 */
class SessionTest extends \PHPUnit_Framework_TestCase
{
    const USER1     = "test_user1";
    const USER2     = "test_user2";

    private $_user;

    public function __construct()
    {
        $this->_user1 = new Session();
        $this->_user2 = new Session(new \Zend\Session\Container("openid2"));
    }

    /**
     * testing getLoggedInUser
     *
     */
    public function testGetLoggedInUser()
    {
        $user = $this->_user1;
        $user->delLoggedInUser();
        $this->assertTrue( $user->setLoggedInUser(self::USER1) );
        $this->assertSame( self::USER1, $user->getLoggedInUser() );
        $this->assertTrue( $user->setLoggedInUser(self::USER2) );
        $this->assertSame( self::USER2, $user->getLoggedInUser() );
        $this->assertTrue( $user->delLoggedInUser() );
        $this->assertFalse( $user->getLoggedInUser());

        $user = $this->_user2;
        $user->delLoggedInUser();
        $this->assertTrue( $user->setLoggedInUser(self::USER1) );
        $this->assertSame( self::USER1, $user->getLoggedInUser() );
        $this->assertTrue( $user->setLoggedInUser(self::USER2) );
        $this->assertSame( self::USER2, $user->getLoggedInUser() );
        $this->assertTrue( $user->delLoggedInUser() );
        $this->assertFalse( $user->getLoggedInUser());
    }
}
