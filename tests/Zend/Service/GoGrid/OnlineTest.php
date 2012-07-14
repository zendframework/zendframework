<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\GoGrid;

use Zend\Service\GoGrid\Server;


/**
 * @category   Zend
 * @package    Zend\Service\GoGrid
 * @subpackage UnitTests
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to GoGrid Server object
     *
     * @var Zend\Service\GoGrid\Server
     */
    protected $gogrid;

    /**
     * Server id of testing
     *
     * @var integer
     */
    protected $serverId;

    /**
     * Socket based HTTP client adapter
     *
     * @var Zend_Http_Client_Adapter_Socket
     */
    protected $httpClientAdapterSocket;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_GOGRID_ONLINE_ENABLED') || TESTS_ZEND_SERVICE_GOGRID_ONLINE_ENABLED != true) {
            $this->markTestSkipped('Zend\Service\GoGrid online tests are not enabled');
        }

        if(!defined('TESTS_ZEND_SERVICE_GOGRID_ONLINE_KEY') || !defined('TESTS_ZEND_SERVICE_GOGRID_ONLINE_SECRET')) {
            $this->markTestSkipped('Constants Key and Secret have to be set.');
        }

        $this->gogrid = new Server(TESTS_ZEND_SERVICE_GOGRID_ONLINE_KEY, TESTS_ZEND_SERVICE_GOGRID_ONLINE_SECRET);

        $this->httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();

        $this->gogrid->getHttpClient()
                ->setAdapter($this->httpClientAdapterSocket);

        // terms of use compliance: safe delay between each test
        sleep(10);
    }

    /*
    public function tearDown()
    {
        file_put_contents(__DIR__ . '/_files/' . $this->getName() . '.request', $this->gogrid->getLastRequest());
        file_put_contents(__DIR__ . '/_files/' . $this->getName() . '.response', $this->gogrid->getHttpClient()->getResponse()->toString());
    }
    */

    public function testAddServer()
    {
        $result= $this->gogrid->add(TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME,
                           TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_IMAGE,
                           TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_RAM,
                           TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_IP);
        $this->assertTrue($result->isSuccess());
    }



    public function testGetServer()
    {
        $result = $this->gogrid->get(TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME);
        $this->assertTrue($result->isSuccess());
        $server = $result[0];
        $image = $server->getAttribute('image');
        $ip = $server->getAttribute('ip');
        $ram = $server->getAttribute('ram');
        $this->assertEquals($server->getAttribute('name'),TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME);
        $this->assertEquals($image['name'], TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_IMAGE);
        $this->assertEquals($ip['ip'], TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_IP);
        $this->assertEquals($ram['name'], TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_RAM);
    }

    public function testListServer()
    {
        $result = $this->gogrid->getList();
        $this->assertTrue($result->isSuccess());
        $found = false;
        foreach ($result as $server) {
            if ($server->getAttribute('name')==TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testEditServer()
    {
        $options = array (
            'description' => 'new description'
        );
        $result = $this->gogrid->edit(TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME, $options);
        $this->assertTrue($result->isSuccess());
    }

    public function testStopServer()
    {
        $result = $this->gogrid->stop(TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME);
        $this->assertTrue($result->isSuccess());

    }

    public function testStartServer()
    {
        $result = $this->gogrid->start(TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME);
        $this->assertTrue($result->isSuccess());
    }

    public function testRestartServer()
    {
        $result = $this->gogrid->restart(TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME);
        $this->assertTrue($result->isSuccess());
    }


    public function testDeleteServer()
    {
        $result = $this->gogrid->delete(TESTS_ZEND_SERVICE_GOGRID_ONLINE_SERVER_NAME);
        $this->assertTrue($result->isSuccess());
    }
}


/**
 * @category   Zend
 * @package    Zend\Service\Rackspace\Servers
 * @subpackage UnitTests
 * @group      Zend\Service
 * @group      Zend\Service\GoGrid
 */
class Skip extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend\Service\GoGrid\Server online tests not enabled with an access key ID in '
                             . 'TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}
