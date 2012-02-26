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
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\StrikeIron;
use Zend\Service\StrikeIron;

/**
 * Test helper
 */

/**
 * @ see Zend_Service_StrikeIron_BaseTest
 */

/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_StrikeIron
 */
class BaseTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->soapClient = new BaseTest\MockSoapClient;
        $this->base = new StrikeIron\Base(array('client'   => $this->soapClient,
                                                             'username' => 'user',
                                                             'password' => 'pass'));
    }

    public function testHasNoPredefinedWsdl()
    {
        $this->assertSame(null, $this->base->getWsdl());
    }

    public function testSettingWsdl()
    {
        $wsdl = 'http://example.com/foo';
        $base = new StrikeIron\Base(array('client' => $this->soapClient,
                                                       'wsdl'   => $wsdl));
        $this->assertEquals($wsdl, $base->getWsdl());
    }

    public function testSoapClientDependencyInjection()
    {
        $this->assertSame($this->soapClient, $this->base->getSoapClient());
    }

    public function testSoapClientInitializesDefaultSOAPClient()
    {
        // set soapclient options to non-wsdl mode just to get a
        // soapclient instance without hitting the network
        $base = new StrikeIron\Base(array('options' => array('location' => '',
                                                                          'uri'      => '')));
        $this->assertInstanceOf('SOAPClient', $base->getSoapClient());
    }

    public function testDefaultSoapHeadersHasTheLicenseInfoHeader()
    {
        $this->base->foo();
        $headers = $this->soapClient->calls[0]['headers'];

        $this->assertInternalType('array', $headers);
        $this->assertEquals(1, count($headers));
        $header = $headers[0];

        $this->assertInstanceOf('SoapHeader', $header);
        $this->assertEquals('LicenseInfo', $header->name);
        $this->assertEquals('user', $header->data['RegisteredUser']['UserID']);
        $this->assertEquals('pass', $header->data['RegisteredUser']['Password']);
    }

    public function testAddingInvalidSoapHeaderThrows()
    {
        $invalidHeaders = 'foo';
        try {
            $base = new StrikeIron\Base(array('client'  => $this->soapClient,
                                                           'headers' => $invalidHeaders));
            $this->fail();
        } catch (StrikeIron\Exception $e) {
            $this->assertRegExp('/instance of soapheader/i', $e->getMessage());
        }
    }

    public function testAddingInvalidSoapHeaderArrayThrows()
    {
        $invalidHeaders = array('foo');
        try {
            $base = new StrikeIron\Base(array('client'  => $this->soapClient,
                                                           'headers' => $invalidHeaders));
            $this->fail();
        } catch (StrikeIron\Exception $e) {
            $this->assertRegExp('/instance of soapheader/i', $e->getMessage());
        }
    }

    public function testAddingScalarSoapHeaderNotLicenseInfo()
    {
        $header = new \SoapHeader('foo', 'bar');
        $base = new StrikeIron\Base(array('client'  => $this->soapClient,
                                                       'headers' => $header));
        $base->foo();

        $headers = $this->soapClient->calls[0]['headers'];
        $this->assertEquals(2, count($headers));
        $this->assertEquals($header->name, $headers[0]->name);
        $this->assertEquals('LicenseInfo', $headers[1]->name);
    }

    public function testAddingScalarSoapHeaderThatOverridesLicenseInfo()
    {
        $soapHeaders = new \SoapHeader('http://ws.strikeiron.com',
                                      'LicenseInfo',
                                      array('RegisteredUser' => array('UserID'   => 'foo',
                                                                      'Password' => 'bar')));
        $base = new StrikeIron\Base(array('client'  => $this->soapClient,
                                                       'headers' => $soapHeaders));
        $base->foo();

        $headers = $this->soapClient->calls[0]['headers'];

        $this->assertInternalType('array', $headers);
        $this->assertEquals(1, count($headers));
        $header = $headers[0];

        $this->assertInstanceOf('SoapHeader', $header);
        $this->assertEquals('LicenseInfo', $header->name);
        $this->assertEquals('foo', $header->data['RegisteredUser']['UserID']);
        $this->assertEquals('bar', $header->data['RegisteredUser']['Password']);
    }

    public function testAddingArrayOfSoapHeaders()
    {
        $headers = array(new \SoapHeader('foo', 'bar'),
                         new \SoapHeader('baz', 'qux'));

        $base = new StrikeIron\Base(array('client'  => $this->soapClient,
                                                       'headers' => $headers));
        $base->foo();

        $headers = $this->soapClient->calls[0]['headers'];

        $this->assertInternalType('array', $headers);
        $this->assertEquals(3, count($headers));  // these 2 + default LicenseInfo
    }

    public function testMethodInflection()
    {
        $this->base->foo();
        $this->assertEquals('Foo', $this->soapClient->calls[0]['method']);
    }

    public function testMethodResultNotWrappingNonObject()
    {
        $this->assertEquals(42, $this->base->returnThe42());
    }

    public function testMethodResultWrappingAnyObject()
    {
        $this->assertInstanceOf('Zend\Service\StrikeIron\Decorator',
                          $this->base->returnTheObject());
    }

    public function testMethodResultWrappingAnObjectAndSelectingDefaultResultProperty()
    {
        $this->assertEquals('unwraped', $this->base->wrapThis());
    }

    public function testMethodExceptionsAreWrapped()
    {
        try {
            $this->base->throwTheException();
            $this->fail();
        } catch (\Exception $e) {
            $this->assertInstanceOf('Zend\Service\StrikeIron\Exception', $e);
            $this->assertEquals('Exception: foo', $e->getMessage());
            $this->assertEquals(43, $e->getCode());
        }
    }

    public function testGettingOutputHeaders()
    {
        $this->assertSame(array(), $this->base->getLastOutputHeaders());
        $info = $this->base->foo();
        $this->assertEquals(BaseTest\MockSoapClient::$outputHeaders,
                            $this->base->getLastOutputHeaders());
    }

    public function testGettingSubscriptionInfo()
    {
        $this->assertEquals(0, count($this->soapClient->calls));
        $info = $this->base->getSubscriptionInfo();
        $this->assertEquals(1, count($this->soapClient->calls));
        $this->assertEquals(3, $info->remainingHits);
    }

    public function testGettingSubscriptionInfoWithCaching()
    {
        $this->assertEquals(0, count($this->soapClient->calls));
        $this->base->foo();
        $this->base->getSubscriptionInfo();
        $this->assertEquals(1, count($this->soapClient->calls));
    }

    public function testGettingSubscriptionOverridingCache()
    {
        $this->assertEquals(0, count($this->soapClient->calls));
        $this->base->getSubscriptionInfo();
        $this->assertEquals(1, count($this->soapClient->calls));
        $this->base->getSubscriptionInfo(true);
        $this->assertEquals(2, count($this->soapClient->calls));
    }

    public function testGettingSubscriptionInfoWithDefaultQueryMethod()
    {
        $this->base->getSubscriptionInfo();
        $this->assertEquals('GetRemainingHits', $this->soapClient->calls[0]['method']);
    }

    public function testGettingSubscriptionInfoWithCustomQueryMethod()
    {
        $method = 'SendSubscriptionInfoHeaderPlease';
        $this->base->getSubscriptionInfo(true, $method);
        $this->assertEquals($method, $this->soapClient->calls[0]['method']);
    }

    public function testGettingSubscriptionInfoThrowsWhenHeaderNotFound()
    {
        try {
            $this->base->getSubscriptionInfo(true, 'ReturnNoOutputHeaders');
            $this->fail();
        } catch (StrikeIron\Exception $e) {
            $this->assertRegExp('/no subscriptioninfo header/i', $e->getMessage());
        }
    }
}
