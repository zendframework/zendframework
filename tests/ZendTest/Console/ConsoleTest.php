<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace ZendTest\Console;

use Zend\Console\Console;
use Zend\Console\Adapter;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
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
        $this->assertTrue($adpater instanceof Adapter\AdapterInterface);

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
        $this->assertTrue($console instanceof Adapter\AdapterInterface);
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
       $this->assertTrue($console instanceof Adapter\AdapterInterface);
       $this->assertTrue($console instanceof Adapter\Posix);

       Console::overrideIsConsole(null);
       Console::resetInstance();

       $console = Console::getInstance('Windows');
       $this->assertTrue($console instanceof Adapter\AdapterInterface);
       $this->assertTrue($console instanceof Adapter\Windows);
    }
}
