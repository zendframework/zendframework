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
 * @package    Zend\Service\Rackspace
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Rackspace\Files;
use Zend\Service\Rackspace\Files as RackspaceFiles,
        Zend\Service\Rackspace\Files\ContainerList,
        Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * Test helper
 */

/**
 * @category   Zend
 * @package    Zend\Service\Rackspace\Files
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend\Service
 * @group      Zend\Service\Rackspace
 * @group      Zend\Service\Rackspace\Files
 */
class OfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to RackspaceFiles
     *
     * @var Zend\Service\Rackspace\Files
     */
    protected $_files;
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
        $this->_files = new RackspaceFiles(TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER,TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY);
        $this->_filesPath   = __DIR__ . '/_files';
        $this->_httpClientAdapterTest = new HttpTest();
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
    /**
     * Test the get of all the containers
     *
     * @return void
     */
    public function testGetContainers()
    {
        $this->_files->getHttpClient()
                    ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse('../../_files/testAuthenticate'));
        $this->assertTrue($this->_files->authenticate(),'Authentication failed');

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));
        $containers= $this->_files->getContainers();
        $this->assertTrue($this->_files->isSuccessful(),'Get containers failed');
        $this->assertEquals($this->_files->getCountContainers(),3,'Total containers count is wrong');
        $this->assertEquals($this->_files->getSizeContainers(),27809,'Total objects size is wrong');
        $this->assertEquals($this->_files->getCountObjects(),6,'Total objects count is wrong');
        $this->assertEquals($containers[1]->getName(),'foo');
        $this->assertEquals($containers[1]->getObjectCount(),2);
        $this->assertEquals($containers[1]->getSize(),9756);
        $this->assertEquals($containers[2]->getName(),'test');
        $this->assertEquals($containers[2]->getObjectCount(),3);
        $this->assertEquals($containers[2]->getSize(),17839);
    }
    /**
     * Test the get of a container
     *
     * @return void
     */
    public function testGetContainer()
    {
        $this->_files->getHttpClient()
                    ->setAdapter($this->_httpClientAdapterTest);

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse('../../_files/testAuthenticate'));
        $this->assertTrue($this->_files->authenticate(),'Authentication failed');

        $this->_httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        $container= $this->_files->getContainer('foo');
        $this->assertTrue($this->_files->isSuccessful(),'Get container failed');
        $this->assertEquals($container->getName(),'foo','The name of container is wrong');
        $this->assertEquals($container->getSize(),9756,'The size in bytes is wrong');
        $this->assertEquals($container->getObjectCount(),2,'The objects count is wrong');
        $metadata= array(
            'foo' => 'bar',
            'foo2' => 'bar2'
        );
        $this->assertEquals($container->getMetadata(),$metadata,'The metadata is wrong');
    }
}
