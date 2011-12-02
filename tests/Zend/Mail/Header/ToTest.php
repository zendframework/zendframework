<?php

namespace ZendTest\Mail\Header;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mail\Header\To as ToHeader;

/**
 * This test is primarily to test that AbstractAddressList headers perform 
 * header folding and MIME encoding properly.
 */
class ToTest extends TestCase
{
    public function testHeaderFoldingOccursProperly()
    {
        $header = new ToHeader();
        $list   = $header->getAddressList();
        for ($i = 0; $i < 10; $i++) {
            $list->add(uniqid() . '@zend.com');
        }
        $string = $header->getFieldValue();
        $emails = explode("\r\n ", $string);
        $this->assertEquals(10, count($emails));
    }
}
