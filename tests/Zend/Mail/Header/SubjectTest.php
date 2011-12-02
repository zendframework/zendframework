<?php

namespace ZendTest\Mail\Header;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mail\Header\Subject;

class SubjectTest extends TestCase
{
    public function testHeaderFolding()
    {
        $string  = str_repeat('foobarblahblahblah baz bat', 10);
        $subject = new Subject();
        $subject->setSubject($string);

        $expected = wordwrap($string, 78, "\r\n ");
        $test     = $subject->getFieldValue();
        $this->assertEquals($expected, $test);
    }
}
