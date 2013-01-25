<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
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
 */
class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    /** @var Subscriber */
    protected $subscriber = null;

    protected $adapter = null;

    protected $tableGateway = null;

    public function setUp()
    {
        $client = new HttpClient;
        PubSubHubbub::setHttpClient($client);
        $this->subscriber = new Subscriber;
        $this->adapter = $this->_getCleanMock(
            '\Zend\Db\Adapter\Adapter'
        );
        $this->tableGateway = $this->_getCleanMock(
            '\Zend\Db\TableGateway\TableGateway'
        );
        $this->tableGateway->expects($this->any())->method('getAdapter')
            ->will($this->returnValue($this->adapter));
    }


    public function testAddsHubServerUrl()
    {
        $this->subscriber->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(array('http://www.example.com/hub'), $this->subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray()
    {
        $this->subscriber->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->subscriber->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArrayUsingSetOptions()
    {
        $this->subscriber->setOptions(array('hubUrls' => array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        )));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->subscriber->getHubUrls());
    }

    public function testRemovesHubServerUrl()
    {
        $this->subscriber->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->subscriber->removeHubUrl('http://www.example.com/hub');
        $this->assertEquals(array(
            1 => 'http://www.example.com/hub2'
        ), $this->subscriber->getHubUrls());
    }

    public function testRetrievesUniqueHubServerUrlsOnly()
    {
        $this->subscriber->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2',
            'http://www.example.com/hub'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->subscriber->getHubUrls());
    }

    public function testThrowsExceptionOnSettingEmptyHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->addHubUrl('');
    }

    public function testThrowsExceptionOnSettingNonStringHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->addHubUrl(123);
    }

    public function testThrowsExceptionOnSettingInvalidHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->addHubUrl('http://');
    }

    public function testAddsParameter()
    {
        $this->subscriber->setParameter('foo', 'bar');
        $this->assertEquals(array('foo'=>'bar'), $this->subscriber->getParameters());
    }

    public function testAddsParametersFromArray()
    {
        $this->subscriber->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->subscriber->getParameters());
    }

    public function testAddsParametersFromArrayInSingleMethod()
    {
        $this->subscriber->setParameter(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->subscriber->getParameters());
    }

    public function testAddsParametersFromArrayUsingSetOptions()
    {
        $this->subscriber->setOptions(array('parameters' => array(
            'foo' => 'bar', 'boo' => 'baz'
        )));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->subscriber->getParameters());
    }

    public function testRemovesParameter()
    {
        $this->subscriber->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->subscriber->removeParameter('boo');
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->subscriber->getParameters());
    }

    public function testRemovesParameterIfSetToNull()
    {
        $this->subscriber->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->subscriber->setParameter('boo', null);
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->subscriber->getParameters());
    }

    public function testCanSetTopicUrl()
    {
        $this->subscriber->setTopicUrl('http://www.example.com/topic');
        $this->assertEquals('http://www.example.com/topic', $this->subscriber->getTopicUrl());
    }

    public function testThrowsExceptionOnSettingEmptyTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setTopicUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setTopicUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setTopicUrl('http://');
    }

    public function testThrowsExceptionOnMissingTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->getTopicUrl();
    }

    public function testCanSetCallbackUrl()
    {
        $this->subscriber->setCallbackUrl('http://www.example.com/callback');
        $this->assertEquals('http://www.example.com/callback', $this->subscriber->getCallbackUrl());
    }

    public function testThrowsExceptionOnSettingEmptyCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setCallbackUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setCallbackUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setCallbackUrl('http://');
    }

    public function testThrowsExceptionOnMissingCallbackUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->getCallbackUrl();
    }

    public function testCanSetLeaseSeconds()
    {
        $this->subscriber->setLeaseSeconds('10000');
        $this->assertEquals(10000, $this->subscriber->getLeaseSeconds());
    }

    public function testThrowsExceptionOnSettingZeroAsLeaseSeconds()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setLeaseSeconds(0);
    }

    public function testThrowsExceptionOnSettingLessThanZeroAsLeaseSeconds()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setLeaseSeconds(-1);
    }

    public function testThrowsExceptionOnSettingAnyScalarTypeCastToAZeroOrLessIntegerAsLeaseSeconds()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setLeaseSeconds('0aa');
    }

    public function testCanSetPreferredVerificationMode()
    {
        $this->subscriber->setPreferredVerificationMode(PubSubHubbub::VERIFICATION_MODE_ASYNC);
        $this->assertEquals(PubSubHubbub::VERIFICATION_MODE_ASYNC, $this->subscriber->getPreferredVerificationMode());
    }

    public function testSetsPreferredVerificationModeThrowsExceptionOnSettingBadMode()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->setPreferredVerificationMode('abc');
    }

    public function testPreferredVerificationModeDefaultsToSync()
    {
        $this->assertEquals(PubSubHubbub::VERIFICATION_MODE_SYNC, $this->subscriber->getPreferredVerificationMode());
    }

    public function testCanSetStorageImplementation()
    {
        $storage = new Subscription($this->tableGateway);
        $this->subscriber->setStorage($storage);
        $this->assertThat($this->subscriber->getStorage(), $this->identicalTo($storage));
    }


    public function testGetStorageThrowsExceptionIfNoneSet()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->subscriber->getStorage();
    }

    protected function _getCleanMock($className)
    {
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
