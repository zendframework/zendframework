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
use Zend\Mvc\ResponseSender\SendResponseEvent;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class SendResponseEventTest extends TestCase
{
    public function testContentSentAndHeadersSent()
    {
        $mockResponse = $this->getMockForAbstractClass('Zend\Stdlib\ResponseInterface');
        $mockResponse2 = $this->getMockForAbstractClass('Zend\Stdlib\ResponseInterface');
        $event = new SendResponseEvent();
        $event->setResponse($mockResponse);
        $this->assertFalse($event->headersSent());
        $this->assertFalse($event->contentSent());
        $event->setHeadersSent();
        $event->setContentSent();
        $this->assertTrue($event->headersSent());
        $this->assertTrue($event->contentSent());
        $event->setResponse($mockResponse2);
        $this->assertFalse($event->headersSent());
        $this->assertFalse($event->contentSent());
    }
}
