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

use Zend\Http\Client as HttpClient;
use Zend\Http\Response as HttpResponse;
use Zend\Feed\PubSubHubbub\Publisher;
use Zend\Feed\PubSubHubbub\PubSubHubbub;

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @group      Zend_Feed
 * @group      Zend_Feed_Subsubhubbub
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PublisherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Publisher */
    protected $_publisher = null;

    public function setUp()
    {
        $client = new HttpClient;
        PubSubHubbub::setHttpClient($client);
        $this->_publisher = new Publisher;
    }

    public function getClientSuccess()
    {
        $response = new HttpResponse();
        $response->setStatusCode(204);

        $client = new ClientNotReset();
        $client->setResponse($response);

        return $client;
    }

    public function getClientFail()
    {
        $response = new HttpResponse();
        $response->setStatusCode(404);

        $client = new ClientNotReset();
        $client->setResponse($response);

        return $client;
    }

    public function testAddsHubServerUrl()
    {
        $this->_publisher->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(array('http://www.example.com/hub'), $this->_publisher->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray()
    {
        $this->_publisher->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->_publisher->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArrayUsingSetConfig()
    {
        $this->_publisher->setOptions(array('hubUrls' => array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        )));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->_publisher->getHubUrls());
    }

    public function testRemovesHubServerUrl()
    {
        $this->_publisher->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->_publisher->removeHubUrl('http://www.example.com/hub');
        $this->assertEquals(array(
            1 => 'http://www.example.com/hub2'
        ), $this->_publisher->getHubUrls());
    }

    public function testRetrievesUniqueHubServerUrlsOnly()
    {
        $this->_publisher->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2',
            'http://www.example.com/hub'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->_publisher->getHubUrls());
    }

    public function testThrowsExceptionOnSettingEmptyHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_publisher->addHubUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_publisher->addHubUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_publisher->addHubUrl('http://');
    }

    public function testAddsUpdatedTopicUrl()
    {
        $this->_publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->assertEquals(array('http://www.example.com/topic'), $this->_publisher->getUpdatedTopicUrls());
    }

    public function testAddsUpdatedTopicUrlsFromArray()
    {
        $this->_publisher->addUpdatedTopicUrls(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ));
        $this->assertEquals(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ), $this->_publisher->getUpdatedTopicUrls());
    }

    public function testAddsUpdatedTopicUrlsFromArrayUsingSetConfig()
    {
        $this->_publisher->setOptions(array('updatedTopicUrls' => array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        )));
        $this->assertEquals(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ), $this->_publisher->getUpdatedTopicUrls());
    }

    public function testRemovesUpdatedTopicUrl()
    {
        $this->_publisher->addUpdatedTopicUrls(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ));
        $this->_publisher->removeUpdatedTopicUrl('http://www.example.com/topic');
        $this->assertEquals(array(
            1 => 'http://www.example.com/topic2'
        ), $this->_publisher->getUpdatedTopicUrls());
    }

    public function testRetrievesUniqueUpdatedTopicUrlsOnly()
    {
        $this->_publisher->addUpdatedTopicUrls(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2',
            'http://www.example.com/topic'
        ));
        $this->assertEquals(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ), $this->_publisher->getUpdatedTopicUrls());
    }

    public function testThrowsExceptionOnSettingEmptyUpdatedTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_publisher->addUpdatedTopicUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringUpdatedTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_publisher->addUpdatedTopicUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidUpdatedTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->_publisher->addUpdatedTopicUrl('http://');
    }

    public function testAddsParameter()
    {
        $this->_publisher->setParameter('foo', 'bar');
        $this->assertEquals(array('foo'=> 'bar'), $this->_publisher->getParameters());
    }

    public function testAddsParametersFromArray()
    {
        $this->_publisher->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->_publisher->getParameters());
    }

    public function testAddsParametersFromArrayInSingleMethod()
    {
        $this->_publisher->setParameter(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->_publisher->getParameters());
    }

    public function testAddsParametersFromArrayUsingSetConfig()
    {
        $this->_publisher->setOptions(array('parameters' => array(
            'foo' => 'bar', 'boo' => 'baz'
        )));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->_publisher->getParameters());
    }

    public function testRemovesParameter()
    {
        $this->_publisher->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->_publisher->removeParameter('boo');
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->_publisher->getParameters());
    }

    public function testRemovesParameterIfSetToNull()
    {
        $this->_publisher->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->_publisher->setParameter('boo', null);
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->_publisher->getParameters());
    }

    public function testNotifiesHubWithCorrectParameters()
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->_publisher->addHubUrl('http://www.example.com/hub');
        $this->_publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->_publisher->setParameter('foo', 'bar');
        $this->_publisher->notifyAll();
        $this->assertEquals('hub.mode=publish&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic&foo=bar',
                            $client->getRequest()->getContent());
    }

    public function testNotifiesHubWithCorrectParametersAndMultipleTopics()
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->_publisher->addHubUrl('http://www.example.com/hub');
        $this->_publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->_publisher->addUpdatedTopicUrl('http://www.example.com/topic2');
        $this->_publisher->notifyAll();
        $this->assertEquals('hub.mode=publish&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic2',
                            $client->getRequest()->getContent());
    }

    public function testNotifiesHubAndReportsSuccess()
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->_publisher->addHubUrl('http://www.example.com/hub');
        $this->_publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->_publisher->setParameter('foo', 'bar');
        $this->_publisher->notifyAll();
        $this->assertTrue($this->_publisher->isSuccess());
    }

    public function testNotifiesHubAndReportsFail()
    {
        PubSubHubbub::setHttpClient($this->getClientFail());
        $client = PubSubHubbub::getHttpClient();
        $this->_publisher->addHubUrl('http://www.example.com/hub');
        $this->_publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->_publisher->setParameter('foo', 'bar');
        $this->_publisher->notifyAll();
        $this->assertFalse($this->_publisher->isSuccess());
    }
}

class ClientNotReset extends HttpClient
{
    public function resetParameters($clearCookies = false) {
        // Do nothing
    }
}