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

/**
 * @namespace
 */
namespace ZendTest\Feed\PubSubHubbub;
use Zend\Db\Adapter;

use Zend\Db\Table;

use Zend\Feed\PubSubHubbub;

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

    protected $_subscriber = null;
    
    protected $_adapter = null;
    
    protected $_tableGateway = null;

    public function setUp()
    {
        $client = new \Zend\Http\Client;
        PubSubHubbub\PubSubHubbub::setHttpClient($client);
        $this->_subscriber = new \Zend\Feed\PubSubHubbub\Subscriber;
        $this->_adapter = $this->_getCleanMock(
            '\Zend\Db\Adapter\AbstractAdapter'
        );
        $this->_tableGateway = $this->_getCleanMock(
            '\Zend\Db\Table\AbstractTable'
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

    public function testAddsHubServerUrlsFromArrayUsingSetConfig()
    {
        $this->_subscriber->setConfig(array('hubUrls' => array(
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
        try {
            $this->_subscriber->addHubUrl('');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnSettingNonStringHubServerUrl()
    {
        try {
            $this->_subscriber->addHubUrl(123);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnSettingInvalidHubServerUrl()
    {
        try {
            $this->_subscriber->addHubUrl('http://');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
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

    public function testAddsParametersFromArrayUsingSetConfig()
    {
        $this->_subscriber->setConfig(array('parameters' => array(
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
        try {
            $this->_subscriber->setTopicUrl('');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }


    public function testThrowsExceptionOnSettingNonStringTopicUrl()
    {
        try {
            $this->_subscriber->setTopicUrl(123);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }


    public function testThrowsExceptionOnSettingInvalidTopicUrl()
    {
        try {
            $this->_subscriber->setTopicUrl('http://');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnMissingTopicUrl()
    {
        try {
            $this->_subscriber->getTopicUrl();
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testCanSetCallbackUrl()
    {
        $this->_subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->assertEquals('http://www.example.com/callback', $this->_subscriber->getCallbackUrl());
    }

    public function testThrowsExceptionOnSettingEmptyCallbackUrl()
    {
        try {
            $this->_subscriber->setCallbackUrl('');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }


    public function testThrowsExceptionOnSettingNonStringCallbackUrl()
    {
        try {
            $this->_subscriber->setCallbackUrl(123);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }


    public function testThrowsExceptionOnSettingInvalidCallbackUrl()
    {
        try {
            $this->_subscriber->setCallbackUrl('http://');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnMissingCallbackUrl()
    {
        try {
            $this->_subscriber->getCallbackUrl();
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testCanSetLeaseSeconds()
    {
        $this->_subscriber->setLeaseSeconds('10000');
        $this->assertEquals(10000, $this->_subscriber->getLeaseSeconds());
    }

    public function testThrowsExceptionOnSettingZeroAsLeaseSeconds()
    {
        try {
            $this->_subscriber->setLeaseSeconds(0);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsLeaseSeconds()
    {
        try {
            $this->_subscriber->setLeaseSeconds(-1);
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsLeaseSeconds()
    {
        try {
            $this->_subscriber->setLeaseSeconds('0aa');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testCanSetPreferredVerificationMode()
    {
        $this->_subscriber->setPreferredVerificationMode(PubSubHubbub\PubSubHubbub::VERIFICATION_MODE_ASYNC);
        $this->assertEquals(PubSubHubbub\PubSubHubbub::VERIFICATION_MODE_ASYNC, $this->_subscriber->getPreferredVerificationMode());
    }

    public function testSetsPreferredVerificationModeThrowsExceptionOnSettingBadMode()
    {
        try {
            $this->_subscriber->setPreferredVerificationMode('abc');
            $this->fail('Should not fail as an Exception would be raised and caught');
        } catch (PubSubHubbub\Exception $e) {}
    }

    public function testPreferredVerificationModeDefaultsToSync()
    {
        $this->assertEquals(PubSubHubbub\PubSubHubbub::VERIFICATION_MODE_SYNC, $this->_subscriber->getPreferredVerificationMode());
    }

    public function testCanSetStorageImplementation()
    {
	    $storage = new \Zend\Feed\PubSubHubbub\Model\Subscription($this->_tableGateway);
        $this->_subscriber->setStorage($storage);
        $this->assertThat($this->_subscriber->getStorage(), $this->identicalTo($storage));
    }


    public function testGetStorageThrowsExceptionIfNoneSet()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception');
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
