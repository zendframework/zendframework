<?php
/**
 * @package    Zend_JsonXML
 * @subpackage UnitTests
 */

error_reporting( E_ALL | E_STRICT ); // now required for each test suite

/**
 * Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';

/**
 * @package    Zend_JsonXML
 * @subpackage UnitTests
 */
class Zend_Json_JsonXMLTest extends PHPUnit_Framework_TestCase
{
    /**
     * xml2json Test 1
     * It tests the conversion of a contact list xml into Json format.
     * 
     * XML characteristic to be tested: XML containing an array of child elements.
     *
     */
    public function testUsingXML1()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<contacts>
    <contact>
        <name>
            John Doe
        </name>
        <phone>
            123-456-7890
        </phone>
    </contact>

    <contact>
        <name>
            Jane Doe
        </name>
        <phone>
            123-456-0000
        </phone>
    </contact>

    <contact>
        <name>
            John Smith
        </name>
        <phone>
            123-456-1111
        </phone>
    </contact>

    <contact>
        <name>
            Jane Smith
        </name>
        <phone>
            123-456-9999
        </phone>
    </contact>

</contacts>

EOT;
  
        // There are not going to be any XML attributes in this test XML.
        // Hence, set the flag to ignore XML attributes.
        $ignoreXmlAttributes = true;
        $jsonContents = "";   
        $ex = null;

        // Convert XNL to JSON now.   
        // fromXml function simply takes a String containing XML contents as input.
        try {    
            $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }

        $this->assertSame($ex, null, "Zend_JSON::fromXml returned an exception.");

        // Convert the JSON string into a PHP array.
        $phpArray = Zend_Json::decode($jsonContents);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 1 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertSame("Jane Smith", $phpArray['contacts']['contact'][3]['name'], "The last contact name converted from XML input 1 is not correct");  
    } // End of function testUsingXML1

    /**
     * xml2json Test 2
     * It tests the conversion of book publication xml into Json format.
     * 
     * XML characteristic to be tested: XML containing an array of child elements with XML attributes.
     *
     */
    public function testUsingXML2()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<books>
    <book id="1">
        <title>Code Generation in Action</title>
        <author><first>Jack</first><last>Herrington</last></author>
        <publisher>Manning</publisher>
    </book>
    <book id="2">
        <title>PHP Hacks</title>
        <author><first>Jack</first><last>Herrington</last></author>
        <publisher>O'Reilly</publisher>
    </book>
    <book id="3">
        <title>Podcasting Hacks</title>
        <author><first>Jack</first><last>Herrington</last></author>
        <publisher>O'Reilly</publisher>
    </book>
</books>

EOT;
  
        // There are going to be XML attributes in this test XML.
        // Hence, set the flag NOT to ignore XML attributes.
        $ignoreXmlAttributes = false;
        $jsonContents = "";   
        $ex = null;

        // Convert XNL to JSON now.   
        // fromXml function simply takes a String containing XML contents as input.
        try {    
            $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }

        $this->assertSame($ex, null, "Zend_JSON::fromXml returned an exception.");

        // Convert the JSON string into a PHP array.
        $phpArray = Zend_Json::decode($jsonContents);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 2 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertSame("Podcasting Hacks", $phpArray['books']['book'][2]['title'], "The last book title converted from XML input 2 is not correct");
        // Test one of the expected XML attributes carried over in the JSON result.
        $this->assertSame("3", $phpArray['books']['book'][2]['@attributes']['id'], "The last id attribute converted from XML input 2 is not correct");
    } // End of function testUsingXML2

    /**
     * xml2json Test 3
     * It tests the conversion of food menu xml into Json format.
     * 
     * XML characteristic to be tested: XML containing an array of child elements.
     *
     */
    public function testUsingXML3()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
<?xml version="1.0" encoding="ISO-8859-1" ?>
<breakfast_menu>
	<food>
		<name>Belgian Waffles</name>
		<price>$5.95</price>
		<description>
			two of our famous Belgian Waffles with plenty of real maple
			syrup
		</description>
		<calories>650</calories>
	</food>
	<food>
		<name>Strawberry Belgian Waffles</name>
		<price>$7.95</price>
		<description>
			light Belgian waffles covered with strawberries and whipped
			cream
		</description>
		<calories>900</calories>
	</food>
	<food>
		<name>Berry-Berry Belgian Waffles</name>
		<price>$8.95</price>
		<description>
			light Belgian waffles covered with an assortment of fresh
			berries and whipped cream
		</description>
		<calories>900</calories>
	</food>
	<food>
		<name>French Toast</name>
		<price>$4.50</price>
		<description>
			thick slices made from our homemade sourdough bread
		</description>
		<calories>600</calories>
	</food>
	<food>
		<name>Homestyle Breakfast</name>
		<price>$6.95</price>
		<description>
			two eggs, bacon or sausage, toast, and our ever-popular hash
			browns
		</description>
		<calories>950</calories>
	</food>
