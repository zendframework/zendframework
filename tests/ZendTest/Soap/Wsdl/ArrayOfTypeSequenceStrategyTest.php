<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace ZendTest\Soap\Wsdl;

require_once __DIR__ . '/../TestAsset/commontypes.php';

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class ArrayOfTypeSequenceStrategyTest extends \PHPUnit_Framework_TestCase
{
    private $wsdl;
    private $strategy;

    public function setUp()
    {
        $this->strategy = new \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeSequence();
        $this->wsdl = new \Zend\Soap\Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
    }

    public function testFunctionReturningSimpleArrayOfInts()
    {
        $this->wsdl->addComplexType('int[]');

        $this->assertContains(
            '<xsd:complexType name="ArrayOfInt">'.
                '<xsd:sequence><xsd:element name="item" type="xsd:int" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence>'.
            '</xsd:complexType>',
            $this->wsdl->toXML()
        );
    }

    public function testFunctionReturningSimpleArrayOfString()
    {
        $this->wsdl->addComplexType('string[]');

        $this->assertContains(
            '<xsd:complexType name="ArrayOfString">'.
                '<xsd:sequence><xsd:element name="item" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence>'.
            '</xsd:complexType>',
            $this->wsdl->toXML()
        );
    }

    public function testFunctionReturningNestedArrayOfString()
    {
        $return = $this->wsdl->addComplexType('string[][]');
        $this->assertEquals('tns:ArrayOfArrayOfString', $return);

        $wsdl = $this->wsdl->toXML();

        // Check for ArrayOfArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="tns:ArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfString"><xsd:sequence><xsd:element name="item" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
    }

    public function testFunctionReturningMultipleNestedArrayOfType()
    {
        $return = $this->wsdl->addComplexType('string[][][]');
        $this->assertEquals('tns:ArrayOfArrayOfArrayOfString', $return);

        $wsdl = $this->wsdl->toXML();

        // Check for ArrayOfArrayOfArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="tns:ArrayOfArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="tns:ArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfString"><xsd:sequence><xsd:element name="item" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
    }


    public function testAddComplexTypeObject()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\Wsdl\SequenceTest');

        $this->assertEquals('tns:SequenceTest', $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="SequenceTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testAddComplexTypeArrayOfObject()
    {

         $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTypeA[]');

         $this->assertEquals('tns:ArrayOfComplexTypeA', $return);

         $wsdl = $this->wsdl->toXML();

         $this->assertContains(
            '<xsd:complexType name="ComplexTypeA"><xsd:all><xsd:element name="baz" type="tns:ArrayOfComplexTypeB"/></xsd:all></xsd:complexType>',
            $wsdl,
            $wsdl
         );

         $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexTypeA"><xsd:sequence><xsd:element name="item" type="tns:ComplexTypeA" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
         );
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        $this->setExpectedException('\Zend\Soap\Exception\InvalidArgumentException', 'Cannot add a complex type');
        $this->wsdl->addComplexType('ZendTest\Soap\Wsdl\UnknownClass[]');
    }
}

class SequenceTest
{
    /**
     * @var int
     */
    public $var = 5;
}
