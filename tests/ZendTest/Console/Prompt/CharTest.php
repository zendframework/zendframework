<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace ZendTest\Console\Char;

use Zend\Console\Prompt\Char;
use ZendTest\Console\TestAssets\ConsoleAdapter;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
 * @group      Zend_Console
 */
class CharTest extends \PHPUnit_Framework_TestCase
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

    public function testCanPromptChar()
    {
        fwrite($this->adapter->stream, 'a');

        $char = new Char();
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Please hit a key\n");
        $this->assertEquals('a', $response);
    }

    public function testCanPromptCharWithCharNotInDefaultMask()
    {
        fwrite($this->adapter->stream, 'zywa');

        $char = new Char();
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals('z', $response);
    }

    public function testCanPromptCharWithNewQuestionAndMask()
    {
        fwrite($this->adapter->stream, 'foo123');

        $char = new Char("Give a number", '0123456789');
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Give a number\n");
        $this->assertEquals('1', $response);
    }
}
