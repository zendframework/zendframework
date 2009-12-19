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

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Service/Amazon/Sqs.php';
require_once 'Zend/Http/Client/Adapter/Socket.php';

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Sqs
 */
class Zend_Service_Amazon_Sqs_OnlineTest extends PHPUnit_Framework_TestCase
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
        $this->_amazon = new Zend_Service_Amazon_Sqs(
            constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_ACCESSKEYID'),
            constant('TESTS_ZEND_SERVICE_AMAZON_ONLINE_SECRETKEY')
        );

        $this->_queue_name = constant('TESTS_ZEND_SERVICE_AMAZON_SQS_QUEUE');

        $this->_httpClientAdapterSocket = new Zend_Http_Client_Adapter_Socket();

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
        try {
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
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * Tear down the test case
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_amazon);
    }
}


class Zend_Service_Amazon_Sqs_OnlineTest_Skip extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped(
            'Zend_Service_Amazon_Sqs online tests not enabled with an access key ID in '
            . 'TestConfiguration.php'
        );
    }

    public function testNothing()
    {
    }
}
