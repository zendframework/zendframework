<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\ResponseSender;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\ResponseSender\PhpEnvironmentResponseSender;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class PhpEnvironmentResponseSenderTest extends TestCase
{
    public function testSendResponseIgnoresInvalidResponseTypes()
    {
        $mockResponse = $this->getMockForAbstractClass('Zend\Stdlib\ResponseInterface');
        $mockSendResponseEvent = $this->getSendResponseEventMock($mockResponse);
        $responseSender = new PhpEnvironmentResponseSender();
        ob_start();
        $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $this->assertEquals('', $body);
    }

    public function testSendResponsePrintsResponse()
    {
        $mockResponse = $this->getMock('Zend\Http\PhpEnvironment\Response');
        $mockResponse->expects($this->once())->method('getContent')->will($this->returnValue('body'));
        $mockSendResponseEvent = $this->getSendResponseEventMock($mockResponse);
        $responseSender = new PhpEnvironmentResponseSender();
        ob_start();
        $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $this->assertEquals('body', $body);
    }

    protected function getSendResponseEventMock($response)
    {
        $mockSendResponseEvent = $this->getMock('Zend\Mvc\ResponseSender\SendResponseEvent', array('getResponse'));
        $mockSendResponseEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        return $mockSendResponseEvent;
    }
}
