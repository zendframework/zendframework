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

namespace ZendTest\Config\Reader;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Config\Reader\ReaderInterface;
use ReflectionClass;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
abstract class AbstractReaderTestCase extends TestCase
{
    /**
     * @var ReaderInterface
     */
    protected $reader;
    
    /**
     * Get test asset name for current test case.
     * 
     * @param  string $name
     * @return string
     */
    abstract protected function getTestAssetPath($name);
       
    public function testMissingFile()
    {
        $filename = $this->getTestAssetPath('no-file');
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', "doesn't exist or not readable");
        $config = $this->reader->fromFile($filename); 
    }
    
    public function testFromFile()
    {
        $config = $this->reader->fromFile($this->getTestAssetPath('include-base'));
        $this->assertEquals('foo', $config['foo']);
    }
    
    public function testFromEmptyString()
    {
        $config = $this->reader->fromString('');
        $this->assertTrue(!$config);
    }
}
