<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_ResponseTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'Zend/Amf/Response.php';
require_once 'Zend/Amf/Request.php';
require_once 'Zend/Amf/Value/MessageBody.php';
require_once 'Zend/Amf/Value/MessageHeader.php';
require_once 'Zend/Amf/Value/Messaging/AcknowledgeMessage.php';
require_once 'Zend/Amf/Parse/TypeLoader.php';
require_once 'Contact.php';
require_once 'ContactVO.php';
require_once 'Zend/Date.php';

/**
 * Test case for Zend_Amf_Response
 *
 * @package Zend_Amf
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_Amf_ResponseTest extends PHPUnit_Framework_TestCase
{
    // The message response status code.
    public $responseURI = "/2/onResult";

    /**
     * Zend_Amf_Request object
     * @var Zend_Amf_Request
     */
    protected $_response;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_ResponseTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Setup environment
     */
    public function setUp()
    {
        date_default_timezone_set('America/Chicago');
        Zend_Locale::setDefault('en_US');
        Zend_Amf_Parse_TypeLoader::resetMap();
        $this->_response = new Zend_Amf_Response();
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->_response);
    }

    /**
     * PHP String to Amf String
     *
     */
    public function testPhpStringSerializedToAmf3String()
    {
        // Create php object to serialize
        $data = "zyxwvutsrqpmlkjihgfedcba";

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '1AE5794F-C53D-FB03-5D2A-BEE6ADCD953C';
        $acknowledgeMessage->clientId = '6FC3B309-11DF-CB49-9A4D-0000579EAF16';
        $acknowledgeMessage->messageId = '1CCDEA74-75CF-ACE8-0B46-00002C38B1A4';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122297350100';
        $acknowledgeMessage->body = $data;


        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI, null, $acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/stringAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }


    /**
     * PHP Arrat to Amf Array
     *
     */
    public function testPhpArraySerializedToAmf3Array()
    {
        // Create php object to serialize
        $data = array("g", "f", "e","d","c","b","a");

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '014167F1-FCEB-6346-DCEF-BF03441367F5';
        $acknowledgeMessage->clientId = '6DEB5BBA-AFEE-CCA9-FB3C-00005662BA16';
        $acknowledgeMessage->messageId = '1822F838-FE49-11E8-730F-00000705B926';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122297537400';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI, null, $acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/arrayAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

	/**
     * PHP float to Amf3 Number
     *
     */
    public function testPhpFloatSerializedToAmf3Number()
    {
        $data    =  31.57;

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '712ECAE3-2888-990E-D91C-E29E11AC7D0E';
        $acknowledgeMessage->clientId = '5E55BB37-59AA-A969-7373-0000158CEBB7';
        $acknowledgeMessage->messageId = '67B9E08C-0E35-9168-BA7B-000066ED5FF4';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122357272200';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI, null, $acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/numberAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

	/**
     * PHP DateTime to Amf Date
     *
     */
    public function testPhpDateTimeSerializedToAmf3Date()
    {
        // Create php object to serialize
        date_default_timezone_set('America/Chicago');
        $dateSrc = '1978-10-23 4:20 America/Chicago';
        $date = new DateTime($dateSrc, new DateTimeZone('America/Chicago'));
        $data = $date;

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'F12B5C25-1302-8A8F-2A64-C14D2B3CB7D5';
        $acknowledgeMessage->clientId = '4C2A28C0-41BB-DA28-93C3-000018B12642';
        $acknowledgeMessage->messageId = '0B68113D-6210-20A9-D2AF-00002A9C1CCC';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122301345100';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/dateAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    public function testZendDateTimeSerializedToAmf3Date()
    {
        // Create php object to serialize
        $date = new Zend_Date('October 23, 1978', null, 'en_US');
        $date->set('4:20:00',Zend_Date::TIMES);
        $data = $date;

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'F12B5C25-1302-8A8F-2A64-C14D2B3CB7D5';
        $acknowledgeMessage->clientId = '4C2A28C0-41BB-DA28-93C3-000018B12642';
        $acknowledgeMessage->messageId = '0B68113D-6210-20A9-D2AF-00002A9C1CCC';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122301345100';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/dateAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * Test the largest Integer that AS in can handle
     *
     */
    public function testPhpLargeIntSerializedToAmf3Int()
    {
        // Create php object to serialize
        $data = 268435455;

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'A89F7C97-4D04-8778-18D0-C16C1F29F78E';
        $acknowledgeMessage->clientId = '336B0697-F30B-FD49-0B7E-00002E34A6BB';
        $acknowledgeMessage->messageId = '6D9DC7EC-A273-83A9-ABE3-00005FD752D6';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122301548000';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/largeIntAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * Convert boolean true to php boolean true
     *
     */
    public function testPhpBoolTrueSerializedToAmf3BoolTrue()
    {
        // Create php object to serialize
        $data = true;

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '12CE12FD-5D4B-AE60-853A-D36339532640';
        $acknowledgeMessage->clientId = '16927B78-1DBD-64E9-42BB-000019A34253';
        $acknowledgeMessage->messageId = '6D4F7964-6BF6-22C8-9A16-000046BD6319';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122331688500';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/boolTrueAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * Covert boolean false to PHP boolean false
     *
     */
    public function testPhpBoolFalseSerializedToAmf3BoolFalse()
    {
        // Create php object to serialize
        $data = false;

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '5B65D04A-6703-3C98-D7F1-D36DE839E97E';
        $acknowledgeMessage->clientId = '32E9C012-3FC0-F0C9-4A0B-00005FE13CD9';
        $acknowledgeMessage->messageId = '44777AB6-A085-01A9-1241-000033DFAFEE';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122331758500';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/boolFalseAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * test case for taking a PHP typed object and sending it back to flex as
     * a typed object. uses explicit type
     *
     */
    public function testPhpTypedObjectSerializedToAmf3TypedObjectExplicitType()
    {
        $data = array();

        $contact = new Contact();
        $contact->id = '15';
        $contact->firstname = 'Joe';
        $contact->lastname = 'Smith';
        $contact->email = 'jsmith@adobe.com';
        $contact->mobile = '123-456-7890';
        array_push( $data, $contact );

        $contact = new Contact();
        $contact->id = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname = 'Flex';
        $contact->email = 'was@here.com';
        $contact->mobile = '123-456-7890';
        array_push( $data, $contact );

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'C44AE645-4D12-028B-FF5F-D2E42BE5D86C';
        $acknowledgeMessage->clientId = '40EAAAD2-4A9B-C388-A2FD-00003A809B9E';
        $acknowledgeMessage->messageId = '275CD08C-6461-BBC8-B27B-000030083B2C';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122330856000';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/classMapAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * Test case for taking a PHP typed object and sending it back to flex as
     * a typed object. uses getAsClassName
     *
     */
    public function testPhpTypedObjectSerializedToAmf3TypedObjectGetAsClassName()
    {
        $data = array();

        $contact = new Contact();
        $contact->id = '15';
        $contact->firstname = 'Joe';
        $contact->lastname = 'Smith';
        $contact->email = 'jsmith@adobe.com';
        $contact->mobile = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $contact = new Contact();
        $contact->id = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname = 'Flex';
        $contact->email = 'was@here.com';
        $contact->mobile = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'C44AE645-4D12-028B-FF5F-D2E42BE5D86C';
        $acknowledgeMessage->clientId = '40EAAAD2-4A9B-C388-A2FD-00003A809B9E';
        $acknowledgeMessage->messageId = '275CD08C-6461-BBC8-B27B-000030083B2C';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122330856000';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/classMapAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }
    
    /**
     * Returning a DOMDocument object to AMF is serialized into a XMString ready for E4X
     * 
     * @group ZF-4999
     */
    public function testPhpDomDocumentSerializedToAmf3XmlString()
    {
        $sXML = '<root><element><key>a</key><value>b</value></element></root>';
		$data = new DOMDocument();
		$data->preserveWhiteSpace = false;
		$data->loadXML($sXML);
        
		
        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'B0B0E583-5A80-826B-C2D1-D67A63D2F5E1';
        $acknowledgeMessage->clientId = '3D281DFB-FAC8-E368-3267-0000696DA53F';
        $acknowledgeMessage->messageId = '436381AA-C8C1-9749-2B05-000067CEA2CD';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122766401600';
        $acknowledgeMessage->body = $data;

        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/domdocumentAmf3Response.bin');
        
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }
    
    /**
     * Returning a SimpleXML object to AMF is serialized into a XMString ready for E4X
     * 
     * @group ZF-4999
     */
    public function testSimpleXmlSerializedToAmf3XmlString()
    {
        $sXML = '<root><element><key>a</key><value>b</value></element></root>';
		$data = new DOMDocument();
		$data->preserveWhiteSpace = false;
		$data->loadXML($sXML);
		$data = simplexml_import_dom($data);
        
		
        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Zend_Amf_Value_Messaging_AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'B0B0E583-5A80-826B-C2D1-D67A63D2F5E1';
        $acknowledgeMessage->clientId = '3D281DFB-FAC8-E368-3267-0000696DA53F';
        $acknowledgeMessage->messageId = '436381AA-C8C1-9749-2B05-000067CEA2CD';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122766401600';
        $acknowledgeMessage->body = $data;
        
        $newBody = new Zend_Amf_Value_MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/domdocumentAmf3Response.bin');
        
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }
    
    

    /**
     * PHP string to Amf0 string
     *
     */
    public function testPhpStringSerializedToAmf0String()
    {
        $data = "zyxwvutsrqpmlkjihgfedcba";
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/stringAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * PHP Array to Amf0 Array
     *
     */
    public function testPhpArraySerializedToAmf0Array()
    {
        $data = array("g", "f", "e","d","c","b","a");
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/arrayAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * Check to make sure that we can place arrays in arrays.
     *
     * @group	ZF-4712
     */
    public function testPhpNestedArraySerializedToAmf0Array()
    {
        $data = array("items"=>array("a","b"));
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/nestedArrayAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);

    }
    
    /**
     * Allow sparse arrays to be retruned to Actionscript without loosing the keys. 
     * 
     * @group ZF-5094
     */
    public function testPhpSparseArraySerializedToAmf0Array()
    {
        $data = array(1 => 'foo', 5 => 'bar');
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/sparseArrayAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
        
    }
    
    /**
     * Test to convert string keyed arrays are converted to objects so that we do not loose
     * the key refrence in the associative array. 
     * 
     * @group ZF-5094
     */
    public function testPhpStringKeyArrayToAmf0Object()
    {
        $data = array('foo' => 5, 'bar' => 23);
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/stringKeyArrayAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
        
    }   

	/**
     * PHP Object to Amf0 Object
     *
     */
    public function testPhpObjectSerializedToAmf0Object()
    {
        $data =  array('b'=>'bar',"a" =>'foo');
        $data = (object) $data;
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/objectAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    public function testPhpObjectSerializedToAmf0TypedObjectClassMap()
    {
        Zend_Amf_Parse_TypeLoader::setMapping("ContactVO","Contact");

        $data = array();
        $contact = new Contact();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $contact = new Contact();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/typedObjectAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }


    public function testPhpObjectSerializedToAmf0TypedObjectExplicitType()
    {
        $data = array();

        $contact = new Contact();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';
        array_push( $data, $contact );

        $contact = new Contact();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        array_push( $data, $contact );

        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/typedObjectAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

   public function testPhpObjectSerializedToAmf0TypedObjectGetAsClassName()
    {
        $data = array();

        $contact = new Contact();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $contact = new Contact();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/typedObjectAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }
    
   /**
    * The feature test allows for php to just retun it's class name if nothing is specified. Using
    * _explicitType, setClassMap, getASClassName() should only be used now if you want to override the 
    * PHP class name for specifying the return type. 
    * @group ZF-6130
    */
    public function testPhpObjectNameSerializedToAmf0ClassName()
    {
        $data = array();

        $contact = new ContactVO();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';
        
        array_push( $data, $contact );

        $contact = new ContactVO();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        array_push( $data, $contact );
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/typedObjectAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

	/**
     * PHP float to Amf0 Number
     *
     */
    public function testPhpFloatSerializedToAmf0Number()
    {
        $data =  31.57;
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/numberAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

	/**
     * PHP DateTime to Amf0 date
     *
     */
    public function testPhpDateTimeSerializedToAmf0Date()
    {
        date_default_timezone_set('America/Chicago');
        $dateSrc = '1978-10-23 4:20 America/Chicago';
        $date = new DateTime($dateSrc, new DateTimeZone('America/Chicago'));
        $data = $date;
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/dateAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    public function testZendDateSerializedToAmf0Date()
    {
        $date = new Zend_Date('October 23, 1978', null, 'en_US');
        $date->set('4:20:00',Zend_Date::TIMES);

        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$date);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/dateAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * PHP boolean true to Amf0 bool true.
     *
     */
    public function testPhpBoolTrueSerializedToAmf0Bool()
    {
        $data = true;
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/boolTrueAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

	/**
     * PHP boolean true to Amf0 bool true.
     *
     */
    public function testPhpBoolFalseSerializedToAmf0Bool()
    {
        $data = false;
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/boolFalseAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    public function testPHPNullSerializedToAmf0Null()
    {
        $data = null;
        $newBody = new Zend_Amf_Value_MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(dirname(__FILE__) .'/Response/mock/nullAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    public function testResponseShouldNotHaveMessageHeadersByDefault()
    {
        $headers = $this->_response->getAmfHeaders();
        $this->assertEquals(0, count($headers));
    }

    public function testResponseShouldAggregateMessageHeaders()
    {
        $this->header1 = new Zend_Amf_Value_MessageHeader('foo', false, 'bar');
        $this->header2 = new Zend_Amf_Value_MessageHeader('bar', true, 'baz');
        $this->_response->addAmfHeader($this->header1)
                        ->addAmfHeader($this->header2);
        $headers = $this->_response->getAmfHeaders();
        $this->assertEquals(2, count($headers));
        $this->assertContains($this->header1, $headers);
        $this->assertContains($this->header2, $headers);
    }

    public function testResponseHeadersShouldBeSerializedWhenWritingMessage()
    {
        $this->testResponseShouldAggregateMessageHeaders();
        $this->_response->finalize();
        $response = $this->_response->getResponse();

        $request = new Zend_Amf_Request();
        $request->initialize($response);
        $headers = $request->getAmfHeaders();
        $this->assertEquals(2, count($headers));
    }

    public function testToStringShouldProxyToGetResponse()
    {
        $this->testResponseShouldAggregateMessageHeaders();
        $this->_response->finalize();
        $response = $this->_response->getResponse();

        $test = $this->_response->__toString();
        $this->assertSame($response, $test);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Amf_ResponseTest::main') {
    Zend_Amf_ResponseTest::main();
}
