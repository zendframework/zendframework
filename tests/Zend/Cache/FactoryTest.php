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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache;
use Zend\Cache;

require_once 'FactoryClasses.php';

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->markTestSkipped('Skipping test that duplicated Loader or PluginLoader efforts.');
    }

    public function tearDown()
    {
    }

    public function testFactoryCorrectCall()
    {
        $generated_frontend = Cache\Cache::factory('Core', 'File');
        $this->assertEquals('Zend\Cache\Core', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomBackend()
    {
        $generated_frontend = Cache\Cache::factory('Core', 'FooBarTest', array(), array(), false, false, true);
        $this->assertEquals('Zend\Cache\Core', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomBackend2()
    {
        $generated_frontend = Cache\Cache::factory('Core', '\ZendTest\Cache\FooBarTestBackend', array(), array(), false, true, true);
        $this->assertEquals('Zend\Cache\Core', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomFrontend()
    {
        $generated_frontend = Cache\Cache::factory('\FooBarTest', 'File', array(), array(), false, false, true);
        $this->assertEquals('ZendTest\Cache\Zend_Cache_Frontend_FooBarTest', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomFrontend2()
    {
        $generated_frontend = Cache\Cache::factory('\FooBarTestFrontend', 'File', array(), array(), true, false, true);
        $this->assertEquals('ZendTest\Cache\FooBarTestFrontend', get_class($generated_frontend));
    }
    public function testFactoryLoadsPlatformBackend()
    {
        try {
            $cache = Cache\Cache::factory('Core', 'Zend-Platform');
        } catch (Cache\Exception $e) {
            $message = $e->getMessage();
            if (strstr($message, 'Incorrect backend')) {
                $this->fail('Zend Platform is a valid backend');
            }
        }
    }

    public function testBadFrontend()
    {
        try {
            Cache\Cache::factory('badFrontend', 'File');
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Zend_Exception was expected but not thrown');
    }

    public function testBadBackend()
    {
        try {
            Cache\Cache::factory('Output', 'badBackend');
        } catch (Cache\Exception $e) {
            return;
        }
        $this->fail('Zend_Exception was expected but not thrown');
    }

}
