<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Console\Prompt;

use Zend\Console\Prompt\Password;

/**
 * Tests for {@see \Zend\Console\Prompt\Password}
 *
 * @covers \Zend\Console\Prompt\Password
 */
class PasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Console\Adapter\AbstractAdapter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = $this->getMock(
            'Zend\Console\Adapter\AbstractAdapter',
            array('write', 'writeLine', 'readChar')
        );
    }

    public function testCanPromptPassword()
    {
        $this->adapter->expects($this->at(0))->method('writeLine')->with('Password: ');
        $this->adapter->expects($this->at(1))->method('readChar')->will($this->returnValue('f'));
        $this->adapter->expects($this->at(2))->method('readChar')->will($this->returnValue('o'));
        $this->adapter->expects($this->at(3))->method('readChar')->will($this->returnValue('o'));
        $this->adapter->expects($this->at(4))->method('readChar')->will($this->returnValue(PHP_EOL));

        $char = new Password('Password: ', false);

        $char->setConsole($this->adapter);

        $this->assertEquals('foo', $char->show());
    }

    public function testCanPromptPasswordRepeatedly()
    {
        $this->adapter->expects($this->at(0))->method('writeLine')->with('New password? ');
        $this->adapter->expects($this->at(1))->method('readChar')->will($this->returnValue('b'));
        $this->adapter->expects($this->at(2))->method('readChar')->will($this->returnValue('a'));
        $this->adapter->expects($this->at(3))->method('readChar')->will($this->returnValue('r'));
        $this->adapter->expects($this->at(4))->method('readChar')->will($this->returnValue(PHP_EOL));
        $this->adapter->expects($this->at(5))->method('writeLine')->with('New password? ');
        $this->adapter->expects($this->at(6))->method('readChar')->will($this->returnValue('b'));
        $this->adapter->expects($this->at(7))->method('readChar')->will($this->returnValue('a'));
        $this->adapter->expects($this->at(8))->method('readChar')->will($this->returnValue('z'));
        $this->adapter->expects($this->at(9))->method('readChar')->will($this->returnValue(PHP_EOL));

        $char = new Password('New password? ', false);

        $char->setConsole($this->adapter);

        $this->assertEquals('bar', $char->show());
        $this->assertEquals('baz', $char->show());
    }

    public function testProducesStarSymbolOnInput()
    {
        $this->adapter->expects($this->at(1))->method('readChar')->will($this->returnValue('t'));
        $this->adapter->expects($this->at(2))->method('write')->with('*');
        $this->adapter->expects($this->at(3))->method('readChar')->will($this->returnValue('a'));
        $this->adapter->expects($this->at(4))->method('write')->with('**');
        $this->adapter->expects($this->at(5))->method('readChar')->will($this->returnValue('b'));
        $this->adapter->expects($this->at(6))->method('write')->with('***');
        $this->adapter->expects($this->at(7))->method('readChar')->will($this->returnValue(PHP_EOL));

        $char = new Password('New password? ', true);

        $char->setConsole($this->adapter);

        $char->show();
    }
}
