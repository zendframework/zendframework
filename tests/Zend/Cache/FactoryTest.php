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

/**
 * Zend_Cache
 */
require_once 'Zend/Cache.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


require_once 'Zend/Cache/Backend/File.php';
class Zend_Cache_Backend_FooBarTest extends Zend_Cache_Backend_File { }
class FooBarTestBackend extends Zend_Cache_Backend_File { }

require_once 'Zend/Cache/Core.php';
class Zend_Cache_Frontend_FooBarTest extends Zend_Cache_Core { }
class FooBarTestFrontend extends Zend_Cache_Core { }

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class Zend_Cache_FactoryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
    }

    public function tearDown()
    {
    }

    public function testFactoryCorrectCall()
    {
        $generated_frontend = Zend_Cache::factory('Core', 'File');
        $this->assertEquals('Zend_Cache_Core', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomBackend()
    {
        $generated_frontend = Zend_Cache::factory('Core', 'FooBarTest', array(), array(), false, false, true);
        $this->assertEquals('Zend_Cache_Core', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomBackend2()
    {
        $generated_frontend = Zend_Cache::factory('Core', 'FooBarTestBackend', array(), array(), false, true, true);
        $this->assertEquals('Zend_Cache_Core', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomFrontend()
    {
        $generated_frontend = Zend_Cache::factory('FooBarTest', 'File', array(), array(), false, false, true);
        $this->assertEquals('Zend_Cache_Frontend_FooBarTest', get_class($generated_frontend));
    }

    public function testFactoryCorrectCallWithCustomFrontend2()
    {
        $generated_frontend = Zend_Cache::factory('FooBarTestFrontend', 'File', array(), array(), true, false, true);
        $this->assertEquals('FooBarTestFrontend', get_class($generated_frontend));
    }
    public function testFactoryLoadsPlatformBackend()
    {
        try {
            $cache = Zend_Cache::factory('Core', 'Zend-Platform');
        } catch (Zend_Cache_Exception $e) {
            $message = $e->getMessage();
            if (strstr($message, 'Incorrect backend')) {
                $this->fail('Zend Platform is a valid backend');
            }
        }
    }

    public function testBadFrontend()
    {
        try {
            Zend_Cache::factory('badFrontend', 'File');
        } catch (Zend_Exception $e) {
            return;
        }
        $this->fail('Zend_Exception was expected but not thrown');
    }

    public function testBadBackend()
    {
        try {
            Zend_Cache::factory('Output', 'badBackend');
        } catch (Zend_Exception $e) {
            return;
        }
        $this->fail('Zend_Exception was expected but not thrown');
    }

}
