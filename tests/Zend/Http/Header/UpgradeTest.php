<?php

namespace ZendTest\Http\Header;

use Zend\Http\Header\Upgrade;

class UpgradeTest extends \PHPUnit_Framework_TestCase
{

    public function testUpgradeFromStringCreatesValidUpgradeHeader()
    {
        $upgradeHeader = Upgrade::fromString('Upgrade: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderDescription', $upgradeHeader);
        $this->assertInstanceOf('Zend\Http\Header\Upgrade', $upgradeHeader);
    }

    public function testUpgradeGetFieldNameReturnsHeaderName()
    {
        $upgradeHeader = new Upgrade();
        $this->assertEquals('Upgrade', $upgradeHeader->getFieldName());
    }

    public function testUpgradeGetFieldValueReturnsProperValue()
    {
        $this->markTestIncomplete('Upgrade needs to be completed');

        $upgradeHeader = new Upgrade();
        $this->assertEquals('xxx', $upgradeHeader->getFieldValue());
    }

    public function testUpgradeToStringReturnsHeaderFormattedString()
    {
        $this->markTestIncomplete('Upgrade needs to be completed');

        $upgradeHeader = new Upgrade();

        // @todo set some values, then test output
        $this->assertEmpty('Upgrade: xxx', $upgradeHeader->toString());
    }

    /** Implmentation specific tests here */
    
}

