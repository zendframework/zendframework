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
use ZendTest\Mvc\ResponseSender\TestAsset\ConsoleResponseSender;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class ConsoleResponseSenderTest extends TestCase
{
    public function testSendResponseIgnoresInvalidResponseTypes()
    {
        $mockResponse = $this->getMockForAbstractClass('Zend\Stdlib\ResponseInterface');
        $mockSendResponseEvent = $this->getSendResponseEventMock($mockResponse);
        $responseSender = new ConsoleResponseSender();
        ob_start();
        $result = $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $this->assertEquals('', $body);
        $this->assertNull($result);
    }

    public function testSendResponseTwoTimesPrintsResponseOnceAndReturnsErrorLevel()
    {
        $returnValue = false;
        $mockResponse = $this->getMock('Zend\Console\Response');
        $mockResponse->expects($this->once())->method('getContent')->will($this->returnValue('body'));
        $mockResponse->expects($this->exactly(2))->method('getMetadata')->with('errorLevel', 0)->will($this->returnValue(0));
        $mockSendResponseEvent = $this->getSendResponseEventMock($mockResponse);
        $mockSendResponseEvent->expects($this->once())->method('setContentSent');
        $mockSendResponseEvent->expects($this->any())->method('contentSent')->will($this->returnCallback(
            function() use (&$returnValue) {
                if (false === $returnValue) {
                    $returnValue = true;
                    return false;
                }
                return true;
        }));
        $responseSender = new ConsoleResponseSender();
        ob_start();
        $result = $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $this->assertEquals('body', $body);
        $this->assertEquals(0, $result);

        ob_start();
        $result = $responseSender($mockSendResponseEvent);
        $body = ob_get_clean();
        $this->assertEquals('', $body);
        $this->assertEquals(0, $result);

    }

    protected function getSendResponseEventMock($response)
    {
        $mockSendResponseEvent = $this->getMock('Zend\Mvc\ResponseSender\SendResponseEvent', array('getResponse', 'contentSent', 'setContentSent'));
        $mockSendResponseEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));
        return $mockSendResponseEvent;
    }
}
