<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Log\Processor;

use Zend\Log\Processor\Backtrace;

/**
 * @group      Zend_Log
 */
class BacktraceTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $processor = new Backtrace();

        $event = array(
                'timestamp'    => '',
                'priority'     => 1,
                'priorityName' => 'ALERT',
                'message'      => 'foo',
                'extra'        => array()
        );

        $event = $processor->process($event);

        $this->assertArrayHasKey('file', $event['extra']);
        $this->assertArrayHasKey('line', $event['extra']);
        $this->assertArrayHasKey('class', $event['extra']);
        $this->assertArrayHasKey('function', $event['extra']);
    }
}