</breakfast_menu>

EOT;
  
        // There are not going to be any XML attributes in this test XML.
        // Hence, set the flag to ignore XML attributes.
        $ignoreXmlAttributes = true;
        $jsonContents = "";   
        $ex = null;

        // Convert XNL to JSON now.   
        // fromXml function simply takes a String containing XML contents as input.
        try {    
            $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }

        $this->assertSame($ex, null, "Zend_JSON::fromXml returned an exception.");

        // Convert the JSON string into a PHP array.
        $phpArray = Zend_Json::decode($jsonContents);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 3 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertContains("Homestyle Breakfast", $phpArray['breakfast_menu']['food'][4], "The last breakfast item name converted from XML input 3 is not correct");
    } // End of function testUsingXML3

    /**
     * xml2json Test 4
     * It tests the conversion of RosettaNet purchase order xml into Json format.
     * 
     * XML characteristic to be tested: XML containing an array of child elements and multiple attributes.
     *
     */
    public function testUsingXML4()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<PurchaseRequisition>
	<Submittor>
		<SubmittorName>John Doe</SubmittorName>
		<SubmittorEmail>john@nodomain.net</SubmittorEmail>
		<SubmittorTelephone>1-123-456-7890</SubmittorTelephone>
	</Submittor>
	<Billing/>
	<Approval/>
	<Item number="1">
		<ItemType>Electronic Component</ItemType>
		<ItemDescription>25 microfarad 16 volt surface-mount tantalum capacitor</ItemDescription>
		<ItemQuantity>42</ItemQuantity>
		<Specification>
			<Category type="UNSPSC" value="32121501" name="Fixed capacitors"/>
			<RosettaNetSpecification>
				<query max.records="1">
					<element dicRef="XJA039">
						<name>CAPACITOR - FIXED - TANTAL - SOLID</name>
					</element>
					<element>
						<name>Specific Features</name>
						<value>R</value>
					</element>
					<element>
						<name>Body Material</name>
						<value>C</value>
					</element>
					<element>
						<name>Terminal Position</name>
						<value>A</value>
					</element>
					<element>
						<name>Package: Outline Style</name>
						<value>CP</value>
					</element>
					<element>
						<name>Lead Form</name>
						<value>D</value>
					</element>
					<element>
						<name>Rated Capacitance</name>
						<value>0.000025</value>
					</element>
					<element>
						<name>Tolerance On Rated Capacitance (%)</name>
						<value>10</value>
					</element>
					<element>
						<name>Leakage Current (Short Term)</name>
						<value>0.0000001</value>
					</element>
					<element>
						<name>Rated Voltage</name>
						<value>16</value>
					</element>
					<element>
						<name>Operating Temperature</name>
						<value type="max">140</value>
						<value type="min">-10</value>
					</element>
					<element>
						<name>Mounting</name>
						<value>Surface</value>
					</element>
				</query>
			</RosettaNetSpecification>
		</Specification>
		<Vendor number="1">
			<VendorName>Capacitors 'R' Us, Inc.</VendorName>
			<VendorIdentifier>98-765-4321</VendorIdentifier>
			<VendorImplementation>http://sylviaearle/capaciorsRus/wsdl/buyerseller-implementation.wsdl</VendorImplementation>
		</Vendor>
	</Item>
</PurchaseRequisition>

EOT;
  
        // There are going to be XML attributes in this test XML.
        // Hence, set the flag NOT to ignore XML attributes.
        $ignoreXmlAttributes = false;
        $jsonContents = "";   
        $ex = null;

        // Convert XNL to JSON now.   
        // fromXml function simply takes a String containing XML contents as input.
        try {    
            $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }

        $this->assertSame($ex, null, "Zend_JSON::fromXml returned an exception.");

        // Convert the JSON string into a PHP array.
        $phpArray = Zend_Json::decode($jsonContents);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 4 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertContains("98-765-4321", $phpArray['PurchaseRequisition']['Item']['Vendor'], "The vendor id converted from XML input 4 is not correct");
        // Test for the presence of multiple XML attributes present that were carried over in the JSON result.
        $this->assertContains("UNSPSC", $phpArray['PurchaseRequisition']['Item']['Specification']['Category']['@attributes'], "The type attribute converted from XML input 4 is not correct");
        $this->assertContains("32121501", $phpArray['PurchaseRequisition']['Item']['Specification']['Category']['@attributes'], "The value attribute converted from XML input 4 is not correct");
        $this->assertContains("Fixed capacitors", $phpArray['PurchaseRequisition']['Item']['Specification']['Category']['@attributes'], "The name attribute converted from XML input 4 is not correct");
    } // End of function testUsingXML4

    /**
     * xml2json Test 5
     * It tests the conversion of TV shows xml into Json format.
     * 
     * XML characteristic to be tested: XML containing simple CDATA.
     *
     */
    public function testUsingXML5()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
