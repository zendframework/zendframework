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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Mail_Protocol_Smtp
 */


/**
 * Zend_Mail_Transport_Smtp
 */


/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class Zend_Mail_SmtpTest extends PHPUnit_Framework_TestCase
{
    protected $_params;
    protected $_transport;
    protected $_connection;

    public function setUp()
    {
        $this->_params = array('host'     => TESTS_ZEND_MAIL_SMTP_HOST,
                               'port'     => TESTS_ZEND_MAIL_SMTP_PORT,
                               'username' => TESTS_ZEND_MAIL_SMTP_USER,
                               'password' => TESTS_ZEND_MAIL_SMTP_PASSWORD,
                               'auth'     => TESTS_ZEND_MAIL_SMTP_AUTH);
    }

    public function testTransportSetup()
    {
        try {
            $this->_transport = new Zend_Mail_Transport_Smtp($this->_params['host'], $this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while creating smtp transport');
        }

        try {
            $this->_connection = new Zend_Mail_Protocol_Smtp($this->_params['host'], $this->_params['port']);
            $this->_transport->setConnection($this->_connection);
        } catch (Exception $e) {
            $this->fail('exception raised while setting smtp transport connection');
        }

        $this->_connection = $this->_transport->getConnection();
        if (!($this->_connection instanceof Zend_Mail_Protocol_Abstract)) {
            $this->fail('smtp transport connection is not an instance of protocol abstract');
        }
    }
}
