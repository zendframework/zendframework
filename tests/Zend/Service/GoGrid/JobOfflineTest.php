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
 * @package    Zend_Service_GoGrid
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\GoGrid;
use Zend\Service\GoGrid\Job,
        Zend\Service\GoGrid\ObjectList,
        Zend\Http\Client\Adapter\Test as HttpTest;

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
class JobOfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to GoGrid Job
     *
     * @var Zend\Service\GoGrid\Job
     */
    protected $_job;
    /**
     * HTTP client adapter for testing
     *
     * @var Zend\Http\Client\Adapter\Test
     */
    protected $_httpClientAdapterTest;
    /**
     * Path to test data files
     *
     * @var string
     */
    protected $_filesPath;
    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->_job = new Job(TESTS_ZEND_SERVICE_GOGRID_ONLINE_KEY,TESTS_ZEND_SERVICE_GOGRID_ONLINE_SECRET);
        $this->_filesPath   = __DIR__ . '/_files';
        $this->_httpClientAdapterTest = new HttpTest();

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
        $job= new Job(null,TESTS_ZEND_SERVICE_GOGRID_ONLINE_SECRET);
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
        $job= new Job(TESTS_ZEND_SERVICE_GOGRID_ONLINE_KEY,null);
    }
    /**
     * testJobList
     *
     * @return void
     */
    public function testJobList()
    {
        $this->_job->getHttpClient()
                    ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        $joblist= $this->_job->getList();

        $this->assertEquals(count($joblist),2);
        $this->assertEquals($joblist->getStatus(),'success');
        
        $job= $joblist[0];
        $this->assertEquals($job->getAttribute('id'), '583288');
        $this->assertEquals($job->getAttribute('owner'),'enrico@zend.com');
        $command= $job->getAttribute('command');
        $this->assertEquals($command['name'],'DeleteVirtualServer');
        $history= $job->getAttribute('history');
        $this->assertEquals($history[0]['id'],'3303238');
        $this->assertEquals(count($history),4);
    }
    /**
     * testApiVersion
     *
     * @return void
     */
    public function testApiVersion()
    {
        $this->assertEquals($this->_job->getApiVersion(),Job::VERSION_API);
        $this->_job->setApiVersion('1.0');
        $this->assertEquals($this->_job->getApiVersion(),'1.0');
    }
    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function _loadResponse($name)
    {
        return file_get_contents("$this->_filesPath/$name.response");
    }
}
