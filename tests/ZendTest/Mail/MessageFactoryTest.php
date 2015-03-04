<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail;

use stdClass;
use Zend\Mail\Address;
use Zend\Mail\AddressList;
use Zend\Mail\Header;
use Zend\Mail\Headers;
use Zend\Mail\Message;
use Zend\Mail\MessageFactory;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;

/**
 * @group      Zend_Mail
 */
class MessageFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructMessageWithOptions()
    {
        $options = array(
            'encoding'  => 'UTF-8',
            'from'      => 'matthew@example.com',
            'to'        => 'zf-devteam@example.com',
            'cc'        => 'zf-contributors@example.com',
            'bcc'       => 'zf-devteam@example.com',
            'reply-to'  => 'matthew@example.com',
            'sender'    => 'matthew@example.com',
            'subject'   => 'subject',
            'body'      => 'body',
        );

        $message = MessageFactory::getInstance($options);

        $this->assertInstanceOf('Zend\Mail\Message', $message);
        $this->assertEquals('UTF-8',   $message->getEncoding());
        $this->assertEquals('subject', $message->getSubject());
        $this->assertEquals('body',    $message->getBody());
        $this->assertInstanceOf('Zend\Mail\Address', $message->getSender());
        $this->assertEquals($options['sender'], $message->getSender()->getEmail());

        $getMethods = array(
            'from'      => 'getFrom',
            'to'        => 'getTo',
            'cc'        => 'getCc',
            'bcc'       => 'getBcc',
            'reply-to'  => 'getReplyTo',
        );

        foreach ($getMethods as $key => $method) {
            $value = $message->{$method}();
            $this->assertInstanceOf('Zend\Mail\AddressList', $value);
            $this->assertEquals(1, count($value));
            $this->assertTrue($value->has($options[$key]));
        }
    }

    public function testConstructMessageWithArrayValue()
    {
        $options = array(
            'from' => array('matthew@example.com' => 'Matthew'),
            'to'   => array(
                'zf-devteam@example.com',
                'zf-contributors@example.com',
            ),
        );

        $message = MessageFactory::getInstance($options);

        $from = $message->getFrom();
        $this->assertInstanceOf('Zend\Mail\AddressList', $from);
        $this->assertEquals(1, count($from));
        $this->assertTrue($from->has('matthew@example.com'));
        $this->assertEquals('Matthew', $from->get('matthew@example.com')->getName());

        $to = $message->getTo();
        $this->assertInstanceOf('Zend\Mail\AddressList', $to);
        $this->assertEquals(2, count($to));
        $this->assertTrue($to->has('zf-devteam@example.com'));
        $this->assertTrue($to->has('zf-contributors@example.com'));
    }

    public function testUnreconizeOption()
    {
        $options = array(
            'foo' => 'bar',
        );
        $mail = MessageFactory::getInstance($options);
        $this->assertInstanceOf('Zend\Mail\Message', $mail);
    }

    public function testEmptyOption()
    {
        $options = array();
        $mail = MessageFactory::getInstance();
        $this->assertInstanceOf('Zend\Mail\Message', $mail);
    }

    public function testExceptionForOptionsNotArrayOrTraversable()
    {
        $this->setExpectedException('Zend\Mail\Exception\InvalidArgumentException');
        MessageFactory::getInstance('not-an-array');
    }
}