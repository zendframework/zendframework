<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache\Storage\Adapter\MemcachedResourceManager;

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class MemcachedResourceManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The resource manager
     *
     * @var MemcachedResourceManager
     */
    protected $resourceManager;

    public function setUp()
    {
        $this->resourceManager = new MemcachedResourceManager();
    }

    /**
     * Data provider to test valid resources
     *
     * Returns an array of the following structure:
     * array(array(
     *     <string resource id>,
     *     <mixed input resource>,
     *     <string normalized persistent id>,
     *     <array normalized lib options>,
     *     <array normalized server list>
     * )[, ...])
     *
     * @return array
     */
    public function validResourceProvider()
    {
        $data = array(
            // empty resource
            array(
                'testEmptyResource',
                array(),
                '',
                array(),
                array(),
            ),

            // stringify persistent id
            array(
                'testStringifyPersistentId',
                array('persistent_id' => 1234),
                '1234',
                array(),
                array(),
            ),

            // servers given as string
            array(
                'testServersGivenAsString',
                array(
                    'servers' => '127.0.0.1:1234,127.0.0.1,192.1.0.1?weight=3,localhost,127.0.0.1:11211?weight=0',
                ),
                '',
                array(
                    array('host' => '127.0.0.1', 'port' => 1234,  'weight' => 0),
                    array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 0),
                    array('host' => '192.1.0.1', 'port' => 11211, 'weight' => 3),
                    array('host' => 'localhost', 'port' => 11211, 'weight' => 0),
                ),
                array(),
            ),

            // servers given as list of strings
            array(
                'testServersGivenAsListOfStrings',
                array(
                    'servers' => array(
                        '127.0.0.1:1234',
                        '127.0.0.1',
                        '192.1.0.1?weight=3',
                        'localhost',
                        '127.0.0.1:11211?weight=0'
                    ),
                ),
                '',
                array(
                    array('host' => '127.0.0.1', 'port' => 1234,  'weight' => 0),
                    array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 0),
                    array('host' => '192.1.0.1', 'port' => 11211, 'weight' => 3),
                    array('host' => 'localhost', 'port' => 11211, 'weight' => 0),
                ),
                array(),
            ),

            // servers given as list of arrays
            array(
                'testServersGivenAsListOfArrays',
                array(
                    'servers' => array(
                        array('127.0.0.1', 1234),
                        array('127.0.0.1'),
                        array('192.1.0.1', 11211, 3),
                        array('localhost'),
                        array('127.0.0.1', 11211, 0),
                    ),
                ),
                '',
                array(
                    array('host' => '127.0.0.1', 'port' => 1234,  'weight' => 0),
                    array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 0),
                    array('host' => '192.1.0.1', 'port' => 11211, 'weight' => 3),
                    array('host' => 'localhost', 'port' => 11211, 'weight' => 0),
                ),
                array(),
            ),

            // servers given as list of assoc arrays
            array(
                'testServersGivenAsListOfAssocArrays',
                array(
                    'servers' => array(
                        array(
                           'host' => '127.0.0.1',
                           'port' => 1234,
                        ),
                        array(
                           'host' => '127.0.0.1',
                        ),
                        array(
                            'host'   => '192.1.0.1',
                            'weight' => 3,
                        ),
                        array(
                            'host' => 'localhost',
                        ),
                        array(
                            'host' => '127.0.0.1',
                            'port' => 11211,
                            'weight' => 0,
                        ),
                    ),
                ),
                '',
                array(
                    array('host' => '127.0.0.1', 'port' => 1234,  'weight' => 0),
                    array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 0),
                    array('host' => '192.1.0.1', 'port' => 11211, 'weight' => 3),
                    array('host' => 'localhost', 'port' => 11211, 'weight' => 0),
                ),
                array(),
            ),

            // lib options given as name
            array(
                'testLibOptionsGivenAsName',
                array(
                    'lib_options' => array(
                        'COMPRESSION' => false,
                        'PREFIX_KEY'  => 'test_',
                    ),
                ),
                '',
                array(),
                class_exists('Memcached', false) ? array(
                    \Memcached::OPT_COMPRESSION => false,
                    \Memcached::OPT_PREFIX_KEY  => 'test_',
                ) : array(),
            ),

            // lib options given as constant value
            array(
                'testLibOptionsGivenAsName',
                array(
                    'lib_options' => class_exists('Memcached', false) ? array(
                        \Memcached::OPT_COMPRESSION => false,
                        \Memcached::OPT_PREFIX_KEY  => 'test_',
                    ) : array(),
                ),
                '',
                array(),
                class_exists('Memcached', false) ? array(
                    \Memcached::OPT_COMPRESSION => false,
                    \Memcached::OPT_PREFIX_KEY  => 'test_',
                ) : array(),
            ),
        );

        return $data;
    }

    /**
     * @dataProvider validResourceProvider
     * @param string $resourceId
     * @param mixed  $resource
     * @param string $expectedPersistentId
     * @param array  $expectedServers
     * @param array  $expectedLibOptions
     */
    public function testValidResources($resourceId, $resource, $expectedPersistentId, $expectedServers, $expectedLibOptions)
    {
        // php-memcached is required to set libmemcached options
        if (is_array($resource) && isset($resource['lib_options']) && count($resource['lib_options']) > 0) {
            if (!class_exists('Memcached', false)) {
                $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException', 'Unknown libmemcached option');
            }
        }

        $this->assertSame($this->resourceManager, $this->resourceManager->setResource($resourceId, $resource));
        $this->assertTrue($this->resourceManager->hasResource($resourceId));

        $this->assertSame($expectedPersistentId, $this->resourceManager->getPersistentId($resourceId));
        $this->assertEquals($expectedServers, $this->resourceManager->getServers($resourceId));
        $this->assertEquals($expectedLibOptions, $this->resourceManager->getLibOptions($resourceId));

        $this->assertSame($this->resourceManager, $this->resourceManager->removeResource($resourceId));
        $this->assertFalse($this->resourceManager->hasResource($resourceId));
    }

    public function testSetLibOptionsOnExistingResource()
    {
        $memcachedInstalled = class_exists('Memcached', false);

        $libOptions = array('compression' => false);
        $resourceId = 'testResourceId';
        $resourceMock = $this->getMock('Memcached', array('setOptions'));

        if (!$memcachedInstalled) {
            $this->setExpectedException('Zend\Cache\Exception\InvalidArgumentException');
        } else {
            $resourceMock
                ->expects($this->once())
                ->method('setOptions')
                ->with($this->isType('array'));
        }

        $this->resourceManager->setResource($resourceId, $resourceMock);
        $this->resourceManager->setLibOptions($resourceId, $libOptions);
    }
}
