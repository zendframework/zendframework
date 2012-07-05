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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Amazon\Sqs;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Sqs
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Amazon service consumer object
     *
     * @var Zend_Service_Amazon_Sqs
     */
    protected $_amazon;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $_httpClientAdapterSocket;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_Service_Amazon online tests are not enabled');
        }
        $this->_amazon = new \Zend\Service\Amazon\Sqs\Sqs(
            constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
            constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
        );

        $this->_queue_name = constant('TESTS_ZEND_SERVICE_AMAZON_SQS_QUEUE');

        $this->_httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();

        $this->_amazon->getHttpClient()
                      ->setAdapter($this->_httpClientAdapterSocket);
    }

    /**
     * Test SQS methods
     *
     * @return void
     */
    public function testSqs()
    {
        $queue_url = $this->_amazon->create($this->_queue_name, 45);
        $timeout = $this->_amazon->getAttribute($queue_url, 'VisibilityTimeout');
        $this->assertEquals(45, $timeout, 'VisibilityTimeout attribute is not 45');

        $test_msg = 'this is a test';
        $this->_amazon->send($queue_url, $test_msg);

        $messages = $this->_amazon->receive($queue_url);

        foreach ($messages as $message) {
            $this->assertEquals($test_msg, $message['body']);
        }

        foreach ($messages as $message) {
            $result = $this->_amazon->deleteMessage($queue_url, $message['handle']);
            $this->assertTrue($result, 'Message was not deleted');
        }

        $count = $this->_amazon->count($queue_url);
        $this->assertEquals(0, $count);

        $this->_amazon->delete($queue_url);
    }

    /**
     * Tear down the test case
     *
     * @return void
     */
    public function tearDown()
    {
        if (!constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ENABLED')) {
            return;
        }
        unset($this->_amazon);
    }
}
