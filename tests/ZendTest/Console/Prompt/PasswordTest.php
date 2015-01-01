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

    public function testCanPromptPassword()
    {
        fwrite($this->adapter->stream, 'secret');

        $char = new Password();
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Password : \n");
        $this->assertEquals('secret', $response);
    }

    public function testCanPromptPasswordWithNewQuestion()
    {
        fwrite($this->adapter->stream, 'sh its a secret');

        $char = new Password("What is the secret?");
        $char->setEcho(false);
        $char->setConsole($this->adapter);
        ob_start();
        $response = $char->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "What is the secret\n");
        $this->assertEquals('sh its a secret', $response);
    }
}
