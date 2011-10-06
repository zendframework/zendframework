<?php

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Helper\Escape as EscapeHelper;

class EscapeTest extends TestCase
{
    public function setUp()
    {
        $this->helper = new EscapeHelper;
    }

    public function testUsesUtf8EncodingByDefault()
    {
        $this->assertEquals('UTF-8', $this->helper->getEncoding());
    }

    public function testEncodingIsMutable()
    {
        $this->helper->setEncoding('ASCII');
        $this->assertEquals('ASCII', $this->helper->getEncoding());
    }

    public function testDefaultCallbackIsDefined()
    {
        $callback = $this->helper->getCallback();
        $this->assertTrue(is_callable($callback));
    }

    public function testCallbackIsMutable()
    {
        $this->helper->setCallback('strip_tags');
        $this->assertEquals('strip_tags', $this->helper->getCallback());
    }

    public function testSettingInvalidCallbackRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception');
        $this->helper->setCallback(3.1415);
    }

    public function testInvokingCallbackEscapesText()
    {
        $text     = 'Hey! <b>This is some Text!</b> Yo!';
        $expected = htmlspecialchars($text, ENT_COMPAT, 'UTF-8', false);
        $test     = $this->helper->__invoke($text);
        $this->assertEquals($expected, $test);
    }
}
