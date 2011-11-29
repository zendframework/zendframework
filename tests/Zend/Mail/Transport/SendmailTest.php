<?php

namespace ZendTest\Mail\Transport;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mail\Message,
    Zend\Mail\Transport,
    Zend\Mail\Transport\Sendmail;

class SendmailTest extends TestCase
{
    public $transport;
    public $to;
    public $subject;
    public $message;
    public $additional_headers;
    public $additional_parameters;

    public function setUp()
    {
        $this->transport = new Sendmail();
        $self = $this;
        $this->transport->setCallable(function($to, $subject, $message, $additional_headers, $additional_parameters = null) use ($self) {
            $self->to                    = $to;
            $self->subject               = $subject;
            $self->message               = $message;
            $self->additional_headers    = $additional_headers;
            $self->additional_parameters = $additional_parameters;
        });
        $this->operating_system      = strtoupper(substr(PHP_OS, 0, 3));
    }

    public function tearDown()
    {
        $this->to                    = null;
        $this->subject               = null;
        $this->message               = null;
        $this->additional_headers    = null;
        $this->additional_parameters = null;
    }

    public function getMessage()
    {
        $message = new Message();
        $message->addTo('zf-devteam@zend.com', 'ZF DevTeam')
                ->addCc('matthew@zend.com')
                ->addBcc('zf-crteam@lists.zend.com', 'CR-Team, ZF Project')
                ->addFrom(array(
                    'zf-devteam@zend.com',
                    'Matthew' => 'matthew@zend.com',
                ))
                ->setSender('ralph.schindler@zend.com', 'Ralph Schindler')
                ->setSubject('Testing Zend\Mail\Transport\Sendmail')
                ->setBody('This is only a test.');
        $message->headers()->addHeaders(array(
            'X-Foo-Bar' => 'Matthew',
        ));
        return $message;
    }

    public function testReceivesMailArtifactsOnUnixSystems()
    {
        if ($this->operating_system == 'WIN') {
            $this->markTestSkipped('This test is *nix-specific');
        }

        $message = $this->getMessage();
        $this->transport->setParameters('-R hdrs');

        $this->transport->send($message);
        $this->assertEquals('ZF DevTeam <zf-devteam@zend.com>', $this->to);
        $this->assertEquals('Testing Zend\Mail\Transport\Sendmail', $this->subject);
        $this->assertEquals('This is only a test.', trim($this->message));
        $this->assertNotContains("To: ZF DevTeam <zf-devteam@zend.com>\r\n", $this->additional_headers);
        $this->assertContains("Cc: matthew@zend.com\r\n", $this->additional_headers);
        $this->assertContains("Bcc: \"CR-Team, ZF Project\" <zf-crteam@lists.zend.com>\r\n", $this->additional_headers);
        $this->assertContains("From: zf-devteam@zend.com, Matthew <matthew@zend.com>\r\n", $this->additional_headers);
        $this->assertContains("X-Foo-Bar: Matthew\r\n", $this->additional_headers);
        $this->assertContains("Sender: Ralph Schindler <ralph.schindler@zend.com>\r\n", $this->additional_headers);
        $this->assertEquals('-R hdrs -r ralph.schindler@zend.com', $this->additional_parameters);
    }

    public function testReceivesMailArtifactsOnWindowsSystems()
    {
        if ($this->operating_system != 'WIN') {
            $this->markTestSkipped('This test is Windows-specific');
        }

        $message = $this->getMessage();

        $this->transport->send($message);
        $this->assertEquals('zf-devteam@zend.com', $this->to);
        $this->assertEquals('Testing Zend\Mail\Transport\Sendmail', $this->subject);
        $this->assertEquals('This is only a test.', trim($this->message));
        $this->assertContains("To: ZF DevTeam <zf-devteam@zend.com>\r\n", $this->additional_headers);
        $this->assertContains("Cc: matthew@zend.com\r\n", $this->additional_headers);
        $this->assertContains("Bcc: \"CR-Team, ZF Project\" <zf-crteam@lists.zend.com>\r\n", $this->additional_headers);
        $this->assertContains("From: zf-devteam@zend.com, Matthew <matthew@zend.com>\r\n", $this->additional_headers);
        $this->assertContains("X-Foo-Bar: Matthew\r\n", $this->additional_headers);
        $this->assertContains("Sender: Ralph Schindler <ralph.schindler@zend.com>\r\n", $this->additional_headers);
        $this->assertNull($this->additional_parameters);
    }
}
