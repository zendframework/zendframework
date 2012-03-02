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

use \PHPUnit_Framework_TestCase as TestCase,
    \Zend\Config\Reader\Reader,
    \ReflectionClass;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
abstract class AbstractReaderTestCase extends TestCase
{
    /**
     * @var Reader
     */
    protected $reader;
    
    /**
     * Get test asset name for current test case.
     * 
     * @return string
     */
    abstract protected function getTestAssetPath($name);
       
    public function testMissingFile()
    {
        $filename = $this->getTestAssetPath('no-file');
        $this->setExpectedException('Zend\Config\Exception\RuntimeException', "The file $filename doesn't exists.");
        $config = $this->reader->fromFile($filename); 
    }
    
    public function testFromFile()
    {
        $config = $this->reader->fromFile($this->getTestAssetPath('include-base'));
        $this->assertEquals('foo', $config['base']['foo']);
    }
    
    public function testFromEmptyString()
    {
        $config = $this->reader->fromString('');
        $this->assertTrue(!$config);
    }
}
