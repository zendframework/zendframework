<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Http\Header;

use Zend\Http\Header\Upgrade;

class UpgradeTest extends \PHPUnit_Framework_TestCase
{
    public function testUpgradeFromStringCreatesValidUpgradeHeader()
    {
        $upgradeHeader = Upgrade::fromString('Upgrade: xxx');
        $this->assertInstanceOf('Zend\Http\Header\HeaderInterface', $upgradeHeader);
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

    /** Implementation specific tests here */

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaFromString()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = Upgrade::fromString("Upgrade: xxx\r\n\r\nevilContent");
    }

    /**
     * @see http://en.wikipedia.org/wiki/HTTP_response_splitting
     * @group ZF2015-04
     */
    public function testPreventsCRLFAttackViaConstructor()
    {
        $this->setExpectedException('Zend\Http\Header\Exception\InvalidArgumentException');
        $header = new Upgrade("xxx\r\n\r\nevilContent");
    }
}
