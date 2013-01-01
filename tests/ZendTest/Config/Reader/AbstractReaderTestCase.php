<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Config
 */

namespace ZendTest\Config\Reader;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Config\Reader\ReaderInterface;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
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
