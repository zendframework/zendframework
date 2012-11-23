<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
class PatternCacheFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $sm;

    public function setUp()
    {
        Cache\StorageFactory::resetAdapterPluginManager();
        Cache\StorageFactory::resetPluginManager();
        $this->sm = new ServiceManager();
        $this->sm->setService('Config', array('cache_pattern' => 'capture'));
        $this->sm->setFactory('PatternCacheFactory', 'Zend\Cache\Service\PatternCacheFactory');
    }

    public function tearDown()
    {
        Cache\StorageFactory::resetAdapterPluginManager();
        Cache\StorageFactory::resetPluginManager();
    }

    public function testCreateServiceCache()
    {
        $cache = $this->sm->get('PatternCacheFactory');
        $this->assertEquals('Zend\Cache\Pattern\CaptureCache', get_class($cache));
    }
}
