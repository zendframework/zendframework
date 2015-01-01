<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\RedisResourceManager;

/**
 * PHPUnit test case
 */

/**
 * @group      Zend_Cache
 */
class RedisResourceManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The resource manager
     *
     * @var RedisResourceManager
     */
    protected $resourceManager;

    public function setUp()
    {
        $this->resourceManager = new RedisResourceManager();
    }

    /**
     * @group 6495
     */
    public function testSetServerWithPasswordInUri()
    {
        $dummyResId = '1234567890';
        $server     = 'redis://dummyuser:dummypass@testhost:1234';

        $this->resourceManager->setServer($dummyResId, $server);

        $server = $this->resourceManager->getServer($dummyResId);

        $this->assertEquals('testhost', $server['host']);
        $this->assertEquals(1234, $server['port']);
        $this->assertEquals('dummypass', $this->resourceManager->getPassword($dummyResId));
    }

    /**
     * @group 6495
     */
    public function testSetServerWithPasswordInParameters()
    {
        $server      = 'redis://dummyuser:dummypass@testhost:1234';
        $dummyResId2 = '12345678901';
        $resource    = array(
            'persistent_id' => 1234,
            'server'        => $server,
            'password'      => 'abcd1234'
        );

        $this->resourceManager->setResource($dummyResId2, $resource);

        $server = $this->resourceManager->getServer($dummyResId2);

        $this->assertEquals('testhost', $server['host']);
        $this->assertEquals(1234, $server['port']);
        $this->assertEquals('abcd1234', $this->resourceManager->getPassword($dummyResId2));
    }

    /**
     * @group 6495
     */
    public function testSetServerWithPasswordInUriShouldNotOverridePreviousResource()
    {
        $server      = 'redis://dummyuser:dummypass@testhost:1234';
        $server2     = 'redis://dummyuser:dummypass@testhost2:1234';
        $dummyResId2 = '12345678901';
        $resource    = array(
            'persistent_id' => 1234,
            'server'        => $server,
            'password'      => 'abcd1234'
        );

        $this->resourceManager->setResource($dummyResId2, $resource);
        $this->resourceManager->setServer($dummyResId2, $server2);

        $server = $this->resourceManager->getServer($dummyResId2);

        $this->assertEquals('testhost2', $server['host']);
        $this->assertEquals(1234, $server['port']);
        // Password should not be overridden
        $this->assertEquals('abcd1234', $this->resourceManager->getPassword($dummyResId2));
    }

    /**
     * Test with 'persistent_id'
     */
    public function testValidPersistentId()
    {
        $resourceId = 'testValidPersistentId';
        $resource   = array(
            'persistent_id' => 1234,
            'server' => array(
                'host' => 'localhost'
            ),
        );
        $expectedPersistentId = '1234';
        $this->resourceManager->setResource($resourceId, $resource);
        $this->assertSame($expectedPersistentId, $this->resourceManager->getPersistentId($resourceId));
    }

    /**
     * Test with 'persistend_id'
     */
    public function testNotValidPersistentId()
    {
        $resourceId = 'testNotValidPersistentId';
        $resource   = array(
            'persistend_id' => 1234,
            'server' => array(
                'host' => 'localhost'
            ),
        );
        $expectedPersistentId = '1234';
        $this->resourceManager->setResource($resourceId, $resource);

        $this->assertNotSame($expectedPersistentId, $this->resourceManager->getPersistentId($resourceId));
        $this->assertEmpty($this->resourceManager->getPersistentId($resourceId));
    }
}
