<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace ZendTest\Config\Writer;

use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;
use ZendTest\Config\Writer\TestAssets\PhpReader;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @group      Zend_Config
 */
class PhpArrayTest extends AbstractWriterTestCase
{
    protected $_tempName;

    public function setUp()
    {
        $this->writer = new PhpArray();
        $this->reader = new PhpReader();
    }

    /**
     * @group ZF-8234
     */
    public function testRender()
    {
        $config = new Config(array('test' => 'foo', 'bar' => array(0 => 'baz', 1 => 'foo')));

        $configString = $this->writer->toString($config);

        // build string line by line as we are trailing-whitespace sensitive.
        $expected = "<?php\n";
        $expected .= "return array (\n";
        $expected .= "  'test' => 'foo',\n";
        $expected .= "  'bar' => \n";
        $expected .= "  array (\n";
        $expected .= "    0 => 'baz',\n";
        $expected .= "    1 => 'foo',\n";
        $expected .= "  ),\n";
        $expected .= ");\n";

        $this->assertEquals($expected, $configString);
    }
}
