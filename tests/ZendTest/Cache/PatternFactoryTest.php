<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class PatternFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        Cache\PatternFactory::resetPluginManager();
    }

    public function tearDown()
    {
        Cache\PatternFactory::resetPluginManager();
    }

    public function testDefaultPluginManager()
    {
        $plugins = Cache\PatternFactory::getPluginManager();
        $this->assertInstanceOf('Zend\Cache\PatternPluginManager', $plugins);
    }

    public function testChangePluginManager()
    {
        $plugins = new Cache\PatternPluginManager();
        Cache\PatternFactory::setPluginManager($plugins);
        $this->assertSame($plugins, Cache\PatternFactory::getPluginManager());
    }

    public function testFactory()
    {
        $pattern1 = Cache\PatternFactory::factory('capture');
        $this->assertInstanceOf('Zend\Cache\Pattern\CaptureCache', $pattern1);

        $pattern2 = Cache\PatternFactory::factory('capture');
        $this->assertInstanceOf('Zend\Cache\Pattern\CaptureCache', $pattern2);

        $this->assertNotSame($pattern1, $pattern2);
    }
}
