<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Rackspace\Servers;

use Zend\Service\Rackspace\Servers;


/**
 * @category   Zend
 * @package    Zend\Service\Rackspace\Servers
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 */
class OnlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to Rackspace Servers object
     *
     * @var Zend\Service\Rackspace\Servers
     */
    protected $rackspace;

    /**
     * Check if the resize was successfully done
     *
     * @var boolean
     */
    protected $resize;

    /**
     * List of flavors available
     *
     * @var array
     */
    protected $flavors;

    /**
     * List of images available
     *
     * @var Zend\Service\Rackspace\Servers\ImageList
     */
    protected $images;

    /**
     * Id of the image created
     *
     * @var string
     */
    protected $imageId;

    /**
     * Server id of testing
     *
     * @var integer
     */
    protected $serverId;

    /**
     * Admin password of the server
     *
     * @var string
     */
    protected $adminPass;

    /**
     * Shared Ip group
     *
     * @var Zend\Service\Rackspace\Servers\SharedIpGroup
     */
    protected $sharedIpGroup;

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
        if (!constant('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED') || TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_ENABLED != true) {
            $this->markTestSkipped('Zend\Service\Rackspace\Servers online tests are not enabled');
        }
        if(!defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER') || !defined('TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY')) {
            self::markTestSkipped('Constants User and Key have to be set.');
        }

        $this->rackspace = new Servers(TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_USER,
            TESTS_ZEND_SERVICE_RACKSPACE_ONLINE_KEY);

        $this->httpClientAdapterSocket = new \Zend\Http\Client\Adapter\Socket();

        $this->rackspace->getHttpClient()
                ->setAdapter($this->httpClientAdapterSocket);

        // terms of use compliance: safe delay between each test
        sleep(2);
    }

    /**
     * Wait n seconds for status change
     *
     * @param string  $status
     * @param integer $timeout
     * @return boolean
     */
    protected function waitForStatus($status,$timeout=TESTS_ZEND_SERVICE_RACKSPACE_TIMEOUT)
    {
        $info['status']= null;
        $i=0;
        while ((strtoupper($info['status'])!==strtoupper($status)) && ($i<$timeout)) {
            $info = $this->rackspace->getServer($this->serverId)->toArray();
            $i+=5;
            sleep(5);
        }
        return ($i<$timeout);
    }

    /**
     * Test constants
     */
    public function testConstants()
    {
        $this->assertEquals(10240, Servers::LIMIT_FILE_SIZE);
        $this->assertEquals(5,Servers::LIMIT_NUM_FILE);
        $this->assertEquals('json',Servers::API_FORMAT);
    }

    /**
     * Test authentication
     */
    public function testAuthentication()
    {
        $this->filename = __METHOD__;
        $this->assertTrue($this->rackspace->authenticate());
    }

    /**
     * Test create server
     */
    public function testCreateServer()
    {
        $data = array (
            'name'     => TESTS_ZEND_SERVICE_RACKSPACE_SERVER_NAME,
            'imageId'  => TESTS_ZEND_SERVICE_RACKSPACE_SERVER_IMAGEID,
            'flavorId' => TESTS_ZEND_SERVICE_RACKSPACE_SERVER_FLAVORID
        );
        $server= $this->rackspace->createServer($data);
        $this->assertTrue($server!==false);
        $this->serverId= $server->getId();
        $this->adminPass= $server->getAdminPass();
        $this->assertEquals(TESTS_ZEND_SERVICE_RACKSPACE_SERVER_NAME,$server->getName());
        $this->assertTrue($this->waitForStatus('active'));
    }

    /**
     * Test Get Server
     */
    public function testGetServer()
    {
        $server= $this->rackspace->getServer($this->serverId);
        $this->assertTrue($server!==false);
        $this->assertEquals(TESTS_ZEND_SERVICE_RACKSPACE_SERVER_NAME,$server->getName());
    }

    /**
     * Test list servers
     */
    public function testListServers()
    {
        $servers= $this->rackspace->listServers();
        $this->assertTrue($servers!==false);
    }

    /**
     * Test change server name
     */
    public function testChangeServerName()
    {
        $this->assertTrue($this->rackspace->changeServerName($this->serverId,TESTS_ZEND_SERVICE_RACKSPACE_SERVER_NAME.'_renamed'));
    }

    /**
     * Test rechange server name
     */
    public function testRechangeServerName()
    {
        $this->assertTrue($this->rackspace->changeServerName($this->serverId,TESTS_ZEND_SERVICE_RACKSPACE_SERVER_NAME));
    }

    /**
     * Test change admin password
     */
    public function testChangeServerPassword()
    {
        $this->adminPass= md5(time().rand());
        $this->assertTrue($this->rackspace->changeServerPassword($this->serverId,$this->adminPass));
    }

    /**
     * Test get server IP
     */
    public function testGetServerIp()
    {
        $addresses= $this->rackspace->getServerIp($this->serverId);
        $this->assertTrue(!empty($addresses['public']) && is_array($addresses['public']));
        $this->assertTrue(!empty($addresses['private']) && is_array($addresses['private']));
    }

    /**
     * Test get server public IP
     */
    public function testGetServerPublicIp()
    {
        $public= $this->rackspace->getServerPublicIp($this->serverId);
        $this->assertTrue(!empty($public) && is_array($public));
    }

    /**
     * Test get server private IP
     */
    public function testGetServerPrivateIp()
    {
        $private= $this->rackspace->getServerPrivateIp($this->serverId);
        $this->assertTrue(!empty($private) && is_array($private));
    }

    /**
     * Test reboot the server
     */
    public function testSoftRebootServer()
    {
        $this->assertTrue($this->rackspace->rebootServer($this->serverId));
        $this->assertTrue($this->waitForStatus('active'));
    }

    /**
     * Test hard reboot the server
     */
    public function testHardRebootServer()
    {
        $this->assertTrue($this->rackspace->rebootServer($this->serverId,true));
        $this->assertTrue($this->waitForStatus('active'));
    }

    /**
     * Test rebuild the server image
     */
    public function testRebuildServer()
    {
        $this->assertTrue($this->rackspace->rebuildServer($this->serverId,TESTS_ZEND_SERVICE_RACKSPACE_SERVER_NEW_IMAGEID));
    }

    /**
     * Test resize server
     */
    public function testResizeServer()
    {
         $this->markTestSkipped('Resize server skipped');
    }

    /**
     * Test confirm resize server
     */
    public function testConfirmResizeServer()
    {
        $this->markTestSkipped('Confirm resize server skipped');
    }

    /**
     * Test revert resize server
     */
    public function testRevertResizeServer()
    {
        $this->markTestSkipped('Revert resize server skipped');
    }

    /**
     * Test list flavors
     */
    public function testListFlavors()
    {
        $this->flavors= $this->rackspace->listFlavors(true);
        $this->assertTrue(is_array($this->flavors) && !empty($this->flavors));
        $this->assertTrue(isset($this->flavors[0]['id']));
    }

    /**
     * Test get flavor
     */
    public function testGetFlavor()
    {
        $flavor= $this->rackspace->getFlavor($this->flavors[0]['id']);
        $this->assertTrue(is_array($flavor) && !empty($flavor));
        $this->assertEquals($flavor['id'],$this->flavors[0]['id']);
    }

    /**
     * Test list images
     */
    public function testListImages()
    {
        $this->images= $this->rackspace->listImages(true);
        $this->assertTrue(count($this->images)>0);
        $image= $this->images[0];
        $imageId= $image->getId();
        $this->assertTrue(!empty($imageId));
    }

    /**
     * Test get image
     */
    public function testGetImage()
    {
        $image= $this->images[0];
        $getImage= $this->rackspace->getImage($image->getId());
        $this->assertEquals($getImage->getId(),$image->getId());
    }

    /**
     * Test get image info
     */
    public function testGetImageInfo()
    {
        $image= $this->rackspace->getImage($this->images[0]->getId())->toArray();
        $this->assertTrue(is_array($image) && !empty($image));
        $this->assertEquals($image['id'],$this->images[0]->getId());
    }

    /**
     * Test create image
     */
    public function testCreateImage()
    {
        $image= $this->rackspace->createImage($this->serverId, TESTS_ZEND_SERVICE_RACKSPACE_SERVER_IMAGE_NAME);
        if ($image!==false) {
            $this->imageId= $image->getId();
        }
        $this->assertTrue($image!==false);
        $this->assertEquals($image->getName(),TESTS_ZEND_SERVICE_RACKSPACE_SERVER_IMAGE_NAME);
    }

    /**
     * Test delete image
     */
    public function testDeleteImage()
    {
        if (isset($this->imageId)) {
            $this->assertTrue($this->rackspace->deleteImage($this->imageId));
        } else {
            $this->markTestSkipped('Delete image skipped because the new image has not been created');
        }
    }

    /**
     * Test get backup schedule
     */
    public function testGetBackupSchedule()
    {
        $this->markTestSkipped('Get backup schedule skipped');
    }

    /**
     * Test change backup schedule
     */
    public function testChangeBackupSchedule()
    {
        $this->markTestSkipped('Change backup schedule skipped');
    }

    /**
     * Test disable backup schedule
     */
    public function testDisableBackupSchedule()
    {
        $this->markTestSkipped('Disable backup schedule skipped');
    }

    /**
     * Test create shared Ip group
     */
    public function testCreateSharedIpGroup()
    {
        $this->sharedIpGroup= $this->rackspace->createSharedIpGroup(TESTS_ZEND_SERVICE_RACKSPACE_SERVER_SHARED_IP_GROUP_NAME, $this->serverId);
        $this->assertTrue($this->sharedIpGroup!==false);
        $this->assertEquals($this->sharedIpGroup->getName(),TESTS_ZEND_SERVICE_RACKSPACE_SERVER_SHARED_IP_GROUP_NAME);
    }

    /**
     * Test list shared ip groups
     */
    public function testListSharedIpGroups()
    {
        $groups= $this->rackspace->listSharedIpGroups(true);
        $this->assertTrue($groups!==false);
    }

    /**
     * Test get shared IP group
     */
    public function testGetSharedIpGroup()
    {
        $groupId= $this->sharedIpGroup->getId();
        $group= $this->rackspace->getSharedIpGroup($groupId);
        $this->assertTrue($group!==false);
        $this->assertEquals($group->getId(), $groupId);
    }

    /**
     * Test delete shared ip group
     */
    public function testDeleteSharedIpGroup()
    {
        $this->assertTrue($this->rackspace->deleteSharedIpGroup($this->sharedIpGroup->getId()));
    }

    /**
     * Test delete server
     */
    public function testDeleteServer()
    {
        $this->assertTrue($this->rackspace->deleteServer($this->serverId));
    }
}


/**
 * @category   Zend
 * @package    Zend\Service\Rackspace\Servers
 * @subpackage UnitTests
 * @group      Zend\Service
 * @group      Zend\Service\Rackspace
 */
class Skip extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend\Service\Rackspace\Servers online tests not enabled with an access key ID in '
                             . 'TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}
