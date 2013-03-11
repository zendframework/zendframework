<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Console\Char;

use Zend\Console\Prompt\Confirm;
use ZendTest\Console\TestAssets\ConsoleAdapter;

/**
 * @group      Zend_Console
 */
class ConfirmTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConsoleAdapter
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new ConsoleAdapter();
        $this->adapter->stream = fopen('php://memory', 'w+');
    }

    public function tearDown()
    {
        fclose($this->adapter->stream);
    }
    
    public function testCanPromptConfirm()
    {
        fwrite($this->adapter->stream, 'y');

        $confirm = new Confirm("ZF2 is the better framework ?");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "ZF2 is the better framework ?\n");
        $this->assertTrue($response);
    }
    
    public function testCanPromptConfirmWithDefaultIgnoreCase()
    {
        fwrite($this->adapter->stream, 'Y');

        $confirm = new Confirm("ZF2 is the better framework ?");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "ZF2 is the better framework ?\n");
        $this->assertTrue($response);
    }
    
    public function testCanPromptConfirmWithoutIgnoreCase()
    {
        fwrite($this->adapter->stream, 'Yn');

        $confirm = new Confirm("ZF2 is the better framework ?");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        $confirm->setIgnoreCase(false);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "ZF2 is the better framework ?\n");
        $this->assertFalse($response);
    }
    
    public function testCanPromptConfirmWithYesNoCharChanged()
    {
        fwrite($this->adapter->stream, 'on0');

        $confirm = new Confirm("ZF2 is the better framework ?", "1", "0");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "ZF2 is the better framework ?\n");
        $this->assertFalse($response);
    }
    
    public function testCanPromptConfirmWithYesNoCharChangedWithSetter()
    {
        fwrite($this->adapter->stream, 'oaB');

        $confirm = new Confirm("ZF2 is the better framework ?", "1", "0");
        $confirm->setYesChar("A");
        $confirm->setNoChar("B");
        $confirm->setEcho(false);
        $confirm->setConsole($this->adapter);
        ob_start();
        $response = $confirm->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "ZF2 is the better framework ?\n");
        $this->assertTrue($response);
    }
}
