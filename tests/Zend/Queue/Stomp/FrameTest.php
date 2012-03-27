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
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Queue\Stomp;
use Zend\Queue\Stomp;

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */


/**
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Queue
 */
class FrameTest extends \PHPUnit_Framework_TestCase
{

    protected $body = 'hello world'; // 11 characters

    public function test_to_fromFrame()
    {
        $correct = 'SEND' . Stomp\Frame::EOL;
        $correct .= 'content-length: 11' . Stomp\Frame::EOL;
        $correct .= Stomp\Frame::EOL;
        $correct .= $this->body;
        $correct .= Stomp\Frame::END_OF_FRAME;

        $frame = new Stomp\Frame();
        $frame->setCommand('SEND');
        $frame->setBody($this->body);
        $this->assertEquals($frame->toFrame(), $correct);

        $frame = new Stomp\Frame();
        $frame->fromFrame($correct);
        $this->assertEquals($frame->getCommand(), 'SEND');
        $this->assertEquals($frame->getBody(), $this->body);

        $this->assertEquals($frame->toFrame(), "$frame");

        // fromFrame, but no body
        $correct = 'SEND' . Stomp\Frame::EOL;
        $correct .= 'testing: 11' . Stomp\Frame::EOL;
        $correct .= Stomp\Frame::EOL;
        $correct .= Stomp\Frame::END_OF_FRAME;
        $frame->fromFrame($correct);
        $this->assertEquals($frame->getHeader('testing'), 11);
    }

    public function test_setHeaders()
    {
        $frame = new Stomp\Frame();
        $frame->setHeaders(array('testing' => 1));
        $this->assertEquals(1, $frame->getHeader('testing'));
    }

    public function test_parameters()
    {
        $frame = new Stomp\Frame();

        try {
            $frame->setAutoContentLength(array());
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $frame->setHeader(array(), 1);
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $frame->setHeader('testing', array());
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $frame->getHeader(array());
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $frame->setBody(array());
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $frame->setCommand(array());
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $frame->toFrame();
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }

        try {
            $frame->fromFrame(array());
            $this->fail('Exception should have been thrown');
        } catch(\Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_constant()
    {
        $this->assertTrue(is_string(Stomp\Frame::END_OF_FRAME));
        $this->assertTrue(is_string(Stomp\Frame::CONTENT_LENGTH));
        $this->assertTrue(is_string(Stomp\Frame::EOL));
    }

}
