<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Reader;

use Zend\Config\Reader\Yaml as YamlReader;

/**
 * @group      Zend_Config
 */
class YamlTest extends AbstractReaderTestCase
{
    public function setUp()
    {
        if (!constant('TESTS_ZEND_CONFIG_YAML_ENABLED')) {
            $this->markTestSkipped('Yaml test for Zend\Config skipped');
        }

        if (constant('TESTS_ZEND_CONFIG_YAML_LIB_INCLUDE')) {
            require_once constant('TESTS_ZEND_CONFIG_YAML_LIB_INCLUDE');
        }

        $yamlReader = explode('::', constant('TESTS_ZEND_CONFIG_READER_YAML_CALLBACK'));
        if (isset($yamlReader[1])) {
            $this->reader = new YamlReader(array($yamlReader[0], $yamlReader[1]));
        } else {
            $this->reader = new YamlReader(array($yamlReader[0]));
        }
    }

    /**
     * getTestAssetPath(): defined by AbstractReaderTestCase.
     *
     * @see    AbstractReaderTestCase::getTestAssetPath()
     * @return string
     */
    protected function getTestAssetPath($name)
    {
        return __DIR__ . '/TestAssets/Yaml/' . $name . '.yaml';
    }

    public function testInvalidIniFile()
    {
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $arrayIni = $this->reader->fromFile($this->getTestAssetPath('invalid'));
    }

    public function testFromString()
    {
        $yaml = <<<ECS
test: foo
bar:
    baz
    foo

ECS;

        $arrayYaml = $this->reader->fromString($yaml);
        $this->assertEquals($arrayYaml['test'], 'foo');
        $this->assertEquals($arrayYaml['bar'][0], 'baz');
        $this->assertEquals($arrayYaml['bar'][1], 'foo');
    }

    public function testFromStringWithSection()
    {
        $yaml = <<<ECS
all:
    test: foo
    bar:
        baz
        foo

ECS;

        $arrayYaml = $this->reader->fromString($yaml);
        $this->assertEquals($arrayYaml['all']['test'], 'foo');
        $this->assertEquals($arrayYaml['all']['bar'][0], 'baz');
        $this->assertEquals($arrayYaml['all']['bar'][1], 'foo');
    }
}
