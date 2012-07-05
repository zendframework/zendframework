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
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Amf;

use Zend\Amf\Parser,
    Zend\Amf\Value\Messaging,
    Zend\Amf\Value,
    Zend\Locale\Locale;

/**
 * Test case for Zend_Amf_Response
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Amf
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    // The message response status code.
    public $responseURI = "/2/onResult";

    /**
     * Zend_Amf_Request object
     * @var Zend_Amf_Request
     */
    protected $_response;

    /**
     * Stores the original set timezone
     * @var string
     */
    private $_originaltimezone;

    /**
     * Setup environment
     */
    public function setUp()
    {
        $this->_originaltimezone = date_default_timezone_get();
        date_default_timezone_set('America/Chicago');
        Locale::setFallback('en_US');
        Parser\TypeLoader::resetMap();
        $this->_response = new \Zend\Amf\Response\StreamResponse();
    }

    /**
     * Teardown environment
     */
    public function tearDown()
    {
        unset($this->_response);
        date_default_timezone_set($this->_originaltimezone);
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
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'C626EDB9-8CF4-C305-8915-096C8AA80E2E';
        $acknowledgeMessage->clientId = '49D6F1AF-ADFB-3A48-5B2D-00000A5D0301';
        $acknowledgeMessage->messageId = '5F58E888-58E8-12A9-7A85-00006D91CCB1';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124569861800';
        $acknowledgeMessage->body = $data;


        $newBody = new Value\MessageBody($this->responseURI, null, $acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/stringAmf3Response.bin');

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
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'D3695635-7308-35A2-8451-09F7CAAB868A';
        $acknowledgeMessage->clientId = '54A7E9A2-9C2A-9849-5A3D-000070318519';
        $acknowledgeMessage->messageId = '2E68D735-A68E-D208-9ACC-00006FBCDE26';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124570774300';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI, null, $acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/arrayAmf3Response.bin');

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
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '1D556448-6DF0-6D0B-79C7-09798CC54A93';
        $acknowledgeMessage->clientId = '03EB43E5-3ADA-0F69-DA96-00007A54194D';
        $acknowledgeMessage->messageId = '5E4C2B6B-ADAC-4C49-52B6-0000205BC451';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124569947000';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI, null, $acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/numberAmf3Response.bin');

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
        $date = new \DateTime($dateSrc, new \DateTimeZone('America/Chicago'));
        $data = $date;

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '77D952FE-47FA-D789-83B6-097D43403C6C';
        $acknowledgeMessage->clientId = '2D043296-C81C-7189-4325-000007D62DA1';
        $acknowledgeMessage->messageId = '2A686BAF-7D69-11C8-9A0F-0000513C0958';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124569971300';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/dateAmf3Response.bin');

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
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '1D191AC2-8628-2C9A-09B2-0981CBCCF2CC';
        $acknowledgeMessage->clientId = '13D9DF0B-CCD0-1149-53D2-0000696908C2';
        $acknowledgeMessage->messageId = '03387968-E9BA-E149-A230-00006366BE67';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124570001000';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/largeIntAmf3Response.bin');

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
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '45B8A430-A13A-FE86-D62F-098900BDF482';
        $acknowledgeMessage->clientId = '4000C9FB-C97B-D609-DBAA-000048B69D81';
        $acknowledgeMessage->messageId = '5F9AA1BF-D474-BB69-12C6-0000775127E8';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124570048300';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/boolTrueAmf3Response.bin');

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
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '9C5D0787-7301-432E-FD4F-098681A0EE30';
        $acknowledgeMessage->clientId = '5AC2D840-E652-86A8-CB7A-00000418AAA4';
        $acknowledgeMessage->messageId = '200337C4-0932-7D68-BB24-00005EBD5F95';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124570031900';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/boolFalseAmf3Response.bin');

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

        $contact = new TestAsset\Contact();
        $contact->id = '15';
        $contact->firstname = 'Joe';
        $contact->lastname = 'Smith';
        $contact->email = 'jsmith@adobe.com';
        $contact->mobile = '123-456-7890';
        array_push( $data, $contact );

        $contact = new TestAsset\Contact();
        $contact->id = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname = 'Flex';
        $contact->email = 'was@here.com';
        $contact->mobile = '123-456-7890';
        array_push( $data, $contact );

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'AF307825-478F-C4CA-AC03-09C10CD02CCC';
        $acknowledgeMessage->clientId = '702B4B03-89F5-34C8-1B4E-0000049466FA';
        $acknowledgeMessage->messageId = '704B88DF-6D5E-A228-53E3-00001DA3041F';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124570415500';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/classMapAmf3Response.bin');

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

        $contact = new TestAsset\Contact();
        $contact->id = '15';
        $contact->firstname = 'Joe';
        $contact->lastname = 'Smith';
        $contact->email = 'jsmith@adobe.com';
        $contact->mobile = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $contact = new TestAsset\Contact();
        $contact->id = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname = 'Flex';
        $contact->email = 'was@here.com';
        $contact->mobile = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'AF307825-478F-C4CA-AC03-09C10CD02CCC';
        $acknowledgeMessage->clientId = '702B4B03-89F5-34C8-1B4E-0000049466FA';
        $acknowledgeMessage->messageId = '704B88DF-6D5E-A228-53E3-00001DA3041F';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124570415500';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/classMapAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
    * The feature test allows for php to just retun it's class name if nothing is specified. Using
    * _explicitType, setClassMap, getASClassName() should only be used now if you want to override the
    * PHP class name for specifying the return type.
    * @group ZF-6130
    */
    public function testPhpObjectNameSerializedToAmf3ClassName()
    {
        $data = array();

        $contact = new TestAsset\Contact();
        $contact->id = '15';
        $contact->firstname = 'Joe';
        $contact->lastname = 'Smith';
        $contact->email = 'jsmith@adobe.com';
        $contact->mobile = '123-456-7890';
        array_push( $data, $contact );

        $contact = new TestAsset\Contact();
        $contact->id = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname = 'Flex';
        $contact->email = 'was@here.com';
        $contact->mobile = '123-456-7890';
        array_push( $data, $contact );

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'AF307825-478F-C4CA-AC03-09C10CD02CCC';
        $acknowledgeMessage->clientId = '702B4B03-89F5-34C8-1B4E-0000049466FA';
        $acknowledgeMessage->messageId = '704B88DF-6D5E-A228-53E3-00001DA3041F';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124570415500';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/classMapAmf3Response.bin');

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
        $data = new \DOMDocument();
        $data->preserveWhiteSpace = false;
        $data->loadXML($sXML);

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'B0B0E583-5A80-826B-C2D1-D67A63D2F5E1';
        $acknowledgeMessage->clientId = '3D281DFB-FAC8-E368-3267-0000696DA53F';
        $acknowledgeMessage->messageId = '436381AA-C8C1-9749-2B05-000067CEA2CD';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122766401600';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/domdocumentAmf3Response.bin');

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
        $data = new \DOMDocument();
        $data->preserveWhiteSpace = false;
        $data->loadXML($sXML);
        $data = simplexml_import_dom($data);


        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = 'B0B0E583-5A80-826B-C2D1-D67A63D2F5E1';
        $acknowledgeMessage->clientId = '3D281DFB-FAC8-E368-3267-0000696DA53F';
        $acknowledgeMessage->messageId = '436381AA-C8C1-9749-2B05-000067CEA2CD';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '122766401600';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/domdocumentAmf3Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * Check to make sure that cyclic references work inside of the AMF3 serializer
     * @group ZF-6205
     */
    public function testReferenceObjectsToAmf3()
    {
        $data = new TestAsset\ReferenceTest();
        $data = $data->getReference();

        // Create an acknowlege message for a response to a RemotingMessage
        $acknowledgeMessage = new Messaging\AcknowledgeMessage(null);
        $acknowledgeMessage->correlationId = '839B091C-8DDF-F6DD-2FF1-EAA82AE39608';
        $acknowledgeMessage->clientId = '21CC629C-58AF-2D68-A292-000006F8D883';
        $acknowledgeMessage->messageId = '05E70A68-FF7F-D289-1A94-00004CCECA98';
        $acknowledgeMessage->destination = null;
        $acknowledgeMessage->timeToLive = 0;
        $acknowledgeMessage->timestamp = '124518243200';
        $acknowledgeMessage->body = $data;

        $newBody = new Value\MessageBody($this->responseURI,null,$acknowledgeMessage);

        // serialize the data to an AMF output stream
        $this->_response->setObjectEncoding(0x03);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/referenceObjectAmf3Response.bin');

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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/stringAmf0Response.bin');

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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/arrayAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    /**
     * Check to make sure that we can place arrays in arrays.
     *
     * @group ZF-4712
     */
    public function testPhpNestedArraySerializedToAmf0Array()
    {
        $data = array("items"=>array("a","b"));
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/nestedArrayAmf0Response.bin');
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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/sparseArrayAmf0Response.bin');
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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/stringKeyArrayAmf0Response.bin');
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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/objectAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    public function testPhpObjectSerializedToAmf0TypedObjectClassMap()
    {
        Parser\TypeLoader::setMapping("ContactVO","Contact");

        $data = array();
        $contact = new TestAsset\Contact();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $contact = new TestAsset\Contact();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/typedObjectAmf0Response.bin');
        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }


    public function testPhpObjectSerializedToAmf0TypedObjectExplicitType()
    {
        $data = array();

        $contact = new TestAsset\Contact();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';
        array_push( $data, $contact );

        $contact = new TestAsset\Contact();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        array_push( $data, $contact );

        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/typedObjectAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

   public function testPhpObjectSerializedToAmf0TypedObjectGetAsClassName()
    {
        $data = array();

        $contact = new TestAsset\Contact();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $contact = new TestAsset\Contact();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        unset($contact->_explicitType);
        array_push( $data, $contact );

        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/typedObjectAmf0Response.bin');
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

        $contact = new TestAsset\ContactVO();
        $contact->id        = '15';
        $contact->firstname = 'Joe';
        $contact->lastname  = 'Smith';
        $contact->email     = 'jsmith@adobe.com';
        $contact->mobile    = '123-456-7890';

        array_push( $data, $contact );

        $contact = new TestAsset\ContactVO();
        $contact->id        = '23';
        $contact->firstname = 'Adobe';
        $contact->lastname  = 'Flex';
        $contact->email     = 'was@here.com';
        $contact->mobile    = '123-456-7890';
        array_push( $data, $contact );
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();
        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/typedObjectAmf0Response.bin');
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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/numberAmf0Response.bin');

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
        $date = new \DateTime($dateSrc, new \DateTimeZone('America/Chicago'));
        $data = $date;
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/dateAmf0Response.bin');

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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/boolTrueAmf0Response.bin');

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
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/boolFalseAmf0Response.bin');

        // Check that the response matches the expected serialized value
        $this->assertEquals($mockResponse, $testResponse);
    }

    public function testPHPNullSerializedToAmf0Null()
    {
        $data = null;
        $newBody = new Value\MessageBody('/1/onResult',null,$data);
        $this->_response->setObjectEncoding(0x00);
        $this->_response->addAmfBody($newBody);
        $this->_response->finalize();
        $testResponse = $this->_response->getResponse();

        // Load the expected response.
        $mockResponse = file_get_contents(__DIR__ .'/TestAsset/Response/nullAmf0Response.bin');

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
        $this->header1 = new Value\MessageHeader('foo', false, 'bar');
        $this->header2 = new Value\MessageHeader('bar', true, 'baz');
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

        $request = new \Zend\Amf\Request\StreamRequest();
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
