<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Console\Char;

use Zend\Console\Prompt\Line;
use ZendTest\Console\TestAssets\ConsoleAdapter;

/**
 * @group      Zend_Console
 */
class LineTest extends \PHPUnit_Framework_TestCase
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

    public function testCanReadLine()
    {
        fwrite($this->adapter->stream, 'Bryan is in the kitchen');

        $line = new Line('Where is Bryan ?');
        $line->setConsole($this->adapter);
        ob_start();
        $response = $line->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Where is Bryan ?");
        $this->assertEquals('Bryan is in the kitchen', $response);
    }

    public function testCanReadLineWithMax()
    {
        fwrite($this->adapter->stream, 'Kitchen no ?');

        $line = new Line('Where is Bryan ?', false, 7);
        $line->setConsole($this->adapter);
        ob_start();
        $response = $line->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Where is Bryan ?");
        $this->assertEquals('Kitchen', $response);
    }

    public function testCanReadLineWithEmptyAnswer()
    {
        $line = new Line('Where is Bryan ?', true);
        $line->setConsole($this->adapter);
        ob_start();
        $response = $line->show();
        $text = ob_get_clean();
        $this->assertEquals($text, "Where is Bryan ?");
        $this->assertEquals('', $response);
    }
}
