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
use ZendTest\Console\TestAssets\ConsoleAdapter;

/**
 * @group      Zend_Console
 */
class PasswordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\Console\Adapter\AbstractAdapter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = $this->getMock('Zend\Console\Adapter\AbstractAdapter', array('write', 'readChar'));
    }

    public function testCanPromptPassword()
    {
        $this->adapter->expects($this->at(0))->method('write')->with('Password: ');
        $this->adapter->expects($this->at(1))->method('readChar')->will($this->returnValue('f'));
        $this->adapter->expects($this->at(2))->method('readChar')->will($this->returnValue('o'));
        $this->adapter->expects($this->at(3))->method('readChar')->will($this->returnValue('o'));
        $this->adapter->expects($this->at(4))->method('readChar')->will($this->returnValue(PHP_EOL));

        $char = new Password('Password: ', false);

        $char->setConsole($this->adapter);

        $this->assertEquals('foo', $char->show());
    }

    public function testCanPromptPasswordWithNewQuestion()
    {
        $this->markTestIncomplete();
        fwrite($this->adapter->stream, 'sh its a secret');

        $char = new Password("What is the secret?", false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "What is the secret\n");
        $this->assertEquals('sh its a secret', $response);
    }
}
