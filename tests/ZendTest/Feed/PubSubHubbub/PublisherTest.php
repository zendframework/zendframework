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
 */
class PublisherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Publisher */
    protected $publisher = null;

    public function setUp()
    {
        $client = new HttpClient;
        PubSubHubbub::setHttpClient($client);
        $this->publisher = new Publisher;
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
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->assertEquals(array('http://www.example.com/hub'), $this->publisher->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArray()
    {
        $this->publisher->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->publisher->getHubUrls());
    }

    public function testAddsHubServerUrlsFromArrayUsingSetConfig()
    {
        $this->publisher->setOptions(array('hubUrls' => array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        )));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->publisher->getHubUrls());
    }

    public function testRemovesHubServerUrl()
    {
        $this->publisher->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ));
        $this->publisher->removeHubUrl('http://www.example.com/hub');
        $this->assertEquals(array(
            1 => 'http://www.example.com/hub2'
        ), $this->publisher->getHubUrls());
    }

    public function testRetrievesUniqueHubServerUrlsOnly()
    {
        $this->publisher->addHubUrls(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2',
            'http://www.example.com/hub'
        ));
        $this->assertEquals(array(
            'http://www.example.com/hub', 'http://www.example.com/hub2'
        ), $this->publisher->getHubUrls());
    }

    public function testThrowsExceptionOnSettingEmptyHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->publisher->addHubUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->publisher->addHubUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidHubServerUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->publisher->addHubUrl('http://');
    }

    public function testAddsUpdatedTopicUrl()
    {
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->assertEquals(array('http://www.example.com/topic'), $this->publisher->getUpdatedTopicUrls());
    }

    public function testAddsUpdatedTopicUrlsFromArray()
    {
        $this->publisher->addUpdatedTopicUrls(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ));
        $this->assertEquals(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ), $this->publisher->getUpdatedTopicUrls());
    }

    public function testAddsUpdatedTopicUrlsFromArrayUsingSetConfig()
    {
        $this->publisher->setOptions(array('updatedTopicUrls' => array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        )));
        $this->assertEquals(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ), $this->publisher->getUpdatedTopicUrls());
    }

    public function testRemovesUpdatedTopicUrl()
    {
        $this->publisher->addUpdatedTopicUrls(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ));
        $this->publisher->removeUpdatedTopicUrl('http://www.example.com/topic');
        $this->assertEquals(array(
            1 => 'http://www.example.com/topic2'
        ), $this->publisher->getUpdatedTopicUrls());
    }

    public function testRetrievesUniqueUpdatedTopicUrlsOnly()
    {
        $this->publisher->addUpdatedTopicUrls(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2',
            'http://www.example.com/topic'
        ));
        $this->assertEquals(array(
            'http://www.example.com/topic', 'http://www.example.com/topic2'
        ), $this->publisher->getUpdatedTopicUrls());
    }

    public function testThrowsExceptionOnSettingEmptyUpdatedTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->publisher->addUpdatedTopicUrl('');
    }


    public function testThrowsExceptionOnSettingNonStringUpdatedTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->publisher->addUpdatedTopicUrl(123);
    }


    public function testThrowsExceptionOnSettingInvalidUpdatedTopicUrl()
    {
        $this->setExpectedException('Zend\Feed\PubSubHubbub\Exception\ExceptionInterface');
        $this->publisher->addUpdatedTopicUrl('http://');
    }

    public function testAddsParameter()
    {
        $this->publisher->setParameter('foo', 'bar');
        $this->assertEquals(array('foo'=> 'bar'), $this->publisher->getParameters());
    }

    public function testAddsParametersFromArray()
    {
        $this->publisher->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->publisher->getParameters());
    }

    public function testAddsParametersFromArrayInSingleMethod()
    {
        $this->publisher->setParameter(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->publisher->getParameters());
    }

    public function testAddsParametersFromArrayUsingSetConfig()
    {
        $this->publisher->setOptions(array('parameters' => array(
            'foo' => 'bar', 'boo' => 'baz'
        )));
        $this->assertEquals(array(
            'foo' => 'bar', 'boo' => 'baz'
        ), $this->publisher->getParameters());
    }

    public function testRemovesParameter()
    {
        $this->publisher->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->publisher->removeParameter('boo');
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->publisher->getParameters());
    }

    public function testRemovesParameterIfSetToNull()
    {
        $this->publisher->setParameters(array(
            'foo' => 'bar', 'boo' => 'baz'
        ));
        $this->publisher->setParameter('boo', null);
        $this->assertEquals(array(
            'foo' => 'bar'
        ), $this->publisher->getParameters());
    }

    public function testNotifiesHubWithCorrectParameters()
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertEquals('hub.mode=publish&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic&foo=bar',
                            $client->getRequest()->getContent());
    }

    public function testNotifiesHubWithCorrectParametersAndMultipleTopics()
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic2');
        $this->publisher->notifyAll();
        $this->assertEquals('hub.mode=publish&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic&hub.url=http%3A%2F%2Fwww.example.com%2Ftopic2',
                            $client->getRequest()->getContent());
    }

    public function testNotifiesHubAndReportsSuccess()
    {
        PubSubHubbub::setHttpClient($this->getClientSuccess());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertTrue($this->publisher->isSuccess());
    }

    public function testNotifiesHubAndReportsFail()
    {
        PubSubHubbub::setHttpClient($this->getClientFail());
        $client = PubSubHubbub::getHttpClient();
        $this->publisher->addHubUrl('http://www.example.com/hub');
        $this->publisher->addUpdatedTopicUrl('http://www.example.com/topic');
        $this->publisher->setParameter('foo', 'bar');
        $this->publisher->notifyAll();
        $this->assertFalse($this->publisher->isSuccess());
    }
}

class ClientNotReset extends HttpClient
{
    public function resetParameters($clearCookies = false)
    {
        // Do nothing
    }
}
