<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config\Writer;

use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;
use ZendTest\Config\Writer\TestAssets\PhpReader;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
