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

require_once __DIR__."/../TestAsset/commontypes.php";

use Zend\Soap\Wsdl;
use Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex;

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class ArrayOfTypeComplexStrategyTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Zend\Soap\Wsdl */
    private $wsdl;

    /** @var \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex */
    private $strategy;

    public function setUp()
    {
        $this->strategy = new ArrayOfTypeComplex();
        $this->wsdl = new Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
    }

    public function testNestingObjectsDeepMakesNoSenseThrowingException()
    {
        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'ArrayOfTypeComplex cannot return nested ArrayOfObject deeper than one level');
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTest[][]');
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException', 'Cannot add a complex type \ZendTest\Soap\TestAsset\UnknownClass that is not an object or where class');
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\UnknownClass[]');
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfSimpleObject()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTest[]');
        $this->assertEquals("tns:ArrayOfComplexTest", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexTest"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:ComplexTest[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl,
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testThatOverridingStrategyIsReset()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTest[]');
        $this->assertEquals("tns:ArrayOfComplexTest", $return);
        // $this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof \Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplexStrategy);

        $wsdl = $this->wsdl->toXML();
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfComplexObjects()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:ComplexObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl,
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexObjectStructure"><xsd:all><xsd:element name="boolean" type="xsd:boolean"/><xsd:element name="string" type="xsd:string"/><xsd:element name="int" type="xsd:int"/><xsd:element name="array" type="soap-enc:Array"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testArrayOfObjectWithObject()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectWithObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfComplexObjectWithObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexObjectWithObjectStructure"><xsd:all><xsd:element name="object" type="tns:ComplexTest" nillable="true"/></xsd:all></xsd:complexType>',
            $wsdl,
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    /**
     * @group ZF-4937
     */
    public function testAddingTypesMultipleTimesIsSavedOnlyOnce()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ComplexTest">')
        );
    }

    /**
     * @group ZF-4937
     */
    public function testAddingSingularThenArrayTypeIsRecognizedCorretly()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure');
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ComplexTest">')
        );
    }

    /**
     * @group ZF-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategy()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTypeA');
        $wsdl = $this->wsdl->toXml();
        $this->assertTrue(is_string($wsdl)); // no exception was thrown
    }

    /**
     * @group ZF-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategyAndAddsAllTypesRecursivly()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTypeA');
        $wsdl = $this->wsdl->toXml();

        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ComplexTypeA">'),
            'No definition of complex type A found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfComplexTypeB">'),
            'No definition of complex type B array found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:ComplexTypeB[]"'),
            'No usage of Complex Type B array found.'
        );
    }
}
