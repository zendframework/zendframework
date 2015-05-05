<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Console;

use Zend\Console\Console;

/**
 * @group      Zend_Console
 */
class ConsoleTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Console::overrideIsConsole(null);
        Console::resetInstance();
    }

    public function testCanTestIsConsole()
    {
        $this->assertTrue(Console::isConsole());
        $className = Console::detectBestAdapter();
        $adpater = new $className;
        $this->assertInstanceOf('Zend\Console\Adapter\AdapterInterface', $adpater);

        Console::overrideIsConsole(false);

        $this->assertFalse(Console::isConsole());
        $this->assertEquals(null, Console::detectBestAdapter());
    }

    public function testCanOverrideIsConsole()
    {
        $this->assertEquals(true, Console::isConsole());

        Console::overrideIsConsole(true);
        $this->assertEquals(true, Console::isConsole());

        Console::overrideIsConsole(false);
        $this->assertEquals(false, Console::isConsole());

        Console::overrideIsConsole(1);
        $this->assertEquals(true, Console::isConsole());

        Console::overrideIsConsole('false');
        $this->assertEquals(true, Console::isConsole());
    }

    public function testCanGetInstance()
    {
        $console = Console::getInstance();
        $this->assertInstanceOf('Zend\Console\Adapter\AdapterInterface', $console);
    }

    public function testCanNotGetInstanceInNoConsoleMode()
    {
        Console::overrideIsConsole(false);
        $this->setExpectedException('Zend\Console\Exception\RuntimeException');
        Console::getInstance();
    }

    public function testCanForceInstance()
    {
        $console = Console::getInstance('Posix');
        $this->assertInstanceOf('Zend\Console\Adapter\AdapterInterface', $console);
        $this->assertInstanceOf('Zend\Console\Adapter\Posix', $console);

        Console::overrideIsConsole(null);
        Console::resetInstance();

        $console = Console::getInstance('Windows');
        $this->assertInstanceOf('Zend\Console\Adapter\AdapterInterface', $console);
        $this->assertInstanceOf('Zend\Console\Adapter\Windows', $console);
    }
}
