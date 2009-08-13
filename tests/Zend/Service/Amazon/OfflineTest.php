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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_Amazon
 */
require_once 'Zend/Service/Amazon.php';

/**
 * @see Zend_Service_Amazon_ResultSet
 */
require_once 'Zend/Service/Amazon/ResultSet.php';

/**
 * @see Zend_Service_Amazon_ResultSet
 */
require_once 'Zend/Service/Amazon/SimilarProduct.php';

/**
 * @see Zend_Http_Client_Adapter_Socket
 */
require_once 'Zend/Http/Client/Adapter/Socket.php';

/**
 * @see Zend_Http_Client_Adapter_Test
 */
require_once 'Zend/Http/Client/Adapter/Test.php';


/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 */
class Zend_Service_Amazon_OfflineTest extends PHPUnit_Framework_TestCase
{
    /**
     * Reference to Amazon service consumer object
     *
     * @var Zend_Service_Amazon
     */
    protected $_amazon;

    /**
     * HTTP client adapter for testing
     *
     * @var Zend_Http_Client_Adapter_Test
     */
    protected $_httpClientAdapterTest;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->_amazon = new Zend_Service_Amazon(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'));

        $this->_httpClientAdapterTest = new Zend_Http_Client_Adapter_Test();
    }

    /**
     * Ensures that __construct() throws an exception when given an invalid country code
     *
     * @return void
     */
    public function testConstructExceptionCountryCodeInvalid()
    {
        try {
            $amazon = new Zend_Service_Amazon(constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'), 'oops');
            $this->fail('Expected Zend_Service_Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            $this->assertContains('Unknown country code', $e->getMessage());
        }
    }

    /**
     * @group ZF-2056
     */
    public function testMozardSearchFromFile()
    {
        $xml = file_get_contents(dirname(__FILE__)."/_files/mozart_result.xml");
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $mozartTracks = array(
            'B00005A8JZ' => '29',
            'B0000058HV' => '25',
            'B000BLI3K2' => '500',
            'B00004X0QF' => '9',
            'B000004194' => '19',
            'B00000I9M0' => '9',
            'B000004166' => '20',
            'B00002DEH1' => '58',
            'B0000041EV' => '12',
            'B00004SA87' => '42',
        );

        $result = new Zend_Service_Amazon_ResultSet($dom);

        foreach($result AS $item) {
            $trackCount = $mozartTracks[$item->ASIN];
            $this->assertEquals($trackCount, count($item->Tracks));
        }
    }

    /**
     * @group ZF-2749
     */
    public function testSimilarProductConstructorMissingAttributeDoesNotThrowNotice()
    {
        $dom = new DOMDocument();
        $asin = $dom->createElement("ASIN", "TEST");
        $product = $dom->createElement("product");
        $product->appendChild($asin);

        $similarproduct = new Zend_Service_Amazon_SimilarProduct($product);
    }

    public function dataSignatureEncryption()
    {
        return array(
            array(
                'http://webservices.amazon.com',
                array(
                    'Service' => 'AWSECommerceService',
                    'AWSAccessKeyId' => '00000000000000000000',
                    'Operation' => 'ItemLookup',
                    'ItemId' => '0679722769',
                    'ResponseGroup' => 'ItemAttributes,Offers,Images,Reviews',
                    'Version' => '2009-01-06',
                    'Timestamp' => '2009-01-01T12:00:00Z',
                ),
                "GET\n".
                "webservices.amazon.com\n".
                "/onca/xml\n".
                "AWSAccessKeyId=00000000000000000000&ItemId=0679722769&Operation=I".
                "temLookup&ResponseGroup=ItemAttributes%2COffers%2CImages%2CReview".
                "s&Service=AWSECommerceService&Timestamp=2009-01-01T12%3A00%3A00Z&".
                "Version=2009-01-06",
                'Nace%2BU3Az4OhN7tISqgs1vdLBHBEijWcBeCqL5xN9xg%3D'
            ),
            array(
                'http://ecs.amazonaws.co.uk',
                array(
                    'Service' => 'AWSECommerceService',
                    'AWSAccessKeyId' => '00000000000000000000',
                    'Operation' => 'ItemSearch',
                    'Actor' => 'Johnny Depp',
                    'ResponseGroup' => 'ItemAttributes,Offers,Images,Reviews,Variations',
                    'Version' => '2009-01-01',
                    'SearchIndex' => 'DVD',
                    'Sort' => 'salesrank',
                    'AssociateTag' => 'mytag-20',
                    'Timestamp' => '2009-01-01T12:00:00Z',
                ),
                "GET\n".
                "ecs.amazonaws.co.uk\n".
                "/onca/xml\n".
                "AWSAccessKeyId=00000000000000000000&Actor=Johnny%20Depp&Associate".
                "Tag=mytag-20&Operation=ItemSearch&ResponseGroup=ItemAttributes%2C".
                "Offers%2CImages%2CReviews%2CVariations&SearchIndex=DVD&Service=AW".
                "SECommerceService&Sort=salesrank&Timestamp=2009-01-01T12%3A00%3A0".
                "0Z&Version=2009-01-01",
                'TuM6E5L9u%2FuNqOX09ET03BXVmHLVFfJIna5cxXuHxiU%3D',
            ),
        );
    }

    /**
     * Checking if signature Encryption due on August 15th for Amazon Webservice API is working correctly.
     *
     * @dataProvider dataSignatureEncryption
     * @group ZF-7033
     */
    public function testSignatureEncryption($baseUri, $params, $expectedStringToSign, $expectedSignature)
    {
        $this->assertEquals(
            $expectedStringToSign,
            Zend_Service_Amazon::buildRawSignature($baseUri, $params)
        );

        $this->assertEquals(
            $expectedSignature,
            rawurlencode(Zend_Service_Amazon::computeSignature(
                $baseUri, '1234567890', $params
            ))
        );
    }
}
