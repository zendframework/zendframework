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
}
