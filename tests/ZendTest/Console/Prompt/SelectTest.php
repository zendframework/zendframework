<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Console\Char;

use Zend\Console\Prompt\Select;
use ZendTest\Console\TestAssets\ConsoleAdapter;

/**
 * @group      Zend_Console
 */
class SelectTest extends \PHPUnit_Framework_TestCase
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

    public function testCanSelectOption()
    {
        fwrite($this->adapter->stream, "0");

        $select = new Select('Select an option :', array('foo', 'bar'));
        $select->setConsole($this->adapter);
        ob_start();
        $response = $select->show();
        $text = ob_get_clean();
        $this->assertTrue((bool) preg_match('#0\) foo#', $text));
        $this->assertTrue((bool) preg_match('#1\) bar#', $text));
        $this->assertEquals('0', $response);
    }

    public function testCanSelectOptionWithCustomIndex()
    {
        fwrite($this->adapter->stream, "2");

        $select = new Select('Select an option :', array('2' => 'foo', '6' => 'bar'));
        $select->setConsole($this->adapter);
        ob_start();
        $response = $select->show();
        $text = ob_get_clean();
        $this->assertTrue((bool) preg_match('#2\) foo#', $text));
        $this->assertTrue((bool) preg_match('#6\) bar#', $text));
        $this->assertEquals('2', $response);
    }
}
