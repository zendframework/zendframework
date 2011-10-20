<?php

namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase,
    stdClass,
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

    public function testAllowsRecursiveEscapingOfArrays()
    {
        $original = array(
            'foo' => '<b>bar</b>',
            'baz' => array(
                '<em>bat</em>',
                'second' => array(
                    '<i>third</i>',
                ),
            ),
        );
        $expected = array(
            'foo' => '&lt;b&gt;bar&lt;/b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;/em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;/i&gt;',
                ),
            ),
        );
        $test = $this->helper->__invoke($original, EscapeHelper::RECURSE_ARRAY);
        $this->assertEquals($expected, $test);
    }

    public function testWillCastObjectsToStringsBeforeEscaping()
    {
        $object = new TestAsset\Stringified;
        $test = $this->helper->__invoke($object);
        $this->assertEquals(get_class($object), $test);
    }

    public function testCanRecurseObjectImplementingToArray()
    {
        $original = array(
            'foo' => '<b>bar</b>',
            'baz' => array(
                '<em>bat</em>',
                'second' => array(
                    '<i>third</i>',
                ),
            ),
        );
        $object = new TestAsset\ToArray();
        $object->array = $original;

        $expected = array(
            'foo' => '&lt;b&gt;bar&lt;/b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;/em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;/i&gt;',
                ),
            ),
        );
        $test = $this->helper->__invoke($object, EscapeHelper::RECURSE_OBJECT);
        $this->assertEquals($expected, $test);
    }

    public function testCanRecurseObjectProperties()
    {
        $original = array(
            'foo' => '<b>bar</b>',
            'baz' => array(
                '<em>bat</em>',
                'second' => array(
                    '<i>third</i>',
                ),
            ),
        );
        $object = new stdClass();
        foreach ($original as $key => $value) {
            $object->$key = $value;
        }

        $expected = array(
            'foo' => '&lt;b&gt;bar&lt;/b&gt;',
            'baz' => array(
                '&lt;em&gt;bat&lt;/em&gt;',
                'second' => array(
                    '&lt;i&gt;third&lt;/i&gt;',
                ),
            ),
        );
        $test = $this->helper->__invoke($object, EscapeHelper::RECURSE_OBJECT);
        $this->assertEquals($expected, $test);
    }
}
