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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\GoGrid;
use Zend\Service\GoGrid\Job;

/**
 * Test helper
 */

/**
 * @see Zend_Service_Amazon
 */

/**
 * @see Zend_Service_Amazon_ResultSet
 */

/**
 * @see Zend_Service_Amazon_ResultSet
 */

/**
 * @see Zend_Http_Client_Adapter_Socket
 */

/**
 * @see Zend\Http\Client\Adapter\Test
 */


/**
 * @category   Zend
 * @package    Zend\Service\GoGrid
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
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
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->_job = new Job(TESTS_ZEND_SERVICE_GOGRID_OFFLINE_KEY,TESTS_ZEND_SERVICE_GOGRID_OFFLINE_SECRET);

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
        $job= new Job(null,TESTS_ZEND_SERVICE_GOGRID_OFFLINE_SECRET);
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
        $job= new Job(TESTS_ZEND_SERVICE_GOGRID_OFFLINE_KEY,null);
    }
    /**
     * testJobList
     *
     * @return void
     */
    public function testJobList()
    {
        $file= file_get_contents(__DIR__."/_files/job_list.json");
        $joblist= new Zend\Service\GoGrid\ObjectList($file);

        $this->assertEquals(count($joblist),2);
        $this->assertEquals($joblist['status'],'success');
        
        $job= $joblist[0];
        $this->assertEquals($job->getAttribute('id'), '60531');
        $this->assertEquals($job->getAttribute('owner'),'ankit@gogrid.com');
        $command= $job->getAttribute('command');
        $this->assertEquals($command['name'],'CreateVirtualServer');
        $history= $job->getAttribute('history');
        $this->assertEquals($history[0]['id'],'10242');
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

}
