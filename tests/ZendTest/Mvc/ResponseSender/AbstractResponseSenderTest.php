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
use Zend\Http\Response;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class AbstractResponseSenderTest extends TestCase
{

    /**
     * @runInSeparateProcess
     */
    public function testSendHeadersTwoTimesSendsOnlyOnce()
    {
        if (!function_exists('xdebug_get_headers')) {
            $this->markTestSkipped('Xdebug extension needed, skipped test');
        }
        $headers = array(
            'Content-Length: 2000',
            'Transfer-Encoding: chunked'
        );
        $response = new Response();
        $response->getHeaders()->addHeaders($headers);

        $mockSendResponseEvent = $this->getMock('Zend\Mvc\ResponseSender\SendResponseEvent', array('getResponse'));
        $mockSendResponseEvent->expects($this->any())->method('getResponse')->will($this->returnValue($response));

        $responseSender = $this->getMockForAbstractClass('Zend\Mvc\ResponseSender\AbstractResponseSender');
        $responseSender->sendHeaders($mockSendResponseEvent);
        $this->assertEquals($headers, xdebug_get_headers());
        $responseSender->sendHeaders($mockSendResponseEvent);
        $this->assertEquals(array(), xdebug_get_headers());
    }

}
