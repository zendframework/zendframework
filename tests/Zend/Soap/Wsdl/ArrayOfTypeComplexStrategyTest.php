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
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Soap_Wsdl */

/** Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex */

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class Zend_Soap_Wsdl_ArrayOfTypeComplexStrategyTest extends PHPUnit_Framework_TestCase
{
    private $wsdl;
    private $strategy;

    public function setUp()
    {
        $this->strategy = new Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex();
        $this->wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
    }

    public function testNestingObjectsDeepMakesNoSenseThrowingException()
    {
        try {
            $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[][]');
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        try {
            $this->wsdl->addComplexType('Zend_Soap_Wsdl_UnknownClass[]');
        } catch(Zend_Soap_Wsdl_Exception $e) {

        }
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfSimpleObject()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexTest", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexTest"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexTest[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testThatOverridingStrategyIsReset()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexTest[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexTest", $return);
        #$this->assertTrue($this->wsdl->getComplexTypeStrategy() instanceof Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplexStrategy);

        $wsdl = $this->wsdl->toXML();
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfComplexObjects()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectStructure[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexObjectStructure"><xsd:all><xsd:element name="boolean" type="xsd:boolean"/><xsd:element name="string" type="xsd:string"/><xsd:element name="int" type="xsd:int"/><xsd:element name="array" type="soap-enc:Array"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    public function testArrayOfObjectWithObject()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');
        $this->assertEquals("tns:ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure", $return);

        $wsdl = $this->wsdl->toXML();

        $this->assertContains(
            '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure"><xsd:complexContent><xsd:restriction base="soap-enc:Array"><xsd:attribute ref="soap-enc:arrayType" wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]"/></xsd:restriction></xsd:complexContent></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexObjectWithObjectStructure"><xsd:all><xsd:element name="object" type="tns:Zend_Soap_Wsdl_ComplexTest"/></xsd:all></xsd:complexType>',
            $wsdl
        );

        $this->assertContains(
            '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest"><xsd:all><xsd:element name="var" type="xsd:int"/></xsd:all></xsd:complexType>',
            $wsdl
        );
    }

    /**
     * @group ZF-4937
     */
    public function testAddingTypesMultipleTimesIsSavedOnlyOnce()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest">')
        );
    }

    /**
     * @group ZF-4937
     */
    public function testAddingSingularThenArrayTypeIsRecognizedCorretly()
    {
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure');
        $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]');

        $wsdl = $this->wsdl->toXML();

        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexObjectWithObjectStructure[]"')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexObjectWithObjectStructure">')
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTest">')
        );
    }

    /**
     * @group ZF-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategyAndNotThrowingException()
    {
        try {
            $return = $this->wsdl->addComplexType("Zend_Soap_Wsdl_ComplexTypeA");
            $wsdl = $this->wsdl->toXml();
        } catch(Exception $e) {
            $this->fail("Adding object with nested structure should not throw exception.");
        }
    }

    /**
     * @group ZF-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategyAndAddsAllTypesRecursivly()
    {
        $return = $this->wsdl->addComplexType("Zend_Soap_Wsdl_ComplexTypeA");
        $wsdl = $this->wsdl->toXml();

        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="Zend_Soap_Wsdl_ComplexTypeA">'),
            'No definition of complex type A found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, '<xsd:complexType name="ArrayOfZend_Soap_Wsdl_ComplexTypeB">'),
            'No definition of complex type B array found.'
        );
        $this->assertEquals(1,
            substr_count($wsdl, 'wsdl:arrayType="tns:Zend_Soap_Wsdl_ComplexTypeB[]"'),
            'No usage of Complex Type B array found.'
        );
    }

    /**
     * @group ZF-5754
     */
    public function testNestingOfSameTypesDoesNotLeadToInfiniteRecursionButWillThrowException()
    {
        try {
            $return = $this->wsdl->addComplexType("Zend_Soap_AutoDiscover_Recursion");
        } catch(Exception $e) {
            $this->assertTrue($e instanceof Zend_Soap_Wsdl_Exception);
            $this->assertEquals("Infinite recursion, cannot nest 'Zend_Soap_AutoDiscover_Recursion' into itsself.", $e->getMessage());
        }
    }
}
