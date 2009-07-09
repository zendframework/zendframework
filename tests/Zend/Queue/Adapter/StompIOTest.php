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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 13626 2009-01-14 18:24:57Z matthew $
 */

/*
 * The adapter test class provides a universal test class for all of the
 * abstract methods.
 *
 * All methods marked not supported are explictly checked for for throwing
 * an exception.
 */

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** TestHelp.php */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend/Queue/Adapter/Stomp/IO.php */
require_once 'Zend/Queue/Adapter/Stomp/IO.php';

class Zend_Queue_Adapter_StompIOTest extends PHPUnit_Framework_TestCase
{
    protected $config = array(
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 61613,
    );

    protected $io = false;

    protected $body = 'hello world'; // 11 characters

    public function setUp()
    {
        if ( $this->io === false ) {
            $this->io = new Zend_Queue_Adapter_Stomp_IO($this->config);
        }
    }

    public function test_constructFrame()
    {
        $frame = $this->io->constructFrame('SEND', array(), $this->body);

        $correct = 'SEND' . Zend_Queue_Adapter_Stomp_IO::EOL;
        $correct .= 'content-length: 11' . Zend_Queue_Adapter_Stomp_IO::EOL;
        $correct .= Zend_Queue_Adapter_Stomp_IO::EOL;
        $correct .= $this->body;
        $correct .= Zend_Queue_Adapter_Stomp_IO::END_OF_FRAME;

        $this->assertEquals($frame, $correct);

        // validate parameters
        try {
            $frame = $this->io->constructFrame(array());
            $this->fail('constructFrame() should have failed $action as an array');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try { // this won't test, I think because phpunit suppresses the error
            $frame = $this->io->constructFrame('SEND', 'string');
            $this->fail('constructFrame() should have failed $headers as a string');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }

        try { // this won't test, I think because phpunit suppresses the error
            $frame = $this->io->constructFrame('SEND', array(), array());
            $this->fail('constructFrame() should have failed $body as a array');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_deconstructFrame()
    {
        $correct = array(
            'headers' => array(),
            'body' => $this->body,
            'command' => 'SEND'
        );

        $frame = $this->io->constructFrame($correct['command'], array(), $correct['body']);
        $frame = $this->io->deconstructFrame($frame);
        $this->assertEquals($correct['command'], $frame['command']);
        $this->assertEquals($correct['body'], $frame['body']);

        // validate parameters
        try {
            $frame = $this->io->deconstructFrame(array());
            $this->fail('deconstructFrame() should have failed with an array');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_write_read()
    {
        $frame = $this->io->constructFrame('CONNECT');
        $frame = $this->io->writeAndRead($frame);

        $headers = array(
            'destination' => '/queue/testing',
            'ack' => 'auto'
        );

        $frame = $this->io->constructFrame('SEND', $headers, $this->body);
        $this->io->write($frame);

        $frame = $this->io->constructFrame('SUBSCRIBE', $headers);
        $this->io->write($frame);

        $frame = $this->io->read();
        $frame = $this->io->deconstructFrame($frame);

        $this->assertEquals($this->body, $frame['body']);

        // validate parameters
        try {
            $frame = $this->io->write(array());
            $this->fail('write() should have failed with an array');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_open_close()
    {
        try {
            $obj = new Zend_Queue_Adapter_Stomp_IO($this->config);
        } catch (Exception $e) {
            $this->fail('failed to create Zend_Queue_Adapter_Stomp_IO object:' . $e->getMessage());
        }

        try {
            $obj->close();
        } catch (Exception $e) {
            $this->fail('failed to close Zend_Queue_Adapter_Stomp_IO object:' . $e->getMessage());
        }

        // validate parameters
        $config = array(
            'scheme' => 'tcp',
            'host' => 'blahblahb asfd',
            'port' => '0'
        );

        try {
            $frame = $this->io->open($config);
            $this->fail('open() should have failed with an invalid configuration');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_constant()
    {
        $this->assertTrue(is_string(Zend_Queue_Adapter_Stomp_IO::END_OF_FRAME));
        $this->assertTrue(is_string(Zend_Queue_Adapter_Stomp_IO::CONTENT_LENGTH));
        $this->assertTrue(is_string(Zend_Queue_Adapter_Stomp_IO::EOL));
    }

    public function test_checkSocket()
    {
        $this->assertTrue($this->io->checkSocket());
        $this->io->close();

        try {
            $this->io->checkSocket();
            $this->fail('checkSocket() should have failed on a fclose($socket)');
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }
}
