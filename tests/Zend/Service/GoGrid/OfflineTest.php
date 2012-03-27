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
 * @package    Zend\Service\GoGrid
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\GoGrid;
use Zend\Service\GoGrid\Server;

/**
 * Test helper
 */

/**
 * @category   Zend
 * @package    Zend\Service\GoGrid
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_GoGrid
 */
class OfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to GoGrid Job
     *
     * @var Zend\Service\GoGrid\Job
     */
    protected static $gogrid;
    /**
     * HTTP client adapter for testing
     *
     * @var Zend\Http\Client\Adapter\Test
     */
    protected static $httpClientAdapterTest;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        self::$gogrid = new Server('foo','bar');
        self::$httpClientAdapterTest = new \Zend\Http\Client\Adapter\Test();
        self::$gogrid->getHttpClient()->setAdapter(self::$httpClientAdapterTest);
        
        $filename= __DIR__ . '/_files/' . $this->getName() . '.response';
        if (file_exists($filename)) {
            self::$httpClientAdapterTest->setResponse($this->loadResponse($filename));    
        }    

    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function loadResponse($name)
    {
        return file_get_contents($name);
    }
    
    /**
     * Ensures that __construct() throws an exception when given an empty key attribute
     *
     * @return void
     */
    public function testConstructExceptionMissingKeyAttribute()
    {
        $this->setExpectedException(
            'Zend\Service\GoGrid\Exception\InvalidArgumentException',
            'The key cannot be empty'
        );
        $server= new Server(null,'bar');
    }
    /**
     * Ensures that __construct() throws an exception when given an empty secret attribute
     *
     * @return void
     */
    public function testConstructExceptionMissingSecretAttribute()
    {
        $this->setExpectedException(
            'Zend\Service\GoGrid\Exception\InvalidArgumentException',
            'The secret cannot be empty'
        );
        $server= new Server('foo',null);
    }
    /**
     * testApiVersion
     *
     * @return void
     */
    public function testApiVersion()
    {
        $this->assertEquals(self::$gogrid->getApiVersion(),Server::VERSION_API);
        self::$gogrid->setApiVersion('1.0');
        $this->assertEquals(self::$gogrid->getApiVersion(),'1.0');
    }
    
    public function testAddServer()
    {
        $result= self::$gogrid->add('test-zf', 'centos5.5_32_base', '512MB', '173.204.195.244');
        $this->assertTrue($result->isSuccess());
    }
    
    
    
    public function testGetServer()
    {
        $result = self::$gogrid->get('test-zf');
        $this->assertTrue($result->isSuccess());
        $server = $result[0];
        $image = $server->getAttribute('image');
        $ip = $server->getAttribute('ip');
        $ram = $server->getAttribute('ram');
        $this->assertEquals($server->getAttribute('name'),'test-zf');
        $this->assertEquals($image['name'], 'centos5.5_32_base');
        $this->assertEquals($ip['ip'], '173.204.195.244');
        $this->assertEquals($ram['name'], '512MB');
    }
    
    public function testListServer()
    {
        $result = self::$gogrid->getList();
        $this->assertTrue($result->isSuccess());
        $found = false;
        foreach ($result as $server) {
            if ($server->getAttribute('name')=='test-zf') {
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
        $result = self::$gogrid->edit('test-zf', $options);
        $this->assertTrue($result->isSuccess());
    }
    
    public function testStopServer()
    {
        $result = self::$gogrid->stop('test-zf');
        $this->assertTrue($result->isSuccess());
        
    }
    
    public function testStartServer()
    {
        $result = self::$gogrid->start('test-zf');
        $this->assertTrue($result->isSuccess());
    }
    
    public function testRestartServer()
    {
        $result = self::$gogrid->restart('test-zf');
        $this->assertTrue($result->isSuccess());
    }
    
    
    public function testDeleteServer()
    {
        $result = self::$gogrid->delete('test-zf');
        $this->assertTrue($result->isSuccess());
    }
}