<?xml version="1.0"?>
<tvshows>
    <show>
        <name>The Simpsons</name>
    </show>
    
    <show>
        <name><![CDATA[Lois & Clark]]></name>
    </show>
</tvshows>

EOT;
  
        // There are not going to be any XML attributes in this test XML.
        // Hence, set the flag to ignore XML attributes.
        $ignoreXmlAttributes = true;
        $jsonContents = "";   
        $ex = null;

        // Convert XNL to JSON now.   
        // fromXml function simply takes a String containing XML contents as input.
        try {    
            $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }

        $this->assertSame($ex, null, "Zend_JSON::fromXml returned an exception.");

        // Convert the JSON string into a PHP array.
        $phpArray = Zend_Json::decode($jsonContents);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 5 is NULL");
        // Test for one of the expected CDATA fields in the JSON result.
        $this->assertContains("Lois & Clark", $phpArray['tvshows']['show'][1]['name'], "The CDATA name converted from XML input 5 is not correct");
    } // End of function testUsingXML5

    /**
     * xml2json Test 6
     * It tests the conversion of demo application xml into Json format.
     * 
     * XML characteristic to be tested: XML containing a large CDATA.
     *
     */
    public function testUsingXML6()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
<?xml version="1.0"?>
<demo>
    <application>
        <name>Killer Demo</name>
    </application>

    <author>
        <name>John Doe</name>
    </author>

    <platform>
        <name>LAMP</name>
    </platform>

    <framework>
        <name>Zend</name>
    </framework>

    <language>
        <name>PHP</name>
    </language>

    <listing>
        <code>
            <![CDATA[
/*
It may not be a syntactically valid PHP code.
It is used here just to illustrate the CDATA feature of Zend_Xml2Json
*/
<?php
include 'example.php';
new SimpleXMLElement();
echo(getMovies()->movie[0]->characters->addChild('character'));
getMovies()->movie[0]->characters->character->addChild('name', "Mr. Parser");
getMovies()->movie[0]->characters->character->addChild('actor', "John Doe");
// Add it as a child element.
getMovies()->movie[0]->addChild('rating', 'PG');
getMovies()->movie[0]->rating->addAttribute("type", 'mpaa');
echo getMovies()->asXML();
?>
            ]]>
        </code>
    </listing>
</demo>

EOT;
  
        // There are not going to be any XML attributes in this test XML.
        // Hence, set the flag to ignore XML attributes.
        $ignoreXmlAttributes = true;
        $jsonContents = "";   
        $ex = null;

        // Convert XNL to JSON now.   
        // fromXml function simply takes a String containing XML contents as input.
        try {    
            $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }

        $this->assertSame($ex, null, "Zend_JSON::fromXml returned an exception.");

        // Convert the JSON string into a PHP array.
        $phpArray = Zend_Json::decode($jsonContents);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 6 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertContains("Zend", $phpArray['demo']['framework']['name'], "The framework name field converted from XML input 6 is not correct");
        // Test for one of the expected CDATA fields in the JSON result.
        $this->assertContains('echo getMovies()->asXML();', $phpArray['demo']['listing']['code'], "The CDATA code converted from XML input 6 is not correct");
    } // End of function testUsingXML6

    /**
     * xml2json Test 7
     * It tests the conversion of an invalid xml into Json format.
     * 
     * XML characteristic to be tested: XML containing invalid syntax.
     *
     */
/*
    public function testUsingXML7()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
This is an invalid XML file.
Use this file to test the xml2json feature in the Zend_Json class.
Since it is an invalid XML file, an appropriate exception should be
thrown by the Zend_Json::fromXml function.
<?xml version="1.0"?>
<invalidxml>
        </code>
    </listing>
</invalidxml>

EOT;
  
        // There are not going to be any XML attributes in this test XML.
        // Hence, set the flag to ignore XML attributes.
        $ignoreXmlAttributes = true;
        $jsonContents = "";   
        $ex = null;

        // Convert XNL to JSON now.   
        // fromXml function simply takes a String containing XML contents as input.
        try {    
            $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }

        $this->assertNotSame($ex, null, "Zend_JSON::fromXml returned an exception.");
    } // End of function testUsingXML7
*/
} // End of class Zend_Json_JsonXMLTest


