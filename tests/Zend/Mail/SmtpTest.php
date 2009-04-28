<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Protocol_Smtp
 */
require_once 'Zend/Mail/Protocol/Smtp.php';


/**
 * Zend_Mail_Transport_Smtp
 */
require_once 'Zend/Mail/Transport/Smtp.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
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
