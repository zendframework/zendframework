<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace ZendTest\XmlRpc;

use Zend\XmlRpc\AbstractValue;
use Zend\XmlRpc\Value\BigInteger;
use Zend\XmlRpc\Exception;
use Zend\XmlRpc\Generator\GeneratorInterface as Generator;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class BigIntegerValueTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (extension_loaded('gmp')) {
            $this->markTestSkipped('gmp causes test failure');
        }
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
