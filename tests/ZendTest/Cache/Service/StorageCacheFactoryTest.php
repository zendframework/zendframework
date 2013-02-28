<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Service;

use Zend\Cache;
use Zend\ServiceManager\ServiceManager;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class StorageCacheFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $sm;

    public function setUp()
    {
        Cache\StorageFactory::resetAdapterPluginManager();
        Cache\StorageFactory::resetPluginManager();
        $this->sm = new ServiceManager();
        $this->sm->setService('Config', array('cache' => array(
            'adapter' => 'Memory',
            'plugins' => array('Serializer', 'ClearExpiredByFactor'),
        )));
        $this->sm->setFactory('CacheFactory', 'Zend\Cache\Service\StorageCacheFactory');
    }

    public function tearDown()
    {
        Cache\StorageFactory::resetAdapterPluginManager();
        Cache\StorageFactory::resetPluginManager();
    }

    public function testCreateServiceCache()
    {
        $cache = $this->sm->get('CacheFactory');
        $this->assertEquals('Zend\Cache\Storage\Adapter\Memory', get_class($cache));
    }
}
