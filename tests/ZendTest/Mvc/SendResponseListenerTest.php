<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Mvc\SendResponseListener;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage UnitTest
 */
class SendResponseListenerTest extends TestCase
{

    public function testEventManagerIdentifiers()
    {
        $listener = new SendResponseListener();
        $identifiers = $listener->getEventManager()->getIdentifiers();
        $expected    = array('Zend\Mvc\SendResponseListener');
        $this->assertEquals($expected, array_values($identifiers));
    }

    public function testSendResponseTriggersSendResponseEvent()
    {
        $listener = new SendResponseListener();
        $result = array();
        $listener->getEventManager()->attach('sendResponse', function($e) use (&$result) {
            $result['target'] = $e->getTarget();
            $result['response'] = $e->getResponse();
        }, 10000);
        $mockResponse = $this->getMockForAbstractClass('Zend\Stdlib\ResponseInterface');
        $mockMvcEvent = $this->getMock('Zend\Mvc\MvcEvent', $methods = array('getResponse'));
        $mockMvcEvent->expects($this->any())->method('getResponse')->will($this->returnValue($mockResponse));
        $listener->sendResponse($mockMvcEvent);
        $expected = array(
            'target' => $listener,
            'response' => $mockResponse
        );
        $this->assertEquals($expected, $result);
    }
}
