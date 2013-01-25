<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json;

use Zend\Json;

error_reporting( E_ALL | E_STRICT ); // now required for each test suite

/**
 * Zend_JSON
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_JSON
 * @subpackage UnitTests
 * @group      Zend_JSON
 */
class JsonXmlTest extends \PHPUnit_Framework_TestCase
{

    /**
     * xml2json Test 1
     * It tests the conversion of a contact list xml into JSON format.
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

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        $jsonContents = Json\Json::fromXml($xmlStringContents, $ignoreXmlAttributes);

        // Convert the JSON string into a PHP array.
        $phpArray = Json\Json::decode($jsonContents, Json\Json::TYPE_ARRAY);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 1 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertSame("Jane Smith", $phpArray['contacts']['contact'][3]['name'], "The last contact name converted from XML input 1 is not correct");
    }

    /**
     * xml2json Test 2
     * It tests the conversion of book publication xml into JSON format.
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

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        $jsonContents = Json\Json::fromXml($xmlStringContents, $ignoreXmlAttributes);

        // Convert the JSON string into a PHP array.
        $phpArray = Json\Json::decode($jsonContents, Json\Json::TYPE_ARRAY);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 2 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertSame("Podcasting Hacks", $phpArray['books']['book'][2]['title'], "The last book title converted from XML input 2 is not correct");
        // Test one of the expected XML attributes carried over in the JSON result.
        $this->assertSame("3", $phpArray['books']['book'][2]['@attributes']['id'], "The last id attribute converted from XML input 2 is not correct");
    }

    /**
     * xml2json Test 3
     * It tests the conversion of food menu xml into JSON format.
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

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        $jsonContents = Json\Json::fromXml($xmlStringContents, $ignoreXmlAttributes);

        // Convert the JSON string into a PHP array.
        $phpArray = Json\Json::decode($jsonContents, Json\Json::TYPE_ARRAY);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 3 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertContains("Homestyle Breakfast", $phpArray['breakfast_menu']['food'][4], "The last breakfast item name converted from XML input 3 is not correct");
    }

    /**
     * xml2json Test 4
     * It tests the conversion of RosettaNet purchase order xml into JSON format.
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

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        $jsonContents = Json\Json::fromXml($xmlStringContents, $ignoreXmlAttributes);

        // Convert the JSON string into a PHP array.
        $phpArray = Json\Json::decode($jsonContents, Json\Json::TYPE_ARRAY);
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
     * It tests the conversion of TV shows xml into JSON format.
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

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        $jsonContents = Json\Json::fromXml($xmlStringContents, $ignoreXmlAttributes);

        // Convert the JSON string into a PHP array.
        $phpArray = Json\Json::decode($jsonContents, Json\Json::TYPE_ARRAY);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 5 is NULL");
        // Test for one of the expected CDATA fields in the JSON result.
        $this->assertContains("Lois & Clark", $phpArray['tvshows']['show'][1]['name'], "The CDATA name converted from XML input 5 is not correct");
    }

    /**
     * xml2json Test 6
     * It tests the conversion of demo application xml into JSON format.
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
It is used here just to illustrate the CDATA feature of Zend_Xml2JSON
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

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        $jsonContents = Json\Json::fromXml($xmlStringContents, $ignoreXmlAttributes);

        // Convert the JSON string into a PHP array.
        $phpArray = Json\Json::decode($jsonContents, Json\Json::TYPE_ARRAY);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 6 is NULL");
        // Test for one of the expected fields in the JSON result.
        $this->assertContains("Zend", $phpArray['demo']['framework']['name'], "The framework name field converted from XML input 6 is not correct");
        // Test for one of the expected CDATA fields in the JSON result.
        $this->assertContains('echo getMovies()->asXML();', $phpArray['demo']['listing']['code'], "The CDATA code converted from XML input 6 is not correct");
    }

    /**
     * xml2json Test 7
     * It tests the conversion of an invalid xml into JSON format.
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
Use this file to test the xml2json feature in the Zend_JSON class.
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

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        $jsonContents = Zend_Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
    }
*/

