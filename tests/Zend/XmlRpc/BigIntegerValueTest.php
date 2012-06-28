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
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\XmlRpc;
use Zend\XmlRpc\AbstractValue;
use Zend\XmlRpc\Value\BigInteger;
use Zend\XmlRpc\Exception;
use Zend\XmlRpc\Generator\GeneratorInterface as Generator;
use Zend\Math\BigInteger as MathBigInteger;

/**
 * Test case for Zend_XmlRpc_Value
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class BigIntegerValueTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        try {
            $XmlRpcBigInteger = new BigInteger(0);
        } catch (\Zend\Math\Exception $e) {
            $this->markTestSkipped($e->getMessage());
        }
    }

    // BigInteger

    /**
     * @group ZF-6445
     * @group ZF-8623
     */
    public function testBigIntegerGetValue()
    {
        $bigIntegerValue = (string)(PHP_INT_MAX + 42);
        $bigInteger = new BigInteger($bigIntegerValue);
        $this->assertSame($bigIntegerValue, $bigInteger->getValue());
    }

    /**
     * @group ZF-6445
     */
    public function testBigIntegerGetType()
    {
        $bigIntegerValue = (string)(PHP_INT_MAX + 42);
        $bigInteger = new BigInteger($bigIntegerValue);
        $this->assertSame(AbstractValue::XMLRPC_TYPE_I8, $bigInteger->getType());
    }

    /**
     * @group ZF-6445
     */
    public function testBigIntegerGeneratedXml()
    {
        $bigIntegerValue = (string)(PHP_INT_MAX + 42);
        $bigInteger = new BigInteger($bigIntegerValue);

        $this->assertEquals(
            '<value><i8>' . $bigIntegerValue . '</i8></value>',
            $bigInteger->saveXml()
        );
    }

    /**
     * @group ZF-6445
     * @dataProvider \ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarschalBigIntegerFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);

        $bigIntegerValue = (string)(PHP_INT_MAX + 42);
        $bigInteger = new BigInteger($bigIntegerValue);
        $bigIntegerXml = '<value><i8>' . $bigIntegerValue . '</i8></value>';

        $value = AbstractValue::getXmlRpcValue(
            $bigIntegerXml,
            AbstractValue::XML_STRING
        );

        $this->assertSame($bigIntegerValue, $value->getValue());
        $this->assertEquals(AbstractValue::XMLRPC_TYPE_I8, $value->getType());
        $this->assertEquals($this->wrapXml($bigIntegerXml), $value->saveXml());
    }

    /**
     * @group ZF-6445
     * @dataProvider \ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarschalBigIntegerFromApacheXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);

        $bigIntegerValue = (string)(PHP_INT_MAX + 42);
        $bigInteger = new BigInteger($bigIntegerValue);
        $bigIntegerXml = '<value><ex:i8 xmlns:ex="http://ws.apache.org/xmlrpc/namespaces/extensions">' . $bigIntegerValue . '</ex:i8></value>';

        $value = AbstractValue::getXmlRpcValue(
            $bigIntegerXml,
            AbstractValue::XML_STRING
        );

        $this->assertSame($bigIntegerValue, $value->getValue());
        $this->assertEquals(AbstractValue::XMLRPC_TYPE_I8, $value->getType());
        $this->assertEquals($this->wrapXml($bigIntegerXml), $value->saveXml());
    }

    /**
     * @group ZF-6445
     */
    public function testMarshalBigIntegerFromNative()
    {
        $bigIntegerValue = (string)(PHP_INT_MAX + 42);

        $value = AbstractValue::getXmlRpcValue(
            $bigIntegerValue,
            AbstractValue::XMLRPC_TYPE_I8
        );

        $this->assertEquals(AbstractValue::XMLRPC_TYPE_I8, $value->getType());
        $this->assertSame($bigIntegerValue, $value->getValue());
    }

    // Custom Assertions and Helper Methods

    public function wrapXml($xml)
    {
        return $xml . "\n";
    }
}

