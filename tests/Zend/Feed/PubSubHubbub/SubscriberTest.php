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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Feed\PubSubHubbub;

use Zend\Feed\PubSubHubbub\Subscriber;
use Zend\Feed\PubSubHubbub\PubSubHubbub;
use Zend\Feed\PubSubHubbub\Model\Subscription;
use Zend\Http\Client as HttpClient;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var Subscriber */
    protected $_subscriber = null;
    
    protected $_adapter = null;
    
    protected $_tableGateway = null;

    public function setUp()
    {
        $client = new HttpClient;
        PubSubHubbub::setHttpClient($client);
        $this->_subscriber = new Subscriber;
        $this->_adapter = $this->_getCleanMock(
            '\Zend\Db\Adapter\Adapter'
        );
        $this->_tableGateway = $this->_getCleanMock(
            '\Zend\Db\TableGateway\TableGateway'
        );
        $this->_tableGateway->expects($this->any())->method('getAdapter')
            ->will($this->returnValue($this->_adapter));
    }


    public function testAddsHubServerUrl()
    {
        $this->_subscriber->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(array('http://www.example.com/hub'), $this->_subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray()
    {
        $this->_subscriber->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->_subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArrayUsingSetOptions()
    {
        $this->_subscriber->setOptions(array('hubUrls' => array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        )));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->_subscriber->getHubUrls());
    }

    public function testRemovesHubServerUrl()
    {
        $this->_subscriber->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->_subscriber->removeHubUrl('http://www.example.com/hub');
        $this->assertEquals(array(
            1 => 'http://www.example.com/hub2'
        ), $this->_subscriber->getHubUrls());
    }

    public function testRetrievesUniqueHubServerUrlsOnly()
    {
        $this->_subscriber->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2',
            'http://www.example.com/hub'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->_subscriber->getHubUrls());
    }

    public function testThrowsExceptionOnSettingEmptyHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->addHubUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->addHubUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->addHubUrl('http://');
    }

    public function testAddsParameter()
    {
        $this->_subscriber->setParameter('foo', 'bar');
        $this->assertEquals(array('foo'=>'bar'), $this->_subscriber->getParameters());
    }

    public function testAddsParametersFromArray()
    {
        $this->_subscriber->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->_subscriber->getParameters());
    }

    public function testAddsParametersFromArrayInSingleMethod()
    {
        $this->_subscriber->setParameter(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->_subscriber->getParameters());
    }

    public function testAddsParametersFromArrayUsingSetOptions()
    {
        $this->_subscriber->setOptions(array('parameters' => array(
            'foo' => 'bar', 'boo' => 'baz'
        )));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->_subscriber->getParameters());
    }

    public function testRemovesParameter()
    {
        $this->_subscriber->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->_subscriber->removeParameter('boo');
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->_subscriber->getParameters());
    }

    public function testRemovesParameterIfSetToNull()
    {
        $this->_subscriber->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->_subscriber->setParameter('boo', null);
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->_subscriber->getParameters());
    }

    public function testCanSetTopicUrl()
    {
        $this->_subscriber->setTopicUrl('http://www.example.com/topic');
        $this->assertEquals('http://www.example.com/topic', $this->_subscriber->getTopicUrl());
    }

    public function testThrowsExceptionOnSettingEmptyTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setTopicUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setTopicUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setTopicUrl('http://');
    }

    public function testThrowsExceptionOnMissingTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->getTopicUrl();
    }

    public function testCanSetCallbackUrl()
    {
        $this->_subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->assertEquals('http://www.example.com/callback', $this->_subscriber->getCallbackUrl());
    }

    public function testThrowsExceptionOnSettingEmptyCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setCallbackUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setCallbackUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setCallbackUrl('http://');
    }

    public function testThrowsExceptionOnMissingCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->getCallbackUrl();
    }

    public function testCanSetLeaseSeconds()
    {
        $this->_subscriber->setLeaseSeconds('10000');
        $this->assertEquals(10000, $this->_subscriber->getLeaseSeconds());
    }

    public function testThrowsExceptionOnSettingZeroAsLeaseSeconds()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setLeaseSeconds(0);
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsLeaseSeconds()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setLeaseSeconds(-1);
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsLeaseSeconds()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setLeaseSeconds('0aa');
    }

    public function testCanSetPreferredVerificationMode()
    {
        $this->_subscriber->setPreferredVerificationMode(PubSubHubbub::VERIFICATION_MODE_ASYNC);
        $this->assertEquals(PubSubHubbub::VERIFICATION_MODE_ASYNC, $this->_subscriber->getPreferredVerificationMode());
    }

    public function testSetsPreferredVerificationModeThrowsExceptionOnSettingBadMode()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->setPreferredVerificationMode('abc');
    }

    public function testPreferredVerificationModeDefaultsToSync()
    {
        $this->assertEquals(PubSubHubbub::VERIFICATION_MODE_SYNC, $this->_subscriber->getPreferredVerificationMode());
    }

    public function testCanSetStorageImplementation()
    {
	$storage = new Subscription($this->_tableGateway);
        $this->_subscriber->setStorage($storage);
        $this->assertThat($this->_subscriber->getStorage(), $this->identicalTo($storage));
    }


    public function testGetStorageThrowsExceptionIfNoneSet()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_subscriber->getStorage();
    }
    
    protected function _getCleanMock($className) {
        $class = new \ReflectionClass($className);
        $methods = $class->getMethods();
        $stubMethods = array();
        foreach ($methods as $method) {
            if ($method->isPublic() || ($method->isProtected()
                && $method->isAbstract())) {
                $stubMethods[] = $method->getName();
            }
        }
        $mocked = $this->getMock(
            $className,
            $stubMethods,
            array(),
            str_replace('\\', '_', ($className . '_PubsubSubscriberMock_' . uniqid())),
            false
        );
        return $mocked;
    }

}
