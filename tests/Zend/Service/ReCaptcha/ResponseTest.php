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
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** @see Zend_Service_ReCaptcha_Response */
require_once 'Zend/Service/ReCaptcha/Response.php';

/**
 * @category   Zend
 * @package    Zend_Service_ReCaptcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_ReCaptcha_ResponseTest extends PHPUnit_Framework_TestCase
{
    protected $_response = null;

    public function setUp() {
        $this->_response = new Zend_Service_ReCaptcha_Response();
    }

    public function testSetAndGet() {
        /* Set and get status */
        $status = 'true';
        $this->_response->setStatus($status);
        $this->assertSame(true, $this->_response->getStatus());

        $status = 'false';
        $this->_response->setStatus($status);
        $this->assertSame(false, $this->_response->getStatus());

        /* Set and get the error code */
        $errorCode = 'foobar';
        $this->_response->setErrorCode($errorCode);
        $this->assertSame($errorCode, $this->_response->getErrorCode());
    }

    public function testIsValid() {
        $this->_response->setStatus('true');
        $this->assertSame(true, $this->_response->isValid());
    }

    public function testIsInvalid() {
        $this->_response->setStatus('false');
        $this->assertSame(false, $this->_response->isValid());
    }

    public function testSetFromHttpResponse() {
        $status = 'false';
        $errorCode = 'foobar';
        $responseBody = $status . "\n" . $errorCode;
        $httpResponse = new Zend_Http_Response(200, array('Content-Type' => 'text/html'), $responseBody);

        $this->_response->setFromHttpResponse($httpResponse);

        $this->assertSame(false, $this->_response->getStatus());
        $this->assertSame($errorCode, $this->_response->getErrorCode());
    }

    public function testConstructor() {
        $status = 'true';
        $errorCode = 'ok';

        $response = new Zend_Service_ReCaptcha_Response($status, $errorCode);

        $this->assertSame(true, $response->getStatus());
        $this->assertSame($errorCode, $response->getErrorCode());
    }

    public function testConstructorWithHttpResponse() {
        $status = 'false';
        $errorCode = 'foobar';
        $responseBody = $status . "\n" . $errorCode;
        $httpResponse = new Zend_Http_Response(200, array('Content-Type' => 'text/html'), $responseBody);

        $response = new Zend_Service_ReCaptcha_Response(null, null, $httpResponse);

        $this->assertSame(false, $response->getStatus());
        $this->assertSame($errorCode, $response->getErrorCode());
    }
}