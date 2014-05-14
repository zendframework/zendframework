<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
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
