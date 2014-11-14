<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Reader;

use Zend\Config\Reader\Json;

/**
 * @group      Zend_Config
 */
class JsonTest extends AbstractReaderTestCase
{
    public function setUp()
    {
        $this->reader = new Json();
    }

    /**
     * getTestAssetPath(): defined by AbstractReaderTestCase.
     *
     * @see    AbstractReaderTestCase::getTestAssetPath()
     * @return string
     */
    protected function getTestAssetPath($name)
    {
        return __DIR__ . '/TestAssets/Json/' . $name . '.json';
    }

    public function testInvalidJsonFile()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $arrayJson = $this->reader->fromFile($this->getTestAssetPath('invalid'));
    }

    public function testIncludeAsElement()
    {
        $arrayJson = $this->reader->fromFile($this->getTestAssetPath('include-base_nested'));
        $this->assertEquals($arrayJson['bar']['foo'], 'foo');
    }

    public function testFromString()
    {
        $json = '{ "test" : "foo", "bar" : [ "baz", "foo" ] }';

        $arrayJson = $this->reader->fromString($json);

        $this->assertEquals($arrayJson['test'], 'foo');
        $this->assertEquals($arrayJson['bar'][0], 'baz');
        $this->assertEquals($arrayJson['bar'][1], 'foo');
    }

    public function testInvalidString()
    {
        $json = '{"foo":"bar"';

        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $arrayIni = $this->reader->fromString($json);
    }
}
