<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Writer;

use Zend\Config\Writer\Yaml as YamlWriter;
use Zend\Config\Config;
use Zend\Config\Reader\Yaml as YamlReader;

/**
 * @group      Zend_Config
 */
class YamlTest extends AbstractWriterTestCase
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

        $yamlWriter = explode('::', constant('TESTS_ZEND_CONFIG_WRITER_YAML_CALLBACK'));
        if (isset($yamlWriter[1])) {
            $this->writer = new YamlWriter(array($yamlWriter[0], $yamlWriter[1]));
        } else {
            $this->writer = new YamlWriter(array($yamlWriter[0]));
        }
    }

    public function testNoSection()
    {
        $config = new Config(array('test' => 'foo', 'test2' => array('test3' => 'bar')));

        $this->writer->toFile($this->getTestAssetFileName(), $config);

        $config = $this->reader->fromFile($this->getTestAssetFileName());

        $this->assertEquals('foo', $config['test']);
        $this->assertEquals('bar', $config['test2']['test3']);
    }

    public function testWriteAndReadOriginalFile()
    {
        $config = $this->reader->fromFile(__DIR__ . '/_files/allsections.yaml');

        $this->writer->toFile($this->getTestAssetFileName(), $config);

        $config = $this->reader->fromFile($this->getTestAssetFileName());

        $this->assertEquals('multi', $config['all']['one']['two']['three']);

    }
}
