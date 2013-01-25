<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace ZendTest\Soap\AutoDiscover;

require_once __DIR__ . '/../TestAsset/commontypes.php';

use Zend\Soap\Client;

/**
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage UnitTests
 * @group      Zend_Soap
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    protected $baseuri;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SOAP_AUTODISCOVER_ONLINE_SERVER_BASEURI') || constant('TESTS_ZEND_SOAP_AUTODISCOVER_ONLINE_SERVER_BASEURI') == false) {
            $this->markTestSkipped('The constant TESTS_ZEND_SOAP_AUTODISCOVER_ONLINE_SERVER_BASEURI has to be defined to allow the Online test to work.');
        }
        $this->baseuri = TESTS_ZEND_SOAP_AUTODISCOVER_ONLINE_SERVER_BASEURI;
    }

    public function testNestedObjectArrayResponse()
    {
        $wsdl = $this->baseuri."/server1.php?wsdl";

        $b = new \ZendTest_Soap_TestAsset_ComplexTypeB();
        $b->bar = "test";
        $b->foo = "test";

        $client = new Client($wsdl);
        $ret = $client->request($b);

        $this->assertTrue( is_array($ret) );
        $this->assertEquals(1, count($ret) );
        $this->assertTrue( is_array($ret[0]->baz) );
        $this->assertEquals(3, count($ret[0]->baz) );

        $baz = $ret[0]->baz;
        $this->assertEquals("bar",  $baz[0]->bar);
        $this->assertEquals("bar",  $baz[0]->foo);
        $this->assertEquals("foo",  $baz[1]->bar);
        $this->assertEquals("foo",  $baz[1]->foo);
        $this->assertEquals("test", $baz[2]->bar);
        $this->assertEquals("test", $baz[2]->foo);
    }

    public function testObjectResponse()
    {
        $wsdl = $this->baseuri."/server2.php?wsdl";

        $client = new Client($wsdl);
        $ret = $client->request("test", "test");

        $this->assertTrue( ($ret instanceof \stdClass) );
        $this->assertEquals("test", $ret->foo);
        $this->assertEquals("test", $ret->bar);
    }
}
