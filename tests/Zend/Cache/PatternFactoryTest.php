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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
