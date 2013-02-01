<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\ResponseSender;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\ResponseSender\SimpleStreamResponseSender;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class StreamResponseSenderTest extends TestCase
{
    public function testSendResponseIgnoresInvalidResponseTypes()
    {
        $mockResponse = $this->getMockForAbstractClass('Zend\Stdlib\ResponseInterface');
        $mockSendResponseEvent = $this->getSendResponseEventMock($mockResponse);
        $responseSender = new SimpleStreamResponseSender();
        ob_start();
        $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $this->assertEquals('', $body);
    }

    public function testSendResponseTwoTimesPrintsResponseOnlyOnce()
    {
        $file = fopen(__DIR__ . '/TestAsset/sample-stream-file.txt', 'r');
        $mockResponse = $this->getMock('Zend\Http\Response\Stream');
        $mockResponse->expects($this->once())->method('getStream')->will($this->returnValue($file));
        $mockSendResponseEvent = $this->getSendResponseEventMock($mockResponse);
        $responseSender = new SimpleStreamResponseSender();
        ob_start();
        $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $expected = file_get_contents(__DIR__ . '/TestAsset/sample-stream-file.txt');
        $this->assertEquals($expected, $body);

        ob_start();
        $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $this->assertEquals('', $body);
    }

    protected function getSendResponseEventMock($response)
    {
        $mockSendResponseEvent = $this->getMock('Zend\Mvc\ResponseSender\SendResponseEvent', array('getResponse'));
        $mockSendResponseEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        return $mockSendResponseEvent;
    }
}
