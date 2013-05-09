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

require_once __DIR__ . "/../TestAsset/commontypes.php";

use Zend\Soap\Wsdl;
use Zend\Soap\Wsdl\ComplexTypeStrategy\ArrayOfTypeComplex;
use ZendTest\Soap\WsdlTestHelper;

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 * @group      Zend_Soap_Wsdl
 */
class ArrayOfTypeComplexStrategyTest extends WsdlTestHelper
{

    public function setUp()
    {
        $this->strategy = new ArrayOfTypeComplex();

        parent::setUp();
    }

    public function testNestingObjectsDeepMakesNoSenseThrowingException()
    {
        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException',
            'ArrayOfTypeComplex cannot return nested ArrayOfObject deeper than one level'
        );
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTest[][]');
    }

    public function testAddComplexTypeOfNonExistingClassThrowsException()
    {
        $this->setExpectedException('Zend\Soap\Exception\InvalidArgumentException',
            'Cannot add a complex type \ZendTest\Soap\TestAsset\UnknownClass that is not an object or where class'
        );
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\UnknownClass[]');
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfSimpleObject()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTest[]');
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTest[]');
        $this->assertEquals("tns:ArrayOfComplexTest", $return);

        // single element
        $nodes = $this->xpath->query('//wsdl:types/*/xsd:complexType[@name="ComplexTest"]/xsd:all/xsd:element');
        $this->assertEquals(1, $nodes->length, 'Unable to find complex type in wsdl.');

        $this->assertEquals('var', $nodes->item(0)->getAttribute('name'), 'Invalid attribute name');
        $this->assertEquals('xsd:int', $nodes->item(0)->getAttribute('type'), 'Invalid type name');

        // array of elements
        $nodes = $this->xpath->query(
            '//wsdl:types/*/xsd:complexType[@name="ArrayOfComplexTest"]/xsd:complexContent/xsd:restriction'
        );
        $this->assertEquals(1, $nodes->length, 'Unable to find complex type array definition in wsdl.');
        $this->assertEquals('soap-enc:Array', $nodes->item(0)->getAttribute('base'),
            'Invalid base encoding in complex type.'
        );

        $nodes = $this->xpath->query('xsd:attribute', $nodes->item(0));

        $this->assertEquals('soap-enc:arrayType', $nodes->item(0)->getAttribute('ref'),
            'Invalid attribute reference value in complex type.'
        );
        $this->assertEquals('tns:ComplexTest[]', $nodes->item(0)->getAttributeNS(Wsdl::WSDL_NS_URI, 'arrayType'),
            'Invalid array type reference.'
        );

        $this->testDocumentNodes();
    }

    public function testThatOverridingStrategyIsReset()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTest[]');
        $this->assertEquals("tns:ArrayOfComplexTest", $return);
    }

    /**
     * @group ZF-5046
     */
    public function testArrayOfComplexObjects()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectStructure", $return);

        $nodes = $this->xpath->query(
            '//wsdl:types/xsd:schema/xsd:complexType[@name="ComplexObjectStructure"]/xsd:all'
        );
        $this->assertEquals(4, $nodes->item(0)->childNodes->length, 'Invalid complex object definition.');

        foreach (array(
            'boolean'       => 'xsd:boolean',
            'string'        => 'xsd:string',
            'int'           => 'xsd:int',
            'array'         => 'soap-enc:Array'
                 ) as $name => $type) {
            $node = $this->xpath->query('xsd:element[@name="'.$name.'"]', $nodes->item(0));
            $this->assertEquals($name, $node->item(0)->getAttribute('name'),
                'Invalid name attribute value in complex object definition'
            );
            $this->assertEquals($type, $node->item(0)->getAttribute('type'),
                'Invalid type name in complex object definition'
            );
        }

        // array of elements
        $nodes = $this->xpath->query(
            '//wsdl:types/*/xsd:complexType[@name="ArrayOfComplexObjectStructure"]/xsd:complexContent/xsd:restriction'
        );
        $this->assertEquals(1, $nodes->length, 'Unable to find complex type array definition in wsdl.');
        $this->assertEquals('soap-enc:Array', $nodes->item(0)->getAttribute('base'),
            'Invalid base encoding in complex type.'
        );

        $nodes = $this->xpath->query('xsd:attribute', $nodes->item(0));

        $this->assertEquals('soap-enc:arrayType', $nodes->item(0)->getAttribute('ref'),
            'Invalid attribute reference value in complex type.'
        );
        $this->assertEquals('tns:ComplexObjectStructure[]',
            $nodes->item(0)->getAttributeNS(Wsdl::WSDL_NS_URI, 'arrayType'),
            'Invalid array type reference.'
        );


        $this->testDocumentNodes();
    }

    public function testArrayOfObjectWithObject()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');
        $this->assertEquals("tns:ArrayOfComplexObjectWithObjectStructure", $return);

        // single element
        $nodes = $this->xpath->query('//wsdl:types/*/xsd:complexType[@name="ComplexTest"]/xsd:all/xsd:element');
        $this->assertEquals(1, $nodes->length, 'Unable to find complex type in wsdl.');

        $this->assertEquals('var', $nodes->item(0)->getAttribute('name'), 'Invalid attribute name');
        $this->assertEquals('xsd:int', $nodes->item(0)->getAttribute('type'), 'Invalid type name');

        // single object element
        $nodes = $this->xpath->query(
            '//wsdl:types/*/xsd:complexType[@name="ComplexObjectWithObjectStructure"]/xsd:all/xsd:element'
        );
        $this->assertEquals(1, $nodes->length, 'Unable to find complex object in wsdl.');

        $this->assertEquals('object',           $nodes->item(0)->getAttribute('name'),
            'Invalid attribute name'
        );
        $this->assertEquals('tns:ComplexTest',  $nodes->item(0)->getAttribute('type'),
            'Invalid type name'
        );
        $this->assertEquals('true',             $nodes->item(0)->getAttribute('nillable'),
            'Invalid nillable attribute value'
        );

        // array of elements
        $nodes = $this->xpath->query(
            '//wsdl:types/*/xsd:complexType[@name="ArrayOfComplexObjectWithObjectStructure"]/'
            .'xsd:complexContent/xsd:restriction'
        );
        $this->assertEquals(1, $nodes->length, 'Unable to find complex type array definition in wsdl.');
        $this->assertEquals('soap-enc:Array', $nodes->item(0)->getAttribute('base'),
            'Invalid base encoding in complex type.'
        );

        $nodes = $this->xpath->query('xsd:attribute', $nodes->item(0));

        $this->assertEquals('soap-enc:arrayType', $nodes->item(0)->getAttribute('ref'),
            'Invalid attribute reference value in complex type.'
        );
        $this->assertEquals('tns:ComplexObjectWithObjectStructure[]',
            $nodes->item(0)->getAttributeNS(Wsdl::WSDL_NS_URI, 'arrayType'),
            'Invalid array type reference.'
        );

        $this->testDocumentNodes();
    }

    /**
     * @group ZF-4937
     */
    public function testAddingTypesMultipleTimesIsSavedOnlyOnce()
    {
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');

        // this xpath is proper version of simpler: //*[wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"] - namespaces in attributes and xpath
        $nodes = $this->xpath->query('//*[@*[namespace-uri()="'.Wsdl::WSDL_NS_URI
            .'" and local-name()="arrayType"]="tns:ComplexObjectWithObjectStructure[]"]'
        );
        $this->assertEquals(1, $nodes->length,
            'Invalid array of complex type array type reference detected'
        );

        $nodes = $this->xpath->query(
            '//xsd:complexType[@name="ArrayOfComplexObjectWithObjectStructure"]'
        );
        $this->assertEquals(1, $nodes->length, 'Invalid array complex type detected');

        $nodes = $this->xpath->query('//xsd:complexType[@name="ComplexTest"]');
        $this->assertEquals(1, $nodes->length, 'Invalid complex type detected');

        $this->testDocumentNodes();
    }

    /**
     * @group ZF-4937
     */
    public function testAddingSingularThenArrayTypeIsRecognizedCorretly()
    {
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure');
        $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexObjectWithObjectStructure[]');

        // this xpath is proper version of simpler: //*[wsdl:arrayType="tns:ComplexObjectWithObjectStructure[]"] - namespaces in attributes and xpath
        $nodes = $this->xpath->query('//*[@*[namespace-uri()="'.Wsdl::WSDL_NS_URI.
            '" and local-name()="arrayType"]="tns:ComplexObjectWithObjectStructure[]"]'
        );
        $this->assertEquals(1, $nodes->length,
            'Invalid array of complex type array type reference detected'
        );

        $nodes = $this->xpath->query(
            '//xsd:complexType[@name="ArrayOfComplexObjectWithObjectStructure"]'
        );
        $this->assertEquals(1, $nodes->length, 'Invalid array complex type detected');

        $nodes = $this->xpath->query('//xsd:complexType[@name="ComplexTest"]');
        $this->assertEquals(1, $nodes->length, 'Invalid complex type detected');

        $this->testDocumentNodes();
    }

    /**
     * @group ZF-5149
     */
    public function testArrayOfComplexNestedObjectsIsCoveredByStrategyAndAddsAllTypesRecursivly()
    {
        $return = $this->wsdl->addComplexType('\ZendTest\Soap\TestAsset\ComplexTypeA');
        $this->assertEquals("tns:ComplexTypeA", $return);


        $nodes = $this->xpath->query('//wsdl:types/xsd:schema/xsd:complexType[@name="ComplexTypeB"]/xsd:all');
        $this->assertEquals(2, $nodes->item(0)->childNodes->length, 'Invalid complex object definition.');

        foreach (array(
                     'bar'  => 'xsd:string',
                     'foo'  => 'xsd:string',
                 ) as $name => $type) {
            $node = $this->xpath->query('xsd:element[@name="'.$name.'"]', $nodes->item(0));
            $this->assertEquals($name, $node->item(0)->getAttribute('name'),
                'Invalid name attribute value in complex object definition'
            );
            $this->assertEquals($type, $node->item(0)->getAttribute('type'),
                'Invalid type name in complex object definition'
            );
            $this->assertEquals('true', $node->item(0)->getAttribute('nillable'),
                'Invalid nillable attribute value'
            );
        }

        // single object element
        $nodes = $this->xpath->query(
            '//wsdl:types/*/xsd:complexType[@name="ComplexTypeA"]/xsd:all/xsd:element'
        );
        $this->assertEquals(1, $nodes->length, 'Unable to find complex object in wsdl.');

        $this->assertEquals('baz',
            $nodes->item(0)->getAttribute('name'), 'Invalid attribute name'
        );
        $this->assertEquals('tns:ArrayOfComplexTypeB',
            $nodes->item(0)->getAttribute('type'), 'Invalid type name'
        );

        // array of elements
        $nodes = $this->xpath->query(
            '//wsdl:types/*/xsd:complexType[@name="ArrayOfComplexTypeB"]/xsd:complexContent/xsd:restriction'
        );
        $this->assertEquals(1, $nodes->length,
            'Unable to find complex type array definition in wsdl.'
        );
        $this->assertEquals('soap-enc:Array', $nodes->item(0)->getAttribute('base'),
            'Invalid base encoding in complex type.'
        );

        $nodes = $this->xpath->query('xsd:attribute', $nodes->item(0));

        $this->assertEquals('soap-enc:arrayType',
            $nodes->item(0)->getAttribute('ref'),
            'Invalid attribute reference value in complex type.'
        );
        $this->assertEquals('tns:ComplexTypeB[]',
            $nodes->item(0)->getAttributeNS(Wsdl::WSDL_NS_URI, 'arrayType'),
            'Invalid array type reference.'
        );

        $this->testDocumentNodes();
    }
}
