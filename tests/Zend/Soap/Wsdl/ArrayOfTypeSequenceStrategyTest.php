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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @package Zend_Soap
 * @subpackage UnitTests
 */

require_once dirname(__FILE__)."/../../../TestHelper.php";

/** PHPUnit Test Case */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Soap_Wsdl */
require_once 'Zend/Soap/Wsdl.php';

require_once 'Zend/Soap/Wsdl/Strategy/ArrayOfTypeSequence.php';

class Zend_Soap_Wsdl_ArrayOfTypeSequenceStrategyTest extends PHPUnit_Framework_TestCase
{
    private $wsdl;
    private $strategy;

    public function setUp()
    {
        $this->strategy = new Zend_Soap_Wsdl_Strategy_ArrayOfTypeSequence();
        $this->wsdl = new Zend_Soap_Wsdl('MyService', 'http://localhost/MyService.php', $this->strategy);
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
        $this->assertEquals("tns:ArrayOfArrayOfString", $return);

        $wsdl = $this->wsdl->toXML();

        // Check for ArrayOfArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="ArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
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
        $this->assertEquals("tns:ArrayOfArrayOfArrayOfString", $return);

        $wsdl = $this->wsdl->toXML();

        // Check for ArrayOfArrayOfArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="ArrayOfArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfArrayOfString"><xsd:sequence><xsd:element name="item" type="ArrayOfString" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
        // Check for ArrayOfString
        $this->assertContains(
            '<xsd:complexType name="ArrayOfString"><xsd:sequence><xsd:element name="item" type="xsd:string" minOccurs="0" maxOccurs="unbounded"/></xsd:sequence></xsd:complexType>',
            $wsdl
        );
    }

    public function testSpecifyingNonSimpleArrayTypeThrowsException()
    {
        try {
            $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_SequenceTest[]');
            $this->fail();
        } catch(Zend_Soap_Wsdl_Exception $e) {
            
        }
    }

    public function testStrategyCannotHandleReturnOfObjectsThrowingException()
    {
        try {
            $return = $this->wsdl->addComplexType('Zend_Soap_Wsdl_SequenceTest');
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
}

class Zend_Soap_Wsdl_SequenceTest
{
    /**
     * @var int
     */
    public $var = 5;
}