    /**
     *  @group ZF-3257
     */
    public function testUsingXML8()
    {
        // Set the XML contents that will be tested here.
        $xmlStringContents = <<<EOT
<?xml version="1.0"?>
<a><b id="foo" />bar</a>

EOT;

        // There are not going to be any XML attributes in this test XML.
        // Hence, set the flag to ignore XML attributes.
        $ignoreXmlAttributes = false;
        $jsonContents = "";
        $ex = null;

        // Convert XML to JSON now.
        // fromXml function simply takes a String containing XML contents as input.
        try {
            $jsonContents = Json\Json::fromXml($xmlStringContents, $ignoreXmlAttributes);
        } catch (Exception $ex) {
            ;
        }
        $this->assertSame($ex, null, "Zend_JSON::fromXml returned an exception.");

        // Convert the JSON string into a PHP array.
        $phpArray = Json\Json::decode($jsonContents, Json\Json::TYPE_ARRAY);
        // Test if it is not a NULL object.
        $this->assertNotNull($phpArray, "JSON result for XML input 1 is NULL");

        $this->assertSame("bar", $phpArray['a']['@text'], "The text element of a is not correct");
        $this->assertSame("foo", $phpArray['a']['b']['@attributes']['id'], "The id attribute of b is not correct");

    }

    /**
     * @group ZF-11385
     * @expectedException Zend\Json\Exception\RecursionException
     * @dataProvider providerNestingDepthIsHandledProperly
     */
    public function testNestingDepthIsHandledProperlyWhenNestingDepthExceedsMaximum($xmlStringContents)
    {
        Json\Json::$maxRecursionDepthAllowed = 1;
        Json\Json::fromXml($xmlStringContents, true);
    }

    /**
     * @group ZF-11385
     * @dataProvider providerNestingDepthIsHandledProperly
     */
    public function testNestingDepthIsHandledProperlyWhenNestingDepthDoesNotExceedMaximum($xmlStringContents)
    {
        Json\Json::$maxRecursionDepthAllowed = 25;
        $jsonString = Json\Json::fromXml($xmlStringContents, true);
        $jsonArray = Json\Json::decode($jsonString, Json\Json::TYPE_ARRAY);
        $this->assertNotNull($jsonArray, "JSON decode result is NULL");
        $this->assertSame('A', $jsonArray['response']['message_type']['defaults']['close_rules']['after_responses']);
    }

    /**
     * XML document provider for ZF-11385 tests
     * @return array
     */
    public static function providerNestingDepthIsHandledProperly()
    {
        $xmlStringContents = <<<EOT
<response>
    <status>success</status>
    <description>200 OK</description>
    <message_type>
        <system_name>A</system_name>
        <shortname>B</shortname>
        <long_name>C</long_name>
        <as_verb>D</as_verb>
        <as_noun>E</as_noun>
        <challenge_phrase>F</challenge_phrase>
        <recipient_details>G</recipient_details>
        <sender_details>H</sender_details>
        <example_text>A</example_text>
        <short_description>B</short_description>
        <long_description>C</long_description>
        <version>D</version>
        <developer>E</developer>
        <config_instructions>A</config_instructions>
        <config_fragment>B</config_fragment>
        <icon_small>C</icon_small>
        <icon_medium>D</icon_medium>
        <icon_large>E</icon_large>
        <defaults>
            <close_rules>
                <after_responses>A</after_responses>
            </close_rules>
            <recipient_visibility>B</recipient_visibility>
            <recipient_invite>C</recipient_invite>
            <results_visibility>A</results_visibility>
            <response_visibility>B</response_visibility>
            <recipient_resubmit>C</recipient_resubmit>
            <feed_status>D</feed_status>
        </defaults>
    </message_type>
    <execution_time>0.0790269374847</execution_time>
</response>
EOT;
        return array(array($xmlStringContents));
    }

}
